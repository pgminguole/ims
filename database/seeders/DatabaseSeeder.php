<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RegionSeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            CourtSeeder::class,
            UserSeeder::class,
            RaoSeeder::class,
            DirectorSeeder::class,
            ManagementTeamSeeder::class,
            JudgeSeeder::class,
            // AssetSeeder::class,
            // OfficeSeeder::class
        ]);
    }
}