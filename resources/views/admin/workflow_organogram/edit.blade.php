@extends('layouts.app')
@section('title', $workflow->name . ' — Edit Info')

@section('content')
@php $encWfId = \Illuminate\Support\Facades\Crypt::encryptString($workflow->id); @endphp
<div class="w-full max-w-xl space-y-4">

    <div>
        <div class="text-xs text-slate-400 mb-0.5">
            <a href="{{ route('admin.workflow_organogram.index') }}" class="hover:text-gov-green">Workflow Config</a>
            <span class="mx-1">›</span>
            <a href="{{ route('admin.workflow_organogram.show', $encWfId) }}" class="hover:text-gov-green">{{ $workflow->name }}</a>
            <span class="mx-1">›</span>
            <span class="text-slate-600">Edit Info</span>
        </div>
        <h2 class="text-xl font-bold text-slate-900">Edit Workflow Info</h2>
    </div>

    @if(session('success'))
    <div class="px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-700">
        <i class="fa-solid fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.workflow_organogram.update', $encWfId) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Key (read-only)</label>
                <input type="text" value="{{ $workflow->key }}" disabled
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-100 bg-slate-50 text-slate-400 font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $workflow->name) }}" required
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none">
                @error('name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Name (Bengali)</label>
                <input type="text" name="name_bn" value="{{ old('name_bn', $workflow->name_bn) }}"
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none font-bn">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none resize-none">{{ old('description', $workflow->description) }}</textarea>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $workflow->is_active ? 'checked' : '' }}
                       class="rounded text-gov-green focus:ring-gov-green">
                <span class="text-xs font-semibold text-slate-700 cursor-pointer">Active</span>
            </label>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-save mr-1"></i> Save Changes
                </button>
                <a href="{{ route('admin.workflow_organogram.show', $encWfId) }}"
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
