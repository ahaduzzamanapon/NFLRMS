<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\DealerStock;
use App\Models\License;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkflowController extends Controller
{
    /**
     * Case detail view — shared across DC Front Desk, JM Branch, DC, MoHA.
     */
    public function applicationDetail(Application $application)
    {
        $application->load(['user.district', 'user.upazila', 'vettings', 'logs.actor', 'district', 'upazila']);

        return view('office.application_detail', compact('application'));
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
    public function frontDeskAction(Request $request, Application $application)
    {
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
    public function jmBranchAction(Request $request, Application $application)
    {
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
    public function dcAction(Request $request, Application $application)
    {
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
     * MoHA Actions based on Role.
     */
    public function mohaAction(Request $request, Application $application)
    {
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
