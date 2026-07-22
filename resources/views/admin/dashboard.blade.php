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
<div id="add-user-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900">Add New User</h3>
            <button onclick="document.getElementById('add-user-modal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-700 font-bold text-lg">✕</button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">Full Name</label>
                <input type="text" name="name" required
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green">
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">Email Address</label>
                <input type="email" name="email" required
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green">
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">Password</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green">
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">Role</label>
                <select name="role" required
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    @foreach($roles as $roleValue => $roleLabel)
                    <option value="{{ $roleValue }}">{{ $roleLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">District (optional)</label>
                <select name="district_id"
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <option value="">— Select District —</option>
                    @foreach($districts as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="w-full py-2.5 bg-gov-green hover:bg-gov-light text-white font-black text-xs rounded-lg transition-colors">
                Create User
            </button>
        </form>
    </div>
</div>
@endsection
