<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            RegionSeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            CourtSeeder::class,
            UserSeeder::class,
            AssetSeeder::class,
      
        ]);
    }
}