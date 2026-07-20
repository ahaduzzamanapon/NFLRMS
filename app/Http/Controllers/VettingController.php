<?php

namespace App\Http\Controllers;

use App\Models\Vetting;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Enums\Role;
use Illuminate\Http\Request;

class VettingController extends Controller
{
    /**
     * Display a listing of vetting tasks for the logged-in agency officer.
     */
    public function index()
    {
        $user = auth()->user();
        $agency = $this->getAgencyByRole($user->role);

        if (!$agency) {
            abort(403, 'Unauthorized vetting access.');
        }

        $query = Vetting::where('agency', $agency);

        if ($user->district_id) {
            $query->whereHas('application', function ($q) use ($user) {
                $q->where('district_id', $user->district_id);
            });
        }

        $vettings = $query->with(['application.user', 'application.district', 'application.upazila'])
            ->latest()
            ->get();

        return view('office.vetting_dashboard', compact('vettings', 'agency'));
    }

    /**
     * Show a single vetting detail/report form.
     */
    public function show(Vetting $vetting)
    {
        $vetting->load(['application.user.district', 'application.logs']);

        return view('office.vetting_show', compact('vetting'));
    }

    /**
     * Submit a vetting report.
     */
    public function submit(Request $request, Vetting $vetting)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:cleared,flagged'],
            'remarks' => ['required', 'string'],
        ]);

        $user = auth()->user();

        // Update vetting record
        $vetting->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
            'vetted_by' => $user->id,
            'vetted_at' => now(),
            'report_file' => 'reports/' . $vetting->id . '_clearance.pdf', // Mock uploaded report file
        ]);

        // Check if all vettings for this application are complete
        $application = $vetting->application;
        $totalVettings = $application->vettings()->count();
        $completedVettings = $application->vettings()->whereIn('status', ['cleared', 'flagged'])->count();

        // Log the action
        ApplicationLog::create([
            'application_id' => $application->id,
            'action' => 'vetted_by_' . $vetting->agency,
            'from_status' => $application->status,
            'to_status' => $application->status,
            'actor_id' => $user->id,
            'remarks' => strtoupper($vetting->agency) . ' vetting completed: ' . ucfirst($request->status) . '. Remarks: ' . $request->remarks,
        ]);

        if ($totalVettings === $completedVettings) {
            // Check if any agency flagged the application
            $hasFlags = $application->vettings()->where('status', 'flagged')->exists();
            $nextStatus = $hasFlags ? 'vetted_flagged' : 'vetted_cleared';

            $application->update([
                'status' => $nextStatus,
                'current_actor_role' => Role::DcJmBranch->value, // Route back to JM Branch for review
            ]);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'vetting_completed',
                'from_status' => $application->status,
                'to_status' => $nextStatus,
                'actor_id' => null,
                'remarks' => 'All agency security clearances received. Application sent to JM Branch for final review.',
            ]);
        }

        return redirect()->route('vetting.dashboard')->with('success', 'Security clearance report submitted successfully.');
    }

    /**
     * Map user role to agency code.
     */
    protected function getAgencyByRole($role): ?string
    {
        $roleValue = $role instanceof Role ? $role->value : $role;
        return match ($roleValue) {
            Role::PoliceOfficer->value => 'police',
            Role::SpecialBranch->value => 'sb',
            Role::NsiOfficer->value => 'nsi',
            Role::DgfiOfficer->value => 'dgfi',
            default => null,
        };
    }
}
