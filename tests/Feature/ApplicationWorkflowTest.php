<?php

use App\Enums\Role;
use App\Models\Application;
use App\Models\District;
use App\Models\License;
use App\Models\Upazila;
use App\Models\User;
use App\Models\Vetting;

beforeEach(function () {
    $this->seed();
});

test('it validates authentication and dashboard accessibility for roles', function () {
    // 1. Citizen
    $citizen = User::where('role', Role::CitizenApplicant)->first();
    $this->actingAs($citizen)
        ->get(route('citizen.dashboard'))
        ->assertOk()
        ->assertSee('Citizen Applicant');

    // 2. Dealer
    $dealer = User::where('role', Role::DealerApplicant)->first();
    $this->actingAs($dealer)
        ->get(route('dealer.dashboard'))
        ->assertOk()
        ->assertSee('Dealer Portal');

    // 3. Front Desk
    $frontDesk = User::where('role', Role::DcFrontDesk)->first();
    $this->actingAs($frontDesk)
        ->get(route('front_desk.dashboard'))
        ->assertOk();

    // 4. JM Branch
    $jmBranch = User::where('role', Role::DcJmBranch)->first();
    $this->actingAs($jmBranch)
        ->get(route('jm_branch.dashboard'))
        ->assertOk();

    // 5. DC
    $dc = User::where('role', Role::DistrictCommissioner)->first();
    $this->actingAs($dc)
        ->get(route('dc.dashboard'))
        ->assertOk();

    // 6. Vetting (Police)
    $police = User::where('role', Role::PoliceOfficer)->first();
    $this->actingAs($police)
        ->get(route('vetting.dashboard'))
        ->assertOk();

    // 7. MoHA Desk
    $mohaDesk = User::where('role', Role::MohaDesk)->first();
    $this->actingAs($mohaDesk)
        ->get(route('moha.dashboard'))
        ->assertOk();
});

test('citizen can submit a new license application', function () {
    $citizen = User::where('role', Role::CitizenApplicant)->first();
    $dhaka = District::where('name', 'Dhaka')->first();
    $upazila = Upazila::where('district_id', $dhaka->id)->first();

    $response = $this->actingAs($citizen)
        ->post(route('citizen.apply'), [
            'nid' => '1991234567890',
            'dob' => '1990-01-01',
            'father_name' => 'Md. John Doe',
            'present_address' => 'Dhaka',
            'permanent_address' => 'Dhaka',
            'annual_income' => '2500000',
            'weapon_type' => 'Shotgun',
            'bore' => '12 Gauge',
            'purpose' => 'Self defense',
            'district_id' => $dhaka->id,
            'upazila_id' => $upazila->id,
        ]);

    $app = Application::where('user_id', $citizen->id)->latest()->first();

    $response->assertRedirect(route('payment.initiate', ['application' => $app->id, 'type' => 'service_fee']));

    $this->assertDatabaseHas('applications', [
        'user_id' => $citizen->id,
        'type' => 'new',
        'status' => 'payment_pending',
        'current_actor_role' => Role::CitizenApplicant->value,
    ]);
});

test('dealer can submit Form K dealing license application', function () {
    $dealer = User::where('role', Role::DealerApplicant)->first();
    $dhaka = District::where('name', 'Dhaka')->first();

    $response = $this->actingAs($dealer)
        ->post(route('dealer.apply.store'), [
            'firm_name' => 'Al-Haj Arms',
            'trade_license' => 'TL-5544',
            'business_address' => 'Naya Paltan, Dhaka',
            'district_id' => $dhaka->id,
            'license_class' => 'A',
            'nid' => '1980555666777',
            'mobile' => '01712345678',
            'annual_income' => '12000000',
            'categories' => ['Pistol', 'Revolver', 'Shotgun'],
        ]);

    $app = Application::where('user_id', $dealer->id)->latest()->first();

    $response->assertRedirect(route('payment.initiate', ['application' => $app->id, 'type' => 'service_fee']));

    $this->assertDatabaseHas('applications', [
        'user_id' => $dealer->id,
        'type' => 'new_dealing_license',
        'status' => 'payment_pending',
        'current_actor_role' => Role::DealerApplicant->value,
    ]);
});

test('front desk can receive and forward application', function () {
    $frontDesk = User::where('role', Role::DcFrontDesk)->first();
    $app = Application::where('status', 'submitted')->first();

    $response = $this->actingAs($frontDesk)
        ->post(route('front_desk.action', $app->id), [
            'action' => 'forward',
            'remarks' => 'All documents verified and found authentic.',
        ]);

    $response->assertRedirect(route('front_desk.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'received',
        'current_actor_role' => Role::DcJmBranch->value,
    ]);
});

test('jm branch can dispatch security vetting', function () {
    $jmBranch = User::where('role', Role::DcJmBranch)->first();
    $app = Application::where('status', 'received')->first();

    $response = $this->actingAs($jmBranch)
        ->post(route('jm_branch.action', $app->id), [
            'action' => 'trigger_vetting',
            'remarks' => 'Dispatch to Police, SB, NSI, and DGFI for security vetting.',
        ]);

    $response->assertRedirect(route('jm_branch.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'pending_vetting',
    ]);

    $this->assertDatabaseHas('vettings', [
        'application_id' => $app->id,
        'agency' => 'police',
        'status' => 'pending',
    ]);
});

test('agencies can submit vetting clearances and autocomplete workflow trigger', function () {
    $policeUser = User::where('role', Role::PoliceOfficer)->first();
    $sbUser = User::where('role', Role::SpecialBranch)->first();
    $nsiUser = User::where('role', Role::NsiOfficer)->first();
    $dgfiUser = User::where('role', Role::DgfiOfficer)->first();

    // Find the application with pending vetting
    $app = Application::where('status', 'pending_vetting')->first();

    // Clear out preexisting mock vetting logs to ensure fresh run
    $app->vettings()->delete();

    // Dispatch fresh vetting entries
    $agencies = [
        'police' => $policeUser,
        'sb' => $sbUser,
        'nsi' => $nsiUser,
        'dgfi' => $dgfiUser,
    ];

    foreach ($agencies as $agency => $user) {
        Vetting::create([
            'application_id' => $app->id,
            'agency' => $agency,
            'status' => 'pending',
        ]);
    }

    // Submit clearances one by one
    foreach ($agencies as $agency => $user) {
        $vetting = Vetting::where('application_id', $app->id)->where('agency', $agency)->first();

        $response = $this->actingAs($user)
            ->post(route('vetting.submit', $vetting->id), [
                'status' => 'cleared',
                'remarks' => 'Verified. No adverse record.',
            ]);

        $response->assertRedirect(route('vetting.dashboard'));
    }

    // Since all 4 agencies cleared, application status should auto-update to vetted_cleared
    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'vetted_cleared',
        'current_actor_role' => Role::DcJmBranch->value,
    ]);
});

test('jm branch can recommend to DC and DC can approve', function () {
    $jmBranch = User::where('role', Role::DcJmBranch)->first();
    $dc = User::where('role', Role::DistrictCommissioner)->first();

    $app = Application::where('status', 'pending_vetting')->first();
    $app->update([
        'status' => 'vetted_cleared',
        'current_actor_role' => Role::DcJmBranch->value,
    ]);

    // JM Branch forwards to DC
    $response = $this->actingAs($jmBranch)
        ->post(route('jm_branch.action', $app->id), [
            'action' => 'forward_dc',
            'remarks' => 'Highly recommended. Background checks are all green.',
        ]);

    $response->assertRedirect(route('jm_branch.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'recommended',
        'current_actor_role' => Role::DistrictCommissioner->value,
    ]);

    // DC approves
    $response2 = $this->actingAs($dc)
        ->post(route('dc.action', $app->id), [
            'action' => 'approve',
            'remarks' => 'Approved and license issued.',
        ]);

    $response2->assertRedirect(route('dc.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'waiting_for_license_fee',
    ]);

    $this->assertDatabaseMissing('licenses', [
        'application_id' => $app->id,
    ]);

    // Simulate PayStation callback for license fee
    $invoice = $app->application_number.'_LF';
    $this->get(route('payment.callback', [
        'status' => 'Successful',
        'invoice_number' => $invoice,
        'trx_id' => 'TEST_TRX_LF',
    ]))->assertRedirect(route('citizen.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'approved',
        'license_fee_paid' => true,
    ]);

    $this->assertDatabaseHas('licenses', [
        'application_id' => $app->id,
        'user_id' => $app->user_id,
        'status' => 'active',
    ]);
});

test('dc can forward to MoHA and MoHA screening committee approvals complete workflow', function () {
    $dc = User::where('role', Role::DistrictCommissioner)->first();
    $mohaDesk = User::where('role', Role::MohaDesk)->first();
    $js = User::where('role', Role::JointSecretary)->first();
    $nsc = User::where('role', Role::NationalScreeningCommittee)->first();
    $ss = User::where('role', Role::SeniorSecretary)->first();

    $app = Application::where('status', 'pending_vetting')->first();
    $app->update([
        'status' => 'recommended',
        'current_actor_role' => Role::DistrictCommissioner->value,
    ]);

    // DC forwards to MoHA
    $this->actingAs($dc)
        ->post(route('dc.action', $app->id), [
            'action' => 'forward_moha',
            'remarks' => 'Forwarded for Ministry screening.',
        ])
        ->assertRedirect(route('dc.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'referred_moha',
        'current_actor_role' => Role::MohaDesk->value,
    ]);

    // MoHA Desk -> Joint Secretary
    $this->actingAs($mohaDesk)
        ->post(route('moha.action', $app->id), [
            'action' => 'forward',
            'remarks' => 'MoHA Desk verified.',
        ])
        ->assertRedirect(route('moha.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'moha_processing',
        'current_actor_role' => Role::JointSecretary->value,
    ]);

    // Joint Secretary -> NSC
    $this->actingAs($js)
        ->post(route('moha.action', $app->id), [
            'action' => 'forward',
            'remarks' => 'Joint Secretary reviewed.',
        ])
        ->assertRedirect(route('moha.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'pending_screening',
        'current_actor_role' => Role::NationalScreeningCommittee->value,
    ]);

    // NSC -> Senior Secretary
    $this->actingAs($nsc)
        ->post(route('moha.action', $app->id), [
            'action' => 'forward',
            'remarks' => 'National Screening Committee recommended.',
        ])
        ->assertRedirect(route('moha.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'screened',
        'current_actor_role' => Role::SeniorSecretary->value,
    ]);

    // Senior Secretary approves
    $this->actingAs($ss)
        ->post(route('moha.action', $app->id), [
            'action' => 'approve',
            'remarks' => 'Final approval granted by Senior Secretary.',
        ])
        ->assertRedirect(route('moha.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'waiting_for_license_fee',
    ]);

    $this->assertDatabaseMissing('licenses', [
        'application_id' => $app->id,
    ]);

    // Simulate PayStation callback for license fee
    $invoice = $app->application_number.'_LF';
    $this->get(route('payment.callback', [
        'status' => 'Successful',
        'invoice_number' => $invoice,
        'trx_id' => 'TEST_TRX_LF_MOHA',
    ]))->assertRedirect(route('citizen.dashboard'));

    $this->assertDatabaseHas('applications', [
        'id' => $app->id,
        'status' => 'approved',
        'license_fee_paid' => true,
    ]);

    $this->assertDatabaseHas('licenses', [
        'application_id' => $app->id,
        'status' => 'active',
    ]);
});

test('public verify page can look up licenses', function () {
    $license = License::first();

    $this->get(route('verify', ['license_number' => $license->license_number]))
        ->assertOk()
        ->assertSee($license->license_number);
});
