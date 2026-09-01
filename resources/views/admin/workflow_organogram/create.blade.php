@extends('layouts.app')
@section('title', 'Create Workflow')

@section('content')
<div class="w-full max-w-lg space-y-4">

    <div>
        <div class="text-xs text-slate-400 mb-1">
            <a href="{{ route('admin.workflow_organogram.index') }}" class="hover:text-gov-green">Workflow Config</a>
            <span class="mx-1">›</span>
            <span class="text-slate-600">Create New Workflow</span>
        </div>
        <h2 class="text-xl font-bold text-slate-900">Create Workflow</h2>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.workflow_organogram.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Key <span class="text-rose-500">*</span>
                    <span class="ml-1 font-normal text-slate-400 normal-case">(unique, letters/numbers/dash/underscore)</span>
                </label>
                <input type="text" name="key" value="{{ old('key') }}" required
                       placeholder="e.g. police_special_case"
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none font-mono">
                @error('key')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Name (English) <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Police Special Case Licence"
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none">
                @error('name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Name (Bengali)</label>
                <input type="text" name="name_bn" value="{{ old('name_bn') }}"
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none font-bn">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="2"
                          class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none resize-none"
                          placeholder="Brief description of this workflow...">{{ old('description') }}</textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus mr-1"></i> Create Workflow
                </button>
                <a href="{{ route('admin.workflow_organogram.index') }}"
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
