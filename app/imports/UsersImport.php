<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Court;
use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class UsersImport implements 
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
            // Check if email already exists
            if (User::where('email', $row['email'])->exists()) {
                $this->errors[] = "Row with email '{$row['email']}': Email already exists";
                return null;
            }

            // Check if username already exists
            if (User::where('username', $row['username'])->exists()) {
                $this->errors[] = "Row with username '{$row['username']}': Username already exists";
                return null;
            }

            // Find court if provided
            $court = null;
            if (!empty($row['court'])) {
                $court = Court::where('name', $row['court'])
                    ->orWhere('code', $row['court'])
                    ->first();
                
                if (!$court) {
                    $this->errors[] = "Row with email '{$row['email']}': Court '{$row['court']}' not found";
                }
            }

            // Find location if provided
            $location = null;
            if (!empty($row['location'])) {
                $location = Location::where('name', $row['location'])->first();
                
                if (!$location) {
                    $this->errors[] = "Row with email '{$row['email']}': Location '{$row['location']}' not found";
                }
            }

            // Find registry officer if provided
            $registryOfficer = null;
            if (!empty($row['registry_officer'])) {
                $registryOfficer = User::where('email', $row['registry_officer'])
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) = ?", [$row['registry_officer']])
                    ->first();
            }

            // Find invited by user if provided
            $invitedBy = null;
            if (!empty($row['invited_by'])) {
                $invitedBy = User::where('email', $row['invited_by'])
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) = ?", [$row['invited_by']])
                    ->first();
            }

            // Parse status
            $status = 'active';
            if (!empty($row['status'])) {
                $status = strtolower($row['status']);
                if (!in_array($status, ['active', 'inactive', 'suspended'])) {
                    $status = 'active';
                }
            }

            // Parse boolean fields
            $isApproved = $this->parseBoolean($row['is_approved'] ?? 'yes');
            $block = $this->parseBoolean($row['block'] ?? 'no');
            $requirePasswordReset = $this->parseBoolean($row['require_password_reset'] ?? 'no');
            $isExpire = $this->parseBoolean($row['is_expire'] ?? 'no');

            // Default password (users should reset on first login)
            $password = !empty($row['password']) ? $row['password'] : 'Password123!';

            $this->successCount++;

            $user = new User([
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'username' => $row['username'],
                'phone' => $row['phone'] ?? null,
                'password' => Hash::make($password),
                'access_type' => $row['access_type'],
                'court_id' => $court?->id,
                'location_id' => $location?->id,
                'status' => $status,
                'slug' => Str::slug($row['first_name'] . '-' . $row['last_name']),
                'is_approved' => $isApproved,
                'approved_at' => $isApproved ? now() : null,
                'block' => $block,
                'require_password_reset' => $requirePasswordReset,
                'is_expire' => $isExpire,
                'expire_date' => !empty($row['expire_date']) ? $row['expire_date'] : null,
                'invited_by' => $invitedBy?->id,
                'invited_date' => !empty($row['invited_date']) ? $row['invited_date'] : null,
                'registry_id' => $registryOfficer?->id,
            ]);

            // Assign roles after user is created
            if ($user->save() && !empty($row['roles'])) {
                $roles = array_map('trim', explode(',', $row['roles']));
                $user->assignRole($roles);
            }

            return $user;

        } catch (\Exception $e) {
            $this->errors[] = "Row with email '{$row['email']}': {$e->getMessage()}";
            Log::error('User import error', ['row' => $row, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'username' => 'required|string',
            'access_type' => 'required|in:judge,staff,registry,admin',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'username.required' => 'Username is required',
            'access_type.required' => 'Access type is required',
            'access_type.in' => 'Access type must be one of: judge, staff, registry, admin',
        ];
    }

    private function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        $value = strtolower(trim($value));
        return in_array($value, ['yes', '1', 'true', 'active', 'y']);
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
        Log::error('User import error', ['error' => $e->getMessage()]);
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