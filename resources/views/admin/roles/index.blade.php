@extends('layouts.app')
@section('title', 'Role Management')

@section('content')
<div class="w-full space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900">Role Management</h2>
            <p class="text-xs text-slate-500 mt-1">Manage system and custom roles — system roles are read-only (FR-ADM-02)</p>
        </div>
        <button onclick="document.getElementById('add-role-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg flex items-center space-x-1.5 shadow-sm transition-colors self-start sm:self-auto">
            <span><i class="fa-solid fa-plus"></i></span><span>Add Role</span>
        </button>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-700">
        <i class="fa-solid fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="px-4 py-3 bg-rose-50 border border-rose-200 rounded-lg text-xs font-bold text-rose-700">
        <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Role Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[500px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold uppercase text-slate-500 tracking-wider">
                    <th class="p-3 pl-5">#</th>
                    <th class="p-3">Role Name</th>
                    <th class="p-3">Key</th>
                    <th class="p-3">Type</th>
                    <th class="p-3 pr-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">

                {{-- System Roles --}}
                @foreach($systemRoles as $key => $name)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5 text-slate-400 font-mono text-[10px]">{{ $loop->iteration }}</td>
                    <td class="p-3 font-semibold text-slate-900">{{ $name }}</td>
                    <td class="p-3">
                        <span class="font-mono text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded">{{ $key }}</span>
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-50 text-slate-500 border border-slate-200">System</span>
                    </td>
                    <td class="p-3 pr-5 text-right text-[11px] text-slate-300 italic">Read-only</td>
                </tr>
                @endforeach

                {{-- Custom Roles --}}
                @foreach($customRoles as $key => $name)
                <tr class="hover:bg-slate-50/50 transition-colors" id="role-row-{{ $loop->index }}">
                    <td class="p-3 pl-5 text-slate-400 font-mono text-[10px]">{{ count($systemRoles) + $loop->iteration }}</td>

                    <td class="p-3 font-semibold text-slate-900" id="role-name-cell-{{ $key }}">{{ $name }}</td>

                    <td class="p-3">
                        <span class="font-mono text-[10px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded border border-amber-100">{{ $key }}</span>
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">Custom</span>
                    </td>
                    <td class="p-3 pr-5 text-right">
                        <div class="flex items-center justify-end gap-3" id="role-actions-{{ $key }}">
                            <button onclick="startInlineEdit('{{ $key }}', '{{ addslashes($name) }}')"
                                    class="text-[11px] font-semibold text-blue-500 hover:underline">Edit</button>

                            <form action="{{ route('admin.roles.destroy', $key) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete role &quot;{{ $name }}&quot;? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[11px] font-semibold text-rose-500 hover:underline">Delete</button>
                            </form>
                        </div>

                        {{-- Inline edit actions (hidden by default) --}}
                        <div class="hidden items-center justify-end gap-3" id="role-edit-actions-{{ $key }}">
                            <button onclick="saveInlineEdit('{{ $key }}')"
                                    class="text-[11px] font-semibold text-gov-green hover:underline">Save</button>
                            <button onclick="cancelInlineEdit('{{ $key }}', '{{ addslashes($name) }}')"
                                    class="text-[11px] font-semibold text-slate-400 hover:underline">Cancel</button>
                        </div>
                    </td>
                </tr>
                @endforeach

                @if(count($systemRoles) === 0 && count($customRoles) === 0)
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-slate-400 text-xs">No roles found.</td>
                </tr>
                @endif

            </tbody>
        </table>

        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
            <p class="text-[10px] text-slate-400">
                {{ count($systemRoles) }} system roles &bull; {{ count($customRoles) }} custom roles
            </p>
            <a href="{{ route('admin.acl') }}" class="text-[11px] font-semibold text-gov-green hover:underline">
                Manage Permissions →
            </a>
        </div>
    </div>
</div>

<!-- Hidden PUT form for inline edit submit -->
<form id="inline-edit-form" method="POST" style="display:none">
    @csrf @method('PUT')
    <input type="text" name="role_name" id="inline-edit-value">
</form>

<!-- Add Role Modal -->
<div id="add-role-modal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-black/50 flex items-start md:items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm my-8 md:my-0 flex flex-col overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
            <h3 class="text-sm font-bold text-slate-900">Add Custom Role</h3>
            <button onclick="document.getElementById('add-role-modal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-700 font-bold text-lg"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.roles.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Role Name <span class="text-rose-500">*</span></label>
                <input type="text" name="role_name" required autofocus
                       value="{{ old('role_name') }}"
                       placeholder="e.g. District Auditor"
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                <p class="text-[10px] text-slate-400 mt-1">Role key will be auto-generated from the name.</p>
                @error('role_name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-plus mr-1"></i> Add Role
                </button>
                <button type="button" onclick="document.getElementById('add-role-modal').classList.add('hidden')"
                        class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function startInlineEdit(key, currentName) {
    const cell = document.getElementById(`role-name-cell-${key}`);
    cell.innerHTML = `<input id="inline-input-${key}"
        class="px-2.5 py-1.5 text-xs rounded-lg border border-gov-green ring-1 ring-gov-green outline-none w-full max-w-[220px]"
        value="${currentName}">`;

    document.getElementById(`role-actions-${key}`).classList.add('hidden');
    const editActions = document.getElementById(`role-edit-actions-${key}`);
    editActions.classList.remove('hidden');
    editActions.classList.add('flex');

    document.getElementById(`inline-input-${key}`).focus();
}

function cancelInlineEdit(key, originalName) {
    document.getElementById(`role-name-cell-${key}`).textContent = originalName;
    document.getElementById(`role-actions-${key}`).classList.remove('hidden');
    const editActions = document.getElementById(`role-edit-actions-${key}`);
    editActions.classList.add('hidden');
    editActions.classList.remove('flex');
}

function saveInlineEdit(key) {
    const input = document.getElementById(`inline-input-${key}`);
    if (!input || !input.value.trim()) return;

    const form = document.getElementById('inline-edit-form');
    form.action = `/admin/roles/${key}`;
    document.getElementById('inline-edit-value').value = input.value.trim();
    form.submit();
}
</script>
@endsection
