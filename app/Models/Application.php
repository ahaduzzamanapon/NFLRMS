<?php

namespace App\Models;

use App\Services\WorkflowService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    protected $fillable = [
        'application_number',
        'user_id',
        'dealer_id',
        'type',
        'applicant_type',
        'workflow_type_id',
        'status',
        'district_id',
        'upazila_id',
        'applicant_details',
        'firearm_details',
        'documents',
        'current_actor_role',
        'current_step_id',
        'step_approvals',
        'remarks',
        'service_fee_paid',
        'license_fee_paid',
        'service_fee_amount',
        'license_fee_amount',
        'payment_details',
    ];

    protected $casts = [
        'applicant_details' => 'array',
        'firearm_details' => 'array',
        'documents' => 'array',
        'payment_details' => 'array',
        'step_approvals' => 'array',
    ];

    /**
     * Get the workflow type configured for this application.
     */
    public function workflowType(): BelongsTo
    {
        return $this->belongsTo(WorkflowType::class);
    }

    /**
     * Get the current active step in the workflow organogram.
     */
    public function currentStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }

    /**
     * Get the applicant user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sourcing dealer user.
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dealer_id');
    }

    /**
     * Get the district of this application.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the upazila of this application.
     */
    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class);
    }

    /**
     * Vettings requested for this application.
     */
    public function vettings(): HasMany
    {
        return $this->hasMany(Vetting::class);
    }

    /**
     * The issued license for this application, if any.
     */
    public function license(): HasOne
    {
        return $this->hasOne(License::class);
    }

    /**
     * Status logs for this application.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ApplicationLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the human-readable stage label dynamically from the active workflow organogram.
     */
    public function getStageLabel(): string
    {
        // Terminal or special statuses
        if ($this->status === 'license_issued') {
            return __('License Issued');
        }
        if ($this->status === 'approved') {
            return __('Approved (Pending Printing / Issuance)');
        }
        if ($this->status === 'waiting_for_license_fee') {
            return __('Payment Required (License Fee)');
        }
        if ($this->status === 'payment_pending') {
            return __('Payment Required (Service Fee)');
        }
        if ($this->status === 'returned') {
            return __('Returned to Applicant for Correction');
        }
        if (str_starts_with($this->status, 'rejected')) {
            return __('Application Rejected');
        }

        // Active workflow step
        $step = $this->currentStep;
        if (! $step && $this->current_step_id) {
            $step = WorkflowStep::find($this->current_step_id);
        }

        if (! $step) {
            /** @var WorkflowService $wfService */
            $wfService = app(WorkflowService::class);
            $step = $wfService->getCurrentStep($this);
        }

        if ($step) {
            $isBn = app()->getLocale() === 'bn';
            $toBnDigits = function ($number) {
                return str_replace(range(0, 9), ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'], (string) $number);
            };

            $stepName = __($step->step_name);

            // Parallel multi-role details if applicable
            if ($step->isMultiRole()) {
                $statusInfo = app(WorkflowService::class)->getStepApprovalStatus($this);
                if (! empty($statusInfo['pending_roles'])) {
                    $pendingLabels = array_map(function ($r) {
                        return __(app(WorkflowService::class)->formatRoleLabel($r));
                    }, $statusInfo['pending_roles']);
                    $extra = __('Awaiting :roles', ['roles' => implode(', ', $pendingLabels)]);
                    $stepName .= " ({$extra})";
                }
            }

            $wf = $step->workflowType ?: ($this->workflowType ?: app(WorkflowService::class)->resolveWorkflowType($this));
            $totalSteps = $wf ? $wf->activeSteps()->count() : 0;

            if ($totalSteps > 0 && $step->step_order) {
                $orderStr = $isBn ? $toBnDigits($step->step_order) : $step->step_order;
                $totalStr = $isBn ? $toBnDigits($totalSteps) : $totalSteps;
                $prefix = $isBn ? "ধাপ {$orderStr}/{$totalStr}" : "Step {$orderStr}/{$totalStr}";

                return "{$prefix}: {$stepName}";
            }

            return $stepName;
        }

        // Fallback actor role
        if ($this->current_actor_role) {
            $roleLabel = app(WorkflowService::class)->formatRoleLabel($this->current_actor_role);

            return __('Pending with :role', ['role' => __($roleLabel)]);
        }

        return ucfirst(str_replace('_', ' ', $this->status));
    }
}
