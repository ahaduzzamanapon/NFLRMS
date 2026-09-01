<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed();
});

test('address tab validates and updates independently of personal info', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();

    $response = $this->actingAs($user)
        ->put(route('profile.update'), [
            'active_tab' => 'address',
            'present_address' => '123 Main St, Dhaka',
            'permanent_address' => '456 Village Rd, Rajshahi',
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success')
        ->assertSessionHasNoErrors();

    $freshUser = $user->fresh();
    $this->assertEquals('123 Main St, Dhaka', $freshUser->present_address);
    $this->assertEquals('456 Village Rd, Rajshahi', $freshUser->permanent_address);
});

test('education tab validates and updates independently', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();

    $response = $this->actingAs($user)
        ->put(route('profile.update'), [
            'active_tab' => 'education',
            'edu_qualification' => 'B.Sc. in Engineering',
            'occupation' => 'Software Engineer',
            'annual_income' => 600000,
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success')
        ->assertSessionHasNoErrors();

    $freshUser = $user->fresh();
    $this->assertEquals('B.Sc. in Engineering', $freshUser->edu_qualification);
    $this->assertEquals('Software Engineer', $freshUser->occupation);
    $this->assertEquals(600000, $freshUser->annual_income);
});

test('security tab validates password independently without requiring personal fields', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();

    $response = $this->actingAs($user)
        ->put(route('profile.update'), [
            'active_tab' => 'security',
            'current_password' => 'password',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success')
        ->assertSessionHasNoErrors();

    $this->assertTrue(Hash::check('newsecret123', $user->fresh()->password));
});

test('security tab fails validation when current password is invalid without affecting other tabs', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->put(route('profile.update'), [
            'active_tab' => 'security',
            'current_password' => 'wrongpass',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors(['current_password']);
});

test('personal tab validates personal fields strictly when active_tab is personal', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();

    $response = $this->actingAs($user)
        ->from(route('profile.edit'))
        ->put(route('profile.update'), [
            'active_tab' => 'personal',
            'name' => '',
            'name_bn' => 'পরীক্ষা',
            'email' => 'invalid-email',
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors(['name', 'email']);
});
