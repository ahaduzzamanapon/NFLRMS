<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\District;
use App\Models\License;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function userManagement()
    {
        $users = User::with(['district', 'upazila'])->orderBy('name')->get();
        $districts = District::orderBy('name')->get();

        $defaultRoles = [
            'citizen_applicant' => 'Citizen Applicant',
            'dealer_applicant' => 'Dealer Applicant',
            'dc_front_desk' => 'DC Office — Front Desk',
            'dc_jm_branch' => 'DC Office — JM Branch',
            'district_commissioner' => 'District Commissioner',
            'police_officer' => 'Police Officer (SP/Thana)',
            'special_branch' => 'Special Branch (SB)',
            'nsi_officer' => 'NSI Officer',
            'dgfi_officer' => 'DGFI Officer',
            'moha_desk' => 'MoHA Desk',
            'joint_secretary' => 'Joint Secretary',
            'senior_secretary' => 'Senior Secretary',
            'system_admin' => 'System Admin',
        ];

        $customRoles = json_decode(Setting::get('custom_roles', '{}'), true) ?: [];
        $roles = array_merge($defaultRoles, $customRoles);

        return view('admin.dashboard', compact('users', 'districts', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'name_bn'     => ['required', 'string', 'max:255', 'regex:/^[\p{Bengali}\s().,\-\/]+$/u',],
            'email'       => ['required', 'email', 'unique:users,email'],
            'password'    => ['required', 'min:8'],
            'role'        => ['required', 'string'],
            'district_id' => ['nullable', 'integer'],
        ]);

        User::create([
            'name'        => $request->name,
            'name_bn'     => $request->name_bn,
            'email'       => $request->email,
            'password'    => bcrypt($request->password),
            'role'        => $request->role,
            'district_id' => $request->district_id,
            'is_active'   => true,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'User created successfully.');
    }

    public function editUser(User $user)
    {
        $districts = District::orderBy('name')->get();

        $defaultRoles = [
            'citizen_applicant' => 'Citizen Applicant',
            'dealer_applicant' => 'Dealer Applicant',
            'dc_front_desk' => 'DC Office — Front Desk',
            'dc_jm_branch' => 'DC Office — JM Branch',
            'district_commissioner' => 'District Commissioner',
            'police_officer' => 'Police Officer (SP/Thana)',
            'special_branch' => 'Special Branch (SB)',
            'nsi_officer' => 'NSI Officer',
            'dgfi_officer' => 'DGFI Officer',
            'moha_desk' => 'MoHA Desk',
            'joint_secretary' => 'Joint Secretary',
            'senior_secretary' => 'Senior Secretary',
            'system_admin' => 'System Admin',
        ];

        $customRoles = json_decode(Setting::get('custom_roles', '{}'), true) ?: [];
        $roles = array_merge($defaultRoles, $customRoles);

        return view('admin.edit_user', compact('user', 'districts', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users,email,' . $user->id],
            'role'        => ['required', 'string'],
            'district_id' => ['nullable', 'integer'],
            'password'    => ['nullable', 'min:8'],
        ]);

        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'role'        => $request->role,
            'district_id' => $request->district_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.dashboard')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully.');
    }

    public function toggleUser(User $user)
    {
        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->route('admin.dashboard')->with('success', 'User status updated to ' . ($user->is_active ? 'Active' : 'Inactive') . '.');
    }

    public function saveAcl(Request $request)
    {
        $permissions = $request->input('permissions', []);
        Setting::set('acl_matrix', json_encode($permissions));

        return redirect()->route('admin.acl')->with('success', 'ACL permissions saved successfully.');
    }

    public function saveApiConfig(Request $request)
    {
        $fields = [
            'sms_endpoint', 'sms_token', 'sms_sid', 'sms_encoding', 'sms_rate',
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_from',
            'pay_endpoint', 'pay_store_id', 'pay_store_pass',
            'nid_endpoint', 'nid_client_id', 'nid_secret',
            'wh_approved', 'wh_issued', 'wh_secret'
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            }
        }

        return redirect()->route('admin.api_config')->with('success', 'API configuration saved successfully.');
    }


    public function feeConfig()
    {
        $defaults = [
            'fee_pistol_new'       => 60000,
            'fee_pistol_renewal'   => 20000,
            'fee_longgun_new'      => 40000,
            'fee_longgun_renewal'  => 10000,
            'fee_platform_new'     => 850,
            'fee_platform_renewal' => 720,
            'fee_platform_late'    => 250,
            'fine_t1_pistol'       => 2000,
            'fine_t1_longgun'      => 1000,
            'fine_t2_pistol'       => 5000,
            'fine_t2_longgun'      => 2500,
            'fine_t3_pistol'       => 10000,
            'fine_t3_longgun'      => 5000,
            'sla_vetting'          => 10,
            'sla_moha'             => 15,
            'sla_committee'        => 20,
        ];

        $settings = [];
        foreach ($defaults as $key => $default) {
            $settings[$key] = Setting::get($key, $default);
        }

        return view('admin.fee_config', compact('settings'));
    }

    public function saveFeeConfig(Request $request)
    {
        $fields = [
            'fee_pistol_new', 'fee_pistol_renewal', 'fee_longgun_new', 'fee_longgun_renewal',
            'fee_platform_new', 'fee_platform_renewal', 'fee_platform_late',
            'fine_t1_pistol', 'fine_t1_longgun', 'fine_t2_pistol', 'fine_t2_longgun',
            'fine_t3_pistol', 'fine_t3_longgun',
            'sla_vetting', 'sla_moha', 'sla_committee',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                Setting::set($field, $request->input($field));
            }
        }

        return redirect()->route('admin.fee_config')->with('success', 'Fee configuration saved successfully.');
    }

    public function acl()
    {
        $defaultRoles = [
            'citizen_applicant' => 'Citizen Applicant',
            'dealer_applicant' => 'Dealer Applicant',
            'dc_front_desk' => 'DC Office — Front Desk',
            'dc_jm_branch' => 'DC Office — JM Branch',
            'district_commissioner' => 'District Commissioner',
            'police_officer' => 'Police Officer (SP/Thana)',
            'special_branch' => 'Special Branch (SB)',
            'nsi_officer' => 'NSI Officer',
            'dgfi_officer' => 'DGFI Officer',
            'moha_desk' => 'MoHA Desk',
            'joint_secretary' => 'Joint Secretary',
            'senior_secretary' => 'Senior Secretary',
            'system_admin' => 'System Admin',
        ];

        $customRoles = json_decode(Setting::get('custom_roles', '{}'), true) ?: [];
        $roles = array_merge($defaultRoles, $customRoles);

        $modules = [
            'Citizen Portal', 'Dealer Portal', 'DC Office Queue', 'Police Vetting',
            'SB Vetting', 'NSI Vetting', 'DGFI Vetting', 'MoHA — Political-4',
            'MoHA — Joint Secretary', 'MoHA — Minister', 'National Screening Committee',
            'Executive Dashboards', 'Reports', 'Fee & Fine Config', 'User Management',
            'ACL / RBAC', 'API Configuration', 'Audit Log', 'Emergency Kill-Switch'
        ];

        $defaultMatrix = [
            'Citizen Portal' => ['citizen_applicant' => 'write'],
            'Dealer Portal' => ['dealer_applicant' => 'write'],
            'DC Office Queue' => ['dc_front_desk' => 'approve', 'dc_jm_branch' => 'approve', 'district_commissioner' => 'approve'],
            'Police Vetting' => ['police_officer' => 'approve'],
            'SB Vetting' => ['special_branch' => 'approve'],
            'NSI Vetting' => ['nsi_officer' => 'approve'],
            'DGFI Vetting' => ['dgfi_officer' => 'approve'],
            'MoHA — Political-4' => ['moha_desk' => 'approve'],
            'MoHA — Joint Secretary' => ['joint_secretary' => 'approve'],
            'MoHA — Minister' => ['senior_secretary' => 'approve'],
            'Audit Log' => ['system_admin' => 'approve'],
            'User Management' => ['system_admin' => 'approve'],
            'ACL / RBAC' => ['system_admin' => 'approve'],
            'API Configuration' => ['system_admin' => 'approve'],
            'Fee & Fine Config' => ['system_admin' => 'approve'],
            'Reports' => ['system_admin' => 'approve'],
        ];

        $savedMatrixJson = Setting::get('acl_matrix');
        $matrix = $savedMatrixJson ? json_decode($savedMatrixJson, true) : $defaultMatrix;

        return view('admin.acl', compact('roles', 'modules', 'matrix'));
    }

    public function addCustomRole(Request $request)
    {
        $request->validate([
            'role_name' => ['required', 'string', 'max:50'],
        ]);

        $name = $request->role_name;
        $key = strtolower(str_replace(' ', '_', preg_replace('/[^A-Za-z0-9 ]/', '', $name)));

        $customRoles = json_decode(Setting::get('custom_roles', '{}'), true) ?: [];
        $customRoles[$key] = $name;

        Setting::set('custom_roles', json_encode($customRoles));

        return redirect()->route('admin.acl')->with('success', 'Custom role "' . $name . '" added successfully.');
    }

    public function apiConfig()
    {
        return view('admin.api_config');
    }

    public function auditLog()
    {
        $logs = ApplicationLog::with(['application', 'actor'])
            ->latest()
            ->paginate(50);

        return view('admin.audit_log', compact('logs'));
    }

    public function reports()
    {
        $stats = [
            'total_licenses'   => License::count(),
            'total_apps'       => Application::count(),
            'active_licenses'  => License::where('status', 'active')->count(),
            'pending_apps'     => Application::whereNotIn('status', ['approved', 'rejected', 'license_issued'])->count(),
        ];

        // Count approved applications per district (applications has district_id)
        $byDistrict = District::withCount(['applications' => function ($q) {
            $q->where('status', 'approved');
        }])->orderByDesc('applications_count')->take(8)->get();

        return view('admin.reports', compact('stats', 'byDistrict'));
    }

}
