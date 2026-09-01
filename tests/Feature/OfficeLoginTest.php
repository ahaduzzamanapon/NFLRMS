<?php

use App\Enums\Role;
use App\Models\User;

test('welcome page footer contains office login link', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee(route('office.login'));
    $response->assertSee('Office Login');
});

test('office login page renders with correct heading and without sign up link', function () {
    $response = $this->get(route('office.login'));

    $response->assertStatus(200);
    $response->assertSee('Office Sign In');
    $response->assertDontSee('No account?');
    $response->assertDontSee('Sign up');
});

test('office user can log in through office login', function () {
    $dcUser = User::where('role', Role::DistrictCommissioner)->first()
        ?? User::factory()->create(['role' => Role::DistrictCommissioner]);

    $response = $this->post(route('office.login'), [
        'phone' => $dcUser->phone ?: $dcUser->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dc.dashboard'));
    $this->assertAuthenticatedAs($dcUser);
});

test('invalid credentials show correct credential error message', function () {
    $response = $this->post(route('office.login'), [
        'phone' => '01700000000',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors([
        'phone' => 'Please check your email and password and try again.',
    ]);
    $this->assertGuest();
});

test('citizen user cannot log in through office login and gets office-only message', function () {
    $citizen = User::where('role', Role::CitizenApplicant)->first()
        ?? User::factory()->create(['role' => Role::CitizenApplicant]);

    $response = $this->post(route('office.login'), [
        'phone' => $citizen->phone ?: $citizen->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'phone' => 'Only Office users can sign in from this page.',
    ]);
    $this->assertGuest();
});

test('dealer user cannot log in through office login and gets office-only message', function () {
    $dealer = User::where('role', Role::DealerApplicant)->first()
        ?? User::factory()->create(['role' => Role::DealerApplicant]);

    $response = $this->post(route('office.login'), [
        'phone' => $dealer->phone ?: $dealer->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'phone' => 'Only Office users can sign in from this page.',
    ]);
    $this->assertGuest();
});

test('empty input fields return small user-friendly validation messages', function () {
    $response = $this->post(route('office.login'), [
        'phone' => '',
        'password' => '',
    ]);

    $response->assertSessionHasErrors([
        'phone' => 'Please enter your mobile or email.',
        'password' => 'Please enter your password.',
    ]);
});

test('citizen user can still log in through standard login', function () {
    $citizen = User::where('role', Role::CitizenApplicant)->first()
        ?? User::factory()->create(['role' => Role::CitizenApplicant]);

    $response = $this->post(route('login'), [
        'phone' => $citizen->phone ?: $citizen->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('citizen.dashboard'));
    $this->assertAuthenticatedAs($citizen);
});
