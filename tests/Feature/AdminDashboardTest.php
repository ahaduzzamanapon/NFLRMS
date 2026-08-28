<?php

use App\Enums\Role;
use App\Models\User;

test('admin login redirects to admin home dashboard', function () {
    $admin = User::where('role', Role::SystemAdmin)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    $response = $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
});

test('admin home dashboard can be accessed by system admin', function () {
    $admin = User::where('role', Role::SystemAdmin)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Admin Home Dashboard');
    $response->assertSee('Total Licenses');
    $response->assertSee('Total Citizens');
    $response->assertSee('Total Dealers');
    $response->assertSee('District-wise License Statistics');
    $response->assertSee('Thana-wise License Statistics');
});

test('user management can be accessed at separate route by system admin', function () {
    $admin = User::where('role', Role::SystemAdmin)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    $response = $this->actingAs($admin)->get(route('admin.users'));

    $response->assertStatus(200);
    $response->assertSee('User Management');
});

test('non-admin user cannot access admin home or user management', function () {
    $citizen = User::where('role', Role::CitizenApplicant)->first()
        ?? User::factory()->create(['role' => Role::CitizenApplicant]);

    $this->actingAs($citizen)->get(route('admin.dashboard'))->assertStatus(403);
    $this->actingAs($citizen)->get(route('admin.users'))->assertStatus(403);
});
