@extends('layouts.app')
@section('title', 'Role Management')

@section('content')
<div class="w-full space-y-4">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Role Management</h2>
            <p class="text-xs text-slate-500 mt-0.5">System roles are read-only. You can add, edit, and delete custom roles.</p>
        </div>
        <button onclick="document.getElementById('add-role-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg flex items-center gap-1.5 shadow-sm transition-colors">
            <i class="fa-solid fa-plus"></i> Add Role
        </button>
    </div>

    @if(session('success'))
    <div class="px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-700">
        <i class="fa-solid fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="px-4 py-2.5 bg-rose-50 border border-rose-200 rounded-lg text-xs font-bold text-rose-700">
        <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Role Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 w-8">#</th>
                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Role Name</th>
                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Key</th>
                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500">Type</th>
                    <th class="px-5 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">

                {{-- System Roles --}}
                @foreach($systemRoles as $key => $name)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-5 py-3 text-slate-400 font-mono">{{ $loop->iteration }}</td>
                    <td class="px-5 py-3 font-semibold text-slate-800">{{ $name }}</td>
                    <td class="px-5 py-3">
                        <span class="font-mono text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded">{{ $key }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-slate-100 text-slate-500 border border-slate-200">System</span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <span class="text-[10px] text-slate-300 italic">Read-only</span>
                    </td>
                </tr>
                @endforeach

                {{-- Custom Roles --}}
                @foreach($customRoles as $key => $name)
                <tr class="hover:bg-amber-50/30" id="row-{{ $loop->iteration }}">
                    <td class="px-5 py-3 text-slate-400 font-mono">{{ count($systemRoles) + $loop->iteration }}</td>

                    {{-- View mode --}}
                    <td class="px-5 py-3 font-semibold text-slate-800 view-cell" data-key="{{ $key }}">{{ $name }}</td>
                    <td class="px-5 py-3">
                        <span class="font-mono text-[10px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded border border-amber-100">{{ $key }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-amber-100 text-amber-700 border border-amber-200">Custom</span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex justify-end gap-1.5 view-actions" data-key="{{ $key }}" data-name="{{ $name }}">
                            <button onclick="startEdit('{{ $key }}', '{{ addslashes($name) }}')"
                                    class="px-2.5 py-1.5 text-[10px] font-bold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                                <i class="fa-solid fa-pen mr-0.5"></i> Edit
                            </button>
                            <form action="{{ route('admin.roles.destroy', $key) }}" method="POST"
                                  onsubmit="return confirm('Delete role &quot;{{ $name }}&quot;? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 text-[10px] font-bold rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors">
                                    <i class="fa-solid fa-trash mr-0.5"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach

                @if(count($customRoles) === 0 && count($systemRoles) === 0)
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-xs">No roles found.</td>
                </tr>
                @endif

            </tbody>
        </table>

        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
            <p class="text-[10px] text-slate-400">
                {{ count($systemRoles) }} system roles &bull; {{ count($customRoles) }} custom roles
            </p>
            <a href="{{ route('admin.acl') }}" class="text-[10px] font-bold text-gov-green hover:text-gov-light">
                <i class="fa-solid fa-key mr-0.5"></i> Manage Permissions →
            </a>
        </div>
    </div>

</div>

<!-- ===== INLINE EDIT FORM (hidden, injected by JS) ===== -->
<form id="edit-form" method="POST" style="display:none">
    @csrf @method('PUT')
    <input type="text" name="role_name" id="edit-input">
</form>

<!-- ===== ADD ROLE MODAL ===== -->
<div id="add-role-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closeAddModal()">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-base">Add Custom Role</h3>
            <button onclick="closeAddModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.roles.store') }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Role Name <span class="text-rose-500">*</span></label>
                <input type="text" name="role_name" id="add-role-name" required
                       placeholder="e.g. District Auditor"
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none">
                <p class="text-[10px] text-slate-400 mt-1">Key will be auto-generated from the name.</p>
                @error('role_name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2 pt-1">
                <button type="submit"
                        class="flex-1 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-xl transition-colors shadow-sm">
                    <i class="fa-solid fa-plus mr-1"></i> Create Role
                </button>
                <button type="button" onclick="closeAddModal()"
                        class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
<script>document.getElementById('add-role-modal').classList.remove('hidden');</script>
@endif

@endsection

@section('scripts')
<script>
function closeAddModal() {
    document.getElementById('add-role-modal').classList.add('hidden');
}

// Inline edit: replace cell content with input + save/cancel
function startEdit(key, currentName) {
    // Restore any previous inline edit
    document.querySelectorAll('.editing-cell').forEach(el => el.remove());
    document.querySelectorAll('.was-hidden').forEach(el => {
        el.classList.remove('was-hidden', 'hidden');
    });

    const viewCell = document.querySelector(`.view-cell[data-key="${key}"]`);
    const actionsDiv = document.querySelector(`.view-actions[data-key="${key}"]`);
    const row = viewCell.closest('tr');

    // Hide original content
    viewCell.classList.add('hidden', 'was-hidden');
    actionsDiv.classList.add('hidden', 'was-hidden');

    // Inject inline input cell
    const td1 = document.createElement('td');
    td1.className = 'px-5 py-2 editing-cell';
    td1.innerHTML = `
        <input id="inline-input-${key}"
               class="px-2.5 py-1.5 text-xs rounded-lg border border-gov-green ring-1 ring-gov-green outline-none w-full max-w-xs"
               value="${currentName}">
    `;

    // Inject save/cancel actions
    const td2 = document.createElement('td');
    td2.className = 'px-5 py-2 text-right editing-cell';
    td2.innerHTML = `
        <div class="flex justify-end gap-1.5">
            <button onclick="saveEdit('${key}')"
                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg bg-gov-green hover:bg-gov-light text-white transition-colors">
                <i class="fa-solid fa-check mr-0.5"></i> Save
            </button>
            <button onclick="cancelEdit()"
                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
                Cancel
            </button>
        </div>
    `;

    viewCell.after(td1);
    // Find original actions td (the last td in the row) and insert before it
    const lastTd = row.lastElementChild;
    row.insertBefore(td2, lastTd);

    document.getElementById(`inline-input-${key}`).focus();
}

function cancelEdit() {
    document.querySelectorAll('.editing-cell').forEach(el => el.remove());
    document.querySelectorAll('.was-hidden').forEach(el => {
        el.classList.remove('was-hidden', 'hidden');
    });
}

function saveEdit(key) {
    const input = document.getElementById(`inline-input-${key}`);
    if (!input || !input.value.trim()) return;

    const form = document.getElementById('edit-form');
    form.action = `/admin/roles/${key}`;
    document.getElementById('edit-input').value = input.value.trim();
    form.submit();
}
</script>
@endsection
