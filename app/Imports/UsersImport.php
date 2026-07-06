<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Court;
use App\Models\Location;
use App\Models\Role;
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
    private $rolesMap = [];
    private $defaultRoleId = null;

    public function __construct()
    {
        // Pre-load all roles into a map for quick lookup
        $roles = Role::all();
        foreach ($roles as $role) {
            $this->rolesMap[strtolower($role->name)] = $role->id;
        }
        
        // Set default role to 'staff'
        $this->defaultRoleId = $this->rolesMap['staff'] ?? null;
        
        if (!$this->defaultRoleId) {
            Log::warning('Default role "staff" not found in database');
        }
    }

    public function model(array $row)
    {
        try {
            // Check if email already exists (only if email is provided)
            if (!empty($row['email']) && User::where('email', $row['email'])->exists()) {
                $this->errors[] = "Row with email '{$row['email']}': Email already exists";
                return null;
            }

            // Generate a unique identifier for the user if no email
            $email = !empty($row['email']) ? $row['email'] : $this->generateUniqueIdentifier($row['name']);

            // Find court if provided
            $court = null;
            if (!empty($row['court'])) {
                $court = Court::where('name', $row['court'])
                    ->orWhere('code', $row['court'])
                    ->first();
                
                if (!$court) {
                    $this->errors[] = "Row with name '{$row['name']}': Court '{$row['court']}' not found";
                }
            }

            // Find location if provided
            $location = null;
            if (!empty($row['location'])) {
                $location = Location::where('name', $row['location'])->first();
                
                if (!$location) {
                    $this->errors[] = "Row with name '{$row['name']}': Location '{$row['location']}' not found";
                }
            }

            // Find registry officer if provided
            $registryOfficer = null;
            if (!empty($row['registry_officer'])) {
                $registryOfficer = User::where('email', $row['registry_officer'])
                    ->orWhere('name', $row['registry_officer'])
                    ->first();
            }

            // Find invited by user if provided
            $invitedBy = null;
            if (!empty($row['invited_by'])) {
                $invitedBy = User::where('email', $row['invited_by'])
                    ->orWhere('name', $row['invited_by'])
                    ->first();
            }

            // Parse role - use provided role or default to 'staff'
            $roleId = $this->defaultRoleId;
            if (!empty($row['role'])) {
                $roleName = strtolower(trim($row['role']));
                $roleId = $this->rolesMap[$roleName] ?? $this->defaultRoleId;
                
                if (!$roleId && $this->rolesMap[$roleName] === null) {
                    $this->errors[] = "Row with name '{$row['name']}': Role '{$row['role']}' not found. Using default 'staff' role.";
                }
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

            // Generate slug from name
            $slug = Str::slug($row['name']);

            // Ensure slug is unique
            $originalSlug = $slug;
            $counter = 1;
            while (User::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $this->successCount++;

            $user = new User([
                'name' => $row['name'],
                'email' => $email,
                'phone' => $row['phone'] ?? null,
                'password' => Hash::make($password),
                'role_id' => $roleId,
                'court_id' => $court?->id,
                'location_id' => $location?->id,
                'status' => $status,
                'slug' => $slug,
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

            if ($user->save()) {
                // You can still assign Spatie roles if needed, but now we also have role_id
                if (!empty($row['roles'])) {
                    $roles = array_map('trim', explode(',', $row['roles']));
                    $user->assignRole($roles);
                }
            }

            return $user;

        } catch (\Exception $e) {
            $this->errors[] = "Row with name '{$row['name']}': {$e->getMessage()}";
            Log::error('User import error', ['row' => $row, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'role' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not exceed 255 characters',
            'email.email' => 'Email must be a valid email address',
            'role.string' => 'Role must be a text value',
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

    private function generateUniqueIdentifier($name)
    {
        $baseSlug = Str::slug($name);
        $identifier = $baseSlug . '@' . Str::random(8) . '.local';
        
        // Ensure the generated identifier is unique
        $counter = 1;
        while (User::where('email', $identifier)->exists()) {
            $identifier = $baseSlug . '-' . $counter . '@' . Str::random(8) . '.local';
            $counter++;
        }
        
        return $identifier;
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