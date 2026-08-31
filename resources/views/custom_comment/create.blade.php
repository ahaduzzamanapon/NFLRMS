@extends('layouts.app')
@section('title', isset($customComment) ? 'Edit Custom Comment' : 'Add Custom Comment')

@section('content')
<div class="max-w-full space-y-5">

    <!-- Page Header & Back Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold font-serif text-slate-900">
                {{ isset($customComment) ? 'Edit Custom Comment' : 'Add Custom Comment' }}
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ isset($customComment) ? 'Update the details of your saved comment' : 'Create a reusable comment template to quickly fill remarks during workflow processing' }}
            </p>
        </div>
        <a href="{{ route('custom_comment.index') }}" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200 transition-colors flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-left"></i><span>Back to Comments</span>
        </a>
    </div>

    <!-- Create / Edit Form Card -->
    <form action="{{ isset($customComment) ? route('custom_comment.update', Crypt::encryptString($customComment->id)) : route('custom_comment.store') }}" method="POST"
          class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @if(isset($customComment)) @method('PUT') @endif

        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase text-slate-900 tracking-widest">
                {{ isset($customComment) ? 'Edit Custom Comment Form' : 'New Custom Comment Form' }}
            </span>
        </div>

        @if ($errors->any())
        <div class="p-4 bg-red-50 border-b border-red-200 text-red-800 text-xs font-bold space-y-1">
            <span class="block text-sm font-bold font-serif"><i class="fa-solid fa-triangle-exclamation"></i> Please resolve the following errors:</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="p-5 space-y-4">
            <div>
                <label for="title" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $customComment->title ?? '') }}" required
                       placeholder="e.g. Documents Verified, Insufficient Documents, Application Rejected"
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white @error('title') border-rose-400 @enderror">
                @error('title')<span class="text-[10px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="comment" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Description / Remark Text</label>
                <textarea name="comment" id="comment" rows="4" required
                          placeholder="Write the comment/remarks text that will be inserted into the Remarks field..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white resize-none @error('comment') border-rose-400 @enderror">{{ old('comment', $customComment->comment ?? '') }}</textarea>
                @error('comment')<span class="text-[10px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
            </div>

            @if(auth()->user()->role instanceof \App\Enums\Role && auth()->user()->role === \App\Enums\Role::SystemAdmin)
            <div>
                <label for="role_id" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Available To (Role)</label>
                <select name="role_id" id="role_id"
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white @error('role_id') border-rose-400 @enderror">
                    <option value="">— Everyone (All Roles) —</option>
                    @foreach($roles as $roleKey => $roleLabel)
                    <option value="{{ $roleKey }}" {{ old('role_id', $customComment->role_id ?? '') === $roleKey ? 'selected' : '' }}>{{ $roleLabel }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 font-medium mt-1">Select a role to make this comment available to that role's Quick Fill. Leave empty for all users.</p>
                @error('role_id')<span class="text-[10px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
            </div>
            @endif

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('custom_comment.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 {{ isset($customComment) ? 'bg-amber-500 hover:bg-amber-600' : 'bg-gov-green hover:bg-gov-light' }} text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> {{ isset($customComment) ? 'Update Comment' : 'Save Comment' }}
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
