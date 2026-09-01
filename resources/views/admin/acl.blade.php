@extends('layouts.app')
@section('title', 'ACL / Role Permissions')

@section('content')
<div class="max-w-full space-y-5">

    <!-- Header Section (wrapped in ACL save form to include save button) -->
    <form method="POST" action="{{ route('admin.acl.save') }}" class="space-y-5">
        @csrf

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">Access Control List (ACL) & Role-Based Permissions</h2>
                <p class="text-xs text-slate-500 mt-1 font-medium">Super Admin &bull; fine-grained per-module permissions (BRS §5.13 &bull; FR-ADM-03)</p>
            </div>
            <button type="submit" class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-semibold text-xs rounded-lg transition-colors flex items-center space-x-1.5 shadow-sm self-start sm:self-auto">
                <span><i class="fa-solid fa-floppy-disk"></i></span><span>Save Matrix</span>
            </button>
        </div>

        <!-- Permissions Matrix Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="p-3 pl-5 text-[11px] font-semibold uppercase text-slate-500 tracking-wider w-44">Module</th>
                            @foreach($roles as $roleKey => $roleLabel)
                            <th class="p-3 text-[10px] font-semibold uppercase text-slate-500 tracking-wider text-center whitespace-nowrap">{{ $roleLabel }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($modules as $module)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3 pl-5 font-medium text-slate-700">{{ $module }}</td>
                            @foreach($roles as $roleKey => $roleLabel)
                            @php $perm = $matrix[$module][$roleKey] ?? 'none'; @endphp
                            <td class="p-3 text-center">
                                <input type="hidden" name="permissions[{{ $module }}][{{ $roleKey }}]" value="{{ $perm }}">
                                <button type="button" onclick="cyclePerm(this)" data-perm="{{ $perm }}"
                                        class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase border cursor-pointer
                                    @if($perm==='none') border-slate-200 text-slate-400 bg-slate-50
                                    @elseif($perm==='read') border-blue-200 text-blue-600 bg-blue-50
                                    @elseif($perm==='write') border-amber-200 text-amber-600 bg-amber-50
                                    @else border-emerald-200 text-emerald-600 bg-emerald-50 @endif">
                                    {{ strtoupper($perm) }}
                                </button>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
                <p class="text-[10px] text-slate-500 font-normal">Click a cell to cycle permission: none &rarr; read &rarr; write &rarr; approve. Emergency Kill-Switch requires two-admin sign-off.</p>
            </div>
        </div>
    </form>

    <!-- Role Management -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Role Management</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">System roles are read-only. Custom roles can be edited or deleted.</p>
            </div>
        </div>

        <!-- Role List -->
        <div class="divide-y divide-slate-50">
            @php
            $systemRoleList = [
                'citizen_applicant'  => 'Citizen Applicant',
                'dealer_applicant'   => 'Dealer Applicant',
                'dc_front_desk'      => 'DC Office — Front Desk',
                'dc_jm_branch'       => 'DC Office — JM Branch',
                'district_commissioner' => 'District Commissioner',
                'police_officer'     => 'Police Officer (SP/Thana)',
                'special_branch'     => 'Special Branch (SB)',
                'nsi_officer'        => 'NSI Officer',
                'dgfi_officer'       => 'DGFI Officer',
                'moha_desk'          => 'MoHA Desk',
                'joint_secretary'    => 'Joint Secretary',
                'senior_secretary'   => 'Senior Secretary',
                'system_admin'       => 'System Admin',
            ];
            $customRoleList = json_decode(\App\Models\Setting::get('custom_roles', '{}'), true) ?: [];
            @endphp

            @foreach($systemRoleList as $rk => $rl)
            <div class="flex items-center justify-between px-5 py-2.5">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 flex-shrink-0"></span>
                    <span class="text-xs font-semibold text-slate-800">{{ $rl }}</span>
                    <span class="font-mono text-[10px] text-slate-400">{{ $rk }}</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-slate-100 text-slate-500 border border-slate-200 flex-shrink-0">System</span>
            </div>
            @endforeach

            @foreach($customRoleList as $rk => $rl)
            <div class="flex items-center justify-between px-5 py-2.5 bg-amber-50/40">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                    <span class="text-xs font-semibold text-slate-800">{{ $rl }}</span>
                    <span class="font-mono text-[10px] text-slate-400">{{ $rk }}</span>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-amber-100 text-amber-700 border border-amber-200">Custom</span>
                    <form method="POST" action="{{ route('admin.acl.role.destroy') }}" onsubmit="return confirm('Delete role &quot;{{ $rl }}&quot;?')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="role_key" value="{{ $rk }}">
                        <button type="submit" class="w-6 h-6 flex items-center justify-center rounded bg-rose-50 hover:bg-rose-100 text-rose-500 text-[10px] transition-colors" title="Delete role">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Add Custom Role -->
        <div class="border-t border-slate-100 px-5 py-4 bg-slate-50">
            <form method="POST" action="{{ route('admin.acl.role.store') }}" class="flex gap-2 items-center">
                @csrf
                <input type="text" name="role_name" required placeholder="e.g. District Auditor"
                       class="flex-grow px-3 py-2 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-lg flex items-center gap-1.5 shadow-sm whitespace-nowrap transition-colors">
                    <i class="fa-solid fa-plus"></i> Add Role
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const perms = ['none','read','write','approve'];
const classes = {
    none: 'border-slate-200 text-slate-400 bg-slate-50',
    read: 'border-blue-200 text-blue-600 bg-blue-50',
    write: 'border-amber-200 text-amber-600 bg-amber-50',
    approve: 'border-emerald-200 text-emerald-600 bg-emerald-50',
};
function cyclePerm(btn) {
    const cur = btn.dataset.perm;
    const next = perms[(perms.indexOf(cur) + 1) % perms.length];
    btn.dataset.perm = next;
    btn.textContent = next.toUpperCase();
    btn.className = `px-2 py-0.5 rounded text-[10px] font-semibold uppercase border cursor-pointer ${classes[next]}`;

    // Update input value
    const input = btn.previousElementSibling;
    if (input && input.type === 'hidden') {
        input.value = next;
    }
}
</script>
@endsection
