<?php

use App\Enums\Role;
use App\Models\User;

test('senior secretary user is redirected to senior secretary home dashboard on login', function () {
    $seniorSec = User::factory()->create(['role' => Role::SeniorSecretary]);

    $response = $this->actingAs($seniorSec)->get('/login');

    $response->assertRedirect(route('senior_secretary.dashboard'));
});

test('senior secretary home dashboard can be accessed by senior secretary', function () {
    $seniorSec = User::factory()->create(['role' => Role::SeniorSecretary]);

    $response = $this->actingAs($seniorSec)->get(route('senior_secretary.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Senior Secretary Dashboard');
    $response->assertSee('Total Licenses');
    $response->assertSee('Approved Licenses');
    $response->assertSee('Pending Licenses');
    $response->assertSee('Suspended Licenses');
    $response->assertSee('Total Citizens');
    $response->assertSee('Total Dealers');
    $response->assertSee('District-wise License Statistics');
    $response->assertSee('Thana-wise License Statistics');
});

test('non-senior secretary user cannot access senior secretary dashboard', function () {
    $citizen = User::factory()->create(['role' => Role::CitizenApplicant]);

    $this->actingAs($citizen)->get(route('senior_secretary.dashboard'))->assertStatus(403);
});

test('senior secretary can access reports and analytics page', function () {
    $seniorSec = User::factory()->create(['role' => Role::SeniorSecretary]);

    $response = $this->actingAs($seniorSec)->get(route('admin.reports'));

    $response->assertStatus(200);
    $response->assertSee('Reports & Analytics');
});
