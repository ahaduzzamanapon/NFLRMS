<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\WorkflowStep;
use App\Models\WorkflowType;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WorkflowOrganogramController extends Controller
{
    /** List all 4 workflow types */
    public function index()
    {
        $workflows = WorkflowType::withCount('steps')->orderBy('id')->get();

        return view('admin.workflow_organogram.index', compact('workflows'));
    }

    /** Show steps of one workflow */
    public function show(string $encryptedId)
    {
        $id = $this->decrypt($encryptedId);
        $workflow = WorkflowType::with('steps')->findOrFail($id);
        $allRoles = $this->availableRoles();

        return view('admin.workflow_organogram.show', compact('workflow', 'allRoles'));
    }

    /** Edit workflow metadata */
    public function edit(string $encryptedId)
    {
        $id = $this->decrypt($encryptedId);
        $workflow = WorkflowType::findOrFail($id);

        return view('admin.workflow_organogram.edit', compact('workflow'));
    }

    /** Update workflow metadata */
    public function update(Request $request, string $encryptedId)
    {
        $id = $this->decrypt($encryptedId);
        $workflow = WorkflowType::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $workflow->update($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.workflow_organogram.show', $encryptedId)
            ->with('success', 'ওয়ার্কফ্লো তথ্য আপডেট হয়েছে।');
    }

    /** Store a new step */
    public function storeStep(Request $request, string $encryptedId)
    {
        $id = $this->decrypt($encryptedId);
        $workflow = WorkflowType::findOrFail($id);

        $data = $request->validate([
            'role_key' => 'required|string|max:100',
            'role_name' => 'required|string|max:255',
            'step_name' => 'required|string|max:255',
            'can_approve' => 'boolean',
            'can_reject' => 'boolean',
            'can_return' => 'boolean',
        ]);

        $nextOrder = $workflow->steps()->max('step_order') + 1;

        $workflow->steps()->create($data + [
            'step_order' => $nextOrder,
            'can_approve' => $request->boolean('can_approve'),
            'can_reject' => $request->boolean('can_reject'),
            'can_return' => $request->boolean('can_return'),
            'is_active' => true,
        ]);

        return redirect()->route('admin.workflow_organogram.show', $encryptedId)
            ->with('success', 'নতুন ধাপ যোগ হয়েছে।');
    }

    /** Edit step form */
    public function editStep(string $encryptedWfId, string $encryptedStepId)
    {
        $wfId = $this->decrypt($encryptedWfId);
        $stepId = $this->decrypt($encryptedStepId);

        $workflow = WorkflowType::findOrFail($wfId);
        $step = WorkflowStep::where('workflow_type_id', $wfId)->findOrFail($stepId);
        $allRoles = $this->availableRoles();

        return view('admin.workflow_organogram.edit_step', compact('workflow', 'step', 'allRoles', 'encryptedWfId', 'encryptedStepId'));
    }

    /** Update step */
    public function updateStep(Request $request, string $encryptedWfId, string $encryptedStepId)
    {
        $wfId = $this->decrypt($encryptedWfId);
        $stepId = $this->decrypt($encryptedStepId);

        $step = WorkflowStep::where('workflow_type_id', $wfId)->findOrFail($stepId);

        $data = $request->validate([
            'role_key' => 'required|string|max:100',
            'role_name' => 'required|string|max:255',
            'step_name' => 'required|string|max:255',
            'can_approve' => 'boolean',
            'can_reject' => 'boolean',
            'can_return' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $step->update($data + [
            'can_approve' => $request->boolean('can_approve'),
            'can_reject' => $request->boolean('can_reject'),
            'can_return' => $request->boolean('can_return'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.workflow_organogram.show', $encryptedWfId)
            ->with('success', 'ধাপ আপডেট হয়েছে।');
    }

    /** Delete step */
    public function destroyStep(string $encryptedWfId, string $encryptedStepId)
    {
        $wfId = $this->decrypt($encryptedWfId);
        $stepId = $this->decrypt($encryptedStepId);

        $step = WorkflowStep::where('workflow_type_id', $wfId)->findOrFail($stepId);
        $step->delete();

        // Re-number remaining steps
        WorkflowStep::where('workflow_type_id', $wfId)
            ->orderBy('step_order')
            ->get()
            ->each(fn ($s, $i) => $s->update(['step_order' => $i + 1]));

        return redirect()->route('admin.workflow_organogram.show', $encryptedWfId)
            ->with('success', 'ধাপ মুছে ফেলা হয়েছে।');
    }

    /** Move step up */
    public function moveUp(string $encryptedWfId, string $encryptedStepId)
    {
        $wfId = $this->decrypt($encryptedWfId);
        $stepId = $this->decrypt($encryptedStepId);

        $step = WorkflowStep::where('workflow_type_id', $wfId)->findOrFail($stepId);
        $prev = WorkflowStep::where('workflow_type_id', $wfId)
            ->where('step_order', '<', $step->step_order)
            ->orderByDesc('step_order')
            ->first();

        if ($prev) {
            [$step->step_order, $prev->step_order] = [$prev->step_order, $step->step_order];
            $step->save();
            $prev->save();
        }

        return redirect()->route('admin.workflow_organogram.show', $encryptedWfId);
    }

    /** Move step down */
    public function moveDown(string $encryptedWfId, string $encryptedStepId)
    {
        $wfId = $this->decrypt($encryptedWfId);
        $stepId = $this->decrypt($encryptedStepId);

        $step = WorkflowStep::where('workflow_type_id', $wfId)->findOrFail($stepId);
        $next = WorkflowStep::where('workflow_type_id', $wfId)
            ->where('step_order', '>', $step->step_order)
            ->orderBy('step_order')
            ->first();

        if ($next) {
            [$step->step_order, $next->step_order] = [$next->step_order, $step->step_order];
            $step->save();
            $next->save();
        }

        return redirect()->route('admin.workflow_organogram.show', $encryptedWfId);
    }

    /** Available roles: system + custom */
    private function availableRoles(): array
    {
        $systemRoles = [
            'dc_front_desk' => 'DC Front Desk',
            'dc_jm_branch' => 'DC JM Branch',
            'district_commissioner' => 'District Commissioner',
            'police_officer' => 'Police Officer',
            'special_branch' => 'Special Branch (SB)',
            'nsi_officer' => 'NSI Officer',
            'dgfi_officer' => 'DGFI Officer',
            'moha_desk' => 'MoHA Desk',
            'joint_secretary' => 'Joint Secretary',
            'senior_secretary' => 'Senior Secretary',
            'national_screening_committee' => 'National Screening Committee',
            'executive' => 'Executive',
            'system_admin' => 'System Administrator',
        ];

        $customRoles = json_decode(Setting::get('custom_roles', '{}'), true) ?: [];

        return array_merge($systemRoles, $customRoles);
    }

    private function decrypt(string $encryptedId): int
    {
        try {
            return (int) Crypt::decryptString($encryptedId);
        } catch (DecryptException) {
            abort(404);
        }
    }
}
