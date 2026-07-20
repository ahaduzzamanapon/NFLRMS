@extends('layouts.app')
@section('title', 'ACL / Role Permissions')

@section('content')
<div class="max-w-6xl space-y-5">

    <!-- Header Section (wrapped in ACL save form to include save button) -->
    <form method="POST" action="{{ route('admin.acl.save') }}" class="space-y-5">
        @csrf

        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-black font-serif text-slate-900">Access Control List (ACL) & Role-Based Permissions</h2>
                <p class="text-xs text-slate-500 mt-1">Super Admin &bull; fine-grained per-module permissions (BRS §5.13 &bull; FR-ADM-03)</p>
            </div>
            <button type="submit" class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors flex items-center space-x-1.5 shadow-sm">
                <span>💾</span><span>Save Matrix</span>
            </button>
        </div>

        <!-- Permissions Matrix Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="p-3 pl-5 text-[10px] font-extrabold uppercase text-slate-500 tracking-wider w-44">Module</th>
                            @foreach($roles as $roleKey => $roleLabel)
                            <th class="p-3 text-[9px] font-extrabold uppercase text-slate-500 tracking-wider text-center whitespace-nowrap">{{ $roleLabel }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($modules as $module)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3 pl-5 font-semibold text-slate-700">{{ $module }}</td>
                            @foreach($roles as $roleKey => $roleLabel)
                            @php $perm = $matrix[$module][$roleKey] ?? 'none'; @endphp
                            <td class="p-3 text-center">
                                <input type="hidden" name="permissions[{{ $module }}][{{ $roleKey }}]" value="{{ $perm }}">
                                <button type="button" onclick="cyclePerm(this)" data-perm="{{ $perm }}"
                                        class="px-2 py-0.5 rounded text-[9px] font-black uppercase border cursor-pointer
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
                <p class="text-[9px] text-slate-500">Click a cell to cycle permission: none &rarr; read &rarr; write &rarr; approve. Emergency Kill-Switch requires two-admin sign-off.</p>
            </div>
        </div>
    </form>

    <!-- Create Custom Role Section -->
    <form method="POST" action="{{ route('admin.acl.role.store') }}" class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        @csrf
        <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-2">Create Custom Role</label>
        <div class="flex items-center space-x-3">
            <input type="text" name="role_name" required placeholder="e.g. District Auditor"
                   class="flex-grow px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
            <button type="submit" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-black text-xs rounded-lg flex items-center space-x-1">
                <span>+</span><span>Add role</span>
            </button>
            @foreach(['none','read','write','approve'] as $perm)
            <span class="px-2.5 py-1 rounded border text-[9px] font-black uppercase
                @if($perm==='none') border-slate-300 text-slate-500
                @elseif($perm==='read') border-blue-300 text-blue-600
                @elseif($perm==='write') border-amber-300 text-amber-600
                @else border-emerald-300 text-emerald-600 @endif">
                {{ $perm }}
            </span>
            @endforeach
        </div>
    </form>

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
    btn.className = `px-2 py-0.5 rounded text-[9px] font-black uppercase border cursor-pointer ${classes[next]}`;
    
    // Update input value
    const input = btn.previousElementSibling;
    if (input && input.type === 'hidden') {
        input.value = next;
    }
}
</script>
@endsection
