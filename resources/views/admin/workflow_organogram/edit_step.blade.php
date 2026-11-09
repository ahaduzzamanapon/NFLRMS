@extends('layouts.app')
@section('title', 'Edit Step — Step ' . $step->step_order)

@section('content')
<div class="w-full max-w-lg space-y-4">

    <div>
        <div class="text-xs text-slate-400 mb-0.5">
            <a href="{{ route('admin.workflow_organogram.index') }}" class="hover:text-gov-green">Workflow Config</a>
            <span class="mx-1">›</span>
            <a href="{{ route('admin.workflow_organogram.show', $encryptedWfId) }}" class="hover:text-gov-green">{{ $workflow->name }}</a>
            <span class="mx-1">›</span>
            <span class="text-slate-600">Edit Step</span>
        </div>
        <h2 class="text-xl font-bold text-slate-900">Edit Step — Step {{ $step->step_order }}</h2>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.workflow_organogram.steps.update', [$encryptedWfId, $encryptedStepId]) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            @php
                $isMulti = $step->isMultiRole();
                $currentKeys = $step->getAllRoleKeys();
            @endphp

            <!-- Role Type Toggle -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Approver Type</label>
                <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 rounded-lg text-center text-xs font-semibold max-w-xs">
                    <button type="button" id="btn_type_single" onclick="setApproverMode('single')"
                            class="py-1 rounded {{ !$isMulti ? 'bg-white text-gov-green shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold' }} transition-all">Single Role</button>
                    <button type="button" id="btn_type_multi" onclick="setApproverMode('multi')"
                            class="py-1 rounded {{ $isMulti ? 'bg-white text-gov-green shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold' }} transition-all">Multiple Roles (Parallel)</button>
                </div>
            </div>

            <!-- Single Role Selection -->
            <div id="single_role_container" class="{{ $isMulti ? 'hidden' : '' }}">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Role <span class="text-rose-500">*</span></label>
                <select name="role_key" id="edit_role_key" {{ !$isMulti ? 'required' : '' }}
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none bg-white">
                    @foreach($allRoles as $key => $label)
                    <option value="{{ $key }}" {{ old('role_key', $step->role_key) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Multi Role Selection (Checkboxes) -->
            <div id="multi_role_container" class="{{ !$isMulti ? 'hidden' : '' }}">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Assign Roles (Parallel Approvers) <span class="text-rose-500">*</span>
                </label>
                <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-2.5 bg-slate-50 space-y-1.5">
                    @foreach($allRoles as $key => $label)
                    <label class="flex items-center gap-2 text-xs cursor-pointer hover:bg-white p-1 rounded transition-colors">
                        <input type="checkbox" name="role_keys[]" value="{{ $key }}"
                               {{ in_array($key, old('role_keys', $currentKeys)) ? 'checked' : '' }}
                               class="edit-multi-role-cb rounded text-gov-green focus:ring-gov-green">
                        <span class="text-slate-700 font-medium">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <p class="text-[10px] text-blue-600 mt-1 flex items-center gap-1 font-semibold">
                    <i class="fa-solid fa-circle-info"></i> All selected roles must approve before application advances to next step.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Display Name <span class="text-rose-500">*</span></label>
                <input type="text" name="role_name" id="edit_role_name" value="{{ old('role_name', $step->role_name) }}" required
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Step Label <span class="text-rose-500">*</span></label>
                <input type="text" name="step_name" value="{{ old('step_name', $step->step_name) }}" required
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none">
            </div>

            <div class="space-y-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-700">Permissions</p>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="can_approve" value="1" {{ $step->can_approve ? 'checked' : '' }} class="rounded text-gov-green focus:ring-gov-green">
                    <span class="text-xs font-semibold text-emerald-700">✓ Can Approve</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="can_reject" value="1" {{ $step->can_reject ? 'checked' : '' }} class="rounded text-gov-green focus:ring-gov-green">
                    <span class="text-xs font-semibold text-rose-700">✗ Can Reject</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="can_return" value="1" {{ $step->can_return ? 'checked' : '' }} class="rounded text-gov-green focus:ring-gov-green">
                    <span class="text-xs font-semibold text-amber-700">↩ Can Return</span>
                </label>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $step->is_active ? 'checked' : '' }} class="rounded text-gov-green focus:ring-gov-green">
                <span class="text-xs font-semibold text-slate-700">Active</span>
            </label>

            @if($errors->any())
            <div class="text-rose-600 text-xs space-y-0.5">
                @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
            </div>
            @endif

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-save mr-1"></i> Update Step
                </button>
                <a href="{{ route('admin.workflow_organogram.show', $encryptedWfId) }}"
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const roleLabels = @json($allRoles);

function setApproverMode(mode) {
    const singleContainer = document.getElementById('single_role_container');
    const multiContainer = document.getElementById('multi_role_container');
    const btnSingle = document.getElementById('btn_type_single');
    const btnMulti = document.getElementById('btn_type_multi');
    const singleSelect = document.getElementById('edit_role_key');

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

document.getElementById('edit_role_key').addEventListener('change', function () {
    const nameInput = document.getElementById('edit_role_name');
    if (this.value && roleLabels[this.value]) {
        nameInput.value = roleLabels[this.value];
    }
});

document.querySelectorAll('.edit-multi-role-cb').forEach(cb => {
    cb.addEventListener('change', function () {
        const checked = Array.from(document.querySelectorAll('.edit-multi-role-cb:checked'))
            .map(c => roleLabels[c.value] || c.value);
        const nameInput = document.getElementById('edit_role_name');
        if (checked.length > 0) {
            nameInput.value = checked.join(', ');
        }
    });
});
</script>
@endsection
