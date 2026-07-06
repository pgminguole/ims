<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'super_admin', 'description' => 'Super Administrator with full access'],
            ['name' => 'admin', 'description' => 'System Administrator'],
            ['name' => 'auditor', 'description' => 'Auditor'],
            ['name' => 'ict_staff', 'description' => 'ICT Staff'],
            ['name' => 'registry', 'description' => 'Registry Staff'],
            ['name' => 'judge', 'description' => 'Judge'],
            ['name' => 'staff', 'description' => 'General Staff'],
            ['name' => 'direcor', 'description' => 'Director'],
            ['name' => 'deputydirector', 'description' => 'Deputy Director'],
            ['name' => 'rao', 'description' => 'Regional Administrator'],
            ['name' => 'ict_system_admin', 'description' => 'Regional ICT System Administrator'],
          
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(['name' => $roleData['name']], $roleData);
            
            // Sync with Spatie Roles if they exist
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleData['name'], 'guard_name' => 'web']);
            }
        }
    }
}