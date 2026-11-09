@extends('layouts.app')
@section('title', $workflow->name . ' — Steps')

@section('content')
@php
    $encWfId = \Illuminate\Support\Facades\Crypt::encryptString($workflow->id);
    $reorderUrl = route('admin.workflow_organogram.steps.reorder', $encWfId);
@endphp
<div class="w-full space-y-4">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="text-xs text-slate-400 mb-0.5">
                <a href="{{ route('admin.workflow_organogram.index') }}" class="hover:text-gov-green">Workflow Config</a>
                <span class="mx-1">›</span>
                <span class="text-slate-600 font-semibold">{{ $workflow->name }}</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900">{{ $workflow->name }}</h2>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.workflow_organogram.edit', $encWfId) }}"
               class="px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition-colors">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Info
            </a>
            <a href="{{ route('admin.workflow_organogram.index') }}"
               class="px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-700">
        <i class="fa-solid fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Drag reorder toast -->
    <div id="reorder-toast" class="hidden px-4 py-2.5 bg-blue-50 border border-blue-200 rounded-lg text-xs font-bold text-blue-700">
        <i class="fa-solid fa-arrows-up-down mr-1"></i> Order saved automatically.
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- LEFT: Step Chain -->
        <div class="lg:col-span-2 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-slate-600 uppercase tracking-wider">
                    <i class="fa-solid fa-diagram-project text-gov-green mr-1"></i>
                    Approval Chain
                    <span class="ml-1 px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded font-semibold normal-case tracking-normal">{{ $workflow->steps->count() }} steps</span>
                </p>
                <p class="text-[10px] text-slate-400 flex items-center gap-1">
                    <i class="fa-solid fa-grip-lines"></i> Drag to reorder
                </p>
            </div>

            @if($workflow->steps->isEmpty())
            <div class="bg-white rounded-xl border-2 border-dashed border-slate-200 p-8 text-center text-slate-400">
                <i class="fa-solid fa-circle-nodes text-2xl mb-2 block"></i>
                <p class="font-semibold text-sm">No steps yet.</p>
                <p class="text-xs mt-0.5">Add the first step using the form on the right.</p>
            </div>
            @else

            <!-- Start node -->
            <div class="flex items-center gap-2 px-3">
                <div class="w-2 h-2 rounded-full bg-gov-green"></div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Application Submitted</span>
            </div>
            <div class="flex pl-[19px]"><div class="w-px h-3 bg-slate-300"></div></div>

            <!-- Sortable list -->
            <div id="sortable-steps" class="space-y-0">
                @foreach($workflow->steps as $idx => $step)
                @php
                    $encStepId = \Illuminate\Support\Facades\Crypt::encryptString($step->id);
                @endphp

                <div class="step-item" data-id="{{ $step->id }}">
                    <!-- Connector -->
                    @if($idx > 0)
                    <div class="flex pl-[19px]"><div class="w-px h-3 bg-slate-300"></div></div>
                    @endif

                    <!-- Card -->
                    <div class="bg-white rounded-lg border {{ $step->is_active ? 'border-slate-200' : 'border-slate-100 opacity-60' }} shadow-sm flex items-center gap-3 px-3 py-2.5 cursor-grab active:cursor-grabbing hover:border-gov-green/40 transition-colors group">

                        <!-- Drag handle + order -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <i class="fa-solid fa-grip-vertical text-slate-300 group-hover:text-slate-400 text-xs"></i>
                            <div class="w-6 h-6 rounded-full {{ $step->is_active ? 'bg-gov-green' : 'bg-slate-300' }} flex items-center justify-center text-white font-black text-[10px] flex-shrink-0 step-order-badge">
                                {{ $step->step_order }}
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-slate-900 text-xs truncate">{{ $step->step_name }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5 flex flex-wrap items-center gap-1">
                                @if($step->isMultiRole())
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-users mr-1 text-[8px]"></i> Parallel ({{ count($step->getAllRoleKeys()) }} Approvers · All Required)
                                    </span>
                                    <span class="text-slate-600 font-medium">
                                        @foreach($step->getAllRoleKeys() as $rk)
                                            <span class="inline-block bg-slate-100 text-slate-700 px-1 py-0.2 rounded text-[9px] mr-0.5">{{ $allRoles[$rk] ?? $rk }}</span>
                                        @endforeach
                                    </span>
                                @else
                                    <span class="text-gov-green font-semibold">{{ $step->role_name }}</span>
                                    <span class="mx-0.5">·</span>
                                    <span class="font-mono text-slate-400">{{ $step->role_key }}</span>
                                @endif
                            </div>
                            <!-- Capabilities inline -->
                            <div class="flex gap-1 mt-1">
                                @if($step->can_approve)
                                <span class="px-1.5 py-px bg-emerald-50 text-emerald-700 text-[9px] font-bold rounded border border-emerald-100">✓ Approve</span>
                                @endif
                                @if($step->can_reject)
                                <span class="px-1.5 py-px bg-rose-50 text-rose-700 text-[9px] font-bold rounded border border-rose-100">✗ Reject</span>
                                @endif
                                @if($step->can_return)
                                <span class="px-1.5 py-px bg-amber-50 text-amber-700 text-[9px] font-bold rounded border border-amber-100">↩ Return</span>
                                @endif
                                @if(!$step->is_active)
                                <span class="px-1.5 py-px bg-slate-100 text-slate-500 text-[9px] font-bold rounded">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.workflow_organogram.steps.edit', [$encWfId, $encStepId]) }}"
                               class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition-colors"
                               title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.workflow_organogram.steps.destroy', [$encWfId, $encStepId]) }}" method="POST"
                                  onsubmit="return confirm('Delete this step?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition-colors"
                                        title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- End node -->
            <div class="flex pl-[19px]"><div class="w-px h-3 bg-slate-300"></div></div>
            <div class="flex items-center gap-2 px-3">
                <div class="w-2 h-2 rounded-full bg-slate-700"></div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Final Decision / Licence Issued</span>
            </div>
            @endif
        </div>

        <!-- RIGHT: Add Step Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sticky top-4">
                <h4 class="text-xs font-bold text-slate-900 mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-plus-circle text-gov-green"></i> Add Step
                </h4>

                <form action="{{ route('admin.workflow_organogram.steps.store', $encWfId) }}" method="POST" class="space-y-3">
                    @csrf

                    <!-- Role Type Toggle -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Approver Type</label>
                        <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 rounded-lg text-center text-xs font-semibold">
                            <button type="button" id="btn_type_single" onclick="setApproverMode('single')"
                                    class="py-1 rounded bg-white text-gov-green shadow-xs transition-all">Single Role</button>
                            <button type="button" id="btn_type_multi" onclick="setApproverMode('multi')"
                                    class="py-1 rounded text-slate-600 hover:text-slate-900 transition-all">Multiple Roles (Parallel)</button>
                        </div>
                    </div>

                    <!-- Single Role Selection -->
                    <div id="single_role_container">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Role <span class="text-rose-500">*</span></label>
                        <select name="role_key" id="role_key_select"
                                class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none bg-white">
                            <option value="">— Select role —</option>
                            @foreach($allRoles as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Multi Role Selection (Checkboxes) -->
                    <div id="multi_role_container" class="hidden">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Assign Roles (Parallel Approvers) <span class="text-rose-500">*</span>
                        </label>
                        <div class="max-h-40 overflow-y-auto border border-slate-200 rounded-lg p-2 bg-slate-50 space-y-1.5">
                            @foreach($allRoles as $key => $label)
                            <label class="flex items-center gap-2 text-xs cursor-pointer hover:bg-white p-1 rounded transition-colors">
                                <input type="checkbox" name="role_keys[]" value="{{ $key }}" class="multi-role-cb rounded text-gov-green focus:ring-gov-green">
                                <span class="text-slate-700 font-medium">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-[9px] text-blue-600 mt-1 flex items-center gap-1 font-semibold">
                            <i class="fa-solid fa-circle-info"></i> All selected roles must approve before application moves to next step.
                        </p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Display Name</label>
                        <input type="text" name="role_name" id="role_name_input"
                               class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none"
                               placeholder="e.g. DC Front Desk">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Step Label <span class="text-rose-500">*</span></label>
                        <input type="text" name="step_name" required
                               class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none"
                               placeholder="e.g. DC Front Desk Verification">
                    </div>

                    <div class="space-y-1.5 pt-0.5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Permissions</p>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="can_approve" value="1" checked class="rounded text-gov-green focus:ring-gov-green">
                            <span class="text-xs font-semibold text-emerald-700">✓ Can Approve</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="can_reject" value="1" checked class="rounded text-gov-green focus:ring-gov-green">
                            <span class="text-xs font-semibold text-rose-700">✗ Can Reject</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="can_return" value="1" class="rounded text-gov-green focus:ring-gov-green">
                            <span class="text-xs font-semibold text-amber-700">↩ Can Return</span>
                        </label>
                    </div>

                    @if($errors->any())
                    <div class="text-rose-600 text-xs space-y-0.5">
                        @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
                    </div>
                    @endif

                    <button type="submit"
                            class="w-full py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors shadow-sm">
                        <i class="fa-solid fa-plus mr-1"></i> Add Step
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const roleLabels = @json($allRoles);

function setApproverMode(mode) {
    const singleContainer = document.getElementById('single_role_container');
    const multiContainer = document.getElementById('multi_role_container');
    const btnSingle = document.getElementById('btn_type_single');
    const btnMulti = document.getElementById('btn_type_multi');
    const singleSelect = document.getElementById('role_key_select');

    if (mode === 'single') {
        singleContainer.classList.remove('hidden');
        multiContainer.classList.add('hidden');
        btnSingle.className = 'py-1 rounded bg-white text-gov-green shadow-xs transition-all font-bold';
        btnMulti.className = 'py-1 rounded text-slate-600 hover:text-slate-900 transition-all font-semibold';
        singleSelect.setAttribute('required', 'required');
    } else {
        singleContainer.classList.add('hidden');
        multiContainer.classList.remove('hidden');
        btnSingle.className = 'py-1 rounded text-slate-600 hover:text-slate-900 transition-all font-semibold';
        btnMulti.className = 'py-1 rounded bg-white text-gov-green shadow-xs transition-all font-bold';
        singleSelect.removeAttribute('required');
    }
}

// Auto-fill role name on role select
document.getElementById('role_key_select').addEventListener('change', function () {
    const nameInput = document.getElementById('role_name_input');
    if (this.value && roleLabels[this.value]) {
        nameInput.value = roleLabels[this.value];
    }
});

// Auto-fill role name on multi-role checkboxes
document.querySelectorAll('.multi-role-cb').forEach(cb => {
    cb.addEventListener('change', function () {
        const checked = Array.from(document.querySelectorAll('.multi-role-cb:checked'))
            .map(c => roleLabels[c.value] || c.value);
        const nameInput = document.getElementById('role_name_input');
        if (checked.length > 0) {
            nameInput.value = checked.join(', ');
        }
    });
});

// Drag-and-drop reorder
const sortable = new Sortable(document.getElementById('sortable-steps'), {
    animation: 150,
    handle: '.fa-grip-vertical',
    ghostClass: 'opacity-50',
    onEnd: function () {
        // Collect new order (step data-id attributes)
        const items = document.querySelectorAll('#sortable-steps .step-item');
        const order = Array.from(items).map(el => el.dataset.id);

        // Update order badges visually
        items.forEach((el, i) => {
            const badge = el.querySelector('.step-order-badge');
            if (badge) badge.textContent = i + 1;
        });

        // Send to server
        fetch('{{ $reorderUrl }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ order }),
        }).then(res => res.json()).then(data => {
            if (data.success) {
                const toast = document.getElementById('reorder-toast');
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 2500);
            }
        });
    }
});
</script>
@endsection
