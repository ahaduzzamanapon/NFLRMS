<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

test('profile photo can be uploaded, stored, and its photo url is generated correctly', function () {
    Storage::fake('public');

    $user = User::where('role', Role::CitizenApplicant)->first();
    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($user)
        ->put(route('profile.update'), [
            'active_tab' => 'personal',
            'name' => 'Citizen Applicant',
            'name_bn' => 'নাগরিক আবেদনকারী',
            'email' => $user->email,
            'phone' => '01712345678',
            'profile_photo' => $file,
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success')
        ->assertSessionHasNoErrors();

    $freshUser = $user->fresh();
    expect($freshUser->profile_photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($freshUser->profile_photo_path);
    expect($freshUser->photo_url)->toBe(asset('storage/'.$freshUser->profile_photo_path));
});

test('profile photo stored in public disk is accessible through public storage link', function () {
    $user = User::where('role', Role::CitizenApplicant)->first();
    $file = UploadedFile::fake()->image('real_test_avatar.jpg');

    $response = $this->actingAs($user)
        ->put(route('profile.update'), [
            'active_tab' => 'personal',
            'name' => 'Citizen Applicant',
            'name_bn' => 'নাগরিক আবেদনকারী',
            'email' => $user->email,
            'phone' => '01712345678',
            'profile_photo' => $file,
        ]);

    $response->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success')
        ->assertSessionHasNoErrors();

    $freshUser = $user->fresh();
    expect($freshUser->profile_photo_path)->not->toBeNull();

    // Check physical file exists in storage/app/public/profiles/...
    $actualPath = storage_path('app/public/'.$freshUser->profile_photo_path);
    expect(file_exists($actualPath))->toBeTrue();

    // Check accessible through public/storage link
    $publicLinkedPath = public_path('storage/'.$freshUser->profile_photo_path);
    expect(file_exists($publicLinkedPath))->toBeTrue();

    // Clean up created test file
    if (file_exists($actualPath)) {
        unlink($actualPath);
    }
});
