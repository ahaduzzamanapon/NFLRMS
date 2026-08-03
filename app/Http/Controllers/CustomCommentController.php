<?php

namespace App\Http\Controllers;

use App\Models\CustomComment;
use App\Models\Setting;
use Illuminate\Http\Request;

class CustomCommentController extends Controller
{
    /**
     * Module name used in the ACL matrix.
     */
    protected const MODULE = 'Custom Comment';

    /**
     * Show the custom comment management page.
     */
    public function index()
    {
        $this->authorizeModule();

        $comments = CustomComment::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('custom_comment.index', compact('comments'));
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
        ]);

        CustomComment::create([
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('custom_comment.index')
            ->with('success', 'Custom comment created successfully.');
    }

    /**
     * Show the edit form for a custom comment.
     */
    public function edit(CustomComment $customComment)
    {
        $this->authorizeModule();

        // Only the owner can edit their own comment.
        abort_if($customComment->user_id !== auth()->id(), 403);

        $comments = CustomComment::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('custom_comment.index', compact('comments', 'customComment'));
    }

    /**
     * Update a custom comment.
     */
    public function update(Request $request, CustomComment $customComment)
    {
        $this->authorizeModule();

        // Only the owner can update their own comment.
        abort_if($customComment->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['required', 'string'],
        ]);

        $customComment->update($validated);

        return redirect()->route('custom_comment.index')
            ->with('success', 'Custom comment updated successfully.');
    }

    /**
     * Delete a custom comment.
     */
    public function destroy(CustomComment $customComment)
    {
        $this->authorizeModule();

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
        $roleVal = $role instanceof \App\Enums\Role ? $role->value : $role;

        $permission = $matrix[self::MODULE][$roleVal] ?? 'none';

        abort_if($permission === 'none', 403, 'You do not have permission to access the Custom Comment module.');
    }
}
