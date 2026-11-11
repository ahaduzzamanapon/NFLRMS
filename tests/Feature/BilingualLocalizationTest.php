<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\App;

test('locale switch route sets session and changes app language', function () {
    // Switch to Bengali
    $response = $this->get(route('lang.switch', 'bn'));
    $response->assertRedirect();
    $response->assertSessionHas('locale', 'bn');

    // Make next request with the session
    $pageResponse = $this->withSession(['locale' => 'bn'])->get(route('login'));
    $pageResponse->assertOk();
    $pageResponse->assertSee('lang="bn"', false);
    $pageResponse->assertSee('Kalpurush');
    $pageResponse->assertSee('স্বরাষ্ট্র মন্ত্রণালয়');

    // Switch to English
    $responseEn = $this->get(route('lang.switch', 'en'));
    $responseEn->assertRedirect();
    $responseEn->assertSessionHas('locale', 'en');

    $pageResponseEn = $this->withSession(['locale' => 'en'])->get(route('login'));
    $pageResponseEn->assertOk();
    $pageResponseEn->assertSee('lang="en"', false);
    $pageResponseEn->assertSee('Ministry of Home Affairs');
});

test('translations return correct strings in both languages', function () {
    App::setLocale('en');
    expect(__('Ministry of Home Affairs'))->toBe('Ministry of Home Affairs')
        ->and(__('System Administration'))->toBe('System Administration')
        ->and(__('My Applications'))->toBe('My Applications');

    App::setLocale('bn');
    expect(__('Ministry of Home Affairs'))->toBe('স্বরাষ্ট্র মন্ত্রণালয়')
        ->and(__('System Administration'))->toBe('সিস্টেম প্রশাসন')
        ->and(__('My Applications'))->toBe('আমার আবেদনসমূহ');
});

test('kalpurush font file exists in public fonts directory', function () {
    $fontPath = public_path('fonts/kalpurush.ttf');
    expect(file_exists($fontPath))->toBeTrue()
        ->and(filesize($fontPath))->toBeGreaterThan(100000);
});

test('authenticated user sees localized layout with kalpurush font in bangla', function () {
    $admin = User::factory()->create([
        'role' => Role::SystemAdmin,
        'name' => 'John Admin',
        'name_bn' => 'জন এডমিন',
    ]);

    $response = $this->actingAs($admin)
        ->withSession(['locale' => 'bn'])
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('lang="bn"', false);
    $response->assertSee('Kalpurush');
    $response->assertSee('সিস্টেম প্রশাসন');
    $response->assertSee('জন এডমিন');
});
