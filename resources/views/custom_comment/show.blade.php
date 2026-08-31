@extends('layouts.app')
@section('title', 'Custom Comment Details')

@section('content')
<div class="max-w-full space-y-5">

    <!-- Page Header & Navigation -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold font-serif text-slate-900">Custom Comment Details</h2>
            <p class="text-xs text-slate-500 mt-0.5">View complete information for this saved comment template</p>
        </div>
        <a href="{{ route('custom_comment.index') }}" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition-colors flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-left"></i><span>Back to Comments</span>
        </a>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden space-y-0">

        <!-- Card Header -->
        <div class="p-5 border-b border-slate-100 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Title</span>
                <h3 class="text-lg font-bold text-slate-900 mt-0.5">{{ $customComment->title }}</h3>
            </div>
            <div class="flex items-center space-x-2">
                @if($customComment->role_id)
                <span class="px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-bold uppercase">
                    <i class="fa-solid fa-bullseye mr-1"></i> {{ $roles[$customComment->role_id] ?? $customComment->role_id }}
                </span>
                @else
                <span class="px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold uppercase">
                    <i class="fa-solid fa-globe mr-1"></i> All Roles
                </span>
                @endif
            </div>
        </div>

        <!-- Description Box -->
        <div class="p-5 border-b border-slate-100">
            <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block mb-2">Comment Text / Description</span>
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-slate-800 text-sm leading-relaxed font-medium">
                {{ $customComment->comment }}
            </div>
        </div>

        <!-- Metadata Grid -->
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs bg-slate-50/30">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Created By</span>
                <div class="font-semibold text-slate-900 mt-1">{{ $customComment->user->name ?? 'System' }}</div>
                <div class="text-[11px] text-slate-500">{{ $customComment->user->email ?? 'N/A' }}</div>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Creator Role</span>
                <div class="font-semibold text-slate-900 mt-1">
                    {{ $customComment->user ? $customComment->user->roleLabel() : 'System' }}
                </div>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Created Date</span>
                <div class="font-semibold text-slate-900 mt-1">{{ $customComment->created_at->format('d M Y · h:i A') }}</div>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Last Updated</span>
                <div class="font-semibold text-slate-900 mt-1">{{ $customComment->updated_at->format('d M Y · h:i A') }}</div>
            </div>
        </div>

        <!-- Action Footer -->
        @php
            $roleVal = auth()->user()->role instanceof \App\Enums\Role ? auth()->user()->role->value : auth()->user()->role;
            $canEdit = $roleVal === 'system_admin' || $customComment->user_id === auth()->id();
        @endphp
        @if($canEdit)
        <div class="p-5 border-t border-slate-100 bg-slate-50 flex items-center justify-end space-x-3">
            <a href="{{ route('custom_comment.edit', Crypt::encryptString($customComment->id)) }}"
               class="px-4 py-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-bold border border-blue-200 transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-pen-to-square"></i><span>Edit Comment</span>
            </a>
            <form action="{{ route('custom_comment.destroy', Crypt::encryptString($customComment->id)) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this custom comment?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold border border-rose-200 transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-trash-can"></i><span>Delete Comment</span>
                </button>
            </form>
        </div>
        @endif

    </div>

</div>
@endsection
