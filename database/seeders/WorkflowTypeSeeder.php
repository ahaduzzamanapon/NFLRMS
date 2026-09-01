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
                'name_bn' => null,
                'description' => 'New firearms licence application process for general citizens.',
                'is_active' => true,
            ],
            [
                'key' => 'citizen_renew',
                'name' => 'Citizen Licence Renewal',
                'name_bn' => null,
                'description' => 'Licence renewal process for existing licence holders.',
                'is_active' => true,
            ],
            [
                'key' => 'dealer_new',
                'name' => 'Dealer New Licence (Form K)',
                'name_bn' => null,
                'description' => 'New licence application process for firearms dealers (Form K).',
                'is_active' => true,
            ],
            [
                'key' => 'dealer_renew',
                'name' => 'Dealer Licence Renewal',
                'name_bn' => null,
                'description' => 'Licence renewal process for existing dealers.',
                'is_active' => true,
            ],
        ];

        foreach ($types as $typeData) {
            WorkflowType::firstOrCreate(['key' => $typeData['key']], $typeData);
        }

        // Default steps for citizen_new
        $citizenNew = WorkflowType::where('key', 'citizen_new')->first();

        if ($citizenNew && $citizenNew->steps()->count() === 0) {
            $steps = [
                ['step_order' => 1,  'role_key' => 'dc_front_desk',         'role_name' => 'DC Front Desk',           'step_name' => 'DC Front Desk — Intake & Verification',   'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 2,  'role_key' => 'dc_jm_branch',          'role_name' => 'DC JM Branch',            'step_name' => 'JM Branch — Review & Recommendation',     'can_approve' => true, 'can_reject' => true,  'can_return' => true],
                ['step_order' => 3,  'role_key' => 'district_commissioner', 'role_name' => 'District Commissioner',   'step_name' => 'District Commissioner — Recommendation',  'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 4,  'role_key' => 'police_officer',        'role_name' => 'Police Officer (Thana)',  'step_name' => 'Thana Police Vetting',                    'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 5,  'role_key' => 'special_branch',        'role_name' => 'Special Branch (SB)',     'step_name' => 'Special Branch (SB) Vetting',             'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 6,  'role_key' => 'nsi_officer',           'role_name' => 'NSI Officer',             'step_name' => 'NSI Vetting',                             'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 7,  'role_key' => 'dgfi_officer',          'role_name' => 'DGFI Officer',            'step_name' => 'DGFI Vetting',                            'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 8,  'role_key' => 'moha_desk',             'role_name' => 'MoHA Desk (Political-4)', 'step_name' => 'MoHA Desk — Verification',                'can_approve' => true, 'can_reject' => true,  'can_return' => true],
                ['step_order' => 9,  'role_key' => 'joint_secretary',       'role_name' => 'Joint Secretary',         'step_name' => 'Joint Secretary — Approval',              'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 10, 'role_key' => 'senior_secretary',      'role_name' => 'Senior Secretary',        'step_name' => 'Senior Secretary — Final Approval',       'can_approve' => true, 'can_reject' => true,  'can_return' => false],
            ];

            foreach ($steps as $step) {
                $citizenNew->steps()->create($step + ['is_active' => true]);
            }
        }

        // Default steps for citizen_renew
        $citizenRenew = WorkflowType::where('key', 'citizen_renew')->first();

        if ($citizenRenew && $citizenRenew->steps()->count() === 0) {
            $steps = [
                ['step_order' => 1, 'role_key' => 'dc_front_desk',         'role_name' => 'DC Front Desk',         'step_name' => 'DC Front Desk — Verification',            'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 2, 'role_key' => 'dc_jm_branch',          'role_name' => 'DC JM Branch',          'step_name' => 'JM Branch — Recommendation',              'can_approve' => true, 'can_reject' => true,  'can_return' => true],
                ['step_order' => 3, 'role_key' => 'district_commissioner', 'role_name' => 'District Commissioner', 'step_name' => 'District Commissioner — Recommendation',  'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 4, 'role_key' => 'moha_desk',             'role_name' => 'MoHA Desk',             'step_name' => 'MoHA Desk — Verification & Approval',     'can_approve' => true, 'can_reject' => true,  'can_return' => false],
            ];

            foreach ($steps as $step) {
                $citizenRenew->steps()->create($step + ['is_active' => true]);
            }
        }

        // Default steps for dealer_new
        $dealerNew = WorkflowType::where('key', 'dealer_new')->first();

        if ($dealerNew && $dealerNew->steps()->count() === 0) {
            $steps = [
                ['step_order' => 1, 'role_key' => 'dc_front_desk',         'role_name' => 'DC Front Desk',         'step_name' => 'DC Front Desk — Intake & Verification',   'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 2, 'role_key' => 'dc_jm_branch',          'role_name' => 'DC JM Branch',          'step_name' => 'JM Branch — Review',                      'can_approve' => true, 'can_reject' => true,  'can_return' => true],
                ['step_order' => 3, 'role_key' => 'district_commissioner', 'role_name' => 'District Commissioner', 'step_name' => 'District Commissioner — Recommendation',  'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 4, 'role_key' => 'police_officer',        'role_name' => 'Police Officer',        'step_name' => 'Police Vetting',                           'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 5, 'role_key' => 'moha_desk',             'role_name' => 'MoHA Desk',             'step_name' => 'MoHA Desk — Approval',                    'can_approve' => true, 'can_reject' => true,  'can_return' => false],
                ['step_order' => 6, 'role_key' => 'senior_secretary',      'role_name' => 'Senior Secretary',      'step_name' => 'Senior Secretary — Final Approval',       'can_approve' => true, 'can_reject' => true,  'can_return' => false],
            ];

            foreach ($steps as $step) {
                $dealerNew->steps()->create($step + ['is_active' => true]);
            }
        }

        // Default steps for dealer_renew
        $dealerRenew = WorkflowType::where('key', 'dealer_renew')->first();

        if ($dealerRenew && $dealerRenew->steps()->count() === 0) {
            $steps = [
                ['step_order' => 1, 'role_key' => 'dc_front_desk',    'role_name' => 'DC Front Desk',    'step_name' => 'DC Front Desk — Verification',       'can_approve' => true, 'can_reject' => true, 'can_return' => false],
                ['step_order' => 2, 'role_key' => 'dc_jm_branch',     'role_name' => 'DC JM Branch',     'step_name' => 'JM Branch — Recommendation',         'can_approve' => true, 'can_reject' => true, 'can_return' => true],
                ['step_order' => 3, 'role_key' => 'moha_desk',        'role_name' => 'MoHA Desk',        'step_name' => 'MoHA Desk — Approval',               'can_approve' => true, 'can_reject' => true, 'can_return' => false],
                ['step_order' => 4, 'role_key' => 'senior_secretary', 'role_name' => 'Senior Secretary', 'step_name' => 'Senior Secretary — Final Approval',  'can_approve' => true, 'can_reject' => true, 'can_return' => false],
            ];

            foreach ($steps as $step) {
                $dealerRenew->steps()->create($step + ['is_active' => true]);
            }
        }
    }
}
