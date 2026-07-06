<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagementTeamSeeder extends Seeder
{
    public function run()
    {
        // ─── Step 1: Register new top-management Spatie roles ────────────────────
        // (RoleSeeder only defined up to ict_system_admin; these are higher-tier)
        $managementRoles = [
            ['name' => 'chief_justice',            'description' => 'Chief Justice of Ghana'],
            ['name' => 'judicial_secretary',        'description' => 'Judicial Secretary'],
            ['name' => 'deputy_judicial_secretary', 'description' => 'Deputy Judicial Secretary'],
        ];

        foreach ($managementRoles as $roleData) {
            // Custom role table
            $customRole = Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );

            // Spatie role
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                \Spatie\Permission\Models\Role::firstOrCreate([
                    'name'       => $roleData['name'],
                    'guard_name' => 'web',
                ]);
            }
        }

        $this->command->info('Management roles registered.');

        // ─── Step 2: Define all management team members ──────────────────────────
        //
        // NOTE on dual emails:
        //   Paul Baffoe-Bonnie  → personal: paul.baffoe-bonnie@jsg.gov.gh
        //                         functional: chiefjustice@jsg.gov.gh
        //   Musah Ahmed         → personal: musah.ahmed@jsg.gov.gh
        //                         functional: judicialsecretary@jsg.gov.gh
        //   Patricia Dadson     → personal: patricia.dadson@jsg.gov.gh
        //                         functional: courtmanager@jsg.gov.gh
        //   We use the personal email as the primary login key.
        //
        // NOTE on directors (rows 9–23):
        //   These were already seeded by DirectorSeeder. updateOrCreate is safe
        //   and will not create duplicates — only confirms/updates their records.

        $members = [

            // ── TOP MANAGEMENT (new records) ─────────────────────────────────────
            [
                'name'        => 'H/L Justice Paul Baffoe-Bonnie',
                'email'       => 'paul.baffoe-bonnie@jsg.gov.gh',   // personal/login email
                'department'  => 'Office of the Chief Justice',
                'position'    => 'Chief Justice',
                'spatie_role' => 'chief_justice',
                'role_id'     => Role::where('name', 'chief_justice')->value('id'),
                'employee_id' => 'MGT-CJ-001',
                'notes'       => 'Functional email: chiefjustice@jsg.gov.gh',
            ],
            [
                'name'        => 'Musah Ahmed, Esq.',
                'email'       => 'musah.ahmed@jsg.gov.gh',           // personal/login email
                'department'  => 'Office of the Judicial Secretary',
                'position'    => 'Judicial Secretary',
                'spatie_role' => 'judicial_secretary',
                'role_id'     => Role::where('name', 'judicial_secretary')->value('id'),
                'employee_id' => 'MGT-JS-001',
                'notes'       => 'Functional email: judicialsecretary@jsg.gov.gh',
            ],
            [
                'name'        => 'H/L Dr. Cyracus Badinye Bapuuroh',
                'email'       => 'cyracus.bapuuroh@jsg.gov.gh',
                'department'  => 'Office of the Judicial Secretary',
                'position'    => 'Deputy Judicial Secretary',
                'spatie_role' => 'deputy_judicial_secretary',
                'role_id'     => Role::where('name', 'deputy_judicial_secretary')->value('id'),
                'employee_id' => 'MGT-DJS-001',
                'notes'       => null,
            ],
            [
                'name'        => 'H/L Justice Frederick Tetteh',
                'email'       => 'frederick.tetteh@jsg.gov.gh',
                'department'  => 'Office of the Judicial Secretary (Northern Sector)',
                'position'    => 'Deputy Judicial Secretary, Northern Sector',
                'spatie_role' => 'deputy_judicial_secretary',
                'role_id'     => Role::where('name', 'deputy_judicial_secretary')->value('id'),
                'employee_id' => 'MGT-DJS-002',
                'notes'       => null,
            ],
            [
                // Dual position: Deputy Judicial Secretary (Tech) + Court Manager, Law Court Complex
                // Dual email: patricia.dadson@jsg.gov.gh + courtmanager@jsg.gov.gh
                'name'        => 'Mrs. Patricia Naa Afarley Dadson',
                'email'       => 'patricia.dadson@jsg.gov.gh',        // personal/login email
                'department'  => 'Office of the Judicial Secretary',
                'position'    => 'Deputy Judicial Secretary (Incorporation of Technology) / Court Manager, Law Court Complex',
                'spatie_role' => 'deputy_judicial_secretary',
                'role_id'     => Role::where('name', 'deputy_judicial_secretary')->value('id'),
                'employee_id' => 'MGT-DJS-003',
                'notes'       => 'Functional email: courtmanager@jsg.gov.gh',
            ],

            // ── DIRECTORS (already in DirectorSeeder — updateOrCreate is safe) ──
            [
                'name'        => 'Cyril Ablade',
                'email'       => 'cyril.ablade@jsg.gov.gh',
                'department'  => 'Estates',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-EST-004',
                'notes'       => null,
            ],
            [
                'name'        => 'Nii Boye Quartey',
                'email'       => 'nii.quartey@jsg.gov.gh',
                'department'  => 'Administration',
                'position'    => 'Director, Administration (Southern Sector)',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-ADM-005',
                'notes'       => null,
            ],
            [
                'name'        => 'Joseph Roddi Ampong-Fosu',
                'email'       => 'joseph.ampong-fosu@jsg.gov.gh',
                'department'  => 'Communications',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-COM-007',
                'notes'       => null,
            ],
            [
                'name'        => 'Benedictus A. Darko',
                'email'       => 'benedictus.darko@jsg.gov.gh',
                'department'  => 'Logistics',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-LOG-009',
                'notes'       => null,
            ],
            [
                'name'        => 'Vera A. M. Takyi',
                'email'       => 'vera.ofosu@jsg.gov.gh',
                'department'  => 'Finance',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-FIN-008',
                'notes'       => null,
            ],
            [
                'name'        => 'Frederick Baidoo',
                'email'       => 'frederick.baidoo@jsg.gov.gh',
                'department'  => 'Human Resource',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-HR-001',
                'notes'       => null,
            ],
            [
                'name'        => 'Noble Kekeli Nutifafa',
                'email'       => 'noble.knutifafa@jsg.gov.gh',
                'department'  => 'ICT',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-ICT-011',
                'notes'       => null,
            ],
            [
                'name'        => 'Barnabas Frans',
                'email'       => 'barnabas.frans@jsg.gov.gh',
                'department'  => 'Procurement',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-PRO-010',
                'notes'       => null,
            ],
            [
                'name'        => 'Emmanuel Ampadu',
                'email'       => 'emmanuel.ampadu@jsg.gov.gh',
                'department'  => 'Transport',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-TRN-012',
                'notes'       => null,
            ],
            [
                'name'        => 'Christian Kpordje',
                'email'       => 'christian.kpordje@jsg.gov.gh',
                'department'  => 'Security',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-SEC-014',
                'notes'       => null,
            ],
            [
                'name'        => 'Papa Kweku Maisie',
                'email'       => 'papa.maisie@jsg.gov.gh',
                'department'  => 'Monitoring and Evaluation',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-ME-013',
                'notes'       => null,
            ],
            [
                'name'        => 'John Kwasi Hagan Jnr.',
                'email'       => 'john.hagan@jsg.gov.gh',
                'department'  => 'Audit Department',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-AUD-006',
                'notes'       => null,
            ],
            [
                'name'        => 'Gifty Yeboah Preko',
                'email'       => 'gifty.nyarko@jsg.gov.gh',
                'department'  => 'Judicial Reforms & Projects',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-JRP-003',
                'notes'       => null,
            ],
            [
                'name'        => 'Samuel Asiamah Dapaah',
                'email'       => 'samuel.dapaah@jsg.gov.gh',
                'department'  => 'Facilities Management',
                'position'    => 'Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-FAC-015',
                'notes'       => null,
            ],
            [
                'name'        => 'Charles Kwesi Baidoo',
                'email'       => 'charles.baidoo@jsg.gov.gh',
                'department'  => 'Court Services',
                'position'    => 'Ag. Director',
                'spatie_role' => 'direcor',
                'role_id'     => Role::where('name', 'direcor')->value('id'),
                'employee_id' => 'DIR-CS-002',
                'notes'       => null,
            ],
        ];

        // ─── Step 3: Seed all members ─────────────────────────────────────────────
        $newCount    = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($members as $data) {
            try {
                $exists = User::where('email', $data['email'])->exists();

                User::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'name'                   => $data['name'],
                        'password'               => Hash::make('Jsg@Mgt2025!'),
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

                // Re-fetch to assign Spatie role
                $user = User::where('email', $data['email'])->first();
                $user->assignRole($data['spatie_role']);

                $label = $exists ? '[UPDATED]' : '[NEW]    ';
                $this->command->info("  {$label} {$data['name']} → {$data['position']}");
                $exists ? $updatedCount++ : $newCount++;

            } catch (\Exception $e) {
                $this->command->warn("  [SKIP]    {$data['name']}: " . $e->getMessage());
                $skippedCount++;
            }
        }

        $this->command->info("\n=================================");
        $this->command->info("Management Team Seeding Summary:");
        $this->command->info("New records   : {$newCount}");
        $this->command->info("Updated       : {$updatedCount}");
        $this->command->warn("Skipped       : {$skippedCount}");
        $this->command->info("Total         : " . ($newCount + $updatedCount));
        $this->command->info("=================================\n");
        $this->command->comment("NOTE: Functional/shared emails not stored as separate accounts:");
        $this->command->comment("  chiefjustice@jsg.gov.gh     -> paul.baffoe-bonnie@jsg.gov.gh");
        $this->command->comment("  judicialsecretary@jsg.gov.gh -> musah.ahmed@jsg.gov.gh");
        $this->command->comment("  courtmanager@jsg.gov.gh     -> patricia.dadson@jsg.gov.gh");
    }
}
