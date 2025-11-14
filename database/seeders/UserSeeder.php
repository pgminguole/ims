<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin User
        User::create([
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@judicial.gov.gh',
            'password' => Hash::make('password'),
            'employee_id' => 'JSG001',
            'phone' => '+233240000001',
            'position' => 'System Administrator',
            'department' => 'ICT',
            'role_id' => 1,
            'is_active' => true,
            'is_approved' => true,
            'approved_at' => now(),
            'status' => 'active',
        ]);

        // ICT Staff
        User::create([
            'first_name' => 'ICT',
            'last_name' => 'Manager',
            'email' => 'ict@judicial.gov.gh',
            'password' => Hash::make('password'),
            'employee_id' => 'JSG002',
            'phone' => '+233240000002',
            'position' => 'ICT Manager',
            'department' => 'ICT',
            'role_id' => 1,
            'is_active' => true,
            'is_approved' => true,
            'approved_at' => now(),
            'status' => 'active',
        ]);

        // Auditor
        User::create([
            'first_name' => 'Internal',
            'last_name' => 'Auditor',
            'email' => 'auditor@judicial.gov.gh',
            'password' => Hash::make('password'),
            'employee_id' => 'JSG003',
            'phone' => '+233240000003',
            'position' => 'Internal Auditor',
            'department' => 'Internal Audit',
            'role_id' => 2,
            'is_active' => true,
            'is_approved' => true,
            'approved_at' => now(),
            'status' => 'active',
        ]);

        // Judge
        User::create([
            'first_name' => 'Justice Kwame',
            'last_name' => 'Mensah',
            'email' => 'judge.mensah@judicial.gov.gh',
            'password' => Hash::make('password'),
            'employee_id' => 'JSG004',
            'phone' => '+233240000004',
            'position' => 'High Court Judge',
            'department' => 'Judiciary',
            'court_id' => 1,
            'region_id' => 1,
            'role_id' => 3,
            'is_active' => true,
            'is_approved' => true,
            'approved_at' => now(),
            'status' => 'active',
        ]);

        // Registry Staff
        User::create([
            'first_name' => 'Registry',
            'last_name' => 'Officer',
            'email' => 'registry@judicial.gov.gh',
            'password' => Hash::make('password'),
            'employee_id' => 'JSG005',
            'phone' => '+233240000005',
            'position' => 'Registry Officer',
            'department' => 'Registry',
            'role_id' =>1,
            'is_active' => true,
            'is_approved' => true,
            'approved_at' => now(),
            'status' => 'active',
        ]);

        // Regular Staff
        User::create([
            'first_name' => 'Regular',
            'last_name' => 'Staff',
            'email' => 'staff@judicial.gov.gh',
            'password' => Hash::make('password'),
            'employee_id' => 'JSG006',
            'phone' => '+233240000006',
            'position' => 'Administrative Officer',
            'department' => 'Administration',
            'role_id' => 2,
            'is_active' => true,
            'is_approved' => true,
            'approved_at' => now(),
            'status' => 'active',
        ]);
    }
}