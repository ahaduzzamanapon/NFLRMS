@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="max-w-xl space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-slate-400 hover:text-gov-green transition-colors">← Back</a>
        <h2 class="text-2xl font-black font-serif text-slate-900">Edit User</h2>
    </div>

    @if($errors->any())
    <div class="px-4 py-3 bg-rose-50 border border-rose-200 rounded-xl text-xs font-semibold text-rose-700">
        <ul class="list-disc pl-4 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green transition-all">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">Role</label>
                    <select name="role" required
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                        @foreach($roles as $roleValue => $roleLabel)
                        @php
                            $userRoleVal = $user->role instanceof \App\Enums\Role ? $user->role->value : $user->role;
                        @endphp
                        <option value="{{ $roleValue }}" {{ $userRoleVal === $roleValue ? 'selected' : '' }}>{{ $roleLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">District (optional)</label>
                    <select name="district_id"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                        <option value="">— None —</option>
                        @foreach($districts as $d)
                        <option value="{{ $d->id }}" {{ $user->district_id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">
                    New Password <span class="text-slate-300 font-normal normal-case">(leave blank to keep current)</span>
                </label>
                <input type="password" name="password" minlength="8" placeholder="Min 8 characters"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green transition-all">
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-4 py-2.5 border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-gov-green hover:bg-gov-light text-white font-black text-xs rounded-xl transition-colors shadow-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-2xl border border-rose-200 shadow-sm p-5">
        <div class="text-xs font-black text-rose-700 mb-1">Danger Zone</div>
        <p class="text-[10px] text-slate-400 mb-3">This action is irreversible. All data for this user will be permanently deleted.</p>
        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
              onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl transition-colors">
                Delete User
            </button>
        </form>
    </div>
</div>
@endsection
