@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="max-w-5xl space-y-5">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black font-serif text-slate-900">User Management</h2>
            <p class="text-xs text-slate-500 mt-1">Create, deactivate & reassign accounts — no deployment required (FR-ADM-01)</p>
        </div>
        <button onclick="document.getElementById('add-user-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg flex items-center space-x-1.5 shadow-sm transition-colors">
            <span>+</span><span>Add User</span>
        </button>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-700">✓ {{ session('success') }}</div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">
                    <th class="p-3 pl-5">Name</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Unit / Office</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 pr-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @foreach($users as $u)
                @php
                    $words = explode(' ', $u->name);
                    $initials = count($words) >= 2 ? strtoupper(substr($words[0],0,1).substr($words[1],0,1)) : strtoupper(substr($u->name,0,2));
                    $roleVal = $u->role instanceof \App\Enums\Role ? $u->role->value : $u->role;
                    $unit = match(true) {
                        in_array($roleVal, ['dc_front_desk','dc_jm_branch','district_commissioner']) => ($u->district->name ?? 'Dhaka') . ' DC Office',
                        $roleVal === 'police_officer' => 'Thana ' . ($u->upazila->name ?? 'HQ'),
                        $roleVal === 'special_branch' => 'SB ' . ($u->district->name ?? 'Dhaka') . ' Zone',
                        $roleVal === 'nsi_officer' => 'NSI HQ',
                        $roleVal === 'dgfi_officer' => 'DGFI Liaison',
                        $roleVal === 'moha_desk' => 'Political-4 Branch',
                        $roleVal === 'joint_secretary' => 'Joint Secretary Office',
                        $roleVal === 'senior_secretary' => 'Ministry of Home Affairs',
                        $roleVal === 'system_admin' => 'System Operations',
                        default => $u->district->name ?? 'National',
                    };
                    $isActive = $u->is_active ?? true;
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-[10px] font-black text-slate-600 flex items-center justify-center border border-slate-200/50 flex-shrink-0">
                                {{ $initials }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                <div class="text-[9px] text-slate-400">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-3 font-semibold text-slate-600">{{ $u->roleLabel() }}</td>
                    <td class="p-3 text-slate-500 font-medium">{{ $unit }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase
                            {{ $isActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="p-3 pr-5 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.users.edit', $u->id) }}"
                               class="text-[10px] font-black text-blue-500 hover:underline">Edit</a>

                            <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="text-[10px] font-black {{ $isActive ? 'text-amber-500 hover:underline' : 'text-gov-green hover:underline' }}">
                                    {{ $isActive ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            @if($u->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete {{ addslashes($u->name) }}? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] font-black text-rose-500 hover:underline">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id="add-user-modal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-black/50 flex items-start md:items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md my-8 md:my-0 max-h-[90vh] flex flex-col overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
            <h3 class="text-sm font-black text-slate-900">Add New User</h3>
            <button onclick="document.getElementById('add-user-modal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto" id="addUserForm" novalidate>
            @csrf

            <!-- Validation Summary Alert -->
            <div id="addUserValidationAlert" class="hidden bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 9v4m0 4h.01M10.29 3.86l-8.18 14.18A2 2 0 0 0 3.82 21h16.36a2 2 0 0 0 1.71-2.96L13.71 3.86a2 2 0 0 0-3.42 0z"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-xs font-bold text-rose-700 leading-relaxed">
                    Please fill in the highlighted required field(s) above before continuing.
                </span>
            </div>

            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">Full Name</label>
                <input type="text" name="name" id="new_user_name" required minlength="2"
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('name') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green"
                       value="{{ old('name') }}">
                <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error" data-for="name"></span>
                @error('name')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">Email Address</label>
                <input type="email" name="email" id="new_user_email" required
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('email') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green"
                       value="{{ old('email') }}">
                <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error" data-for="email"></span>
                @error('email')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">Password</label>
                <input type="password" name="password" id="new_user_password" required minlength="8"
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('password') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green">
                <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error" data-for="password"></span>
                @error('password')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">Role</label>
                <select name="role" id="new_user_role" required
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('role') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <option value="">— Select Role —</option>
                    @foreach($roles as $roleValue => $roleLabel)
                    <option value="{{ $roleValue }}" {{ old('role')==$roleValue ? 'selected' : '' }}>{{ $roleLabel }}</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error" data-for="role"></span>
                @error('role')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">District (optional)</label>
                <select name="district_id"
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('district_id') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <option value="">— Select District —</option>
                    @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ old('district_id')==$d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
                @error('district_id')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
            </div>
            <button type="submit"
                    class="w-full py-2.5 bg-gov-green hover:bg-gov-light text-white font-black text-xs rounded-lg transition-colors">
                Create User
            </button>
        </form>
    </div>
</div>

<script>
    (function () {
        const form = document.getElementById('addUserForm');
        if (!form) return;

        function showError(fieldName, message) {
            const span = form.querySelector(`.js-error[data-for="${fieldName}"]`);
            if (span) span.textContent = message || '';
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) input.classList.toggle('border-rose-400', !!message);
        }

        function clearAllErrors() {
            form.querySelectorAll('.js-error').forEach(s => s.textContent = '');
            form.querySelectorAll('input, select').forEach(el => el.classList.remove('border-rose-400'));
        }

        function maybeHideAlert() {
            const alertBox = document.getElementById('addUserValidationAlert');
            const hasVisibleError = Array.from(form.querySelectorAll('.js-error')).some(s => s.textContent.trim() !== '');
            if (!hasVisibleError) alertBox?.classList.add('hidden');
        }

        function validateField(input, message) {
            if (!input.checkValidity()) {
                showError(input.name, message);
                return false;
            }
            showError(input.name, '');
            return true;
        }

        function validateForm() {
            clearAllErrors();
            let valid = true;

            valid = validateField(document.getElementById('new_user_name'), 'Full name must be at least 2 characters.') && valid;
            valid = validateField(document.getElementById('new_user_email'), 'Enter a valid email address.') && valid;
            valid = validateField(document.getElementById('new_user_password'), 'Password must be at least 8 characters.') && valid;
            valid = validateField(document.getElementById('new_user_role'), 'Please select a role.') && valid;

            return valid;
        }

        // Live validation on blur/change
        // ['new_user_name', 'new_user_email', 'new_user_password'].forEach(id => {
        //     const el = document.getElementById(id);
        //     el?.addEventListener('blur', () => {
        //         const messages = {
        //             new_user_name: 'Full name must be at least 2 characters.',
        //             new_user_email: 'Enter a valid email address.',
        //             new_user_password: 'Password must be at least 8 characters.',
        //         };
        //         validateField(el, messages[id]);
        //         maybeHideAlert();
        //     });
        // });
        document.getElementById('new_user_role')?.addEventListener('change', function () {
            showError('role', '');
            maybeHideAlert();
        });

        form.addEventListener('submit', function (e) {
            const alertBox = document.getElementById('addUserValidationAlert');
            if (!validateForm()) {
                e.preventDefault();
                alertBox?.classList.remove('hidden');
                const firstError = form.querySelector('.js-error:not(:empty)');
                firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alertBox?.classList.add('hidden');
            }
        });
    })();
</script>
@endsection
