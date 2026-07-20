<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\License;
use App\Models\District;
use App\Models\Upazila;
use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the applicant's applications.
     */
    public function index()
    {
        $user = auth()->user();
        $applications = $user->applications()->latest()->get();
        $licenses = $user->licenses()->latest()->get();

        return view('citizen.dashboard', compact('applications', 'licenses'));
    }

    /**
     * Show the form for creating a new application.
     */
    public function create()
    {
        $user = auth()->user();
        $districts = District::orderBy('name')->get();
        return view('citizen.apply', compact('user', 'districts'));
    }

    public function store(Request $request)
    {
        $isDealer = auth()->user()->role === Role::DealerApplicant || $request->input('type') === 'new_dealing_license';

        if ($isDealer) {
            $request->validate([
                'firm_name' => ['required', 'string', 'max:255'],
                'trade_license' => ['required', 'string', 'max:255'],
                'business_address' => ['required', 'string'],
                'district_id' => ['required', 'integer', 'exists:districts,id'],
                'license_class' => ['required', 'string'],
                'nid' => ['required', 'string', 'min:10', 'max:17'],
                'mobile' => ['required', 'string'],
                'annual_income' => ['required', 'numeric', 'min:0'],
                'categories' => ['required', 'array', 'min:1'],
            ]);

            $appNumber = 'DEAL-' . strtoupper(Str::random(8)) . '-' . date('Y');

            $application = Application::create([
                'application_number' => $appNumber,
                'user_id' => auth()->id(),
                'type' => 'new_dealing_license',
                'applicant_type' => 'dealer',
                'status' => 'submitted',
                'district_id' => $request->district_id,
                'upazila_id' => auth()->user()->upazila_id ?? \App\Models\Upazila::where('district_id', $request->district_id)->first()?->id,
                'applicant_details' => [
                    'nid' => $request->nid,
                    'firm_name' => $request->firm_name,
                    'trade_license' => $request->trade_license,
                    'business_address' => $request->business_address,
                    'license_class' => $request->license_class,
                    'mobile' => $request->mobile,
                    'annual_income' => $request->annual_income,
                ],
                'firearm_details' => [
                    'weapon_type' => 'Dealing License',
                    'categories' => $request->categories,
                ],
                'current_actor_role' => Role::DcFrontDesk->value,
            ]);
        } else {
            $request->validate([
                'nid' => ['required', 'string', 'min:10', 'max:17'],
                'dob' => ['required', 'date'],
                'father_name' => ['required', 'string', 'max:255'],
                'present_address' => ['required', 'string'],
                'permanent_address' => ['required', 'string'],
                'annual_income' => ['required', 'numeric', 'min:0'],
                'weapon_type' => ['required', 'string'],
                'bore' => ['required', 'string'],
                'purpose' => ['required', 'string'],
                'district_id' => ['required', 'integer', 'exists:districts,id'],
                'upazila_id' => ['required', 'integer', 'exists:upazilas,id'],
            ]);

            $appNumber = 'FL-' . strtoupper(Str::random(8)) . '-' . date('Y');

            $application = Application::create([
                'application_number' => $appNumber,
                'user_id' => auth()->id(),
                'type' => 'new',
                'applicant_type' => 'citizen',
                'status' => 'submitted',
                'district_id' => $request->district_id,
                'upazila_id' => $request->upazila_id,
                'applicant_details' => [
                    'nid' => $request->nid,
                    'dob' => $request->dob,
                    'father_name' => $request->father_name,
                    'present_address' => $request->present_address,
                    'permanent_address' => $request->permanent_address,
                    'annual_income' => $request->annual_income,
                ],
                'firearm_details' => [
                    'weapon_type' => $request->weapon_type,
                    'bore' => $request->bore,
                    'purpose' => $request->purpose,
                ],
                'current_actor_role' => Role::DcFrontDesk->value,
            ]);
        }

        // Create log entry
        ApplicationLog::create([
            'application_id' => $application->id,
            'action' => 'submitted',
            'from_status' => 'draft',
            'to_status' => 'submitted',
            'actor_id' => auth()->id(),
            'remarks' => 'Application submitted and routed to Front Desk / ICT Cell.',
        ]);

        $route = auth()->user()->role === \App\Enums\Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard';
        return redirect()->route($route)->with('success', 'Application submitted successfully! Your tracking number is ' . $appNumber);
    }

    /**
     * Display the specified application details.
     */
    public function show(Application $application)
    {
        // Check if user is authorized to view this application
        $user = auth()->user();
        if ($user->role === Role::CitizenApplicant || $user->role === Role::DealerApplicant) {
            if ($application->user_id !== $user->id) {
                abort(403);
            }
        }

        return view('citizen.show', compact('application'));
    }

    /**
     * Show renewal apply form.
     */
    public function renewalForm(License $license)
    {
        $user = auth()->user();
        if ($license->user_id !== $user->id) {
            abort(403);
        }

        $licenses = $user->licenses()->latest()->get();

        return view('citizen.apply_renewal', compact('license', 'user', 'licenses'));
    }

    /**
     * Submit renewal application.
     */
    public function storeRenewal(Request $request, License $license)
    {
        $user = auth()->user();
        if ($license->user_id !== $user->id) {
            abort(403);
        }

        $appNumber = 'RL-' . strtoupper(Str::random(8)) . '-' . date('Y');

        $application = Application::create([
            'application_number' => $appNumber,
            'user_id' => $user->id,
            'type' => 'renewal',
            'applicant_type' => $user->role === Role::DealerApplicant ? 'dealer' : 'citizen',
            'status' => 'submitted',
            'district_id' => $user->district_id,
            'upazila_id' => $user->upazila_id,
            'applicant_details' => [
                'license_id' => $license->id,
                'license_number' => $license->license_number,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'firearm_details' => $license->firearm_details,
            'current_actor_role' => Role::DcFrontDesk->value,
        ]);

        ApplicationLog::create([
            'application_id' => $application->id,
            'action' => 'submitted',
            'from_status' => 'draft',
            'to_status' => 'submitted',
            'actor_id' => $user->id,
            'remarks' => 'License renewal application submitted to DC Front Desk.',
        ]);

        $route = auth()->user()->role === \App\Enums\Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard';
        return redirect()->route($route)->with('success', 'Renewal application submitted successfully! Tracking number is ' . $appNumber);
    }

    /**
     * Handle general renewal redirect from the sidebar link.
     */
    public function renewalGeneral()
    {
        $user = auth()->user();
        $license = $user->licenses()->first();
        if ($license) {
            return redirect()->route('citizen.renew', $license->id);
        }
        $route = auth()->user()->role === \App\Enums\Role::DealerApplicant ? 'dealer.dashboard' : 'citizen.dashboard';
        return redirect()->route($route)->with('error', 'You do not have any active licenses to renew.');
    }
}
