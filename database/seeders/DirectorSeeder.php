<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DirectorSeeder extends Seeder
{
    public function run()
    {
        /**
         * Role IDs from RoleSeeder:
         *   8  => 'direcor'       (Director)
         *   9  => 'deputydirector' (Deputy Director)
         *
         * Position field captures the exact title (including Ag., H/L Justice, etc.)
         */

        $directors = [
            // ── DIRECTORS ────────────────────────────────────────────────────────
            [
                'name'        => 'Frederick Baidoo',
                'email'       => 'frederick.baidoo@jsg.gov.gh',
                'department'  => 'Human Resource',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-HR-001',
            ],
            [
                'name'        => 'Charles K. Baidoo',
                'email'       => 'charles.baidoo@jsg.gov.gh',
                'department'  => 'Court Services',
                'position'    => 'Ag. Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-CS-002',
            ],
            [
                'name'        => 'Gifty Yeboah Preko',
                'email'       => 'gifty.nyarko@jsg.gov.gh',
                'department'  => 'Judicial Reforms & Projects',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-JRP-003',
            ],
            [
                'name'        => 'Cyril Ablade',
                'email'       => 'cyril.ablade@jsg.gov.gh',
                'department'  => 'Estates',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-EST-004',
            ],
            [
                'name'        => 'Nii Boye Quartey',
                'email'       => 'nii.quartey@jsg.gov.gh',
                'department'  => 'Administration',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-ADM-005',
            ],
            [
                'name'        => 'John Kwasi Hagan Jnr.',
                'email'       => 'john.hagan@jsg.gov.gh',
                'department'  => 'Audit Department',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-AUD-006',
            ],
            [
                'name'        => 'Joseph Roddi Ampong-Fosu',
                'email'       => 'joseph.ampong-fosu@jsg.gov.gh',
                'department'  => 'Communications',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-COM-007',
            ],
            [
                'name'        => 'Vera A. M. Takyi',
                'email'       => 'vera.ofosu@jsg.gov.gh',
                'department'  => 'Finance',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-FIN-008',
            ],
            [
                'name'        => 'Benedictus A. Darko',
                'email'       => 'benedictus.darko@jsg.gov.gh',
                'department'  => 'Logistics',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-LOG-009',
            ],
            [
                'name'        => 'Barnabas Frans',
                'email'       => 'barnabas.frans@jsg.gov.gh',
                'department'  => 'Procurement',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-PRO-010',
            ],
            [
                'name'        => 'Noble Kekeli Nutifafa',
                'email'       => 'noble.knutifafa@jsg.gov.gh',
                'department'  => 'ICT',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-ICT-011',
            ],
            [
                'name'        => 'Emmanuel Ampadu',
                'email'       => 'emmanuel.ampadu@jsg.gov.gh',
                'department'  => 'Transport',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-TRN-012',
            ],
            [
                'name'        => 'Papa Kweku Maisie',
                'email'       => 'papa.maisie@jsg.gov.gh',
                'department'  => 'Monitoring and Evaluation',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-ME-013',
            ],
            [
                'name'        => 'Christian Kpordje',
                'email'       => 'christian.kpordje@jsg.gov.gh',
                'department'  => 'Security',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-SEC-014',
            ],
            [
                'name'        => 'Samuel Asiamah Dapaah',
                'email'       => 'samuel.dapaah@jsg.gov.gh',
                'department'  => 'Facilities Management',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-FAC-015',
            ],
            [
                'name'        => 'H/L Justice Eric Kyei-Baffour',
                'email'       => 'eric.kyei-baffour@jsg.gov.gh',
                'department'  => 'Public Complaints and Court Inspectorate Unit',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-PCC-016',
            ],
            [
                'name'        => 'H/L Justice Issifu Omoro Tanko Amadu',
                'email'       => 'tanko.amadu@jsg.gov.gh',
                'department'  => 'Judicial Training Institute',
                'position'    => 'Director',
                'role'        => 'direcor',
                'role_id'     => 8,
                'employee_id' => 'DIR-JTI-017',
            ],

            // ── DEPUTY DIRECTORS ─────────────────────────────────────────────────
            [
                'name'        => 'Sheila Ahene',
                'email'       => 'sheila.ahene@jsg.gov.gh',
                'department'  => 'Human Resource',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-HR-001',
            ],
            [
                'name'        => 'Joseph W. T. Adjoteye',
                'email'       => 'joseph.adjoteye@jsg.gov.gh',
                'department'  => 'Estates',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-EST-002',
            ],
            [
                'name'        => 'Rosemary Mroba Gaisie',
                'email'       => 'rosemary.gaisie@jsg.gov.gh',
                'department'  => 'Communications',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-COM-003',
            ],
            [
                'name'        => 'Charles Idan',
                'email'       => 'charles.idan@jsg.gov.gh',
                'department'  => 'Finance',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-FIN-004',
            ],
            [
                'name'        => 'Moses Ansah-Barnor Ankrah',
                'email'       => 'moses.ankrah@jsg.gov.gh',
                'department'  => 'Finance',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-FIN-005',
            ],
            [
                'name'        => 'Francis Baiden',
                'email'       => 'francis.baiden@jsg.gov.gh',
                'department'  => 'ICT',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-ICT-006',
            ],
            [
                'name'        => 'Christiana Pourideme',
                'email'       => 'christiana.puorideme@jsg.gov.gh',
                'department'  => 'ADR',
                'position'    => 'Ag. Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-ADR-007',
            ],
            [
                'name'        => 'Oheneba Nti',  // Name inferred from email: oheneba.nti@jsg.gov.gh
                'email'       => 'oheneba.nti@jsg.gov.gh',
                'department'  => 'Facilities Management',
                'position'    => 'Deputy Facilities Manager (21 Town Houses)',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-FAC-008',
            ],
            [
                'name'        => 'George William Dove',
                'email'       => 'george.dove@jsg.gov.gh',
                'department'  => 'Public Complaints and Court Inspectorate Unit',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-PCC-009',
            ],
            [
                'name'        => 'James Maynard Kwame Doe',
                'email'       => 'james.doe@jsg.gov.gh',
                'department'  => 'Private Process Server',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-PPS-010',
            ],
            [
                'name'        => 'Felix Yao Atsuvia',
                'email'       => 'felix.atsuvia@jsg.gov.gh',
                'department'  => 'Private Process Server',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-PPS-011',
            ],
            [
                'name'        => 'Jacob Zurobire Soung',
                'email'       => 'soung.zurobire@jsg.gov.gh',
                'department'  => 'Judicial Training Institute',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-JTI-012',
            ],
            [
                'name'        => 'Dorcas Amartey',
                'email'       => 'dorcas.amartey@jsg.gov.gh',
                'department'  => 'Judicial Training Institute',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-JTI-013',
            ],
            [
                'name'        => 'Perpetual Quarshie',
                'email'       => 'perpetual.quarshie@jsg.gov.gh',
                'department'  => 'Judicial Training Institute',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-JTI-014',
            ],
            [
                'name'        => 'Moses Owereko',
                'email'       => 'moses.owereko@jsg.gov.gh',
                'department'  => 'Documentation Department',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-DOC-015',
            ],
            [
                'name'        => 'Sampson Yekple',
                'email'       => 'sampson.yekple@jsg.gov.gh',
                'department'  => 'Protocol Unit',
                'position'    => 'Deputy Director',
                'role'        => 'deputydirector',
                'role_id'     => 9,
                'employee_id' => 'DEP-PRO-016',
            ],
        ];

        $directorCount  = 0;
        $deputyCount    = 0;
        $skippedCount   = 0;

        foreach ($directors as $data) {
            try {
                $user = User::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'name'                   => $data['name'],
                        'password'               => Hash::make('Jsg@Dir2025!'),
                        'employee_id'            => $data['employee_id'],
                        'position'               => $data['position'],
                        'department'             => $data['department'],
                        'role_id'                => $data['role_id'],
                        'is_active'              => true,
                        'is_approved'            => true,
                        'approved_at'            => now(),
                        'status'                 => 'active',
                        'require_password_reset' => 'yes',
                    ]
                );

                $user->assignRole($data['role']);

                if ($data['role_id'] === 8) {
                    $this->command->info("  [Director] {$data['name']} -> {$data['department']} ({$data['position']})");
                    $directorCount++;
                } else {
                    $this->command->line("  [Deputy]   {$data['name']} -> {$data['department']} ({$data['position']})");
                    $deputyCount++;
                }
            } catch (\Exception $e) {
                $this->command->warn("  SKIPPED {$data['name']}: " . $e->getMessage());
                $skippedCount++;
            }
        }

        $this->command->info("\n=================================");
        $this->command->info("Director/Deputy Seeding Summary:");
        $this->command->info("Directors  : {$directorCount}");
        $this->command->info("Deputies   : {$deputyCount}");
        $this->command->warn("Skipped    : {$skippedCount}");
        $this->command->info("=================================\n");
    }
}
