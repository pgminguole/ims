<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // List of departments/offices
        $departments = [
            'Human Resource',
            'Court Services',
            'Judicial Reforms & Projects',
            'Estates',
            'Administration',
            'Audit Department',
            'Communications',
            'Finance',
            'Logistics',
            'Procurement',
            'ICT',
            'Transport',
            'ADR',
            'Monitoring and Evaluation',
            'Security',
            'Facilities Management',
            '21 Town Houses',
            'Public Complaints and Court Inspectorate Unit',
            'Private Process Server',
            'Judicial Training Institute',
            'Documentation Department',
            'Protocol Unit',
            'Development (Northern Sector)',
        ];

        // Get all regions
        $regions = Region::all();

        if ($regions->isEmpty()) {
            $this->command->warn('No regions found. Please seed regions first.');
            return;
        }

        $this->command->info('Creating offices across ' . $regions->count() . ' regions...');

        $totalCreated = 0;

        // Create each department for each region
        foreach ($regions as $region) {
            foreach ($departments as $department) {
                // Generate office code
                $regionCode = strtoupper(substr($region->name, 0, 3));
                $deptCode = strtoupper(Str::slug(substr($department, 0, 3), ''));
                $code = $regionCode . '-' . $deptCode . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

                $slug = Str::slug($department . '-' . $region->name . '-' . Str::random(5));
                
                Office::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $department . ' - ' . $region->name,
                        'code' => $code,
                        'description' => 'Department of ' . $department . ' operating in ' . $region->name . ' Region',
                        'region_id' => $region->id,
                        'location_id' => null, // Can be set manually later
                        'court_id' => null, // Can be set manually later
                        'phone' => null,
                        'email' => Str::slug($department) . '.' . Str::slug($region->name) . '@judiciary.gov.gh',
                        'address' => $region->name . ' Region',
                        'is_active' => true,
                        'capacity' => rand(5, 50),
                        'manager_id' => null, // Can be assigned later
                    ]
                );

                $totalCreated++;
            }

            $this->command->info('Created ' . count($departments) . ' offices for ' . $region->name . ' Region');
        }

        $this->command->info('Successfully created ' . $totalCreated . ' offices across all regions!');
    }
}