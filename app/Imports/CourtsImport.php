<?php

namespace App\Imports;

use App\Models\Court;
use App\Models\Region;
use App\Models\Location;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class CourtsImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    private $errors = [];
    private $successCount = 0;

    public function model(array $row)
    {
        try {
            // Find region by name or code
            $region = Region::where('name', $row['region'])
                ->orWhere('code', $row['region'])
                ->first();

            if (!$region) {
                $this->errors[] = "Row with court '{$row['name']}': Region '{$row['region']}' not found";
                return null;
            }

            // Find location if provided
            $location = null;
            if (!empty($row['location'])) {
                $location = Location::where('name', $row['location'])
                    ->where('region_id', $region->id)
                    ->first();
            }

            // Find presiding judge if provided
            $presidingJudge = null;
            if (!empty($row['presiding_judge'])) {
                $presidingJudge = User::where('access_type', 'judge')
                    ->where(function($query) use ($row) {
                        $query->where('email', $row['presiding_judge'])
                              ->orWhereRaw("CONCAT(first_name, ' ', last_name) = ?", [$row['presiding_judge']]);
                    })
                    ->first();
            }

            // Find registry officer if provided
            $registryOfficer = null;
            if (!empty($row['registry_officer'])) {
                $registryOfficer = User::where('access_type', 'registry')
                    ->where(function($query) use ($row) {
                        $query->where('email', $row['registry_officer'])
                              ->orWhereRaw("CONCAT(first_name, ' ', last_name) = ?", [$row['registry_officer']]);
                    })
                    ->first();
            }

            // Check if court code already exists
            if (Court::where('code', $row['code'])->exists()) {
                $this->errors[] = "Row with court '{$row['name']}': Court code '{$row['code']}' already exists";
                return null;
            }

            $this->successCount++;

            return new Court([
                'name' => $row['name'],
                'code' => $row['code'],
                'type' => $row['type'],
                'region_id' => $region->id,
                'location_id' => $location?->id,
                'address' => $row['address'] ?? '',
                'presiding_judge' => $presidingJudge?->id,
                'registry_officer' => $registryOfficer?->id,
                'is_active' => isset($row['is_active']) ? 
                    (strtolower($row['is_active']) === 'yes' || $row['is_active'] === '1' || strtolower($row['is_active']) === 'active') : true,
            ]);
        } catch (\Exception $e) {
            $this->errors[] = "Row with court '{$row['name']}': {$e->getMessage()}";
            Log::error('Court import error', ['row' => $row, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'required|in:high_court,district_court,magistrate_court,special_court',
            'region' => 'required|string',
            'address' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Court name is required',
            'code.required' => 'Court code is required',
            'type.required' => 'Court type is required',
            'type.in' => 'Court type must be one of: high_court, district_court, magistrate_court, special_court',
            'region.required' => 'Region is required',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
        Log::error('Court import error', ['error' => $e->getMessage()]);
    }

    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}