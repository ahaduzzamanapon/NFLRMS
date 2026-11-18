<?php

use App\Enums\Role;
use App\Models\User;

beforeEach(function () {
    $this->seed();
});

test('dealer apply page shows profile notice above Part 2 and Complete Profile button when incomplete', function () {
    $dealer = User::where('role', Role::DealerApplicant)->first();
    expect($dealer->isProfileComplete())->toBeFalse();

    $response = $this->actingAs($dealer)->get(route('dealer.apply'));

    $response->assertOk()
        ->assertSee('Pulled from your Profile')
        ->assertSee('Fields below are pulled from your')
        ->assertSee('Complete Profile')
        ->assertDontSee('Update Profile')
        ->assertSee(route('profile.edit'));
});

test('dealer profile is complete when only dealer apply profile fields (name, nid, phone, annual_income) are filled', function () {
    $dealer = User::where('role', Role::DealerApplicant)->first();

    // Fill only the 4 fields used in Dealer Apply (proprietor details)
    // Leave all citizen-specific fields (name_bn, father_name, mother_name, etc.) null
    $dealer->update([
        'name' => 'Dealer Proprietor',
        'nid' => '19801234567890123',
        'phone' => '01712345678',
        'annual_income' => 1500000,
        'name_bn' => null,
        'dob' => null,
        'father_name' => null,
        'mother_name' => null,
        'present_address' => null,
        'permanent_address' => null,
        'district_id' => null,
        'upazila_id' => null,
        'edu_qualification' => null,
        'occupation' => null,
        'employer_address' => null,
        'tin_number' => null,
    ]);

    $freshDealer = $dealer->fresh();
    expect($freshDealer->isProfileComplete())->toBeTrue();
    expect($freshDealer->profileMissingFields())->toBeEmpty();

    $response = $this->actingAs($freshDealer)->get(route('dealer.apply'));

    $response->assertOk()
        ->assertSee('Pulled from your Profile')
        ->assertSee('Update Profile')
        ->assertDontSee('Complete Profile')
        ->assertSee(route('profile.edit'));
});

test('dealer profile is incomplete if any dealer-required field is missing', function () {
    $dealer = User::where('role', Role::DealerApplicant)->first();

    $dealer->update([
        'name' => 'Dealer Proprietor',
        'nid' => '19801234567890123',
        'phone' => '01712345678',
        'annual_income' => null, // missing annual income
    ]);

    $freshDealer = $dealer->fresh();
    expect($freshDealer->isProfileComplete())->toBeFalse();
    expect($freshDealer->profileMissingFields())->toContain('annual_income');

    $response = $this->actingAs($freshDealer)->get(route('dealer.apply'));

    $response->assertOk()
        ->assertSee('Complete Profile')
        ->assertDontSee('Update Profile');
});

test('citizen profile completeness requirements remain unchanged', function () {
    $citizen = User::where('role', Role::CitizenApplicant)->first();

    // Giving a citizen only the 4 dealer fields must NOT make citizen profile complete
    $citizen->update([
        'name' => 'Citizen Applicant',
        'nid' => '19801234567890123',
        'phone' => '01712345678',
        'annual_income' => 1500000,
        'name_bn' => null,
    ]);

    expect($citizen->fresh()->isProfileComplete())->toBeFalse();
    expect($citizen->fresh()->profileMissingFields())->toContain('name_bn');
});
