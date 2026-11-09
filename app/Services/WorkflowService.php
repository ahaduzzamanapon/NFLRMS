<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Application;
use App\Models\ApplicationLog;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vetting;
use App\Models\WorkflowStep;
use App\Models\WorkflowType;

class WorkflowService
{
    /**
     * Resolve the workflow type for an application based on its type and applicant_type.
     */
    public function resolveWorkflowType(Application $application): ?WorkflowType
    {
        if ($application->workflow_type_id) {
            $wf = WorkflowType::find($application->workflow_type_id);
            if ($wf && $wf->is_active) {
                return $wf;
            }
        }

        $isDealer = $application->applicant_type === 'dealer'
            || str_contains($application->type ?? '', 'dealer')
            || $application->type === 'new_dealing_license';

        $isRenewal = str_contains($application->type ?? '', 'renew')
            || $application->applicant_type === 'renewal';

        $key = match (true) {
            $isDealer && $isRenewal => 'dealer_renew',
            $isDealer => 'dealer_new',
            $isRenewal => 'citizen_renew',
            default => 'citizen_new',
        };

        $workflow = WorkflowType::where('key', $key)->where('is_active', true)->first();

        if (! $workflow) {
            $workflow = WorkflowType::where('is_active', true)->first();
        }

        if ($workflow && ! $application->workflow_type_id) {
            $application->update(['workflow_type_id' => $workflow->id]);
        }

        return $workflow;
    }

    /**
     * Initialize workflow for a newly submitted application (Step 1).
     */
    public function initWorkflow(Application $application): Application
    {
        $workflow = $this->resolveWorkflowType($application);

        if (! $workflow) {
            return $application;
        }

        $firstStep = $workflow->activeSteps()->first();

        if ($firstStep) {
            $application->update([
                'workflow_type_id' => $workflow->id,
                'current_step_id' => $firstStep->id,
                'current_actor_role' => $firstStep->primaryRoleKey(),
                'step_approvals' => [],
            ]);
        }

        return $application;
    }

    /**
     * Get the current active step for the application, auto-resolving if not set.
     */
    public function getCurrentStep(Application $application): ?WorkflowStep
    {
        if ($application->current_step_id) {
            $step = WorkflowStep::find($application->current_step_id);
            if ($step) {
                return $step;
            }
        }

        $workflow = $this->resolveWorkflowType($application);
        if (! $workflow) {
            return null;
        }

        // Try to match current_actor_role to a step
        $currentRole = $application->current_actor_role;
        $matchedStep = null;

        if ($currentRole) {
            $matchedStep = $workflow->activeSteps()
                ->get()
                ->first(function (WorkflowStep $s) use ($currentRole) {
                    return in_array($currentRole, $s->getAllRoleKeys());
                });
        }

        if (! $matchedStep) {
            $matchedStep = $workflow->activeSteps()->first();
        }

        if ($matchedStep) {
            $application->update([
                'workflow_type_id' => $workflow->id,
                'current_step_id' => $matchedStep->id,
            ]);
        }

        return $matchedStep;
    }

    /**
     * Check if a given user can act on the application at its current stage.
     */
    public function canUserAct(Application $application, User $user): bool
    {
        if (in_array($application->status, ['approved', 'license_issued', 'draft'])
            || str_starts_with($application->status, 'rejected')) {
            return false;
        }

        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        // System Admin can always oversee
        if ($userRole === Role::SystemAdmin->value) {
            return true;
        }

        // Check district restriction (except MoHA, Executive, Admin)
        $nationalRoles = [
            Role::MohaDesk->value,
            Role::JointSecretary->value,
            Role::SeniorSecretary->value,
            Role::NationalScreeningCommittee->value,
            Role::Executive->value,
            Role::SystemAdmin->value,
        ];

        if ($user->district_id && ! in_array($userRole, $nationalRoles)) {
            if ($application->district_id && $application->district_id !== $user->district_id) {
                return false;
            }
        }

        $step = $this->getCurrentStep($application);
        if (! $step) {
            return $application->current_actor_role === $userRole;
        }

        $stepRoles = $step->getAllRoleKeys();
        if (! in_array($userRole, $stepRoles)) {
            return false;
        }

        // If specific user_ids assigned, check if user is in that list
        if (! empty($step->user_ids) && is_array($step->user_ids)) {
            if (! in_array($user->id, $step->user_ids)) {
                return false;
            }
        }

        // Check if user (or role) already approved this step
        $approvals = $application->step_approvals ?? [];
        if (isset($approvals[$userRole])) {
            return false;
        }

        return true;
    }

    /**
     * Get the detailed approval status of the current step (for parallel multi-role review).
     */
    public function getStepApprovalStatus(Application $application): array
    {
        $step = $this->getCurrentStep($application);
        if (! $step) {
            return [
                'has_step' => false,
                'step_name' => 'Pending Review',
                'is_multi' => false,
                'required_roles' => [],
                'approvals' => [],
                'approved_roles' => [],
                'pending_roles' => [],
                'is_complete' => false,
            ];
        }

        $requiredRoles = $step->getAllRoleKeys();
        $approvals = $application->step_approvals ?? [];
        $approvedRoles = array_keys($approvals);
        $pendingRoles = array_values(array_diff($requiredRoles, $approvedRoles));

        $isComplete = count($pendingRoles) === 0;

        return [
            'has_step' => true,
            'step' => $step,
            'step_name' => $step->step_name,
            'is_multi' => $step->isMultiRole(),
            'approval_type' => $step->approval_type ?? 'all',
            'required_roles' => $requiredRoles,
            'approvals' => $approvals,
            'approved_roles' => $approvedRoles,
            'pending_roles' => $pendingRoles,
            'is_complete' => $isComplete,
        ];
    }

    /**
     * Process an officer action (approve/forward, reject, return) on an application.
     */
    public function processAction(Application $application, User $user, string $action, string $remarks): array
    {
        $step = $this->getCurrentStep($application);
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        if ($action === 'reject') {
            return $this->handleReject($application, $user, $remarks);
        }

        if ($action === 'return') {
            return $this->handleReturn($application, $user, $remarks);
        }

        // Approve / Forward
        return $this->handleApprove($application, $user, $remarks, $step);
    }

    /**
     * Handle approval / forwarding. Supports both single-actor and parallel multi-actor steps.
     */
    protected function handleApprove(Application $application, User $user, string $remarks, ?WorkflowStep $step): array
    {
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;
        $approvals = $application->step_approvals ?? [];

        // Record this user's approval
        $approvals[$userRole] = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $userRole,
            'action' => 'approved',
            'remarks' => $remarks,
            'acted_at' => now()->toIso8601String(),
        ];

        $application->step_approvals = $approvals;
        $application->save();

        // Also if user is vetting agency officer, update Vetting record
        $this->syncVettingRecordIfApplicable($application, $user, $remarks);

        // Check if all required roles have approved
        $requiredRoles = $step ? $step->getAllRoleKeys() : [$userRole];
        $approvedRoles = array_keys($approvals);
        $pendingRoles = array_diff($requiredRoles, $approvedRoles);

        $isMulti = $step && $step->isMultiRole();
        $isComplete = empty($pendingRoles);

        if ($isMulti && ! $isComplete) {
            // Partial approval received, still waiting for remaining roles
            $pendingRoleLabels = implode(', ', array_map([$this, 'formatRoleLabel'], $pendingRoles));

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'step_partial_approval',
                'from_status' => $application->status,
                'to_status' => $application->status,
                'actor_id' => $user->id,
                'remarks' => "Approved by {$user->name} (".($this->formatRoleLabel($userRole))."). Awaiting approval from: {$pendingRoleLabels}. Remarks: {$remarks}",
            ]);

            return [
                'success' => true,
                'advance' => false,
                'message' => "Your approval has been recorded. Application is awaiting approval from: {$pendingRoleLabels}.",
            ];
        }

        // Step is complete! Advance to next step
        ApplicationLog::create([
            'application_id' => $application->id,
            'action' => 'step_approved',
            'from_status' => $application->status,
            'to_status' => $application->status,
            'actor_id' => $user->id,
            'remarks' => ($step ? "Step '{$step->step_name}' fully approved." : 'Approved by '.$user->name).'. Remarks: '.$remarks,
        ]);

        return $this->advanceToNextStep($application, $user, $step);
    }

    /**
     * Advance application to the next active step in the workflow.
     */
    protected function advanceToNextStep(Application $application, User $user, ?WorkflowStep $currentStep): array
    {
        $workflow = $this->resolveWorkflowType($application);

        $nextStep = null;
        if ($workflow && $currentStep) {
            $nextStep = $workflow->activeSteps()
                ->where('step_order', '>', $currentStep->step_order)
                ->orderBy('step_order')
                ->first();
        }

        if ($nextStep) {
            $nextRole = $nextStep->primaryRoleKey();
            $nextStatus = $this->resolveStatusForStep($nextStep);

            $application->update([
                'current_step_id' => $nextStep->id,
                'current_actor_role' => $nextRole,
                'status' => $nextStatus,
                'step_approvals' => [],
            ]);

            // If next step contains vetting agencies, ensure vetting records exist
            $this->ensureVettingRecordsForStep($application, $nextStep);

            ApplicationLog::create([
                'application_id' => $application->id,
                'action' => 'forwarded',
                'from_status' => $currentStep ? $this->resolveStatusForStep($currentStep) : $application->status,
                'to_status' => $nextStatus,
                'actor_id' => $user->id,
                'remarks' => "Advanced to step '{$nextStep->step_name}' (Assigned: {$nextStep->role_name}).",
            ]);

            return [
                'success' => true,
                'advance' => true,
                'message' => "Step completed. Application forwarded to {$nextStep->step_name}.",
            ];
        }

        // Final step completed! License issuance / fee payment stage
        $feeAmount = $this->calculateLicenseFee($application);
        $applicantRole = $application->applicant_type === 'dealer'
            ? Role::DealerApplicant->value
            : Role::CitizenApplicant->value;

        $application->update([
            'current_step_id' => null,
            'status' => 'waiting_for_license_fee',
            'current_actor_role' => $applicantRole,
            'license_fee_amount' => $feeAmount,
            'step_approvals' => [],
        ]);

        ApplicationLog::create([
            'application_id' => $application->id,
            'action' => 'final_approval',
            'from_status' => $application->status,
            'to_status' => 'waiting_for_license_fee',
            'actor_id' => $user->id,
            'remarks' => 'All workflow steps completed. Final approval granted. Awaiting license fee payment of BDT '.number_format($feeAmount).'.',
        ]);

        return [
            'success' => true,
            'advance' => true,
            'message' => 'Final approval granted. Awaiting license fee payment from applicant.',
        ];
    }

    /**
     * Handle rejection.
     */
    protected function handleReject(Application $application, User $user, string $remarks): array
    {
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;
        $applicantRole = $application->applicant_type === 'dealer'
            ? Role::DealerApplicant->value
            : Role::CitizenApplicant->value;

        $rejectStatus = 'rejected_'.$userRole;

        $application->update([
            'status' => $rejectStatus,
            'current_actor_role' => $applicantRole,
        ]);

        ApplicationLog::create([
            'application_id' => $application->id,
            'action' => 'rejected',
            'from_status' => $application->status,
            'to_status' => $rejectStatus,
            'actor_id' => $user->id,
            'remarks' => 'Application rejected by '.$user->name.' ('.$this->formatRoleLabel($userRole).'). Reason: '.$remarks,
        ]);

        return [
            'success' => true,
            'advance' => false,
            'message' => 'Application has been rejected.',
        ];
    }

    /**
     * Handle returning application to previous step.
     */
    protected function handleReturn(Application $application, User $user, string $remarks): array
    {
        $currentStep = $this->getCurrentStep($application);
        $workflow = $this->resolveWorkflowType($application);

        $prevStep = null;
        if ($workflow && $currentStep) {
            $prevStep = $workflow->activeSteps()
                ->where('step_order', '<', $currentStep->step_order)
                ->orderByDesc('step_order')
                ->first();
        }

        if ($prevStep) {
            $application->update([
                'current_step_id' => $prevStep->id,
                'current_actor_role' => $prevStep->primaryRoleKey(),
                'status' => $this->resolveStatusForStep($prevStep),
                'step_approvals' => [],
            ]);

            $destName = $prevStep->step_name;
        } else {
            $applicantRole = $application->applicant_type === 'dealer'
                ? Role::DealerApplicant->value
                : Role::CitizenApplicant->value;

            $application->update([
                'current_step_id' => null,
                'current_actor_role' => $applicantRole,
                'status' => 'returned',
                'step_approvals' => [],
            ]);

            $destName = 'Applicant';
        }

        ApplicationLog::create([
            'application_id' => $application->id,
            'action' => 'returned',
            'from_status' => $application->status,
            'to_status' => $application->status,
            'actor_id' => $user->id,
            'remarks' => "Returned by {$user->name} to {$destName}. Reason: {$remarks}",
        ]);

        return [
            'success' => true,
            'advance' => false,
            'message' => "Application returned to {$destName}.",
        ];
    }

    /**
     * Resolve default status string for a step.
     */
    protected function resolveStatusForStep(WorkflowStep $step): string
    {
        $role = $step->primaryRoleKey();

        return match ($role) {
            'dc_front_desk' => 'submitted',
            'dc_jm_branch' => 'received',
            'district_commissioner' => 'recommended',
            'police_officer', 'special_branch', 'nsi_officer', 'dgfi_officer' => 'pending_vetting',
            'moha_desk' => 'referred_moha',
            'joint_secretary' => 'moha_processing',
            'national_screening_committee' => 'pending_screening',
            'senior_secretary' => 'screened',
            default => 'processing_'.$role,
        };
    }

    /**
     * Automatically ensure Vetting rows exist if a step involves vetting agencies.
     */
    protected function ensureVettingRecordsForStep(Application $application, WorkflowStep $step): void
    {
        $vettingRoleMap = [
            'police_officer' => 'police',
            'special_branch' => 'sb',
            'nsi_officer' => 'nsi',
            'dgfi_officer' => 'dgfi',
        ];

        foreach ($step->getAllRoleKeys() as $rk) {
            if (isset($vettingRoleMap[$rk])) {
                $agency = $vettingRoleMap[$rk];
                Vetting::firstOrCreate([
                    'application_id' => $application->id,
                    'agency' => $agency,
                ], [
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * If user is a vetting officer, sync Vetting table record.
     */
    protected function syncVettingRecordIfApplicable(Application $application, User $user, string $remarks): void
    {
        $userRole = $user->role instanceof Role ? $user->role->value : (string) $user->role;

        $agencyMap = [
            'police_officer' => 'police',
            'special_branch' => 'sb',
            'nsi_officer' => 'nsi',
            'dgfi_officer' => 'dgfi',
        ];

        if (isset($agencyMap[$userRole])) {
            $agency = $agencyMap[$userRole];
            $vetting = Vetting::where('application_id', $application->id)
                ->where('agency', $agency)
                ->first();

            if ($vetting) {
                $vetting->update([
                    'status' => 'cleared',
                    'remarks' => $remarks,
                    'vetted_by' => $user->id,
                    'vetted_at' => now(),
                ]);
            }
        }
    }

    /**
     * Calculate license fee based on application details and system settings.
     */
    public function calculateLicenseFee(Application $application): int
    {
        if ($application->applicant_type === 'dealer') {
            return 100000;
        }

        $weaponType = $application->firearm_details['weapon_type'] ?? '';
        $isHandgun = in_array($weaponType, ['Pistol', 'Revolver']);

        if ($application->type === 'renewal') {
            return $isHandgun
                ? (int) Setting::get('fee_pistol_renewal', 20000)
                : (int) Setting::get('fee_longgun_renewal', 10000);
        }

        return $isHandgun
            ? (int) Setting::get('fee_pistol_new', 60000)
            : (int) Setting::get('fee_longgun_new', 40000);
    }

    /**
     * Human-friendly label for a role key.
     */
    public function formatRoleLabel(string $roleKey): string
    {
        $customRoles = json_decode(Setting::get('custom_roles', '{}'), true) ?: [];
        if (isset($customRoles[$roleKey])) {
            return $customRoles[$roleKey];
        }

        $enum = Role::tryFrom($roleKey);
        if ($enum) {
            return $enum->label();
        }

        return ucwords(str_replace('_', ' ', $roleKey));
    }
}
