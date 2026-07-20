<?php

namespace App\Enums;

enum Role: string
{
    case CitizenApplicant = 'citizen_applicant';
    case DealerApplicant = 'dealer_applicant';
    case DcFrontDesk = 'dc_front_desk';
    case DcJmBranch = 'dc_jm_branch';
    case DistrictCommissioner = 'district_commissioner';
    case PoliceOfficer = 'police_officer';
    case SpecialBranch = 'special_branch';
    case NsiOfficer = 'nsi_officer';
    case DgfiOfficer = 'dgfi_officer';
    case MohaDesk = 'moha_desk';
    case JointSecretary = 'joint_secretary';
    case SeniorSecretary = 'senior_secretary';
    case NationalScreeningCommittee = 'national_screening_committee';
    case Executive = 'executive';
    case SystemAdmin = 'system_admin';

    /**
     * Get the human-readable label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::CitizenApplicant => 'Citizen Applicant',
            self::DealerApplicant => 'Dealer Applicant',
            self::DcFrontDesk => 'DC Office — Front Desk / ICT Cell',
            self::DcJmBranch => 'DC Office — JM Branch',
            self::DistrictCommissioner => 'District Commissioner',
            self::PoliceOfficer => 'Police Officer (SP/Thana)',
            self::SpecialBranch => 'Special Branch (SB)',
            self::NsiOfficer => 'NSI Officer',
            self::DgfiOfficer => 'DGFI Officer',
            self::MohaDesk => 'MoHA Desk (Political-4/Sasan-4)',
            self::JointSecretary => 'Joint / Additional Secretary',
            self::SeniorSecretary => 'Senior Secretary / Hon\'ble Minister',
            self::NationalScreeningCommittee => 'National Screening Committee',
            self::Executive => 'Executive Dashboard (Minister/Secretary)',
            self::SystemAdmin => 'System Administrator',
        };
    }
}
