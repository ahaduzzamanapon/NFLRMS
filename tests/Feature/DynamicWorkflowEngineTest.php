<?php

use App\Enums\Role;
use App\Models\Application;
use App\Models\User;
use App\Models\WorkflowStep;
use App\Models\WorkflowType;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Crypt;

beforeEach(function () {
    $this->seed();
});

test('application attaches to workflow and advances through sequential and parallel steps', function () {
    $workflowService = app(WorkflowService::class);

    // Create a custom test workflow with 3 steps: Single -> Parallel (2 roles) -> Final Single
    $workflow = WorkflowType::create([
        'key' => 'test_workflow',
        'name' => 'Test Firearms Approval',
        'is_active' => true,
    ]);

    $step1 = WorkflowStep::create([
        'workflow_type_id' => $workflow->id,
        'step_order' => 1,
        'role_key' => Role::DcFrontDesk->value,
        'role_keys' => [Role::DcFrontDesk->value],
        'role_name' => 'Front Desk',
        'step_name' => 'Front Desk Intake',
        'can_approve' => true,
        'can_reject' => true,
        'can_return' => false,
        'is_active' => true,
    ]);

    $step2Parallel = WorkflowStep::create([
        'workflow_type_id' => $workflow->id,
        'step_order' => 2,
        'role_key' => Role::PoliceOfficer->value,
        'role_keys' => [Role::PoliceOfficer->value, Role::SpecialBranch->value],
        'role_name' => 'Police & Special Branch',
        'step_name' => 'Joint Security Clearance',
        'approval_type' => 'all',
        'can_approve' => true,
        'can_reject' => true,
        'can_return' => true,
        'is_active' => true,
    ]);

    $step3Final = WorkflowStep::create([
        'workflow_type_id' => $workflow->id,
        'step_order' => 3,
        'role_key' => Role::DistrictCommissioner->value,
        'role_keys' => [Role::DistrictCommissioner->value],
        'role_name' => 'District Commissioner',
        'step_name' => 'DC Final Approval',
        'can_approve' => true,
        'can_reject' => true,
        'can_return' => true,
        'is_active' => true,
    ]);

    // Create a citizen application linked to this workflow
    $citizen = User::where('role', Role::CitizenApplicant)->first();
    $frontDeskUser = User::where('role', Role::DcFrontDesk)->first();
    $policeUser = User::where('role', Role::PoliceOfficer)->first();
    $sbUser = User::where('role', Role::SpecialBranch)->first();
    $dcUser = User::where('role', Role::DistrictCommissioner)->first();

    $app = Application::create([
        'application_number' => 'TEST-WF-001',
        'user_id' => $citizen->id,
        'type' => 'new',
        'applicant_type' => 'citizen',
        'status' => 'submitted',
        'workflow_type_id' => $workflow->id,
        'current_step_id' => $step1->id,
        'current_actor_role' => Role::DcFrontDesk->value,
        'applicant_details' => ['name' => $citizen->name],
        'firearm_details' => ['weapon_type' => 'Shotgun'],
    ]);

    // 1. Verify Step 1 state
    expect($workflowService->canUserAct($app, $frontDeskUser))->toBeTrue();
    expect($workflowService->canUserAct($app, $policeUser))->toBeFalse();

    // 2. Front Desk Approves -> moves to Step 2 (Parallel)
    $res1 = $workflowService->processAction($app, $frontDeskUser, 'approve', 'Initial intake verified');
    expect($res1['success'])->toBeTrue();
    expect($res1['advance'])->toBeTrue();

    $app->refresh();
    expect($app->current_step_id)->toBe($step2Parallel->id);

    // 3. In Step 2 (Parallel): Both Police and SB can act
    expect($workflowService->canUserAct($app, $policeUser))->toBeTrue();
    expect($workflowService->canUserAct($app, $sbUser))->toBeTrue();
    expect($workflowService->canUserAct($app, $dcUser))->toBeFalse();

    // Police Officer approves first
    $res2 = $workflowService->processAction($app, $policeUser, 'approve', 'Police clear');
    expect($res2['success'])->toBeTrue();
    expect($res2['advance'])->toBeFalse(); // Not advancing yet because SB hasn't approved

    $app->refresh();
    expect($app->current_step_id)->toBe($step2Parallel->id); // Still on step 2
    expect($app->step_approvals)->toHaveKey(Role::PoliceOfficer->value);
    expect($workflowService->canUserAct($app, $policeUser))->toBeFalse(); // Cannot approve twice
    expect($workflowService->canUserAct($app, $sbUser))->toBeTrue(); // SB can still act

    // SB Officer approves
    $res3 = $workflowService->processAction($app, $sbUser, 'approve', 'SB clear');
    expect($res3['success'])->toBeTrue();
    expect($res3['advance'])->toBeTrue(); // All required roles approved, advances to step 3!

    $app->refresh();
    expect($app->current_step_id)->toBe($step3Final->id);
    expect($app->current_actor_role)->toBe(Role::DistrictCommissioner->value);
    expect($app->step_approvals)->toBeEmpty(); // Reset for step 3

    // 4. DC Approves -> Final approval!
    expect($workflowService->canUserAct($app, $dcUser))->toBeTrue();
    $res4 = $workflowService->processAction($app, $dcUser, 'approve', 'Final clearance approved');
    expect($res4['success'])->toBeTrue();
    expect($res4['advance'])->toBeTrue();

    $app->refresh();
    expect($app->status)->toBe('waiting_for_license_fee');
    expect($app->current_step_id)->toBeNull();
});

test('admin can create a multi-role parallel step in workflow organogram', function () {
    $admin = User::where('role', Role::SystemAdmin)->first();
    $workflow = WorkflowType::firstOrCreate(['key' => 'citizen_new'], [
        'name' => 'Citizen New Licence Application',
        'is_active' => true,
    ]);

    $encWfId = Crypt::encryptString($workflow->id);

    $response = $this->actingAs($admin)->post(route('admin.workflow_organogram.steps.store', $encWfId), [
        'step_name' => 'Joint Security Review',
        'role_keys' => ['police_officer', 'special_branch', 'nsi_officer', 'dgfi_officer'],
        'approval_type' => 'all',
        'can_approve' => 1,
        'can_reject' => 1,
        'can_return' => 0,
    ]);

    $response->assertRedirect(route('admin.workflow_organogram.show', $encWfId));

    $step = WorkflowStep::where('workflow_type_id', $workflow->id)
        ->where('step_name', 'Joint Security Review')
        ->first();

    expect($step)->not->toBeNull();
    expect($step->isMultiRole())->toBeTrue();
    expect($step->getAllRoleKeys())->toBe(['police_officer', 'special_branch', 'nsi_officer', 'dgfi_officer']);
});

test('officer can submit workflow action via unified route', function () {
    $frontDesk = User::where('role', Role::DcFrontDesk)->first();
    $citizen = User::where('role', Role::CitizenApplicant)->first();

    $workflow = WorkflowType::firstOrCreate(['key' => 'citizen_new'], [
        'name' => 'Citizen New Licence Application',
        'is_active' => true,
    ]);

    $step1 = $workflow->activeSteps()->first() ?? WorkflowStep::create([
        'workflow_type_id' => $workflow->id,
        'step_order' => 1,
        'role_key' => Role::DcFrontDesk->value,
        'role_name' => 'DC Front Desk',
        'step_name' => 'DC Front Desk — Intake & Verification',
        'can_approve' => true,
        'can_reject' => true,
        'can_return' => false,
        'is_active' => true,
    ]);

    $app = Application::create([
        'application_number' => 'UNIFIED-ACT-001',
        'user_id' => $citizen->id,
        'type' => 'new',
        'applicant_type' => 'citizen',
        'status' => 'submitted',
        'workflow_type_id' => $workflow->id,
        'current_step_id' => $step1->id,
        'current_actor_role' => Role::DcFrontDesk->value,
        'applicant_details' => ['name' => $citizen->name],
        'firearm_details' => ['weapon_type' => 'Shotgun'],
    ]);

    $response = $this->actingAs($frontDesk)
        ->post(route('workflow.action', Crypt::encryptString($app->id)), [
            'action' => 'approve',
            'remarks' => 'Approved via unified workflow route.',
        ]);

    $response->assertSessionHas('success');

    $app->refresh();
    // Step should have advanced or recorded approval
    expect($app->status)->not->toBe('submitted');
});
