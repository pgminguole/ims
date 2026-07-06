<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CheckUserPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Find an admin user (Regional ICT Admin)
        $adminUser = User::query()->role('admin')->first();

        if (!$adminUser) {
            $this->command->error("No user with 'admin' role found.");
            return;
        }

        $this->command->info("Checking permissions for user: {$adminUser->name} ({$adminUser->email})");
        $this->command->info("Region ID: " . ($adminUser->region_id ?? 'None'));
        $this->command->info("Assigned Roles: " . implode(', ', $adminUser->getRoleNames()->toArray()));

        $permissionsToCheck = [
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_courts', 'create_courts', 'edit_courts', 'delete_courts',
            'view_offices', 'create_offices', 'edit_offices', 'delete_offices',
            'view_locations', 'create_locations', 'edit_locations', 'delete_locations',
            'manage_users', 'manage_courts' // Check old ones too
        ];

        $headers = ['Permission', 'Can?'];
        $rows = [];

        foreach ($permissionsToCheck as $perm) {
            $can = $adminUser->can($perm) ? 'YES' : 'NO';
            $rows[] = [$perm, $can];
        }

        $this->command->table($headers, $rows);
        
        // Also verify direct permission via role
        $role = $adminUser->roles->first();
        if ($role) {
            $this->command->info("\nDirect Role Permissions for '{$role->name}':");
            $rolePerms = $role->permissions->pluck('name')->toArray();
            $this->command->info(implode(', ', $rolePerms));
        }
    }
}
