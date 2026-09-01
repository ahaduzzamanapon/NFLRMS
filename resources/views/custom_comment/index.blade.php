@extends('layouts.app')
@section('title', 'Custom Comments')

@section('content')
@php
    $userRole = auth()->user()->role;
    $roleVal = $userRole instanceof \App\Enums\Role ? $userRole->value : $userRole;
    $isAdmin = $roleVal === 'system_admin';
@endphp

<div class="max-w-full space-y-5">

    <!-- Page Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold font-serif text-slate-900">Custom Comments</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                {{ $isAdmin ? 'Manage all custom comment templates across the system.' : 'Manage reusable comments to quickly fill remarks during workflow processing.' }}
            </p>
        </div>
        <a href="{{ route('custom_comment.create') }}"
           class="px-4 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg flex items-center space-x-1.5 shadow-sm transition-colors self-start sm:self-auto">
            <i class="fa-solid fa-plus"></i>
            <span>Add Custom Comment</span>
        </a>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-700">
        <i class="fa-solid fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Existing Comments Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase text-slate-900 tracking-widest">
                {{ $isAdmin ? 'All Custom Comments (Admin Management)' : 'My Saved Comments' }}
            </span>
            <span class="ml-2 px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold">{{ $comments->total() }}</span>
        </div>
        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200 text-[10px] font-semibold uppercase text-slate-500 tracking-wider">
                        <th class="py-3 px-4 pl-5 w-14 text-center whitespace-nowrap">SL No.</th>
                        <th class="py-3 px-4">Comment</th>
                        <th class="py-3 px-4 whitespace-nowrap">Available For</th>
                        <th class="py-3 px-4 pr-5 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @forelse($comments as $index => $comment)
                        @php
                            $canManage = $isAdmin || $comment->user_id === auth()->id() || $comment->role_id === $roleVal || is_null($comment->role_id);
                            $slNo = $comments->firstItem() + $index;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 pl-5 text-center font-bold text-slate-500 text-xs whitespace-nowrap">{{ $slNo }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-900 text-xs block mb-0.5">{{ $comment->title }}</span>
                                <p class="text-[11px] text-slate-600 font-normal leading-relaxed line-clamp-2">{{ $comment->comment }}</p>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($comment->role_id)
                                <span class="inline-block px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[9px] font-bold uppercase">
                                    <i class="fa-solid fa-bullseye mr-1"></i> {{ $roles[$comment->role_id] ?? $comment->role_id }}
                                </span>
                                @else
                                <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[9px] font-bold uppercase">
                                    <i class="fa-solid fa-globe mr-1"></i> All Roles
                                </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 pr-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('custom_comment.show', Crypt::encryptString($comment->id)) }}"
                                       class="px-2.5 py-1.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold border border-slate-200 transition-colors">
                                        <i class="fa-solid fa-eye mr-1"></i> View Details
                                    </a>
                                    @if($canManage)
                                    <a href="{{ route('custom_comment.edit', Crypt::encryptString($comment->id)) }}"
                                       class="px-2.5 py-1.5 rounded bg-blue-50 hover:bg-blue-100 text-blue-600 text-[11px] font-semibold border border-blue-200 transition-colors">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('custom_comment.destroy', Crypt::encryptString($comment->id)) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded bg-rose-50 hover:bg-rose-100 text-rose-600 text-[11px] font-semibold border border-rose-200 transition-colors">
                                            <i class="fa-solid fa-trash-can mr-1"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-400 font-normal">
                                <i class="fa-solid fa-comments text-3xl text-slate-300 block mb-2"></i>
                                No custom comments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
            {{ $comments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
