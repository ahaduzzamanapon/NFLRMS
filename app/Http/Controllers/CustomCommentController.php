<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\CustomComment;
use App\Models\Setting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CustomCommentController extends Controller
{
    /**
     * Module name used in the ACL matrix.
     */
    protected const MODULE = 'Custom Comment';

    /**
     * Get available roles for the admin role selector.
     */
    protected function roles(): array
    {
        $defaultRoles = [
            'citizen_applicant' => 'Citizen Applicant',
            'dealer_applicant' => 'Dealer Applicant',
            'dc_front_desk' => 'DC Office — Front Desk',
            'dc_jm_branch' => 'DC Office — JM Branch',
            'district_commissioner' => 'District Commissioner',
            'police_officer' => 'Police Officer (SP/Thana)',
            'special_branch' => 'Special Branch (SB)',
            'nsi_officer' => 'NSI Officer',
            'dgfi_officer' => 'DGFI Officer',
            'moha_desk' => 'MoHA Desk',
            'joint_secretary' => 'Joint Secretary',
            'senior_secretary' => 'Senior Secretary',
            'system_admin' => 'System Admin',
        ];

        $customRoles = json_decode(Setting::get('custom_roles', '{}'), true) ?: [];

        return array_merge($defaultRoles, $customRoles);
    }

    /**
     * Query scope: comments visible to the current user.
     * - Own comments (user_id = current user)
     * - OR comments assigned to the current user's role (role_id = current role)
     */
    protected function visibleComments()
    {
        $role = auth()->user()->role;
        $roleVal = $role instanceof Role ? $role->value : $role;

        return CustomComment::where(function ($q) use ($roleVal) {
            $q->where('user_id', auth()->id())
                ->orWhere('role_id', $roleVal);
        });
    }

    /**
     * Show the custom comment management page.
     */
    public function index()
    {
        $this->authorizeModule();

        $comments = $this->visibleComments()
            ->latest()
            ->get();

        $roles = $this->roles();

        return view('custom_comment.index', compact('comments', 'roles'));
    }

    /**
     * Store a newly created custom comment.
     */
    public function store(Request $request)
    {
        $this->authorizeModule();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['required', 'string'],
            'role_id' => ['nullable', 'string'],
        ]);

        CustomComment::create([
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'user_id' => auth()->id(),
            'role_id' => $validated['role_id'] ?: null,
        ]);

        return redirect()->route('custom_comment.index')
            ->with('success', 'Custom comment created successfully.');
    }

    /**
     * Show the edit form for a custom comment.
     */
    public function edit(string $encryptedId)
    {
        $this->authorizeModule();

        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $customComment = CustomComment::findOrFail($id);

        // Only the owner can edit their own comment.
        abort_if($customComment->user_id !== auth()->id(), 403);

        $comments = $this->visibleComments()
            ->latest()
            ->get();

        $roles = $this->roles();

        return view('custom_comment.index', compact('comments', 'customComment', 'roles'));
    }

    /**
     * Update a custom comment.
     */
    public function update(Request $request, string $encryptedId)
    {
        $this->authorizeModule();

        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $customComment = CustomComment::findOrFail($id);

        // Only the owner can update their own comment.
        abort_if($customComment->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['required', 'string'],
            'role_id' => ['nullable', 'string'],
        ]);

        $customComment->update([
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'role_id' => $validated['role_id'] ?: null,
        ]);

        return redirect()->route('custom_comment.index')
            ->with('success', 'Custom comment updated successfully.');
    }

    /**
     * Delete a custom comment.
     */
    public function destroy(string $encryptedId)
    {
        $this->authorizeModule();

        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $customComment = CustomComment::findOrFail($id);

        // Only the owner can delete their own comment.
        abort_if($customComment->user_id !== auth()->id(), 403);

        $customComment->delete();

        return redirect()->route('custom_comment.index')
            ->with('success', 'Custom comment deleted successfully.');
    }

    /**
     * Check if the current user has permission to access this module.
     */
    protected function authorizeModule(): void
    {
        $matrix = json_decode(Setting::get('acl_matrix', '{}'), true) ?: [];
        $role = auth()->user()->role;
        $roleVal = $role instanceof Role ? $role->value : $role;

        $permission = $matrix[self::MODULE][$roleVal] ?? 'none';

        abort_if($permission === 'none', 403, 'You do not have permission to access the Custom Comment module.');
    }
}
