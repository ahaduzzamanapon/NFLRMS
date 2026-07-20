<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\DealerStock;
use App\Models\District;
use App\Models\License;
use App\Models\Upazila;
use App\Models\User;
use App\Models\Vetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $dhaka      = District::where('name', 'Dhaka')->first();
        $chattogram = District::where('name', 'Chattogram')->first();
        $sylhet     = District::where('name', 'Sylhet')->first();
        $rajshahi   = District::where('name', 'Rajshahi')->first();
        $khulna     = District::where('name', 'Khulna')->first();

        $dhakaUpazila   = $dhaka ? Upazila::where('district_id', $dhaka->id)->first() : null;
        $ctgUpazila     = $chattogram ? Upazila::where('district_id', $chattogram->id)->first() : null;

        // Office users
        $frontDesk  = User::where('role', Role::DcFrontDesk->value)->first();
        $jmBranch   = User::where('role', Role::DcJmBranch->value)->first();
        $dc         = User::where('role', Role::DistrictCommissioner->value)->first();
        $police     = User::where('role', Role::PoliceOfficer->value)->first();
        $sb         = User::where('role', Role::SpecialBranch->value)->first();
        $nsi        = User::where('role', Role::NsiOfficer->value)->first();
        $dgfi       = User::where('role', Role::DgfiOfficer->value)->first();
        $mohaDesk   = User::where('role', Role::MohaDesk->value)->first();
        $jointSec   = User::where('role', Role::JointSecretary->value)->first();
        $nsc        = User::where('role', Role::NationalScreeningCommittee->value)->first();
        $seniorSec  = User::where('role', Role::SeniorSecretary->value)->first();
        $citizen    = User::where('role', Role::CitizenApplicant->value)->first();
        $dealer     = User::where('role', Role::DealerApplicant->value)->first();

        // ─────────────────────────────────────────────────────────────
        // Citizen applicants with real names (extra users for variety)
        // ─────────────────────────────────────────────────────────────
        $citizens = [
            ['name' => 'Md. Rafiqul Islam',      'email' => 'rafiqul@citizen.bd',    'nid' => '1991234567890'],
            ['name' => 'Nasrin Sultana',          'email' => 'nasrin@citizen.bd',     'nid' => '1982345678901'],
            ['name' => 'Fahim Ahmed Chowdhury',   'email' => 'fahim@citizen.bd',      'nid' => '1975678901234'],
            ['name' => 'Salma Begum',             'email' => 'salma@citizen.bd',      'nid' => '1988901234567'],
            ['name' => 'Kamal Hossain',           'email' => 'kamal@citizen.bd',      'nid' => '1969012345678'],
            ['name' => 'Roksana Akter',           'email' => 'roksana@citizen.bd',    'nid' => '1994123456789'],
            ['name' => 'Abul Kashem Mia',         'email' => 'kashem@citizen.bd',     'nid' => '1960234567890'],
            ['name' => 'Mahmuda Khatun',          'email' => 'mahmuda@citizen.bd',    'nid' => '1977345678901'],
        ];

        $citizenUsers = [];
        foreach ($citizens as $c) {
            $citizenUsers[] = User::firstOrCreate(
                ['email' => $c['email']],
                [
                    'name'        => $c['name'],
                    'password'    => bcrypt('password'),
                    'role'        => Role::CitizenApplicant,
                    'nid'         => $c['nid'],
                    'district_id' => $dhaka?->id,
                    'upazila_id'  => $dhakaUpazila?->id,
                ]
            );
        }

        $weapons = [
            ['type' => 'Shotgun',  'bore' => '12 Gauge', 'brand' => 'Remington',       'serial' => 'RM-' . rand(1000,9999),  'category' => 'Long Gun'],
            ['type' => 'Rifle',    'bore' => '.308',     'brand' => 'Winchester',       'serial' => 'WN-' . rand(1000,9999),  'category' => 'Long Gun'],
            ['type' => 'Pistol',   'bore' => '9mm',      'brand' => 'Glock',            'serial' => 'GL-' . rand(1000,9999),  'category' => 'Handgun'],
            ['type' => 'Revolver', 'bore' => '.32',      'brand' => 'Smith & Wesson',   'serial' => 'SW-' . rand(1000,9999),  'category' => 'Handgun'],
            ['type' => 'Shotgun',  'bore' => '20 Gauge', 'brand' => 'Mossberg',         'serial' => 'MS-' . rand(1000,9999),  'category' => 'Long Gun'],
            ['type' => 'Pistol',   'bore' => '.45 ACP',  'brand' => 'Colt',             'serial' => 'CT-' . rand(1000,9999),  'category' => 'Handgun'],
            ['type' => 'Rifle',    'bore' => '.22',      'brand' => 'Ruger',            'serial' => 'RG-' . rand(1000,9999),  'category' => 'Long Gun'],
            ['type' => 'Revolver', 'bore' => '.38',      'brand' => 'Taurus',           'serial' => 'TR-' . rand(1000,9999),  'category' => 'Handgun'],
        ];

        // ─────────────────────────────────────────────────────────────
        // 1. Applications at FRONT DESK (status: submitted)
        // ─────────────────────────────────────────────────────────────
        foreach (array_slice($citizenUsers, 0, 2) as $i => $cu) {
            $w = $weapons[$i];
            $app = Application::create([
                'application_number' => 'NFLRMS-2026-' . str_pad(Application::count() + 1, 6, '0', STR_PAD_LEFT),
                'user_id'            => $cu->id,
                'type'               => 'new_license',
                'applicant_type'     => 'individual',
                'status'             => 'submitted',
                'current_actor_role' => Role::DcFrontDesk->value,
                'district_id'        => $dhaka?->id,
                'upazila_id'         => $dhakaUpazila?->id,
                'applicant_details'  => ['name' => $cu->name, 'nid' => $cu->nid, 'occupation' => 'Businessman', 'annual_income' => 1500000],
                'firearm_details'    => ['weapon_type' => $w['type'], 'bore' => $w['bore'], 'brand' => $w['brand'], 'serial_number' => $w['serial']],
            ]);
            ApplicationLog::create([
                'application_id' => $app->id, 'action' => 'submitted',
                'from_status' => null, 'to_status' => 'submitted',
                'actor_id' => $cu->id, 'remarks' => 'Application submitted by citizen.',
            ]);
        }

        // ─────────────────────────────────────────────────────────────
        // 2. Applications at JM BRANCH (status: received)
        // ─────────────────────────────────────────────────────────────
        foreach (array_slice($citizenUsers, 2, 2) as $i => $cu) {
            $w = $weapons[$i + 2];
            $app = Application::create([
                'application_number' => 'NFLRMS-2026-' . str_pad(Application::count() + 1, 6, '0', STR_PAD_LEFT),
                'user_id'            => $cu->id,
                'type'               => 'new_license',
                'applicant_type'     => 'individual',
                'status'             => 'received',
                'current_actor_role' => Role::DcJmBranch->value,
                'district_id'        => $dhaka?->id,
                'upazila_id'         => $dhakaUpazila?->id,
                'applicant_details'  => ['name' => $cu->name, 'nid' => $cu->nid, 'occupation' => 'Farmer', 'annual_income' => 800000],
                'firearm_details'    => ['weapon_type' => $w['type'], 'bore' => $w['bore'], 'brand' => $w['brand'], 'serial_number' => $w['serial']],
                'created_at'         => now()->subDays(rand(2, 8)),
            ]);
            ApplicationLog::create([
                'application_id' => $app->id, 'action' => 'submitted',
                'from_status' => null, 'to_status' => 'submitted',
                'actor_id' => $cu->id, 'remarks' => 'Application submitted.',
            ]);
            ApplicationLog::create([
                'application_id' => $app->id, 'action' => 'received',
                'from_status' => 'submitted', 'to_status' => 'received',
                'actor_id' => $frontDesk?->id, 'remarks' => 'Documents verified. Forwarded to JM Branch.',
            ]);
        }

        // ─────────────────────────────────────────────────────────────
        // 3. Applications in VETTING (status: pending_vetting)
        // ─────────────────────────────────────────────────────────────
        foreach (array_slice($citizenUsers, 4, 2) as $i => $cu) {
            $w = $weapons[$i + 4];
            $app = Application::create([
                'application_number' => 'NFLRMS-2026-' . str_pad(Application::count() + 1, 6, '0', STR_PAD_LEFT),
                'user_id'            => $cu->id,
                'type'               => 'new_license',
                'applicant_type'     => 'individual',
                'status'             => 'pending_vetting',
                'current_actor_role' => Role::DcJmBranch->value,
                'district_id'        => $dhaka?->id,
                'upazila_id'         => $dhakaUpazila?->id,
                'applicant_details'  => ['name' => $cu->name, 'nid' => $cu->nid, 'occupation' => 'Retired Officer', 'annual_income' => 600000],
                'firearm_details'    => ['weapon_type' => $w['type'], 'bore' => $w['bore'], 'brand' => $w['brand'], 'serial_number' => $w['serial']],
                'created_at'         => now()->subDays(rand(5, 12)),
            ]);

            // Create vetting entries
            $agencies = [
                ['agency' => 'police',  'user' => $police,  'status' => 'pending'],
                ['agency' => 'sb',      'user' => $sb,      'status' => 'cleared'],
                ['agency' => 'nsi',     'user' => $nsi,     'status' => 'pending'],
                ['agency' => 'dgfi',    'user' => $dgfi,    'status' => $i === 0 ? 'pending' : 'flagged'],
            ];

            foreach ($agencies as $ag) {
                Vetting::create([
                    'application_id' => $app->id,
                    'agency'         => $ag['agency'],
                    'status'         => $ag['status'],
                    'vetted_by'     => $ag['user']?->id,
                    'remarks'        => $ag['status'] === 'cleared' ? 'No adverse record found.' : ($ag['status'] === 'flagged' ? 'Previous criminal record — minor traffic offence.' : null),
                    'vetted_at'   => $ag['status'] !== 'pending' ? now()->subDays(rand(1, 3)) : null,
                ]);
            }

            ApplicationLog::create([
                'application_id' => $app->id, 'action' => 'triggered_vetting',
                'from_status' => 'received', 'to_status' => 'pending_vetting',
                'actor_id' => $jmBranch?->id, 'remarks' => 'Security vetting dispatched to Police, SB, NSI, and DGFI.',
            ]);
        }

        // Applications for JM Branch (status: vetted_cleared)
        $roksana = User::firstOrCreate(
            ['email' => 'roksana.akter2@citizen.bd'],
            [
                'name'        => 'Roksana Akter',
                'password'    => bcrypt('password'),
                'role'        => Role::CitizenApplicant,
                'nid'         => '1995111222333',
                'district_id' => $dhaka?->id,
                'upazila_id'  => $dhakaUpazila?->id,
            ]
        );
        $vettedClearApp = Application::create([
            'application_number' => 'NFLRMS-2026-JM-CLR',
            'user_id'            => $roksana->id,
            'type'               => 'new_license',
            'applicant_type'     => 'individual',
            'status'             => 'vetted_cleared',
            'current_actor_role' => Role::DcJmBranch->value,
            'district_id'        => $dhaka?->id,
            'upazila_id'         => $dhakaUpazila?->id,
            'applicant_details'  => ['name' => 'Roksana Akter', 'nid' => '1995111222333', 'occupation' => 'Teacher', 'annual_income' => 500000],
            'firearm_details'    => ['weapon_type' => 'Shotgun', 'bore' => '12 Gauge', 'brand' => 'Mossberg', 'serial_number' => 'MB-9911'],
            'created_at'         => now()->subDays(10),
        ]);
        foreach (['police' => $police, 'sb' => $sb, 'nsi' => $nsi, 'dgfi' => $dgfi] as $agencyKey => $officer) {
            Vetting::create([
                'application_id' => $vettedClearApp->id, 'agency' => $agencyKey,
                'status' => 'cleared', 'vetted_by' => $officer?->id,
                'remarks' => 'Clean record.', 'vetted_at' => now()->subDays(2),
            ]);
        }
        ApplicationLog::create([
            'application_id' => $vettedClearApp->id, 'action' => 'vetting_completed',
            'from_status' => 'pending_vetting', 'to_status' => 'vetted_cleared',
            'remarks' => 'All agency security clearances received. Application sent to JM Branch for final review.',
        ]);

        // Applications for JM Branch (status: vetted_flagged)
        $kashem = User::firstOrCreate(
            ['email' => 'kashem.mia2@citizen.bd'],
            [
                'name'        => 'Abul Kashem Mia',
                'password'    => bcrypt('password'),
                'role'        => Role::CitizenApplicant,
                'nid'         => '1965111222333',
                'district_id' => $dhaka?->id,
                'upazila_id'  => $dhakaUpazila?->id,
            ]
        );
        $vettedFlagApp = Application::create([
            'application_number' => 'NFLRMS-2026-JM-FLG',
            'user_id'            => $kashem->id,
            'type'               => 'new_license',
            'applicant_type'     => 'individual',
            'status'             => 'vetted_flagged',
            'current_actor_role' => Role::DcJmBranch->value,
            'district_id'        => $dhaka?->id,
            'upazila_id'         => $dhakaUpazila?->id,
            'applicant_details'  => ['name' => 'Abul Kashem Mia', 'nid' => '1965111222333', 'occupation' => 'Retired', 'annual_income' => 900000],
            'firearm_details'    => ['weapon_type' => 'Rifle', 'bore' => '.22', 'brand' => 'Ruger', 'serial_number' => 'RG-5522'],
            'created_at'         => now()->subDays(12),
        ]);
        foreach (['police' => $police, 'sb' => $sb, 'nsi' => $nsi, 'dgfi' => $dgfi] as $agencyKey => $officer) {
            Vetting::create([
                'application_id' => $vettedFlagApp->id, 'agency' => $agencyKey,
                'status' => $agencyKey === 'police' ? 'flagged' : 'cleared',
                'vetted_by' => $officer?->id,
                'remarks' => $agencyKey === 'police' ? 'Minor traffic violation record exists.' : 'Clean.',
                'vetted_at' => now()->subDays(3),
            ]);
        }
        ApplicationLog::create([
            'application_id' => $vettedFlagApp->id, 'action' => 'vetting_completed',
            'from_status' => 'pending_vetting', 'to_status' => 'vetted_flagged',
            'remarks' => 'All agency security clearances received. Adverse records found.',
        ]);

        // ─────────────────────────────────────────────────────────────
        // 4. Applications at DC (status: recommended) — vetted_cleared
        // ─────────────────────────────────────────────────────────────
        foreach (array_slice($citizenUsers, 6, 1) as $cu) {
            $w = $weapons[6];
            $app = Application::create([
                'application_number' => 'NFLRMS-2026-' . str_pad(Application::count() + 1, 6, '0', STR_PAD_LEFT),
                'user_id'            => $cu->id,
                'type'               => 'new_license',
                'applicant_type'     => 'individual',
                'status'             => 'recommended',
                'current_actor_role' => Role::DistrictCommissioner->value,
                'district_id'        => $dhaka?->id,
                'upazila_id'         => $dhakaUpazila?->id,
                'applicant_details'  => ['name' => $cu->name, 'nid' => $cu->nid, 'occupation' => 'Industrialist', 'annual_income' => 5000000],
                'firearm_details'    => ['weapon_type' => $w['type'], 'bore' => $w['bore'], 'brand' => $w['brand'], 'serial_number' => $w['serial']],
                'created_at'         => now()->subDays(14),
            ]);

            foreach (['police' => $police, 'sb' => $sb, 'nsi' => $nsi, 'dgfi' => $dgfi] as $agencyKey => $officer) {
                Vetting::create([
                    'application_id' => $app->id,
                    'agency'         => $agencyKey,
                    'status'         => 'cleared',
                    'vetted_by'     => $officer?->id,
                    'remarks'        => 'Background check complete. No adverse record.',
                    'vetted_at'   => now()->subDays(rand(2, 7)),
                ]);
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 5. Applications at MoHA Desk (Handgun — referred_moha)
        // ─────────────────────────────────────────────────────────────
        foreach (array_slice($citizenUsers, 7, 1) as $cu) {
            $w = $weapons[7]; // Revolver
            $app = Application::create([
                'application_number' => 'NFLRMS-2026-' . str_pad(Application::count() + 1, 6, '0', STR_PAD_LEFT),
                'user_id'            => $cu->id,
                'type'               => 'new_license',
                'applicant_type'     => 'individual',
                'status'             => 'referred_moha',
                'current_actor_role' => Role::MohaDesk->value,
                'district_id'        => $dhaka?->id,
                'upazila_id'         => $dhakaUpazila?->id,
                'applicant_details'  => ['name' => $cu->name, 'nid' => $cu->nid, 'occupation' => 'Politician', 'annual_income' => 3000000],
                'firearm_details'    => ['weapon_type' => $w['type'], 'bore' => $w['bore'], 'brand' => $w['brand'], 'serial_number' => $w['serial']],
                'created_at'         => now()->subDays(20),
            ]);

            foreach (['police' => $police, 'sb' => $sb, 'nsi' => $nsi, 'dgfi' => $dgfi] as $agencyKey => $officer) {
                Vetting::create([
                    'application_id' => $app->id,
                    'agency'         => $agencyKey,
                    'status'         => 'cleared',
                    'vetted_by'     => $officer?->id,
                    'remarks'        => 'Cleared.',
                    'vetted_at'   => now()->subDays(rand(5, 15)),
                ]);
            }

            ApplicationLog::create([
                'application_id' => $app->id, 'action' => 'referred_moha',
                'from_status' => 'recommended', 'to_status' => 'referred_moha',
                'actor_id' => $dc?->id, 'remarks' => 'Handgun application — referred to MoHA per protocol.',
            ]);
        }

        // MoHA Desk → Joint Secretary (moha_processing)
        $fahim = User::firstOrCreate(
            ['email' => 'fahim.chowdhury2@citizen.bd'],
            [
                'name'        => 'Fahim Ahmed Chowdhury',
                'password'    => bcrypt('password'),
                'role'        => Role::CitizenApplicant,
                'nid'         => '1985111222333',
                'district_id' => $chattogram?->id,
                'upazila_id'  => $ctgUpazila?->id,
            ]
        );
        $jsApp = Application::create([
            'application_number' => 'NFLRMS-2026-000156',
            'user_id'            => $fahim->id,
            'type'               => 'new_license',
            'applicant_type'     => 'individual',
            'status'             => 'moha_processing',
            'current_actor_role' => Role::JointSecretary->value,
            'district_id'        => $chattogram?->id,
            'upazila_id'         => $ctgUpazila?->id,
            'applicant_details'  => ['name' => 'Fahim Ahmed Chowdhury', 'nid' => '1985111222333', 'occupation' => 'Lawyer', 'annual_income' => 2500000],
            'firearm_details'    => ['weapon_type' => 'Pistol', 'bore' => '9mm', 'brand' => 'Glock', 'serial_number' => 'GL-2024'],
            'created_at'         => now()->subDays(25),
        ]);
        foreach (['police' => $police, 'sb' => $sb, 'nsi' => $nsi, 'dgfi' => $dgfi] as $agencyKey => $officer) {
            Vetting::create([
                'application_id' => $jsApp->id, 'agency' => $agencyKey,
                'status' => 'cleared', 'vetted_by' => $officer?->id,
                'remarks' => 'Cleared after background check.', 'vetted_at' => now()->subDays(10),
            ]);
        }
        ApplicationLog::create([
            'application_id' => $jsApp->id, 'action' => 'forwarded_moha',
            'from_status' => 'referred_moha', 'to_status' => 'moha_processing',
            'actor_id' => $mohaDesk?->id, 'remarks' => 'Forwarded to Joint Secretary for review.',
        ]);

        // Joint Secretary → National Screening Committee (pending_screening)
        $salma = User::firstOrCreate(
            ['email' => 'salma.begum2@citizen.bd'],
            [
                'name'        => 'Salma Begum',
                'password'    => bcrypt('password'),
                'role'        => Role::CitizenApplicant,
                'nid'         => '1990111222333',
                'district_id' => $dhaka?->id,
                'upazila_id'  => $dhakaUpazila?->id,
            ]
        );
        $nscApp = Application::create([
            'application_number' => 'NFLRMS-2026-000214',
            'user_id'            => $salma->id,
            'type'               => 'new_license',
            'applicant_type'     => 'individual',
            'status'             => 'pending_screening',
            'current_actor_role' => Role::NationalScreeningCommittee->value,
            'district_id'        => $dhaka?->id,
            'upazila_id'         => $dhakaUpazila?->id,
            'applicant_details'  => ['name' => 'Salma Begum', 'nid' => '1990111222333', 'occupation' => 'Businesswoman', 'annual_income' => 4500000],
            'firearm_details'    => ['weapon_type' => 'Pistol', 'bore' => '.32', 'brand' => 'Taurus', 'serial_number' => 'TR-4567'],
            'created_at'         => now()->subDays(30),
        ]);
        foreach (['police' => $police, 'sb' => $sb, 'nsi' => $nsi, 'dgfi' => $dgfi] as $agencyKey => $officer) {
            Vetting::create([
                'application_id' => $nscApp->id, 'agency' => $agencyKey,
                'status' => 'cleared', 'vetted_by' => $officer?->id,
                'remarks' => 'Cleared by agency.', 'vetted_at' => now()->subDays(12),
            ]);
        }
        ApplicationLog::create([
            'application_id' => $nscApp->id, 'action' => 'forwarded_screening',
            'from_status' => 'moha_processing', 'to_status' => 'pending_screening',
            'actor_id' => $jointSec?->id, 'remarks' => 'Forwarded to National Screening Committee for evaluation.',
        ]);

        // National Screening Committee → Senior Secretary (screened)
        $kamal = User::firstOrCreate(
            ['email' => 'kamal.hossain2@citizen.bd'],
            [
                'name'        => 'Kamal Hossain',
                'password'    => bcrypt('password'),
                'role'        => Role::CitizenApplicant,
                'nid'         => '1970111222333',
                'district_id' => $dhaka?->id,
                'upazila_id'  => $dhakaUpazila?->id,
            ]
        );
        $secApp = Application::create([
            'application_number' => 'NFLRMS-2026-000305',
            'user_id'            => $kamal->id,
            'type'               => 'new_license',
            'applicant_type'     => 'individual',
            'status'             => 'screened',
            'current_actor_role' => Role::SeniorSecretary->value,
            'district_id'        => $dhaka?->id,
            'upazila_id'         => $dhakaUpazila?->id,
            'applicant_details'  => ['name' => 'Kamal Hossain', 'nid' => '1970111222333', 'occupation' => 'Doctor', 'annual_income' => 3500000],
            'firearm_details'    => ['weapon_type' => 'Revolver', 'bore' => '.38', 'brand' => 'Colt', 'serial_number' => 'CT-9876'],
            'created_at'         => now()->subDays(35),
        ]);
        foreach (['police' => $police, 'sb' => $sb, 'nsi' => $nsi, 'dgfi' => $dgfi] as $agencyKey => $officer) {
            Vetting::create([
                'application_id' => $secApp->id, 'agency' => $agencyKey,
                'status' => 'cleared', 'vetted_by' => $officer?->id,
                'remarks' => 'Cleared.', 'vetted_at' => now()->subDays(15),
            ]);
        }
        ApplicationLog::create([
            'application_id' => $secApp->id, 'action' => 'screened',
            'from_status' => 'pending_screening', 'to_status' => 'screened',
            'actor_id' => $nsc?->id, 'remarks' => 'Committee evaluated the case. Recommended and forwarded to Senior Secretary.',
        ]);

        // ─────────────────────────────────────────────────────────────
        // 6. Approved licenses with QR codes
        // ─────────────────────────────────────────────────────────────
        $licenseData = [
            ['num' => 'BD-HND-DHK-004521', 'user' => $citizen, 'type' => 'Revolver', 'bore' => '.32', 'brand' => 'Smith & Wesson', 'status' => 'active', 'days_ago' => 365, 'years' => 3],
            ['num' => 'BD-LNG-DHK-001192', 'user' => $citizen, 'type' => 'Shotgun',  'bore' => '12 Gauge', 'brand' => 'Remington', 'status' => 'suspended', 'days_ago' => 730, 'years' => -1],
            ['num' => 'BD-LNG-CTG-002044', 'user' => $citizenUsers[0] ?? $citizen, 'type' => 'Rifle', 'bore' => '.308', 'brand' => 'Winchester', 'status' => 'active', 'days_ago' => 180, 'years' => 3],
            ['num' => 'BD-HND-RJH-003312', 'user' => $citizenUsers[1] ?? $citizen, 'type' => 'Pistol', 'bore' => '9mm', 'brand' => 'Glock', 'status' => 'active', 'days_ago' => 90, 'years' => 3],
            ['num' => 'BD-LNG-SYL-004801', 'user' => $citizenUsers[2] ?? $citizen, 'type' => 'Shotgun', 'bore' => '20 Gauge', 'brand' => 'Mossberg', 'status' => 'active', 'days_ago' => 60, 'years' => 3],
        ];

        foreach ($licenseData as $ld) {
            if (! $ld['user']) continue;
            if (License::where('license_number', $ld['num'])->exists()) continue;
            License::create([
                'license_number'  => $ld['num'],
                'user_id'         => $ld['user']->id,
                'type'            => 'citizen_arms',
                'issue_date'      => now()->subDays($ld['days_ago']),
                'expiry_date'     => now()->addYears($ld['years']),
                'status'          => $ld['status'],
                'firearm_details' => [
                    'weapon_type' => $ld['type'],
                    'bore'        => $ld['bore'],
                    'brand'       => $ld['brand'],
                    'serial_number' => strtoupper(substr($ld['brand'], 0, 2)) . '-' . rand(1000, 9999),
                ],
                'qrcode' => 'https://nflrms.gov.bd/verify/' . $ld['num'],
            ]);
        }

        // ─────────────────────────────────────────────────────────────
        // 7. Dealer application (pending_vetting — 4 agencies)
        // ─────────────────────────────────────────────────────────────
        if ($dealer) {
            $dealerApp = Application::create([
                'application_number' => 'NFLRMS-2026-DEAL-001',
                'user_id'            => $dealer->id,
                'type'               => 'new_dealing_license',
                'applicant_type'     => 'dealer',
                'status'             => 'pending_vetting',
                'current_actor_role' => Role::DcJmBranch->value,
                'district_id'        => $dhaka?->id,
                'upazila_id'         => $dhakaUpazila?->id,
                'applicant_details'  => [
                    'name'             => $dealer->name,
                    'nid'              => '1980555666777',
                    'firm_name'        => 'Karim Arms & Ammunition',
                    'trade_license'    => 'TL-DHK-2024-00821',
                    'business_address' => '12 Motijheel C/A, Dhaka-1000',
                    'license_class'    => 'A',
                    'occupation'       => 'Arms Dealer',
                    'annual_income'    => 8000000,
                ],
                'firearm_details'    => ['weapon_type' => 'Dealing License', 'categories' => ['Pistol', 'Revolver', 'Shotgun', 'Ammunition']],
                'created_at'         => now()->subDays(30),
            ]);

            foreach (['police' => $police, 'sb' => $sb, 'nsi' => $nsi, 'dgfi' => $dgfi] as $agencyKey => $officer) {
                Vetting::create([
                    'application_id' => $dealerApp->id,
                    'agency'         => $agencyKey,
                    'status'         => in_array($agencyKey, ['police', 'sb']) ? 'cleared' : 'pending',
                    'vetted_by'     => in_array($agencyKey, ['police', 'sb']) ? $officer?->id : null,
                    'remarks'        => in_array($agencyKey, ['police', 'sb']) ? 'No adverse record. Dealer premises verified.' : null,
                    'vetted_at'   => in_array($agencyKey, ['police', 'sb']) ? now()->subDays(5) : null,
                ]);
            }

            // Dealer stock items
            $stockItems = [
                ['item' => '12-bore Shotgun (Remington 870)',   'category' => 'Firearm',    'quantity' => 12, 'source' => 'Import — Turkey'],
                ['item' => '9mm Pistol (Glock 17)',             'category' => 'Firearm',    'quantity' => 8,  'source' => 'Import — Austria'],
                ['item' => '.32 Revolver (Smith & Wesson 10)',  'category' => 'Firearm',    'quantity' => 5,  'source' => 'Import — USA'],
                ['item' => '12 Gauge Cartridge (pack of 25)',   'category' => 'Ammunition', 'quantity' => 200,'source' => 'Local — Progoti Arms'],
                ['item' => '9mm Parabellum (box of 50)',        'category' => 'Ammunition', 'quantity' => 150,'source' => 'Import — Germany'],
                ['item' => 'Gun Cleaning Kit',                  'category' => 'Accessory',  'quantity' => 30, 'source' => 'Local'],
                ['item' => 'Pistol Holster (leather)',          'category' => 'Accessory',  'quantity' => 20, 'source' => 'Local'],
            ];

            foreach ($stockItems as $si) {
                DealerStock::create([
                    'user_id'  => $dealer->id,
                    'item'     => $si['item'],
                    'category' => $si['category'],
                    'quantity' => $si['quantity'],
                    'source'   => $si['source'],
                ]);
            }

            // Dealer license (issued)
            if (!License::where('license_number', 'DEAL-DHK-2024-001')->exists()) {
                License::create([
                    'license_number'  => 'DEAL-DHK-2024-001',
                    'user_id'         => $dealer->id,
                    'type'            => 'dealer_dealing',
                    'issue_date'      => now()->subYear(),
                    'expiry_date'     => now()->addYears(2),
                    'status'          => 'active',
                    'firearm_details' => ['weapon_type' => 'Dealing License', 'class' => 'A', 'firm' => 'Karim Arms & Ammunition'],
                    'qrcode'          => 'https://nflrms.gov.bd/verify/DEAL-DHK-2024-001',
                ]);
            }
        }

        // ─────────────────────────────────────────────────────────────
        // 8. Renewal applications (expiring/expired licenses)
        // ─────────────────────────────────────────────────────────────
        if ($citizen) {
            $renewApp = Application::create([
                'application_number' => 'NFLRMS-2026-REN-001',
                'user_id'            => $citizen->id,
                'type'               => 'renewal',
                'applicant_type'     => 'individual',
                'status'             => 'submitted',
                'current_actor_role' => Role::DcFrontDesk->value,
                'district_id'        => $dhaka?->id,
                'upazila_id'         => $dhakaUpazila?->id,
                'applicant_details'  => ['name' => $citizen->name, 'nid' => $citizen->nid ?? '19912345', 'occupation' => 'Retired', 'annual_income' => 400000],
                'firearm_details'    => ['weapon_type' => 'Shotgun', 'bore' => '12 Gauge', 'brand' => 'Remington', 'serial_number' => 'RM-0012'],
                'created_at'         => now()->subDays(3),
            ]);

            ApplicationLog::create([
                'application_id' => $renewApp->id, 'action' => 'submitted',
                'from_status' => null, 'to_status' => 'submitted',
                'actor_id' => $citizen->id, 'remarks' => 'Renewal application submitted.',
            ]);
        }

        if ($dealer) {
            $dealerRenewApp = Application::create([
                'application_number' => 'NFLRMS-2026-DEAL-REN-001',
                'user_id'            => $dealer->id,
                'type'               => 'renewal',
                'applicant_type'     => 'dealer',
                'status'             => 'submitted',
                'current_actor_role' => Role::DcFrontDesk->value,
                'district_id'        => $dhaka?->id,
                'upazila_id'         => $dhakaUpazila?->id,
                'applicant_details'  => [
                    'license_number'   => 'DEAL-DHK-2024-001',
                    'name'             => $dealer->name,
                    'nid'              => '1980555666777',
                    'firm_name'        => 'Karim Arms & Ammunition',
                    'declared_firearms'=> 15,
                    'declared_ammo'    => 500,
                ],
                'firearm_details'    => ['weapon_type' => 'Dealing License', 'class' => 'A', 'firm' => 'Karim Arms & Ammunition'],
                'created_at'         => now()->subDays(2),
            ]);

            ApplicationLog::create([
                'application_id' => $dealerRenewApp->id, 'action' => 'submitted',
                'from_status' => null, 'to_status' => 'submitted',
                'actor_id' => $dealer->id, 'remarks' => 'Dealer renewal application submitted with stock declaration.',
            ]);
        }
    }
}
