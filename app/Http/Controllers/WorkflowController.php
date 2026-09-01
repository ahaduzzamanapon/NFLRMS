<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\CustomComment;
use App\Models\DealerStock;
use App\Models\License;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class WorkflowController extends Controller
{
    /**
     * Case detail view — shared across DC Front Desk, JM Branch, DC, MoHA.
     */
    public function applicationDetail(string $encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $application = Application::with([
            'user.district',
            'user.upazila',
            'vettings',
            'logs.actor',
            'district',
            'upazila',
        ])->findOrFail($id);

        $userRole = auth()->user()->role;
        $roleVal = $userRole instanceof Role ? $userRole->value : $userRole;

        $customComments = CustomComment::where(function ($q) use ($roleVal) {
            $q->where('user_id', auth()->id())
                ->orWhere('role_id', $roleVal);
        })->latest()->get();

        return view('office.application_detail', compact('application', 'customComments'));
    }

    /**
     * All licenses — for executive view.
     */
    public function allLicenses()
    {
        $licenses = License::with(['user', 'application'])->latest()->paginate(50);

        return view('office.all_licenses', compact('licenses'));
    }

    /**
     * DC Front Desk Dashboard.
     */
    public function frontDeskDashboard()
    {
        $user = auth()->user();
        $query = Application::where('status', 'submitted')
            ->where('current_actor_role', Role::DcFrontDesk->value);

        if ($user->district_id) {
            $query->where('district_id', $user->district_id);
        }

        $applications = $query->with(['user', 'district', 'upazila'])
            ->latest()
            ->get();

        return view('office.front_desk', compact('applications'));
    }

    /**
     * Front Desk receives & forwards application.
     */
    public function frontDeskAction(Request $request, string $encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $application = Application::findOrFail($id);

        $request->validate([
            'action' => ['required', 'string', 'in:forward,reject'],
            'remarks' => ['required', 'string'],
        ]);

        if ($request->action === 'forward') {
            $application->update([
                'status' => 'received',
                'current_actor_role' => Role::DcJmBranch->value,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'received',
                'from_status' => 'submitted',
                'to_status' => 'received',
                'actor_id' => auth()->id(),
                'remarks' => 'Documents verified by Front Desk. Forwarded to JM Branch. Remarks: '.$request->remarks,
            ]);

            return redirect()->route('front_desk.dashboard')->with('success', 'Application received and forwarded to JM Branch.');
        } else {
            $application->update([
                'status' => 'rejected_front_desk',
                'current_actor_role' => Role::CitizenApplicant->value,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'rejected',
                'from_status' => 'submitted',
                'to_status' => 'rejected_front_desk',
                'actor_id' => auth()->id(),
                'remarks' => 'Application rejected at Front Desk. Reason: '.$request->remarks,
            ]);

            return redirect()->route('front_desk.dashboard')->with('warning', 'Application rejected.');
        }
    }

    /**
     * DC JM Branch Dashboard.
     */
    public function jmBranchDashboard()
    {
        $user = auth()->user();
        $query = Application::whereIn('status', ['received', 'pending_vetting', 'vetted_cleared', 'vetted_flagged'])
            ->where('current_actor_role', Role::DcJmBranch->value);

        if ($user->district_id) {
            $query->where('district_id', $user->district_id);
        }

        $applications = $query->with(['user', 'vettings'])
            ->latest()
            ->get();

        return view('office.jm_branch', compact('applications'));
    }

    /**
     * JM Branch triggers vetting or forwards to DC.
     */
    public function jmBranchAction(Request $request, string $encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $application = Application::findOrFail($id);

        $request->validate([
            'action' => ['required', 'string', 'in:trigger_vetting,forward_dc,reject'],
            'remarks' => ['required', 'string'],
        ]);

        if ($request->action === 'trigger_vetting') {
            $agencies = ['police', 'sb', 'nsi', 'dgfi'];

            foreach ($agencies as $agency) {
                Vetting::create([
                    'application_id' => $application->id,
                    'agency' => $agency,
                    'status' => 'pending',
                ]);
            }

            $application->update([
                'status' => 'pending_vetting',
                // Keep actor as JmBranch, but status shows it's waiting for agencies
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'triggered_vetting',
                'from_status' => 'received',
                'to_status' => 'pending_vetting',
                'actor_id' => auth()->id(),
                'remarks' => 'Security vetting initiated with Police, SB, NSI, and DGFI. Remarks: '.$request->remarks,
            ]);

            return redirect()->route('jm_branch.dashboard')->with('success', 'Security vetting dispatched successfully.');
        } elseif ($request->action === 'forward_dc') {
            $application->update([
                'status' => 'recommended',
                'current_actor_role' => Role::DistrictCommissioner->value,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'forwarded_dc',
                'from_status' => $application->status,
                'to_status' => 'recommended',
                'actor_id' => auth()->id(),
                'remarks' => 'JM Branch reviewed vetting reports and forwarded recommendation to DC. Remarks: '.$request->remarks,
            ]);

            return redirect()->route('jm_branch.dashboard')->with('success', 'Application recommended and forwarded to District Commissioner.');
        } else {
            $application->update([
                'status' => 'rejected_jm_branch',
                'current_actor_role' => Role::CitizenApplicant->value,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'rejected',
                'from_status' => $application->status,
                'to_status' => 'rejected_jm_branch',
                'actor_id' => auth()->id(),
                'remarks' => 'Rejected by JM Branch. Reason: '.$request->remarks,
            ]);

            return redirect()->route('jm_branch.dashboard')->with('warning', 'Application rejected.');
        }
    }

    /**
     * District Commissioner Dashboard.
     */
    public function dcDashboard()
    {
        $user = auth()->user();
        $query = Application::where('status', 'recommended')
            ->where('current_actor_role', Role::DistrictCommissioner->value);

        if ($user->district_id) {
            $query->where('district_id', $user->district_id);
        }

        $applications = $query->with(['user', 'vettings'])
            ->latest()
            ->get();

        return view('office.dc_dashboard', compact('applications'));
    }

    /**
     * DC Action (Approve / Forward to MoHA / Reject).
     */
    public function dcAction(Request $request, string $encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $application = Application::findOrFail($id);

        $request->validate([
            'action' => ['required', 'string', 'in:approve,forward_moha,reject'],
            'remarks' => ['required', 'string'],
        ]);

        if ($request->action === 'approve') {
            $feeAmount = $this->calculateLicenseFee($application);

            $application->update([
                'status' => 'waiting_for_license_fee',
                'current_actor_role' => $application->applicant_type === 'dealer' ? Role::DealerApplicant->value : Role::CitizenApplicant->value,
                'license_fee_amount' => $feeAmount,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'approved_by_dc',
                'from_status' => 'recommended',
                'to_status' => 'waiting_for_license_fee',
                'actor_id' => auth()->id(),
                'remarks' => 'Approved by District Commissioner. Awaiting license fee payment of BDT '.number_format($feeAmount).'. Remarks: '.$request->remarks,
            ]);

            return redirect()->route('dc.dashboard')->with('success', 'Application approved. Awaiting license fee payment of BDT '.number_format($feeAmount).' from applicant.');
        } elseif ($request->action === 'forward_moha') {
            $application->update([
                'status' => 'referred_moha',
                'current_actor_role' => Role::MohaDesk->value,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'referred_moha',
                'from_status' => 'recommended',
                'to_status' => 'referred_moha',
                'actor_id' => auth()->id(),
                'remarks' => 'Referred to Ministry of Home Affairs (MoHA) for national level screening. Remarks: '.$request->remarks,
            ]);

            return redirect()->route('dc.dashboard')->with('success', 'Application referred to Ministry of Home Affairs.');
        } else {
            $application->update([
                'status' => 'rejected_dc',
                'current_actor_role' => Role::CitizenApplicant->value,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'rejected',
                'from_status' => 'recommended',
                'to_status' => 'rejected_dc',
                'actor_id' => auth()->id(),
                'remarks' => 'Rejected by District Commissioner. Reason: '.$request->remarks,
            ]);

            return redirect()->route('dc.dashboard')->with('warning', 'Application rejected.');
        }
    }

    /**
     * MoHA / Ministry Dashboard.
     */
    public function mohaDashboard()
    {
        $user = auth()->user();

        $userRoleVal = $user->role instanceof Role ? $user->role->value : $user->role;

        // Roles in MoHA: MohaDesk, JointSecretary, SeniorSecretary, NationalScreeningCommittee
        $applications = Application::whereIn('status', ['referred_moha', 'moha_processing', 'pending_screening', 'screened'])
            ->where('current_actor_role', $userRoleVal)
            ->with(['user', 'vettings', 'logs'])
            ->latest()
            ->get();

        return view('office.moha_dashboard', compact('applications', 'user'));
    }

    /**
     * Senior Secretary Home Dashboard.
     */
    public function seniorSecretaryDashboard()
    {
        $user = auth()->user();

        // Count Cards (realistic dummy metrics, structured for easy DB conversion)
        $stats = [
            'total_licenses' => 14,
            'total_approved_licenses' => 8,
            'total_pending_licenses' => 4,
            'total_suspended_licenses' => 2,
            'total_citizens' => 6,
            'total_dealers' => 5,
            'total_firearms' => 120,
            'total_ammunition' => 1200,
        ];

        // District-wise License Statistics
        $districtStats = [
            ['name' => 'Dhaka', 'count' => 4850, 'approved' => 4120, 'pending' => 480, 'percentage' => 32.6],
            ['name' => 'Chattogram', 'count' => 2940, 'approved' => 2480, 'pending' => 340, 'percentage' => 19.8],
            ['name' => 'Rajshahi', 'count' => 1820, 'approved' => 1520, 'pending' => 210, 'percentage' => 12.3],
            ['name' => 'Khulna', 'count' => 1460, 'approved' => 1210, 'pending' => 180, 'percentage' => 9.8],
            ['name' => 'Sylhet', 'count' => 1250, 'approved' => 1050, 'pending' => 140, 'percentage' => 8.4],
            ['name' => 'Barisal', 'count' => 980, 'approved' => 820, 'pending' => 110, 'percentage' => 6.6],
            ['name' => 'Rangpur', 'count' => 870, 'approved' => 740, 'pending' => 90, 'percentage' => 5.9],
            ['name' => 'Mymensingh', 'count' => 680, 'approved' => 580, 'pending' => 80, 'percentage' => 4.6],
        ];

        // Thana-wise License Statistics
        $thanaStats = [
            ['name' => 'Gulshan', 'count' => 1240, 'district' => 'Dhaka', 'percentage' => 8.3],
            ['name' => 'Dhanmondi', 'count' => 980, 'district' => 'Dhaka', 'percentage' => 6.6],
            ['name' => 'Uttara', 'count' => 890, 'district' => 'Dhaka', 'percentage' => 6.0],
            ['name' => 'Kotwali', 'count' => 750, 'district' => 'Chattogram', 'percentage' => 5.1],
            ['name' => 'Mirpur', 'count' => 680, 'district' => 'Dhaka', 'percentage' => 4.6],
            ['name' => 'Boalia', 'count' => 540, 'district' => 'Rajshahi', 'percentage' => 3.6],
            ['name' => 'Panchlaish', 'count' => 490, 'district' => 'Chattogram', 'percentage' => 3.3],
            ['name' => 'Sadarganj', 'count' => 420, 'district' => 'Sylhet', 'percentage' => 2.8],
        ];

        // License Status Summary & Processing Progress
        $licenseStatusSummary = [
            'approved_rate' => 83.6,
            'pending_rate' => 12.3,
            'suspended_rate' => 4.1,
            'vetting_completed' => 91.2,
            'moha_reviewed' => 88.5,
            'restricted_weapon_share' => 14.8,
        ];

        // Recent Applications / Licenses
        $recentApplications = [
            [
                'id' => 1,
                'app_no' => 'APP-2026-0981',
                'license_no' => 'NFL-2026-8841',
                'applicant_name' => 'Brig. Gen. (Retd.) Tariq Mahmud',
                'type' => 'Restricted Firearm (Pistol .9mm)',
                'category' => 'Citizen',
                'district' => 'Dhaka',
                'thana' => 'Gulshan',
                'status' => 'approved',
                'status_label' => 'Approved',
                'date' => '2026-08-27',
            ],
            [
                'id' => 2,
                'app_no' => 'APP-2026-0975',
                'license_no' => 'NFL-2026-8835',
                'applicant_name' => 'Bengal Arms & Ammunition Ltd.',
                'type' => 'Dealer License Renewal',
                'category' => 'Dealer',
                'district' => 'Chattogram',
                'thana' => 'Kotwali',
                'status' => 'approved',
                'status_label' => 'Approved',
                'date' => '2026-08-26',
            ],
            [
                'id' => 3,
                'app_no' => 'APP-2026-0968',
                'license_no' => 'Pending Issuance',
                'applicant_name' => 'Advocate Nazmul Haque',
                'type' => 'Shotgun 12-Gauge',
                'category' => 'Citizen',
                'district' => 'Rajshahi',
                'thana' => 'Boalia',
                'status' => 'pending_screening',
                'status_label' => 'Nat. Screening Review',
                'date' => '2026-08-25',
            ],
            [
                'id' => 4,
                'app_no' => 'APP-2026-0952',
                'license_no' => 'Pending Issuance',
                'applicant_name' => 'Shahjalal Security Equipment Ltd.',
                'type' => 'Dealer License New',
                'category' => 'Dealer',
                'district' => 'Sylhet',
                'thana' => 'Sadarganj',
                'status' => 'moha_processing',
                'status_label' => 'MoHA Review',
                'date' => '2026-08-24',
            ],
            [
                'id' => 5,
                'app_no' => 'APP-2026-0941',
                'license_no' => 'NFL-2024-5120',
                'applicant_name' => 'Kabir Hossain',
                'type' => 'Rifle .22 LR',
                'category' => 'Citizen',
                'district' => 'Khulna',
                'thana' => 'Sonadanga',
                'status' => 'suspended',
                'status_label' => 'Suspended',
                'date' => '2026-08-22',
            ],
            [
                'id' => 6,
                'app_no' => 'APP-2026-0930',
                'license_no' => 'NFL-2026-8810',
                'applicant_name' => 'Dr. Farhana Ahmed',
                'type' => 'Pistol .32 ACP',
                'category' => 'Citizen',
                'district' => 'Dhaka',
                'thana' => 'Dhanmondi',
                'status' => 'approved',
                'status_label' => 'Approved',
                'date' => '2026-08-20',
            ],
        ];

        // Recent Activities Log
        $recentActivities = [
            [
                'action' => 'Final Approval Granted',
                'description' => 'Approved Restricted Firearm License for APP-2026-0981 (Brig. Gen. Retd. Tariq Mahmud)',
                'time' => '2 hours ago',
                'icon' => 'fa-solid fa-circle-check',
                'color' => 'text-emerald-600 bg-emerald-50 border-emerald-200',
            ],
            [
                'action' => 'Screening Committee Minutes Uploaded',
                'description' => 'National Screening Committee submitted clearance for 42 pending applications',
                'time' => '5 hours ago',
                'icon' => 'fa-solid fa-file-signature',
                'color' => 'text-blue-600 bg-blue-50 border-blue-200',
            ],
            [
                'action' => 'Inter-Agency Security Clearance',
                'description' => 'SB, NSI & DGFI completed combined clearance report for Chattogram Zone',
                'time' => '1 day ago',
                'icon' => 'fa-solid fa-shield-halved',
                'color' => 'text-purple-600 bg-purple-50 border-purple-200',
            ],
            [
                'action' => 'Dealer Stock Audit Completed',
                'description' => 'Annual stock ledger inspection completed for 18 dealers in Dhaka South',
                'time' => '2 days ago',
                'icon' => 'fa-solid fa-boxes-stacked',
                'color' => 'text-amber-600 bg-amber-50 border-amber-200',
            ],
            [
                'action' => 'License Suspension Order',
                'description' => 'Suspended license NFL-2024-5120 due to non-compliance with renewal directive',
                'time' => '3 days ago',
                'icon' => 'fa-solid fa-triangle-exclamation',
                'color' => 'text-rose-600 bg-rose-50 border-rose-200',
            ],
        ];

        return view('office.senior_secretary_dashboard', compact(
            'user',
            'stats',
            'districtStats',
            'thanaStats',
            'licenseStatusSummary',
            'recentApplications',
            'recentActivities'
        ));
    }

    /**
     * MoHA Actions based on Role.
     */
    public function mohaAction(Request $request, string $encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $application = Application::findOrFail($id);

        $request->validate([
            'action' => ['required', 'string', 'in:forward,approve,reject'],
            'remarks' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $nextActor = null;
        $nextStatus = $application->status;

        if ($request->action === 'forward') {
            if ($user->role === Role::MohaDesk) {
                $nextActor = Role::JointSecretary;
                $nextStatus = 'moha_processing';
            } elseif ($user->role === Role::JointSecretary) {
                $nextActor = Role::NationalScreeningCommittee;
                $nextStatus = 'pending_screening';
            } elseif ($user->role === Role::NationalScreeningCommittee) {
                $nextActor = Role::SeniorSecretary;
                $nextStatus = 'screened';
            }

            $application->update([
                'status' => $nextStatus,
                'current_actor_role' => $nextActor->value,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'forwarded_moha',
                'from_status' => $application->status,
                'to_status' => $nextStatus,
                'actor_id' => $user->id,
                'remarks' => 'Forwarded by '.$user->name.'. Remarks: '.$request->remarks,
            ]);

            return redirect()->route('moha.dashboard')->with('success', 'Application forwarded successfully.');
        } elseif ($request->action === 'approve') {
            // Final Approval by Senior Secretary / Hon'ble Minister
            if ($user->role !== Role::SeniorSecretary) {
                abort(403);
            }

            $feeAmount = $this->calculateLicenseFee($application);

            $application->update([
                'status' => 'waiting_for_license_fee',
                'current_actor_role' => $application->applicant_type === 'dealer' ? Role::DealerApplicant->value : Role::CitizenApplicant->value,
                'license_fee_amount' => $feeAmount,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'approved_moha',
                'from_status' => $application->status,
                'to_status' => 'waiting_for_license_fee',
                'actor_id' => $user->id,
                'remarks' => 'Final approval granted by Senior Secretary / Hon\'ble Minister. Awaiting license fee payment of BDT '.number_format($feeAmount).'. Remarks: '.$request->remarks,
            ]);

            return redirect()->route('moha.dashboard')->with('success', 'Application approved. Awaiting license fee payment of BDT '.number_format($feeAmount).' from applicant.');
        } else {
            $application->update([
                'status' => 'rejected_moha',
                'current_actor_role' => Role::CitizenApplicant->value,
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'rejected',
                'from_status' => $application->status,
                'to_status' => 'rejected_moha',
                'actor_id' => $user->id,
                'remarks' => 'Rejected by MoHA ('.$user->name.'). Reason: '.$request->remarks,
            ]);

            return redirect()->route('moha.dashboard')->with('warning', 'Application rejected.');
        }
    }

    /**
     * Executive Dashboard.
     */
    public function executiveDashboard()
    {
        $stats = [
            'total_applications' => Application::count(),
            'approved_licenses' => License::count(),
            'pending_vetting' => Vetting::where('status', 'pending')->count(),
            'total_dealers' => User::whereIn('role', [Role::DealerApplicant->value, 'dealer_applicant'])->count(),
            'total_dealer_stock' => DealerStock::sum('quantity'),
            'total_revenue' => License::count() * 45000 + Application::whereNotIn('status', ['approved', 'rejected'])->count() * 850,
            'renewal_rate' => 96.5,
        ];

        $latestApplications = Application::with(['user', 'district', 'upazila'])
            ->latest()
            ->take(10)
            ->get();

        $licenses = License::with('user')->latest()->get();

        return view('office.executive_dashboard', compact('stats', 'latestApplications', 'licenses'));
    }

    /**
     * System Administrator Dashboard.
     */
    public function adminDashboard()
    {
        $users = User::with(['district', 'upazila'])->get();
        $auditLogs = ApplicationLog::with(['application', 'actor'])->latest()->take(50)->get();

        return view('admin.dashboard', compact('users', 'auditLogs'));
    }

    /**
     * Helper: Issue a license.
     */
    protected function issueLicense(Application $application)
    {
        $licenseNum = 'LIC-'.strtoupper(Str::random(10));

        License::create([
            'license_number' => $licenseNum,
            'user_id' => $application->user_id,
            'application_id' => $application->id,
            'type' => $application->applicant_type === 'dealer' ? 'dealer_dealing' : 'citizen_arms',
            'issue_date' => now(),
            'expiry_date' => now()->addYears(3),
            'status' => 'active',
            'firearm_details' => $application->firearm_details,
            'qrcode' => route('verify', ['license_number' => $licenseNum]),
        ]);
    }

    /**
     * Calculate license fee based on settings.
     */
    protected function calculateLicenseFee(Application $application): int
    {
        if ($application->applicant_type === 'dealer') {
            return 100000;
        }

        $weaponType = $application->firearm_details['weapon_type'] ?? '';
        $isHandgun = in_array($weaponType, ['Pistol', 'Revolver']);

        if ($application->type === 'renewal') {
            return $isHandgun
                ? Setting::get('fee_pistol_renewal', 20000)
                : Setting::get('fee_longgun_renewal', 10000);
        }

        return $isHandgun
            ? Setting::get('fee_pistol_new', 60000)
            : Setting::get('fee_longgun_new', 40000);
    }
}
