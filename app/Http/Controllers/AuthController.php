<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\District;
use App\Models\Upazila;
use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form with quick login helper.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUserDashboard(Auth::user());
        }

        $quickUsers = User::all();
        return view('auth.login', compact('quickUsers'));
    }

    /**
     * Handle authentication request.
     */
    public function login(Request $request)
    {
        $loginValue = $request->input('phone') ?? $request->input('email');
        $loginField = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $request->validate([
            'password' => ['required'],
        ]);

        $password = $request->input('password');

        $credentials = [
            $loginField => $loginValue,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectUserDashboard(Auth::user());
        }

        // Fallback: Try with 'password' if the typed password was 'demo1234'
        if ($password === 'demo1234') {
            $credentials['password'] = 'password';
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return $this->redirectUserDashboard(Auth::user());
            }
        }

        // Fallback: If they typed email in 'phone' field or vice versa
        $fallbackField = $loginField === 'email' ? 'phone' : 'email';
        $credentials = [
            $fallbackField => $loginValue,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectUserDashboard(Auth::user());
        }

        if ($password === 'demo1234') {
            $credentials['password'] = 'password';
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return $this->redirectUserDashboard(Auth::user());
            }
        }

        return back()->withErrors([
            'phone' => 'The provided credentials do not match our records.',
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('phone', 'email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Show registration form for applicants.
     */
    public function showRegister()
    {
        $districts = District::orderBy('name')->get();
        return view('auth.register', compact('districts'));
    }

    /**
     * Handle registration for applicants.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:citizen_applicant,dealer_applicant'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'upazila_id' => ['required', 'integer', 'exists:upazilas,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'district_id' => $data['district_id'],
            'upazila_id' => $data['upazila_id'],
        ]);

        Auth::login($user);

        return $this->redirectUserDashboard($user);
    }

    /**
     * Get upazilas of a district (API helper).
     */
    public function getUpazilas(District $district)
    {
        return response()->json($district->upazilas()->orderBy('name')->get());
    }

    /**
     * Redirect users based on their role.
     */
    protected function redirectUserDashboard(User $user)
    {
        $roleVal = $user->role instanceof Role ? $user->role->value : $user->role;

        return match ($roleVal) {
            Role::CitizenApplicant->value => redirect()->route('citizen.dashboard'),
            Role::DealerApplicant->value => redirect()->route('dealer.dashboard'),
            Role::DcFrontDesk->value => redirect()->route('front_desk.dashboard'),
            Role::DcJmBranch->value => redirect()->route('jm_branch.dashboard'),
            Role::DistrictCommissioner->value => redirect()->route('dc.dashboard'),
            Role::PoliceOfficer->value, Role::SpecialBranch->value, Role::NsiOfficer->value, Role::DgfiOfficer->value => redirect()->route('vetting.dashboard'),
            Role::MohaDesk->value, Role::JointSecretary->value, Role::SeniorSecretary->value, Role::NationalScreeningCommittee->value => redirect()->route('moha.dashboard'),
            Role::Executive->value => redirect()->route('executive.dashboard'),
            Role::SystemAdmin->value => redirect()->route('admin.dashboard'),
            default => redirect()->route('profile.edit')->with('warning', 'You have been logged in. Since you have a custom role, please contact your administrator for system permissions.'),
        };
    }
}
