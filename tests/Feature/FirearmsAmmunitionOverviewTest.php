<?php

use App\Enums\Role;
use App\Http\Controllers\OverviewController;
use App\Models\User;

test('admin dashboard displays firearms and ammunition total cards', function () {
    $admin = User::where('role', Role::SystemAdmin->value)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Total Firearms');
    $response->assertSee('Total Ammunition');
    $response->assertSee('120');
    $response->assertSee('5,000');
});

test('senior secretary dashboard displays firearms and ammunition total cards', function () {
    $seniorSec = User::where('role', Role::SeniorSecretary->value)->first()
        ?? User::factory()->create(['role' => Role::SeniorSecretary]);

    $response = $this->actingAs($seniorSec)->get(route('senior_secretary.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Total Firearms');
    $response->assertSee('Total Ammunition');
    $response->assertSee('120');
    $response->assertSee('5,000');
});

test('admin can access firearms and ammunition overview list pages', function () {
    $admin = User::where('role', Role::SystemAdmin->value)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    $firearmsResponse = $this->actingAs($admin)->get(route('overview.firearms'));
    $firearmsResponse->assertStatus(200);
    $firearmsResponse->assertSee('Firearms Overview List');
    $firearmsResponse->assertSee('Citizen');
    $firearmsResponse->assertSee('Dealer');

    $ammoResponse = $this->actingAs($admin)->get(route('overview.ammunition'));
    $ammoResponse->assertStatus(200);
    $ammoResponse->assertSee('Ammunition Overview List');
    $ammoResponse->assertSee('Citizen');
    $ammoResponse->assertSee('Dealer');
});

test('senior secretary can access firearms and ammunition overview list pages', function () {
    $seniorSec = User::where('role', Role::SeniorSecretary->value)->first()
        ?? User::factory()->create(['role' => Role::SeniorSecretary]);

    $firearmsResponse = $this->actingAs($seniorSec)->get(route('overview.firearms'));
    $firearmsResponse->assertStatus(200);

    $ammoResponse = $this->actingAs($seniorSec)->get(route('overview.ammunition'));
    $ammoResponse->assertStatus(200);
});

test('unauthorized roles cannot access firearms or ammunition overview list pages', function () {
    $citizen = User::where('role', Role::CitizenApplicant->value)->first()
        ?? User::factory()->create(['role' => Role::CitizenApplicant]);

    $this->actingAs($citizen)->get(route('overview.firearms'))->assertStatus(403);
    $this->actingAs($citizen)->get(route('overview.ammunition'))->assertStatus(403);
});

test('dummy data total quantities match overview controller data sums', function () {
    $firearmsData = OverviewController::getFirearmsData();
    $ammoData = OverviewController::getAmmunitionData();

    $firearmsSum = array_sum(array_column($firearmsData, 'quantity'));
    $ammoSum = array_sum(array_column($ammoData, 'quantity'));

    expect($firearmsSum)->toBe(120);
    expect($ammoSum)->toBe(5000);
});
