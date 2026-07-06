<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Get the first region for the ICT Admin
        $region = Region::first();
        if (!$region) {
            $this->command->warn('No regions found. ICT Admin will not have a region assigned.');
        }

        // Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'ict@judicial.gov.gh'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('1234@abcd'),
                'employee_id' => 'SUP001',
                'phone' => '+233240000000',
                'position' => 'Super Administrator',
                'department' => 'ICT',
                'role_id' => 1, // super_admin
                'is_active' => true,
                'is_approved' => true,
                'approved_at' => now(),
                'status' => 'active',
            ]
        );
        $superAdmin->assignRole('super_admin');

        // ICT Admin
        $ictAdmin = User::updateOrCreate(
            ['email' => 'admin@judicial.gov.gh'],
            [
                'name' => 'ICT Administrator',
                'password' => Hash::make('nana@system1admin'),
                'employee_id' => 'JSG001',
                'phone' => '+233240000001',
                'position' => 'ICT Administrator',
                'department' => 'ICT',
                'role_id' => 2, // admin
                'region_id' => $region ? $region->id : null,
                'is_active' => true,
                'is_approved' => true,
                'approved_at' => now(),
                'status' => 'active',
            ]
        );
        $ictAdmin->assignRole('admin');

        // Auditor
        $auditor = User::updateOrCreate(
            ['email' => 'auditor@judicial.gov.gh'],
            [
                'name' => 'Auditor',
                'password' => Hash::make('audit@Psg123@gh'),
                'employee_id' => 'JSG003',
                'phone' => '+233240000003',
                'position' => 'Internal Auditor',
                'department' => 'Internal Audit',
                'role_id' => 3, // auditor
                'is_active' => true,
                'is_approved' => true,
                'approved_at' => now(),
                'status' => 'active',
            ]
        );
        $auditor->assignRole('auditor');
    }
}