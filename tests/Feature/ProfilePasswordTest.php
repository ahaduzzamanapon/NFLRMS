<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed();
});

test('user can change password with correct current password', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();

    $response = $this->actingAs($user)
        ->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    $this->assertAuthenticatedAs($user);
});

test('password change fails when current password is incorrect', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();
    $oldPasswordHash = $user->password;

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors(['current_password']);

    $this->assertEquals($oldPasswordHash, $user->fresh()->password);
    $this->assertFalse(Hash::check('newpassword123', $user->fresh()->password));
});

test('password change fails when current password is missing', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => '',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors(['current_password']);
});

test('password change fails when password confirmation does not match', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors(['password']);
});

test('user can update profile without changing password', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();
    $oldPasswordHash = $user->password;

    $response = $this->actingAs($user)
        ->put(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $this->assertEquals('Updated Name', $user->fresh()->name);
    $this->assertEquals($oldPasswordHash, $user->fresh()->password);
});
