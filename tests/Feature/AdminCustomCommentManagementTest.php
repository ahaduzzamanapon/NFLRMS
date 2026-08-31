<?php

use App\Enums\Role;
use App\Models\CustomComment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

beforeEach(function () {
    $this->seed();

    // Enable Custom Comment permission for DC Front Desk role in ACL Matrix
    $aclMatrix = json_decode(Setting::get('acl_matrix', '{}'), true) ?: [];
    $aclMatrix['Custom Comment']['dc_front_desk'] = 'view';
    Setting::set('acl_matrix', json_encode($aclMatrix));
});

test('admin can see all custom comments and navigate to separate create form', function () {
    $admin = User::where('role', Role::SystemAdmin)->first();

    // 1. List Page contains Add Custom Comment button
    $this->actingAs($admin)
        ->get(route('custom_comment.index'))
        ->assertOk()
        ->assertSee('Add Custom Comment')
        ->assertSee('All Custom Comments (Admin Management)');

    // 2. Separate Create form page
    $this->actingAs($admin)
        ->get(route('custom_comment.create'))
        ->assertOk()
        ->assertSee('New Custom Comment Form');

    // 3. Store new comment via separate form
    $this->actingAs($admin)
        ->post(route('custom_comment.store'), [
            'title' => 'Admin Test Comment',
            'comment' => 'Comment created by admin via separate add form.',
            'role_id' => 'dc_front_desk',
        ])
        ->assertRedirect(route('custom_comment.index'));

    $this->assertDatabaseHas('custom_comments', [
        'title' => 'Admin Test Comment',
        'role_id' => 'dc_front_desk',
    ]);
});

test('non-admin user with permission sees role-available and own comments but hides unrelated comments', function () {
    $frontDeskUser = User::where('role', Role::DcFrontDesk)->first();

    // Create a comment specifically for police officer role by police user (unrelated to front desk)
    $policeUser = User::where('role', Role::PoliceOfficer)->first();
    $unrelatedComment = CustomComment::create([
        'title' => 'Unrelated Police Internal Note',
        'comment' => 'Only for police officers.',
        'user_id' => $policeUser->id,
        'role_id' => 'police_officer',
    ]);

    // Create a comment created by front desk user specifically
    $ownComment = CustomComment::create([
        'title' => 'Front Desk Personal Template',
        'comment' => 'Custom front desk template.',
        'user_id' => $frontDeskUser->id,
        'role_id' => 'dc_front_desk',
    ]);

    $response = $this->actingAs($frontDeskUser)
        ->get(route('custom_comment.index'));

    $response->assertOk()
        ->assertSee('Front Desk Personal Template')
        ->assertDontSee('Unrelated Police Internal Note');
});

test('non-admin user can access create form and edit eligible comments', function () {
    $frontDeskUser = User::where('role', Role::DcFrontDesk)->first();

    // Create form accessible
    $this->actingAs($frontDeskUser)
        ->get(route('custom_comment.create'))
        ->assertOk()
        ->assertSee('New Custom Comment Form');

    // Store comment
    $this->actingAs($frontDeskUser)
        ->post(route('custom_comment.store'), [
            'title' => 'Front Desk New Custom Note',
            'comment' => 'Created by front desk officer.',
        ])
        ->assertRedirect(route('custom_comment.index'));

    $comment = CustomComment::where('title', 'Front Desk New Custom Note')->first();

    // Edit form accessible
    $this->actingAs($frontDeskUser)
        ->get(route('custom_comment.edit', Crypt::encryptString($comment->id)))
        ->assertOk()
        ->assertSee('Edit Custom Comment Form');

    // Update comment
    $this->actingAs($frontDeskUser)
        ->put(route('custom_comment.update', Crypt::encryptString($comment->id)), [
            'title' => 'Front Desk Updated Custom Note',
            'comment' => 'Updated text.',
        ])
        ->assertRedirect(route('custom_comment.index'));

    expect($comment->fresh()->title)->toBe('Front Desk Updated Custom Note');
});

test('unauthorized user without ACL permission cannot access custom comment management', function () {
    $citizen = User::where('role', Role::CitizenApplicant)->first();

    $this->actingAs($citizen)
        ->get(route('custom_comment.index'))
        ->assertStatus(403);

    $this->actingAs($citizen)
        ->get(route('custom_comment.create'))
        ->assertStatus(403);
});

test('custom comment list contains SL No column and pagination aware serial numbers', function () {
    $admin = User::where('role', Role::SystemAdmin)->first();

    // Page 1
    $response = $this->actingAs($admin)
        ->get(route('custom_comment.index'));

    $response->assertOk()
        ->assertSee('SL No.')
        ->assertSee('>1<', false);

    // Page 2
    $responsePage2 = $this->actingAs($admin)
        ->get(route('custom_comment.index', ['page' => 2]));

    $responsePage2->assertOk()
        ->assertSee('SL No.')
        ->assertSee('>11<', false);
});
