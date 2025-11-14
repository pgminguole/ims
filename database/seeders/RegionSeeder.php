<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $regions = [
            ['name' => 'Greater Accra', 'code' => 'GA', 'description' => 'Greater Accra Region'],
            ['name' => 'Ashanti', 'code' => 'AS', 'description' => 'Ashanti Region'],
            ['name' => 'Western', 'code' => 'WE', 'description' => 'Western Region'],
            ['name' => 'Central', 'code' => 'CE', 'description' => 'Central Region'],
            ['name' => 'Eastern', 'code' => 'EA', 'description' => 'Eastern Region'],
            ['name' => 'Volta', 'code' => 'VO', 'description' => 'Volta Region'],
            ['name' => 'Northern', 'code' => 'NO', 'description' => 'Northern Region'],
            ['name' => 'Upper East', 'code' => 'UE', 'description' => 'Upper East Region'],
            ['name' => 'Upper West', 'code' => 'UW', 'description' => 'Upper West Region'],
            ['name' => 'Bono', 'code' => 'BO', 'description' => 'Bono Region'],
            ['name' => 'Ahafo', 'code' => 'AH', 'description' => 'Ahafo Region'],
            ['name' => 'Bono East', 'code' => 'BE', 'description' => 'Bono East Region'],
            ['name' => 'Oti', 'code' => 'OT', 'description' => 'Oti Region'],
            ['name' => 'Western North', 'code' => 'WN', 'description' => 'Western North Region'],
            ['name' => 'North East', 'code' => 'NE', 'description' => 'North East Region'],
            ['name' => 'Savannah', 'code' => 'SA', 'description' => 'Savannah Region'],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}