@extends('layouts.app')
@section('title', 'Custom Comment')

@section('content')
<div class="max-w-3xl space-y-5">

    <!-- Page Header -->
    <div>
<h2 class="text-xl font-bold font-serif text-slate-900">Custom Comment</h2>
        <p class="text-xs text-slate-500 mt-1 font-medium">
            Create reusable comments to quickly fill remarks on application details.
        </p>
    </div>

    <!-- Create / Edit Form -->
    <form action="{{ isset($customComment) ? route('custom_comment.update', $customComment) : route('custom_comment.store') }}" method="POST"
          class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @if(isset($customComment)) @method('PUT') @endif
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
<span class="text-[10px] font-semibold uppercase text-slate-900 tracking-widest">
                {{ isset($customComment) ? '✏️ Edit Custom Comment' : 'Create New Custom Comment' }}
            </span>
            @if(isset($customComment))
                <a href="{{ route('custom_comment.index') }}" class="text-[10px] font-semibold text-slate-400 hover:text-gov-green transition-colors">← Cancel Edit</a>
            @endif
        </div>

        @if ($errors->any())
<div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-bold space-y-1">
                <span class="block text-sm font-bold font-serif">⚠️ Please resolve the following errors:</span>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="p-5 space-y-4">
            <div>
<label for="title" class="block text-[10px] font-semibold uppercase text-slate-900 mb-1.5">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $customComment->title ?? '') }}" required
                       placeholder="e.g. Documents Verified, Insufficient Documents"
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white @error('title') border-rose-400 @enderror">
                @error('title')<span class="text-[9px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
            </div>
            <div>
<label for="comment" class="block text-[10px] font-semibold uppercase text-slate-900 mb-1.5">Description</label>
                <textarea name="comment" id="comment" rows="4" required
                          placeholder="Write the comment/remarks text that will be inserted into the Remarks field..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white resize-none @error('comment') border-rose-400 @enderror">{{ old('comment', $customComment->comment ?? '') }}</textarea>
                @error('comment')<span class="text-[9px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
            </div>
            <div class="flex justify-end">
                <button type="submit"
class="px-5 py-2.5 {{ isset($customComment) ? 'bg-amber-500 hover:bg-amber-600' : 'bg-gov-green hover:bg-gov-light' }} text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                    {{ isset($customComment) ? '💾 Update Comment' : '💾 Save Comment' }}
                </button>
            </div>
        </div>
    </form>

    <!-- Existing Comments List -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
<span class="text-[10px] font-semibold uppercase text-slate-900 tracking-widest">My Saved Comments</span>
            <span class="ml-2 px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[9px] font-bold">{{ $comments->count() }}</span>
        </div>
        <div class="p-5">
            @forelse($comments as $comment)
                <div class="flex items-start justify-between p-3.5 rounded-lg border border-slate-200 bg-slate-50/70 {{ !$loop->first ? 'mt-3' : '' }}">
                    <div class="space-y-1 min-w-0 pr-3">
<span class="font-semibold text-slate-900 text-sm block">{{ $comment->title }}</span>
                        <p class="text-xs text-slate-600 font-medium leading-relaxed">{{ $comment->comment }}</p>
                        <span class="text-[9px] text-slate-400 font-semibold block mt-1">Created {{ $comment->created_at->format('d M Y · h:i A') }}</span>
                    </div>
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <a href="{{ route('custom_comment.edit', $comment) }}"
class="px-2.5 py-1.5 rounded bg-blue-50 hover:bg-blue-100 text-blue-600 text-[10px] font-semibold border border-blue-200 transition-colors">
                            ✏️ Edit
                        </a>
                        <form action="{{ route('custom_comment.destroy', $comment) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this comment?');">
                            @csrf
                            @method('DELETE')
<button type="submit" class="px-2.5 py-1.5 rounded bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-semibold border border-rose-200 transition-colors">
                                🗑 Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 space-y-2">
                    <span class="text-3xl block">💬</span>
<p class="text-xs text-slate-400 font-normal">No custom comments yet.</p>
                    <p class="text-[10px] text-slate-400 font-medium">Create your first reusable comment above.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
