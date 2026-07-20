<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\District;
use App\Models\Upazila;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find Dhaka district
        $dhaka = District::where('name', 'Dhaka')->first();
        // Find the first upazila in Dhaka district
        $upazila = $dhaka ? Upazila::where('district_id', $dhaka->id)->first() : null;

        $dhakaId = $dhaka ? $dhaka->id : null;
        $upazilaId = $upazila ? $upazila->id : null;

        // Map phone numbers for each role
        $phones = [
            Role::CitizenApplicant->value => '01711234567',
            Role::DealerApplicant->value => '01711000111',
            Role::DcFrontDesk->value => '01711000222',
            Role::DcJmBranch->value => '01711000333',
            Role::DistrictCommissioner->value => '01711000444',
            Role::PoliceOfficer->value => '01711000555',
            Role::SpecialBranch->value => '01711000666',
            Role::NsiOfficer->value => '01711000777',
            Role::DgfiOfficer->value => '01711000888',
            Role::MohaDesk->value => '01711000999',
            Role::JointSecretary->value => '01711000123',
            Role::SeniorSecretary->value => '01711000456',
            Role::NationalScreeningCommittee->value => '01711000789',
            Role::Executive->value => '01711000987',
            Role::SystemAdmin->value => '01711000654',
        ];

        // Create a user for each Role enum case
        $citizen = null;
        foreach (Role::cases() as $role) {
            $user = User::create([
                'name' => $role->label(),
                'email' => str_replace('_', '', $role->value) . '@nflrms.gov.bd',
                'phone' => $phones[$role->value] ?? null,
                'password' => Hash::make('password'),
                'role' => $role,
                'district_id' => $dhakaId,
                'upazila_id' => $upazilaId,
            ]);

            if ($role === Role::CitizenApplicant) {
                $citizen = $user;
            }
        }

        // Seed sample licenses for public lookup
        if ($citizen) {
            \App\Models\License::create([
                'license_number' => 'BD-HND-DHK-004521',
                'user_id' => $citizen->id,
                'type' => 'citizen_arms',
                'issue_date' => now()->subYear(),
                'expiry_date' => now()->addYears(2),
                'status' => 'active',
                'firearm_details' => [
                    'weapon_type' => 'Revolver',
                    'brand' => 'Smith & Wesson',
                    'bore' => '0.32',
                    'serial_number' => 'SW-4521',
                ],
            ]);

            \App\Models\License::create([
                'license_number' => 'BD-LNG-DHK-001192',
                'user_id' => $citizen->id,
                'type' => 'citizen_arms',
                'issue_date' => now()->subYears(2),
                'expiry_date' => now()->subMonth(),
                'status' => 'suspended',
                'firearm_details' => [
                    'weapon_type' => 'Shotgun',
                    'brand' => 'Remington',
                    'bore' => '12 Gauge',
                    'serial_number' => 'RM-1192',
                ],
            ]);
        }
    }
}
