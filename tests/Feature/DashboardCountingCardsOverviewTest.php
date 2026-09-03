<?php

use App\Enums\Role;
use App\Http\Controllers\OverviewController;
use App\Models\User;

test('admin dashboard cards link to their respective list pages', function () {
    $admin = User::where('role', Role::SystemAdmin->value)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertSee(route('overview.licenses'));
    $response->assertSee(route('overview.licenses.approved'));
    $response->assertSee(route('overview.licenses.pending'));
    $response->assertSee(route('overview.licenses.suspended'));
    $response->assertSee(route('overview.citizens'));
    $response->assertSee(route('overview.dealers'));
    $response->assertSee(route('overview.firearms'));
    $response->assertSee(route('overview.ammunition'));
});

test('senior secretary dashboard cards link to their respective list pages', function () {
    $seniorSec = User::where('role', Role::SeniorSecretary->value)->first()
        ?? User::factory()->create(['role' => Role::SeniorSecretary]);

    $response = $this->actingAs($seniorSec)->get(route('senior_secretary.dashboard'));

    $response->assertStatus(200);
    $response->assertSee(route('overview.licenses'));
    $response->assertSee(route('overview.licenses.approved'));
    $response->assertSee(route('overview.licenses.pending'));
    $response->assertSee(route('overview.licenses.suspended'));
    $response->assertSee(route('overview.citizens'));
    $response->assertSee(route('overview.dealers'));
    $response->assertSee(route('overview.firearms'));
    $response->assertSee(route('overview.ammunition'));
});

test('admin can access all 6 new overview list pages with pagination and data', function () {
    $admin = User::where('role', Role::SystemAdmin->value)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    // 1. Total Licenses
    $res1 = $this->actingAs($admin)->get(route('overview.licenses'));
    $res1->assertStatus(200);
    $res1->assertSee('Total Licenses Overview');
    $res1->assertSee('BD-HND-DHK-004521');
    $res1->assertSee('Approved');

    // 2. Approved Licenses
    $res2 = $this->actingAs($admin)->get(route('overview.licenses.approved'));
    $res2->assertStatus(200);
    $res2->assertSee('Approved Licenses List');
    $res2->assertSee('Approved');

    // 3. Pending Licenses
    $res3 = $this->actingAs($admin)->get(route('overview.licenses.pending'));
    $res3->assertStatus(200);
    $res3->assertSee('Pending Licenses List');
    $res3->assertSee('Pending');

    // 4. Suspended Licenses
    $res4 = $this->actingAs($admin)->get(route('overview.licenses.suspended'));
    $res4->assertStatus(200);
    $res4->assertSee('Suspended Licenses List');
    $res4->assertSee('Suspended');

    // 5. Total Citizens
    $res5 = $this->actingAs($admin)->get(route('overview.citizens'));
    $res5->assertStatus(200);
    $res5->assertSee('Citizen Licensees Overview');
    $res5->assertSee('Brig. Gen. (Retd.) Tariq Mahmud');

    // 6. Total Dealers
    $res6 = $this->actingAs($admin)->get(route('overview.dealers'));
    $res6->assertStatus(200);
    $res6->assertSee('Registered Arms Dealers Overview');
    $res6->assertSeeText('Dhaka Arms & Co.');
});

test('senior secretary can access all 6 new overview list pages', function () {
    $seniorSec = User::where('role', Role::SeniorSecretary->value)->first()
        ?? User::factory()->create(['role' => Role::SeniorSecretary]);

    $this->actingAs($seniorSec)->get(route('overview.licenses'))->assertStatus(200);
    $this->actingAs($seniorSec)->get(route('overview.licenses.approved'))->assertStatus(200);
    $this->actingAs($seniorSec)->get(route('overview.licenses.pending'))->assertStatus(200);
    $this->actingAs($seniorSec)->get(route('overview.licenses.suspended'))->assertStatus(200);
    $this->actingAs($seniorSec)->get(route('overview.citizens'))->assertStatus(200);
    $this->actingAs($seniorSec)->get(route('overview.dealers'))->assertStatus(200);
});

test('unauthorized users cannot access new overview list pages', function () {
    $citizen = User::where('role', Role::CitizenApplicant->value)->first()
        ?? User::factory()->create(['role' => Role::CitizenApplicant]);

    $this->actingAs($citizen)->get(route('overview.licenses'))->assertStatus(403);
    $this->actingAs($citizen)->get(route('overview.licenses.approved'))->assertStatus(403);
    $this->actingAs($citizen)->get(route('overview.licenses.pending'))->assertStatus(403);
    $this->actingAs($citizen)->get(route('overview.licenses.suspended'))->assertStatus(403);
    $this->actingAs($citizen)->get(route('overview.citizens'))->assertStatus(403);
    $this->actingAs($citizen)->get(route('overview.dealers'))->assertStatus(403);
});

test('overview datasets match the dashboard counting card metrics', function () {
    $licenses = OverviewController::getLicensesData();
    $citizens = OverviewController::getCitizensData();
    $dealers = OverviewController::getDealersData();

    expect(count($licenses))->toBe(14);
    expect(count(array_filter($licenses, fn ($l) => $l['status'] === 'Approved')))->toBe(8);
    expect(count(array_filter($licenses, fn ($l) => $l['status'] === 'Pending')))->toBe(4);
    expect(count(array_filter($licenses, fn ($l) => $l['status'] === 'Suspended')))->toBe(2);

    expect(count($citizens))->toBe(6);
    expect(count($dealers))->toBe(5);

    $dealerArmsSum = array_sum(array_column($dealers, 'total_firearms'));
    $dealerAmmoSum = array_sum(array_column($dealers, 'total_ammo'));
    expect($dealerArmsSum)->toBe(120);
    expect($dealerAmmoSum)->toBe(5000);
});

test('admin and senior secretary can access dedicated view details pages', function () {
    $admin = User::where('role', Role::SystemAdmin->value)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);
    $seniorSec = User::where('role', Role::SeniorSecretary->value)->first()
        ?? User::factory()->create(['role' => Role::SeniorSecretary]);

    // License Details
    $this->actingAs($admin)->get(route('overview.licenses.show', 1))->assertStatus(200);
    $this->actingAs($seniorSec)->get(route('overview.licenses.show', 1))->assertStatus(200);

    // Citizen Details
    $this->actingAs($admin)->get(route('overview.citizens.show', 1))->assertStatus(200);
    $this->actingAs($seniorSec)->get(route('overview.citizens.show', 1))->assertStatus(200);

    // Dealer Details
    $this->actingAs($admin)->get(route('overview.dealers.show', 1))->assertStatus(200);
    $this->actingAs($seniorSec)->get(route('overview.dealers.show', 1))->assertStatus(200);
});

test('view details pages display complete dossier information moved from tables', function () {
    $admin = User::where('role', Role::SystemAdmin->value)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    // License Show displays weapon details, address, issuer, and note
    $licResponse = $this->actingAs($admin)->get(route('overview.licenses.show', 1));
    $licResponse->assertStatus(200);
    $licResponse->assertSee('BD-HND-DHK-004521');
    $licResponse->assertSee('Brig. Gen. (Retd.) Tariq Mahmud');
    $licResponse->assertSee('Pistol (Glock 17)');
    $licResponse->assertSee('9mm');
    $licResponse->assertSee('House 42, Road 11, Block D, Gulshan-1, Dhaka');
    $licResponse->assertSee('Ministry of Home Affairs (MoHA)');

    // Citizen Show displays NID, income, weapon model, and address
    $citResponse = $this->actingAs($admin)->get(route('overview.citizens.show', 1));
    $citResponse->assertStatus(200);
    $citResponse->assertSee('1965882910482');
    $citResponse->assertSee('tariq.mahmud@gmail.com');
    $citResponse->assertSee('+880 1711-884102');
    $citResponse->assertSee('2,400,000');
    $citResponse->assertSee('Defense Veteran / Security Consultant');

    // Dealer Show displays trade license, proprietor, full stock counts, and address
    $dlrResponse = $this->actingAs($admin)->get(route('overview.dealers.show', 1));
    $dlrResponse->assertStatus(200);
    $dlrResponse->assertSee('TR-DH-2021-9940');
    $dlrResponse->assertSee('Alhaj M. A. Rahman');
    $dlrResponse->assertSee('1,800');
    $dlrResponse->assertSee('1,835');
    $dlrResponse->assertSee('14 Toyenbee Circular Road, Motijheel, Dhaka');
});

test('view details pages return 404 for invalid IDs', function () {
    $admin = User::where('role', Role::SystemAdmin->value)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    $this->actingAs($admin)->get(route('overview.licenses.show', 999))->assertStatus(404);
    $this->actingAs($admin)->get(route('overview.citizens.show', 999))->assertStatus(404);
    $this->actingAs($admin)->get(route('overview.dealers.show', 999))->assertStatus(404);
});

test('unauthorized users cannot access view details pages', function () {
    $citizen = User::where('role', Role::CitizenApplicant->value)->first()
        ?? User::factory()->create(['role' => Role::CitizenApplicant]);

    $this->actingAs($citizen)->get(route('overview.licenses.show', 1))->assertStatus(403);
    $this->actingAs($citizen)->get(route('overview.citizens.show', 1))->assertStatus(403);
    $this->actingAs($citizen)->get(route('overview.dealers.show', 1))->assertStatus(403);
});

test('streamlined list tables render view details links on every row', function () {
    $admin = User::where('role', Role::SystemAdmin->value)->first()
        ?? User::factory()->create(['role' => Role::SystemAdmin]);

    $licRes = $this->actingAs($admin)->get(route('overview.licenses'));
    $licRes->assertStatus(200);
    $licRes->assertSee(route('overview.licenses.show', 1));

    $citRes = $this->actingAs($admin)->get(route('overview.citizens'));
    $citRes->assertStatus(200);
    $citRes->assertSee(route('overview.citizens.show', 1));

    $dlrRes = $this->actingAs($admin)->get(route('overview.dealers'));
    $dlrRes->assertStatus(200);
    $dlrRes->assertSee(route('overview.dealers.show', 1));
});
