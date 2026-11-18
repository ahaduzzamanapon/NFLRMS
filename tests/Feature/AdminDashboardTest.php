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
    $response->assertSee(__('Admin Home Dashboard'));
    $response->assertSee(__('Total Licenses'));
    $response->assertSee(__('Total Citizens'));
    $response->assertSee(__('Total Dealers'));
    $response->assertSee(__('District-wise License Statistics'));
    $response->assertSee(__('Thana-wise License Statistics'));
});

test('user management can be accessed at separate route by system admin', function () {
    $admin = User::where('role', Role::SystemAdmin)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    $response = $this->actingAs($admin)->get(route('admin.users'));

    $response->assertStatus(200);
    $response->assertSee(__('User Management'));
});

test('non-admin user cannot access admin home or user management', function () {
    $citizen = User::where('role', Role::CitizenApplicant)->first()
        ?? User::factory()->create(['role' => Role::CitizenApplicant]);

    $this->actingAs($citizen)->get(route('admin.dashboard'))->assertStatus(403);
    $this->actingAs($citizen)->get(route('admin.users'))->assertStatus(403);
});
