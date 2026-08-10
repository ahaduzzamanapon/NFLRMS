<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class FeeConfigSeeder extends Seeder
{
    public function run(): void
    {
        $fees = [
            // Citizen statutory fees
            'fee_pistol_new' => 60000,
            'fee_pistol_renewal' => 20000,
            'fee_longgun_new' => 40000,
            'fee_longgun_renewal' => 10000,
            'fee_platform_new' => 850,
            'fee_platform_renewal' => 720,
            'fee_platform_late' => 250,
            'fine_t1_pistol' => 2000,
            'fine_t1_longgun' => 1000,
            'fine_t2_pistol' => 5000,
            'fine_t2_longgun' => 2500,
            'fine_t3_pistol' => 10000,
            'fine_t3_longgun' => 5000,
            // Dealer statutory fees (per license class)
            'dealer_fee_class_a_new' => 150000,
            'dealer_fee_class_a_renewal' => 75000,
            'dealer_fee_class_b_new' => 200000,
            'dealer_fee_class_b_renewal' => 100000,
            'dealer_fee_class_c_new' => 250000,
            'dealer_fee_class_c_renewal' => 125000,
            // Dealer platform charges
            'dealer_platform_new' => 2500,
            'dealer_platform_renewal' => 2500,
            // SLA timers
            'sla_vetting' => 10,
            'sla_moha' => 15,
            'sla_committee' => 20,
        ];

        foreach ($fees as $key => $value) {
            Setting::set($key, $value);
        }

        $this->command->info('Fee configuration seeded successfully.');
    }
}
