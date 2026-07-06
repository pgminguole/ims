<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RaoSeeder extends Seeder
{
    public function run()
    {
        // Map each RAO to their region by name (matches RegionSeeder exactly)
        $raos = [
            [
                'name'        => 'John Akoto',
                'email'       => 'john.akoto@jsg.gov.gh',
                'region'      => 'Ashanti',
                'employee_id' => 'RAO-ASH-001',
            ],
            [
                'name'        => 'Diana Naana Asiam',
                'email'       => 'diana.asiam@jsg.gov.gh',
                'region'      => 'Eastern',
                'employee_id' => 'RAO-EAS-002',
            ],
            [
                'name'        => 'Francis Noel Agodzo',
                'email'       => 'noel.agodzo@jsg.gov.gh',
                'region'      => 'Greater Accra',
                'employee_id' => 'RAO-GA-003',
            ],
            [
                'name'        => 'Josephine Ehuren',
                'email'       => 'josephine.ehuren@jsg.gov.gh',
                'region'      => 'Bono',
                'employee_id' => 'RAO-BON-004',
            ],
            [
                'name'        => 'Samuel Owusu-Antwi',
                'email'       => 'samuel.owusu-antwi@jsg.gov.gh',
                'region'      => 'Volta',
                'employee_id' => 'RAO-VOL-005',
            ],
            [
                'name'        => 'Samuel Ben Armoo',
                'email'       => 'samuel.armoo@jsg.gov.gh',
                'region'      => 'Central',
                'employee_id' => 'RAO-CEN-006',
            ],
            [
                'name'        => 'Grace Brown',
                'email'       => 'grace.brown@jsg.gov.gh',
                'region'      => 'Northern',
                'employee_id' => 'RAO-NOR-007',
            ],
            [
                'name'        => 'Gilbert Andam',
                'email'       => 'gilbert.andam@jsg.gov.gh',
                'region'      => 'Western',
                'employee_id' => 'RAO-WES-008',
            ],
            [
                'name'        => 'Julian Akurubire',
                'email'       => 'julian.akurubire@jsg.gov.gh',
                'region'      => 'Upper West',
                'employee_id' => 'RAO-UW-009',
            ],
            [
                'name'        => 'Alhaji Mohammed K. Abbam',
                'email'       => 'alhaji.abbam@jsg.gov.gh',
                'region'      => 'Upper East',
                'employee_id' => 'RAO-UE-010',
            ],
            [
                'name'        => 'Yaw Karikari',
                'email'       => 'yaw.karikari@jsg.gov.gh',
                'region'      => 'Western North',
                'employee_id' => 'RAO-WN-011',
            ],
            [
                'name'        => 'Charles Owusu',
                'email'       => 'charles.owusu@jsg.gov.gh',
                'region'      => 'Bono East',
                'employee_id' => 'RAO-BE-012',
            ],
            [
                'name'        => 'Ernest Boakye-Afram',
                'email'       => 'ernest.afram@jsg.gov.gh',
                'region'      => 'Ahafo',
                'employee_id' => 'RAO-AHA-013',
            ],
            [
                'name'        => 'Lydia Asampana',
                'email'       => 'lydia.asampana@jsg.gov.gh',
                'region'      => 'North East',
                'employee_id' => 'RAO-NE-014',
            ],
            [
                'name'        => 'Issabella Akumwon',
                'email'       => 'issabella.akumwon@jsg.gov.gh',
                'region'      => 'Savannah',
                'employee_id' => 'RAO-SAV-015',
            ],
            [
                'name'        => 'Francis Gbeddy',
                'email'       => 'francis.gbeddy@jsg.gov.gh',
                'region'      => 'Oti',
                'employee_id' => 'RAO-OTI-016',
            ],
        ];

        $successCount = 0;
        $skippedCount = 0;

        foreach ($raos as $raoData) {
            $region = Region::where('name', $raoData['region'])->first();

            if (!$region) {
                $this->command->warn("Region '{$raoData['region']}' not found for {$raoData['name']}. Skipping.");
                $skippedCount++;
                continue;
            }

            $user = User::updateOrCreate(
                ['email' => $raoData['email']],
                [
                    'name'                   => $raoData['name'],
                    'password'               => Hash::make('Jsg@Rao2025!'),
                    'employee_id'            => $raoData['employee_id'],
                    'position'               => 'Regional Administrator/Officer',
                    'department'             => 'Administration',
                    'region_id'              => $region->id,
                    'role_id'                => 10, // 'rao' from RoleSeeder
                    'is_active'              => true,
                    'is_approved'            => true,
                    'approved_at'            => now(),
                    'status'                 => 'active',
                    'require_password_reset' => 'yes',
                ]
            );

            $user->assignRole('rao');

            $this->command->info("  Created RAO: {$raoData['name']} -> {$raoData['region']} Region");
            $successCount++;
        }

        $this->command->info("\n=================================");
        $this->command->info("RAO Seeding Summary:");
        $this->command->info("Created/Updated : {$successCount}");
        $this->command->info("Skipped         : {$skippedCount}");
        $this->command->info("=================================\n");
    }
}
