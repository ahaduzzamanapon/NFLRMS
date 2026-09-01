<?php

namespace Database\Seeders;

use App\Models\WorkflowType;
use Illuminate\Database\Seeder;

class WorkflowTypeSeeder extends Seeder
{
    public function run(): void
    {
        // 4 workflow types
        $types = [
            [
                'key' => 'citizen_new',
                'name' => 'Citizen New Licence Application',
                'name_bn' => 'নাগরিক নতুন লাইসেন্স আবেদন',
                'description' => 'সাধারণ নাগরিকের অস্ত্র লাইসেন্সের জন্য নতুন আবেদন প্রক্রিয়া।',
                'is_active' => true,
            ],
            [
                'key' => 'citizen_renew',
                'name' => 'Citizen Licence Renewal',
                'name_bn' => 'নাগরিক লাইসেন্স নবায়ন',
                'description' => 'বিদ্যমান লাইসেন্সধারী নাগরিকের লাইসেন্স নবায়ন প্রক্রিয়া।',
                'is_active' => true,
            ],
            [
                'key' => 'dealer_new',
                'name' => 'Dealer New Licence (Form K)',
                'name_bn' => 'ডিলার নতুন লাইসেন্স (ফর্ম ক)',
                'description' => 'অস্ত্র ব্যবসায়ী / ডিলারের নতুন লাইসেন্স আবেদন প্রক্রিয়া।',
                'is_active' => true,
            ],
            [
                'key' => 'dealer_renew',
                'name' => 'Dealer Licence Renewal',
                'name_bn' => 'ডিলার লাইসেন্স নবায়ন',
                'description' => 'বিদ্যমান ডিলারের লাইসেন্স নবায়ন প্রক্রিয়া।',
                'is_active' => true,
            ],
        ];

        foreach ($types as $typeData) {
            WorkflowType::firstOrCreate(['key' => $typeData['key']], $typeData);
        }

        // Default steps for citizen_new (the main workflow)
        $citizenNew = WorkflowType::where('key', 'citizen_new')->first();

        if ($citizenNew && $citizenNew->steps()->count() === 0) {
            $steps = [
                ['step_order' => 1,  'role_key' => 'dc_front_desk',               'role_name' => 'DC Front Desk',               'step_name' => 'DC Front Desk যাচাই ও গ্রহণ',       'can_approve' => true,  'can_reject' => true,  'can_return' => false],
                ['step_order' => 2,  'role_key' => 'dc_jm_branch',                'role_name' => 'DC JM Branch',                'step_name' => 'JM Branch পরীক্ষা ও মতামত',        'can_approve' => true,  'can_reject' => true,  'can_return' => true],
                ['step_order' => 3,  'role_key' => 'district_commissioner',       'role_name' => 'District Commissioner',       'step_name' => 'জেলা প্রশাসকের সুপারিশ',           'can_approve' => true,  'can_reject' => true,  'can_return' => false],
                ['step_order' => 4,  'role_key' => 'police_officer',              'role_name' => 'Police Officer (Thana)',       'step_name' => 'থানা পুলিশ ভেটিং',                 'can_approve' => true,  'can_reject' => true,  'can_return' => false],
                ['step_order' => 5,  'role_key' => 'special_branch',              'role_name' => 'Special Branch (SB)',          'step_name' => 'বিশেষ শাখা ভেটিং',                 'can_approve' => true,  'can_reject' => true,  'can_return' => false],
                ['step_order' => 6,  'role_key' => 'nsi_officer',                 'role_name' => 'NSI Officer',                  'step_name' => 'NSI ভেটিং',                          'can_approve' => true,  'can_reject' => true,  'can_return' => false],
                ['step_order' => 7,  'role_key' => 'dgfi_officer',                'role_name' => 'DGFI Officer',                 'step_name' => 'DGFI ভেটিং',                         'can_approve' => true,  'can_reject' => true,  'can_return' => false],
                ['step_order' => 8,  'role_key' => 'moha_desk',                   'role_name' => 'MoHA Desk (Political-4)',      'step_name' => 'স্বরাষ্ট্র মন্ত্রণালয় ডেস্ক যাচাই', 'can_approve' => true,  'can_reject' => true,  'can_return' => true],
                ['step_order' => 9,  'role_key' => 'joint_secretary',             'role_name' => 'Joint Secretary',             'step_name' => 'যুগ্ম সচিব অনুমোদন',               'can_approve' => true,  'can_reject' => true,  'can_return' => false],
                ['step_order' => 10, 'role_key' => 'senior_secretary',            'role_name' => 'Senior Secretary',            'step_name' => 'সিনিয়র সচিব চূড়ান্ত অনুমোদন',     'can_approve' => true,  'can_reject' => true,  'can_return' => false],
            ];

            foreach ($steps as $step) {
                $citizenNew->steps()->create($step + ['is_active' => true]);
            }
        }

        // Default steps for citizen_renew (simplified)
        $citizenRenew = WorkflowType::where('key', 'citizen_renew')->first();

        if ($citizenRenew && $citizenRenew->steps()->count() === 0) {
            $steps = [
                ['step_order' => 1, 'role_key' => 'dc_front_desk',         'role_name' => 'DC Front Desk',         'step_name' => 'DC Front Desk যাচাই',         'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 2, 'role_key' => 'dc_jm_branch',          'role_name' => 'DC JM Branch',          'step_name' => 'JM Branch মতামত',             'can_approve' => true, 'can_reject' => true,  'can_return' => true],
                ['step_order' => 3, 'role_key' => 'district_commissioner', 'role_name' => 'District Commissioner', 'step_name' => 'DC সুপারিশ',                  'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 4, 'role_key' => 'moha_desk',             'role_name' => 'MoHA Desk',             'step_name' => 'MoHA ডেস্ক যাচাই ও অনুমোদন', 'can_approve' => true, 'can_reject' => true,  'can_return' => false],
            ];

            foreach ($steps as $step) {
                $citizenRenew->steps()->create($step + ['is_active' => true]);
            }
        }

        // Default steps for dealer_new
        $dealerNew = WorkflowType::where('key', 'dealer_new')->first();

        if ($dealerNew && $dealerNew->steps()->count() === 0) {
            $steps = [
                ['step_order' => 1, 'role_key' => 'dc_front_desk',         'role_name' => 'DC Front Desk',         'step_name' => 'DC Front Desk গ্রহণ ও যাচাই',     'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 2, 'role_key' => 'dc_jm_branch',          'role_name' => 'DC JM Branch',          'step_name' => 'JM Branch পরীক্ষা',               'can_approve' => true, 'can_reject' => true,  'can_return' => true],
                ['step_order' => 3, 'role_key' => 'district_commissioner', 'role_name' => 'District Commissioner', 'step_name' => 'DC সুপারিশ',                       'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 4, 'role_key' => 'police_officer',        'role_name' => 'Police Officer',        'step_name' => 'পুলিশ ভেটিং',                     'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 5, 'role_key' => 'moha_desk',             'role_name' => 'MoHA Desk',             'step_name' => 'স্বরাষ্ট্র মন্ত্রণালয় ডেস্ক অনুমোদন', 'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 6, 'role_key' => 'senior_secretary',      'role_name' => 'Senior Secretary',      'step_name' => 'সিনিয়র সচিব চূড়ান্ত অনুমোদন',   'can_approve' => true, 'can_reject' => true,  'can_return' => false],
            ];

            foreach ($steps as $step) {
                $dealerNew->steps()->create($step + ['is_active' => true]);
            }
        }

        // Default steps for dealer_renew
        $dealerRenew = WorkflowType::where('key', 'dealer_renew')->first();

        if ($dealerRenew && $dealerRenew->steps()->count() === 0) {
            $steps = [
                ['step_order' => 1, 'role_key' => 'dc_front_desk',    'role_name' => 'DC Front Desk', 'step_name' => 'DC Front Desk যাচাই',          'can_approve' => true, 'can_reject' => true, 'can_return' => false],
                ['step_order' => 2, 'role_key' => 'dc_jm_branch',     'role_name' => 'DC JM Branch',  'step_name' => 'JM Branch মতামত',              'can_approve' => true, 'can_reject' => true, 'can_return' => true],
                ['step_order' => 3, 'role_key' => 'moha_desk',        'role_name' => 'MoHA Desk',     'step_name' => 'MoHA ডেস্ক অনুমোদন',          'can_approve' => true, 'can_reject' => true, 'can_return' => false],
                ['step_order' => 4, 'role_key' => 'senior_secretary', 'role_name' => 'Senior Secretary', 'step_name' => 'সিনিয়র সচিব চূড়ান্ত অনুমোদন', 'can_approve' => true, 'can_reject' => true, 'can_return' => false],
            ];

            foreach ($steps as $step) {
                $dealerRenew->steps()->create($step + ['is_active' => true]);
            }
        }
    }
}
