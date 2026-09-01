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
     * - System Admin: All comments
     * - Own comments (user_id = current user)
     * - OR comments assigned to the current user's role (role_id = current role)
     */
    /**
     * Query scope: comments visible to the current user.
     * - System Admin: All comments
     * - Others:
     *   A. Available for user's role (role_id = user role OR role_id is null)
     *   B. Created by user (user_id = current user)
     */
    protected function visibleComments()
    {
        $role = auth()->user()->role;
        $roleVal = $role instanceof Role ? $role->value : $role;

        if ($roleVal === Role::SystemAdmin->value) {
            return CustomComment::with('user');
        }

        return CustomComment::with('user')->where(function ($q) use ($roleVal) {
            $q->where('user_id', auth()->id())
                ->orWhere('role_id', $roleVal)
                ->orWhereNull('role_id');
        });
    }

    /**
     * Show the custom comment management list page.
     */
    public function index()
    {
        $this->authorizeModule();

        $comments = $this->visibleComments()
            ->latest()
            ->paginate(10);

        $roles = $this->roles();

        return view('custom_comment.index', compact('comments', 'roles'));
    }

    /**
     * Show the separate Add Custom Comment form.
     */
    public function create()
    {
        $this->authorizeModule();

        $roles = $this->roles();

        return view('custom_comment.create', compact('roles'));
    }

    /**
     * Display custom comment details.
     */
    public function show(string $encryptedId)
    {
        $this->authorizeModule();

        try {
            $id = Crypt::decryptString($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        $customComment = CustomComment::with('user')->findOrFail($id);

        $role = auth()->user()->role;
        $roleVal = $role instanceof Role ? $role->value : $role;
        $isAdmin = $roleVal === Role::SystemAdmin->value;

        // Non-admin can only view comments that are visible to them (created by user OR available to role)
        if (! $isAdmin) {
            $isPermitted = $customComment->user_id === auth()->id()
                || $customComment->role_id === $roleVal
                || is_null($customComment->role_id);

            abort_if(! $isPermitted, 403);
        }

        $roles = $this->roles();

        if (request()->wantsJson()) {
            return response()->json([
                'id' => $customComment->id,
                'title' => $customComment->title,
                'comment' => $customComment->comment,
                'creator_name' => $customComment->user->name ?? 'System',
                'creator_email' => $customComment->user->email ?? 'N/A',
                'creator_role' => $customComment->user ? $customComment->user->roleLabel() : 'N/A',
                'role_label' => $customComment->role_id ? ($roles[$customComment->role_id] ?? $customComment->role_id) : 'All Roles',
                'created_at' => $customComment->created_at->format('d M Y · h:i A'),
                'updated_at' => $customComment->updated_at->format('d M Y · h:i A'),
            ]);
        }

        return view('custom_comment.show', compact('customComment', 'roles'));
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
            'role_id' => $request->input('role_id') ?: null,
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

        $customComment = CustomComment::with('user')->findOrFail($id);

        $role = auth()->user()->role;
        $roleVal = $role instanceof Role ? $role->value : $role;
        $isAdmin = $roleVal === Role::SystemAdmin->value;

        // Non-admin can edit comments created by them or available to them
        if (! $isAdmin) {
            $canEdit = $customComment->user_id === auth()->id()
                || $customComment->role_id === $roleVal
                || is_null($customComment->role_id);

            abort_if(! $canEdit, 403);
        }

        $roles = $this->roles();

        return view('custom_comment.create', compact('customComment', 'roles'));
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

        $role = auth()->user()->role;
        $roleVal = $role instanceof Role ? $role->value : $role;
        $isAdmin = $roleVal === Role::SystemAdmin->value;

        if (! $isAdmin) {
            $canEdit = $customComment->user_id === auth()->id()
                || $customComment->role_id === $roleVal
                || is_null($customComment->role_id);

            abort_if(! $canEdit, 403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['required', 'string'],
            'role_id' => ['nullable', 'string'],
        ]);

        $customComment->update([
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'role_id' => $request->input('role_id') ?: null,
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

        $role = auth()->user()->role;
        $roleVal = $role instanceof Role ? $role->value : $role;
        $isAdmin = $roleVal === Role::SystemAdmin->value;

        if (! $isAdmin) {
            $canDelete = $customComment->user_id === auth()->id()
                || $customComment->role_id === $roleVal
                || is_null($customComment->role_id);

            abort_if(! $canDelete, 403);
        }

        $customComment->delete();

        return redirect()->route('custom_comment.index')
            ->with('success', 'Custom comment deleted successfully.');
    }

    /**
     * Check if the current user has permission to access this module.
     */
    protected function authorizeModule(): void
    {
        $role = auth()->user()->role;
        $roleVal = $role instanceof Role ? $role->value : $role;

        if ($roleVal === Role::SystemAdmin->value) {
            return;
        }

        $matrix = json_decode(Setting::get('acl_matrix', '{}'), true) ?: [];
        $permission = $matrix[self::MODULE][$roleVal] ?? 'none';

        abort_if($permission === 'none', 403, 'You do not have permission to access the Custom Comment module.');
    }
}
