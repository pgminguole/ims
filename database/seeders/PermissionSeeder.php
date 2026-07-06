<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Assets
            'view_assets', 'create_assets', 'edit_assets', 'delete_assets',
            'assign_assets', 'return_assets', 'manage_obsolete_assets', 'manage_dts',
            
            // Reports & Audit
            'view_reports', 'view_audit_logs',
            
            // Administration (Granular)
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_courts', 'create_courts', 'edit_courts', 'delete_courts',
            'view_offices', 'create_offices', 'edit_offices', 'delete_offices',
            'view_locations', 'create_locations', 'edit_locations', 'delete_locations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super Admin - Everything
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions($permissions);
        }

        // Admin (ICT Admin) - Operational permissions
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions([
                // Assets
                'view_assets', 'create_assets', 'edit_assets', 'assign_assets', 'return_assets',
                'manage_obsolete_assets', 'manage_dts',
                'view_reports',
                
                // Users
                'view_users', 'create_users', 'edit_users', 
                
                // Courts
                'view_courts', 'create_courts', 'edit_courts',
                
                // Offices
                'view_offices', 'create_offices', 'edit_offices',
                
                // Locations
                'view_locations', 'create_locations', 'edit_locations',
            ]);
        }

        // Auditor - View only
        $auditorRole = Role::where('name', 'auditor')->first();
        if ($auditorRole) {
            $auditorRole->syncPermissions([
                'view_assets',
                'view_reports',
                'view_audit_logs',
                'view_users',
                'view_courts',
                'view_offices',
                'view_locations'
            ]);
        }
    }
}
