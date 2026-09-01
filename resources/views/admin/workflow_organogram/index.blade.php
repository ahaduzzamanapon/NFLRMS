@extends('layouts.app')
@section('title', 'Workflow Config')

@section('content')
<div class="w-full space-y-4">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Workflow Config</h2>
            <p class="text-xs text-slate-500 mt-0.5">Configure approval step chains for each application module.</p>
        </div>
        <button onclick="document.getElementById('create-workflow-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg flex items-center gap-1.5 shadow-sm transition-colors">
            <i class="fa-solid fa-plus"></i> Create Workflow
        </button>
    </div>

    @if(session('success'))
    <div class="px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-700">
        <i class="fa-solid fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Workflow Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach($workflows as $wf)
        @php
            $icons = [
                'citizen_new'   => ['icon' => 'fa-id-card',        'bg' => 'bg-blue-50',   'color' => 'text-blue-600',   'border' => 'border-blue-200'],
                'citizen_renew' => ['icon' => 'fa-rotate',         'bg' => 'bg-teal-50',   'color' => 'text-teal-600',   'border' => 'border-teal-200'],
                'dealer_new'    => ['icon' => 'fa-store',          'bg' => 'bg-amber-50',  'color' => 'text-amber-600',  'border' => 'border-amber-200'],
                'dealer_renew'  => ['icon' => 'fa-arrows-rotate',  'bg' => 'bg-rose-50',   'color' => 'text-rose-600',   'border' => 'border-rose-200'],
            ];
            $cfg = $icons[$wf->key] ?? ['icon' => 'fa-diagram-project', 'bg' => 'bg-slate-50', 'color' => 'text-slate-600', 'border' => 'border-slate-200'];
            $encId = \Illuminate\Support\Facades\Crypt::encryptString($wf->id);
        @endphp
        <div class="bg-white rounded-xl border {{ $cfg['border'] }} shadow-sm p-4 flex flex-col gap-3 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg {{ $cfg['bg'] }} flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid {{ $cfg['icon'] }} {{ $cfg['color'] }}"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $wf->name }}</h3>
                        @if($wf->name_bn)
                        <p class="text-[11px] text-slate-400 font-bn">{{ $wf->name_bn }}</p>
                        @endif
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                    {{ $wf->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                    {{ $wf->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed">{{ $wf->description ?: 'No description added.' }}</p>

            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                <span class="text-xs font-semibold text-slate-500">
                    <i class="fa-solid fa-list-check mr-1 text-gov-green"></i>
                    {{ $wf->steps_count }} steps
                </span>
                <div class="flex gap-1.5">
                    <a href="{{ route('admin.workflow_organogram.edit', $encId) }}"
                       class="px-3 py-1.5 text-[11px] font-bold rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition-colors">
                        <i class="fa-solid fa-pen-to-square mr-0.5"></i> Edit
                    </a>
                    <a href="{{ route('admin.workflow_organogram.show', $encId) }}"
                       class="px-3 py-1.5 text-[11px] font-bold rounded-lg bg-gov-green hover:bg-gov-light text-white transition-colors shadow-sm">
                        <i class="fa-solid fa-diagram-project mr-0.5"></i> Manage Steps
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- ===== CREATE WORKFLOW MODAL ===== -->
<div id="create-workflow-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closeModal()">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

    <!-- Panel -->
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10">

        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Create New Workflow</h3>
                <p class="text-xs text-slate-400 mt-0.5">Add a custom approval workflow type</p>
            </div>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.workflow_organogram.store') }}" method="POST" id="create-wf-form">
            @csrf
            <div class="px-6 py-5 space-y-4">

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                        Key <span class="text-rose-500">*</span>
                        <span class="ml-1 font-normal text-slate-400 normal-case tracking-normal">(letters, numbers, dash, underscore)</span>
                    </label>
                    <input type="text" name="key" required
                           placeholder="e.g. police_special_case"
                           class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none font-mono bg-slate-50 focus:bg-white transition-colors">
                    @error('key')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required
                           placeholder="e.g. Police Special Case Licence"
                           class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none bg-slate-50 focus:bg-white transition-colors">
                    @error('name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Name (Bengali)</label>
                    <input type="text" name="name_bn"
                           class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none font-bn bg-slate-50 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none resize-none bg-slate-50 focus:bg-white transition-colors"
                              placeholder="Brief description of this workflow..."></textarea>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 flex flex-col gap-2">
                <!-- Primary: Create & Manage Steps -->
                <button type="submit" name="redirect_to" value="manage"
                        class="w-full py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-diagram-project"></i>
                    Create &amp; Manage Workflow Steps
                </button>
                <!-- Secondary: Just create -->
                <button type="submit" name="redirect_to" value="index"
                        class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    Create Only
                </button>
                <button type="button" onclick="closeModal()"
                        class="w-full py-2 text-slate-400 hover:text-slate-600 font-semibold text-xs transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>document.getElementById('create-workflow-modal').classList.remove('hidden');</script>
@endif

<script>
function closeModal() {
    document.getElementById('create-workflow-modal').classList.add('hidden');
}
</script>
@endsection
