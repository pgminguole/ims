<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Region;
use App\Models\Court;
use App\Models\Location;
use App\Models\User;
use App\Models\AssetHistory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Throwable;

class AssetsImport implements 
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
            // Check if asset tag already exists
            if (Asset::where('asset_tag', $row['asset_tag'])->exists()) {
                $this->errors[] = "Row with asset tag '{$row['asset_tag']}': Asset tag already exists";
                return null;
            }

            // Find category by name
            $category = null;
            if (!empty($row['category'])) {
                $category = Category::where('name', $row['category'])
                    ->whereNull('parent_id')
                    ->first();
                
                if (!$category) {
                    $this->errors[] = "Row with asset '{$row['asset_name']}': Category '{$row['category']}' not found";
                }
            }

            // Find subcategory by name
            $subcategory = null;
            if (!empty($row['subcategory']) && $category) {
                $subcategory = Category::where('name', $row['subcategory'])
                    ->where('parent_id', $category->id)
                    ->first();
            }

            // Find region by name or code
            $region = null;
            if (!empty($row['region'])) {
                $region = Region::where('name', $row['region'])
                    ->orWhere('code', $row['region'])
                    ->first();
                
                if (!$region) {
                    $this->errors[] = "Row with asset '{$row['asset_name']}': Region '{$row['region']}' not found";
                }
            }

            // Find court by name or code
            $court = null;
            if (!empty($row['court'])) {
                $court = Court::where('name', $row['court'])
                    ->orWhere('code', $row['court'])
                    ->first();
                
                if (!$court) {
                    $this->errors[] = "Row with asset '{$row['asset_name']}': Court '{$row['court']}' not found";
                }
            }

            // Find location by name
            $location = null;
            if (!empty($row['location'])) {
                $location = Location::where('name', $row['location'])->first();
            }

            // Find assigned user if provided
            $assignedUser = null;
            if (!empty($row['assigned_to'])) {
                $assignedUser = User::where('email', $row['assigned_to'])
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) = ?", [$row['assigned_to']])
                    ->first();
            }

            // Parse dates
            $purchaseDate = $this->parseDate($row['purchase_date'] ?? null);
            $receivedDate = $this->parseDate($row['received_date'] ?? null);
            $assignedDate = $this->parseDate($row['assigned_date'] ?? null);
            $warrantyExpiry = $this->parseDate($row['warranty_expiry'] ?? null);
            $lastMaintenance = $this->parseDate($row['last_maintenance'] ?? null);
            $nextMaintenance = $this->parseDate($row['next_maintenance'] ?? null);

            // Parse status and condition
            $status = strtolower($row['status'] ?? 'available');
            if (!in_array($status, ['available', 'assigned', 'maintenance', 'retired', 'lost', 'disposed'])) {
                $status = 'available';
            }

            $condition = strtolower($row['condition'] ?? 'good');
            if (!in_array($condition, ['excellent', 'good', 'fair', 'poor', 'broken'])) {
                $condition = 'good';
            }

            // Generate unique identifiers
            $slug = Str::slug($row['asset_name'] . '-' . $row['asset_tag']);
            $assetId = 'AST-' . strtoupper(uniqid());

            $this->successCount++;

            $asset = new Asset([
                'slug' => $slug,
                'asset_id' => $assetId,
                'asset_name' => $row['asset_name'],
                'asset_tag' => $row['asset_tag'],
                'serial_number' => $row['serial_number'] ?? null,
                'model' => $row['model'] ?? null,
                'brand' => $row['brand'] ?? null,
                'manufacturer' => $row['manufacturer'] ?? null,
                'category_id' => $category?->id,
                'subcategory_id' => $subcategory?->id,
                'region_id' => $region?->id,
                'court_id' => $court?->id,
                'location_id' => $location?->id,
                'purchase_cost' => $row['purchase_cost'] ?? null,
                'current_value' => $row['current_value'] ?? null,
                'purchase_date' => $purchaseDate,
                'recieved_date' => $receivedDate,
                'assigned_date' => $assignedDate,
                'supplier' => $row['supplier'] ?? null,
                'warranty_period' => $row['warranty_period'] ?? null,
                'warranty_expiry' => $warrantyExpiry,
                'warranty_information' => $row['warranty_information'] ?? null,
                'specifications' => $row['specifications'] ?? null,
                'description' => $row['description'] ?? null,
                'condition' => $condition,
                'status' => $status,
                'ip_address' => $row['ip_address'] ?? null,
                'mac_address' => $row['mac_address'] ?? null,
                'depreciation_method' => $row['depreciation_method'] ?? null,
                'maintenance_schedule' => $row['maintenance_schedule'] ?? null,
                'last_maintenance' => $lastMaintenance,
                'next_maintenance' => $nextMaintenance,
                'maintenance_notes' => $row['maintenance_notes'] ?? null,
                'assigned_to' => $assignedUser?->id,
                'assigned_type' => $row['assigned_type'] ?? null,
                'registry_id' => auth()->id(),
            ]);

            if ($asset->save()) {
                // Log the creation
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'action' => 'created',
                    'description' => 'Asset created via Excel import',
                    'performed_by' => auth()->id(),
                    'performed_at' => now()
                ]);
            }

            return $asset;

        } catch (\Exception $e) {
            $this->errors[] = "Row with asset '{$row['asset_name']}': {$e->getMessage()}";
            Log::error('Asset import error', ['row' => $row, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'asset_name' => 'required|string|max:255',
            'asset_tag' => 'required|string|max:255',
            'status' => 'nullable|in:available,assigned,maintenance,retired,lost,disposed',
            'condition' => 'nullable|in:excellent,good,fair,poor,broken',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'asset_name.required' => 'Asset name is required',
            'asset_tag.required' => 'Asset tag is required',
            'status.in' => 'Status must be one of: available, assigned, maintenance, retired, lost, disposed',
            'condition.in' => 'Condition must be one of: excellent, good, fair, poor, broken',
        ];
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle Excel date serial numbers
            if (is_numeric($date)) {
                return Carbon::createFromFormat('Y-m-d', '1899-12-30')
                    ->addDays($date);
            }
            
            // Handle various date formats
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
        Log::error('Asset import error', ['error' => $e->getMessage()]);
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