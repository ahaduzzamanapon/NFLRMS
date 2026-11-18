<?php

use App\Enums\Role;
use App\Models\Application;
use App\Models\User;
use App\Models\WorkflowType;
use Illuminate\Support\Facades\App;

test('locale switch route sets session and changes app language', function () {
    // Switch to Bengali
    $response = $this->get(route('lang.switch', 'bn'));
    $response->assertRedirect();
    $response->assertSessionHas('locale', 'bn');

    // Make next request with the session
    $pageResponse = $this->withSession(['locale' => 'bn'])->get(route('login'));
    $pageResponse->assertOk();
    $pageResponse->assertSee('lang="bn"', false);
    $pageResponse->assertSee('Kalpurush');
    $pageResponse->assertSee('স্বরাষ্ট্র মন্ত্রণালয়');

    // Switch to English
    $responseEn = $this->get(route('lang.switch', 'en'));
    $responseEn->assertRedirect();
    $responseEn->assertSessionHas('locale', 'en');

    $pageResponseEn = $this->withSession(['locale' => 'en'])->get(route('login'));
    $pageResponseEn->assertOk();
    $pageResponseEn->assertSee('lang="en"', false);
    $pageResponseEn->assertSee('Ministry of Home Affairs');
});

test('translations return correct strings in both languages', function () {
    App::setLocale('en');
    expect(__('Ministry of Home Affairs'))->toBe('Ministry of Home Affairs')
        ->and(__('System Administration'))->toBe('System Administration')
        ->and(__('My Applications'))->toBe('My Applications');

    App::setLocale('bn');
    expect(__('Ministry of Home Affairs'))->toBe('স্বরাষ্ট্র মন্ত্রণালয়')
        ->and(__('System Administration'))->toBe('সিস্টেম প্রশাসন')
        ->and(__('My Applications'))->toBe('আমার আবেদনসমূহ');
});

test('kalpurush font file exists in public fonts directory', function () {
    $fontPath = public_path('fonts/kalpurush.ttf');
    expect(file_exists($fontPath))->toBeTrue()
        ->and(filesize($fontPath))->toBeGreaterThan(100000);
});

test('authenticated user sees localized layout with kalpurush font in bangla', function () {
    $admin = User::factory()->create([
        'role' => Role::SystemAdmin,
        'name' => 'John Admin',
        'name_bn' => 'জন এডমিন',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['locale' => 'bn'])
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('lang="bn"', false);
    $response->assertSee('Kalpurush');
    $response->assertSee('সিস্টেম প্রশাসন');
    $response->assertSee('জন এডমিন');
});

test('application defaults to bangla locale for all users including admin and office', function () {
    $response = $this->get(route('login'));
    $response->assertOk();
    $response->assertSee('lang="bn"', false);
    $response->assertSee('Kalpurush');
    $response->assertSee('স্বরাষ্ট্র মন্ত্রণালয়');
});

test('welcome landing page renders in bangla by default with kalpurush', function () {
    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('lang="bn"', false);
    $response->assertSee('Kalpurush');
    $response->assertSee('জাতীয় আগ্নেয়াস্ত্র');
    $response->assertSee('বাংলাদেশ সরকার');
    $response->assertSee('স্বরাষ্ট্র মন্ত্রণালয়');
    $response->assertSee('এনএফএলআরএমএস . প্রডাকশন সিস্টেম');
    $response->assertSee('কেন এনএফএলআরএমএস');
});

test('welcome landing page renders in english when english locale is selected', function () {
    $response = $this->withSession(['locale' => 'en'])->get('/');
    $response->assertOk();
    $response->assertSee('lang="en"', false);
    $response->assertSee('National Firearms');
    $response->assertSee('Govt. of Bangladesh');
    $response->assertSee('Ministry of Home Affairs');
    $response->assertSee('Why NFLRMS');
});

test('application stage label is dynamically derived from workflow step', function () {
    $workflow = WorkflowType::create([
        'key' => 'test_workflow',
        'name' => 'Test Workflow',
        'is_active' => true,
    ]);

    $step1 = $workflow->steps()->create([
        'step_order' => 1,
        'step_name' => 'DC Front Desk — Intake & Verification',
        'role_key' => 'dc_front_desk',
        'role_name' => 'DC Front Desk',
        'can_approve' => true,
        'can_reject' => true,
        'can_return' => false,
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'role' => Role::CitizenApplicant,
    ]);

    $app = Application::create([
        'application_number' => 'APP-TEST-001',
        'user_id' => $user->id,
        'type' => 'new_license',
        'applicant_type' => 'citizen',
        'workflow_type_id' => $workflow->id,
        'current_step_id' => $step1->id,
        'current_actor_role' => 'dc_front_desk',
        'status' => 'submitted',
    ]);

    App::setLocale('en');
    expect($app->getStageLabel())->toContain('Step 1/1: DC Front Desk — Intake & Verification');

    App::setLocale('bn');
    expect($app->getStageLabel())->toContain('ডিসি ফ্রন্ট ডেস্ক — গ্রহণ ও প্রাথমিক যাচাই');
});
