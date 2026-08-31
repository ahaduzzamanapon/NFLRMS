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
            Role::PoliceOfficer,
            Role::SpecialBranch,
            Role::NsiOfficer,
            Role::DgfiOfficer,
        ];

        // Reusable comments mapped to the officer roles that use the application detail page
        $commentTemplates = [
            Role::DcFrontDesk->value => [
                ['title' => 'Documents Verified', 'comment' => 'All statutory documents verified and found complete. Application forwarded for JM Branch review.'],
                ['title' => 'Documents Incomplete', 'comment' => 'Required documents are incomplete. Applicant must submit missing documents before further processing.'],
                ['title' => 'NID Matched', 'comment' => 'National ID verified successfully against the national database. No mismatch found.'],
                ['title' => 'Photo Mismatch', 'comment' => 'Applicant photo does not match the NID record. Applicant must appear physically for verification.'],
                ['title' => 'Application Rejected', 'comment' => 'Application rejected due to incomplete or insufficient information.'],
            ],
            Role::DcJmBranch->value => [
                ['title' => 'Vetting Initiated', 'comment' => 'Security vetting requested from Police, SB, NSI and DGFI for background screening.'],
                ['title' => 'Vetting Cleared', 'comment' => 'All security vetting reports received and cleared. Recommended for approval.'],
                ['title' => 'Vetting Flagged', 'comment' => 'Security vetting report flagged concerns. Application requires further investigation.'],
                ['title' => 'Application Rejected', 'comment' => 'Application rejected due to incomplete or insufficient information.'],
            ],
            Role::DistrictCommissioner->value => [
                ['title' => 'Approved', 'comment' => 'Application approved by the District Commissioner. License fee payment is now pending from the applicant.'],
                ['title' => 'Referred to MoHA', 'comment' => 'Handgun case referred to the Ministry of Home Affairs for national level screening.'],
                ['title' => 'Application Rejected', 'comment' => 'Application rejected due to incomplete or insufficient information.'],
            ],
            Role::MohaDesk->value => [
                ['title' => 'Forwarded to Joint Secretary', 'comment' => 'Application reviewed at Political-4 / Sasan-4 desk and forwarded to the Joint Secretary.'],
                ['title' => 'Pending Ministry Review', 'comment' => 'Application is queued for review at the Ministry level. No action taken yet.'],
                ['title' => 'Application Rejected', 'comment' => 'Application rejected due to incomplete or insufficient information.'],
            ],
            Role::JointSecretary->value => [
                ['title' => 'Recommended for Screening', 'comment' => 'Preliminary review complete. Application recommended to the National Screening Committee.'],
                ['title' => 'Application Rejected', 'comment' => 'Application rejected due to incomplete or insufficient information.'],
            ],
            Role::NationalScreeningCommittee->value => [
                ['title' => 'Screened & Cleared', 'comment' => 'National Screening Committee reviewed the case and found no objections. Forwarded for final approval.'],
                ['title' => 'Screened with Concerns', 'comment' => 'Committee raised concerns regarding the application. Requires senior secretary attention.'],
                ['title' => 'Application Rejected', 'comment' => 'Application rejected due to incomplete or insufficient information.'],
            ],
            Role::SeniorSecretary->value => [
                ['title' => 'Final Approval', 'comment' => 'Final approval granted by the Senior Secretary / Hon\'ble Minister. Awaiting license fee payment.'],
                ['title' => 'Application Rejected', 'comment' => 'Application rejected due to incomplete or insufficient information.'],
                ['title' => 'Rejected', 'comment' => 'Application rejected at the final stage. Applicant has been notified of the decision.'],
            ],
            Role::PoliceOfficer->value => [
                ['title' => 'No Criminal Record', 'comment' => 'Local police station records verified. No criminal case or involvement found against the applicant. Cleared.'],
                ['title' => 'Criminal Case Pending', 'comment' => 'A criminal case is pending against the applicant at the local police station. Flagged for further investigation.'],
                ['title' => 'Clean Background', 'comment' => 'Thorough background check completed through police database. No adverse findings. Cleared.'],
            ],
            Role::SpecialBranch->value => [
                ['title' => 'No Security Concern', 'comment' => 'Special Branch intelligence check completed. No security or subversive activity found against the applicant. Cleared.'],
                ['title' => 'Under Surveillance', 'comment' => 'Applicant is currently under Special Branch surveillance for suspicious activity. Flagged.'],
                ['title' => 'Verified Through SB Database', 'comment' => 'SB national database verified. No threat assessment identified against the applicant. Cleared.'],
            ],
            Role::NsiOfficer->value => [
                ['title' => 'Background Verified', 'comment' => 'NSI background investigation completed. Applicant\'s credentials, associates and affiliations verified with no concerns. Cleared.'],
                ['title' => 'Intelligence Concern', 'comment' => 'NSI intelligence report indicates associations with persons of interest. Flagged for review.'],
                ['title' => 'No Adverse Intelligence', 'comment' => 'No adverse intelligence information found against the applicant in NSI records. Cleared.'],
            ],
            Role::DgfiOfficer->value => [
                ['title' => 'National Security Cleared', 'comment' => 'DGFI national security screening completed. No threat to national security identified. Cleared.'],
                ['title' => 'Security Risk Identified', 'comment' => 'DGFI assessment identified potential national security risk associated with the applicant. Flagged.'],
                ['title' => 'No Strategic Concerns', 'comment' => 'No strategic or national security concerns found after DGFI screening. Cleared.'],
            ],
        ];

        foreach ($officerRoles as $role) {
            $user = User::where('role', $role->value)->first();

            if (! $user) {
                continue;
            }

            $templates = $commentTemplates[$role->value] ?? [];

            foreach ($templates as $template) {
                CustomComment::firstOrCreate(
                    [
                        'title' => $template['title'],
                        'user_id' => $user->id,
                    ],
                    [
                        'comment' => $template['comment'],
                        'role_id' => $role->value,
                    ]
                );
            }
        }
    }
}
