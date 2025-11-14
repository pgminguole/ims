<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // IT Equipment
            [
                'name' => 'Computers',
                'code' => 'COMP',
                'description' => 'Computer systems and workstations',
                'parent_id' => null,
            ],
              [
                'name' => 'DTS',
                'code' => 'dts',
                'description' => 'DTS for courts',
                'parent_id' => null,
            ],
              [
                'name' => 'Child Friendly Court',
                'code' => 'cfa',
                'description' => 'Child friendly courts',
                'parent_id' => null,
            ],
              [
                'name' => 'Camera',
                'code' => 'CAM',
                'description' => 'Camera for Courts',
                'parent_id' => null,
            ],
              [
                'name' => 'Televisions',
                'code' => 'TEL',
                'description' => 'Televisions for courts',
                'parent_id' => null,
            ],
            
            [
                'name' => 'Laptops',
                'code' => 'LAPTOP',
                'description' => 'Portable computers',
                'parent_id' => 1,
            ],
            [
                'name' => 'Desktops',
                'code' => 'DESKTOP',
                'description' => 'Desktop computers',
                'parent_id' => 1,
            ],
            
            // Networking
            [
                'name' => 'Networking',
                'code' => 'NET',
                'description' => 'Network equipment',
                'parent_id' => null,
            ],
            [
                'name' => 'Routers',
                'code' => 'ROUTER',
                'description' => 'Network routers',
                'parent_id' => 4,
            ],
            [
                'name' => 'Switches',
                'code' => 'SWITCH',
                'description' => 'Network switches',
                'parent_id' => 4,
            ],
            
            // Office Equipment
            [
                'name' => 'Office Equipment',
                'code' => 'OFFICE',
                'description' => 'General office equipment',
                'parent_id' => null,
            ],
            [
                'name' => 'Printers',
                'code' => 'PRINTER',
                'description' => 'Printing devices',
                'parent_id' => 7,
            ],
            [
                'name' => 'Scanners',
                'code' => 'SCANNER',
                'description' => 'Document scanners',
                'parent_id' => 7,
            ],
              [
                'name' => 'Photocopier',
                'code' => 'PHOTOCOPIER',
                'description' => 'Document photocopier',
                'parent_id' => 7,
            ],

                 [
                'name' => 'UPS',
                'code' => 'UPS',
                'description' => 'Universal Power Supply for computers',
                'parent_id' => 7,
            ],
            
                 [
                'name' => 'Stabilizer',
                'code' => 'STAB',
                'description' => 'Stabilizers for computers',
                'parent_id' => 7,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}