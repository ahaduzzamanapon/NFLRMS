<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\CustomComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $officerRoles = [
            Role::DcFrontDesk,
            Role::DcJmBranch,
            Role::DistrictCommissioner,
            Role::MohaDesk,
            Role::JointSecretary,
            Role::SeniorSecretary,
            Role::NationalScreeningCommittee,
        ];

        // Reusable comments mapped to the officer roles that use the application detail page
        $commentTemplates = [
            Role::DcFrontDesk->value => [
                ['title' => 'Documents Verified', 'comment' => 'All statutory documents verified and found complete. Application forwarded for JM Branch review.'],
                ['title' => 'Documents Incomplete', 'comment' => 'Required documents are incomplete. Applicant must submit missing documents before further processing.'],
                ['title' => 'NID Matched', 'comment' => 'National ID verified successfully against the national database. No mismatch found.'],
                ['title' => 'Photo Mismatch', 'comment' => 'Applicant photo does not match the NID record. Applicant must appear physically for verification.'],
            ],
            Role::DcJmBranch->value => [
                ['title' => 'Vetting Initiated', 'comment' => 'Security vetting requested from Police, SB, NSI and DGFI for background screening.'],
                ['title' => 'Vetting Cleared', 'comment' => 'All security vetting reports received and cleared. Recommended for approval.'],
                ['title' => 'Vetting Flagged', 'comment' => 'Security vetting report flagged concerns. Application requires further investigation.'],
            ],
            Role::DistrictCommissioner->value => [
                ['title' => 'Approved', 'comment' => 'Application approved by the District Commissioner. License fee payment is now pending from the applicant.'],
                ['title' => 'Referred to MoHA', 'comment' => 'Handgun case referred to the Ministry of Home Affairs for national level screening.'],
            ],
            Role::MohaDesk->value => [
                ['title' => 'Forwarded to Joint Secretary', 'comment' => 'Application reviewed at Political-4 / Sasan-4 desk and forwarded to the Joint Secretary.'],
                ['title' => 'Pending Ministry Review', 'comment' => 'Application is queued for review at the Ministry level. No action taken yet.'],
            ],
            Role::JointSecretary->value => [
                ['title' => 'Recommended for Screening', 'comment' => 'Preliminary review complete. Application recommended to the National Screening Committee.'],
            ],
            Role::NationalScreeningCommittee->value => [
                ['title' => 'Screened & Cleared', 'comment' => 'National Screening Committee reviewed the case and found no objections. Forwarded for final approval.'],
                ['title' => 'Screened with Concerns', 'comment' => 'Committee raised concerns regarding the application. Requires senior secretary attention.'],
            ],
            Role::SeniorSecretary->value => [
                ['title' => 'Final Approval', 'comment' => 'Final approval granted by the Senior Secretary / Hon\'ble Minister. Awaiting license fee payment.'],
                ['title' => 'Rejected', 'comment' => 'Application rejected at the final stage. Applicant has been notified of the decision.'],
            ],
        ];

        foreach ($officerRoles as $role) {
            $user = User::where('role', $role->value)->first();

            if (! $user) {
                continue;
            }

            $templates = $commentTemplates[$role->value] ?? [];

            foreach ($templates as $template) {
                CustomComment::create([
                    'title' => $template['title'],
                    'comment' => $template['comment'],
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
