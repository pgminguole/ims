<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'admin', 'description' => 'System Administrator'],
            ['name' => 'auditor', 'description' => 'Auditor'],
            ['name' => 'ict_staff', 'description' => 'ICT Staff'],
            ['name' => 'registry', 'description' => 'Registry Staff'],
            ['name' => 'judge', 'description' => 'Judge'],
            ['name' => 'staff', 'description' => 'General Staff'],
            ['name' => 'director', 'description' => 'Director'],
            ['name' => 'reg_admin', 'description' => 'Regional Administrator'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}