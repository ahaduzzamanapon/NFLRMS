@extends('layouts.app')
@section('title', 'MoHA Desk')

@section('content')
<div class="max-w-5xl space-y-5">

    <div>
        <h2 class="text-2xl font-black font-serif text-slate-900">
            @if($user->role === \App\Enums\Role::MohaDesk) MoHA — Political-4 / Sasan-4 Desk
            @elseif($user->role === \App\Enums\Role::JointSecretary) MoHA — Joint / Additional Secretary
            @elseif($user->role === \App\Enums\Role::NationalScreeningCommittee) National Screening Committee
            @else MoHA — Senior Secretary / Minister
            @endif
        </h2>
        <p class="text-xs text-slate-500 mt-1">
            {{ $applications->count() }} case(s) awaiting your action &bull; Handgun / Renewal / Dealing
        </p>
    </div>

    <!-- Approval Chain Banner -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4">
        <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest mb-3">Three-tier MoHA approval chain</div>
        <div class="flex items-center space-x-2 text-xs font-bold">
            @php
                $chain = [
                    ['label' => 'Political-4 / Sasan-4', 'role' => \App\Enums\Role::MohaDesk],
                    ['label' => 'Joint / Additional Secretary', 'role' => \App\Enums\Role::JointSecretary],
                    ['label' => 'National Screening Committee', 'role' => \App\Enums\Role::NationalScreeningCommittee],
                    ['label' => 'Senior Secretary / Minister', 'role' => \App\Enums\Role::SeniorSecretary],
                ];
            @endphp
            @foreach($chain as $i => $step)
                <span class="px-3 py-1.5 rounded-lg text-xs font-black
                    {{ $user->role === $step['role'] ? 'bg-gov-gold text-slate-950' : 'bg-slate-100 text-slate-600' }}">
                    {{ $step['label'] }}
                </span>
                @if($i < count($chain) - 1)
                <span class="text-slate-400">→</span>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">
                    <th class="p-3 pl-5">Reference</th>
                    <th class="p-3">Applicant</th>
                    <th class="p-3">Service</th>
                    <th class="p-3">District</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 pr-5 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($applications as $a)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5 font-bold font-mono text-gov-green text-[10px]">{{ $a->application_number }}</td>
                    <td class="p-3">
                        <div class="font-bold text-slate-900">{{ $a->user->name }}</div>
                        <div class="text-[9px] text-slate-400">NID {{ $a->user->nid ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3 font-semibold text-slate-700">
                        {{ ucfirst(str_replace('_',' ',$a->type)) }} &bull; {{ $a->firearm_details['weapon_type'] ?? 'N/A' }}
                    </td>
                    <td class="p-3 text-slate-600">{{ $a->district->name ?? 'N/A' }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border border-amber-200 bg-amber-50 text-amber-700">
                            {{ ucfirst(str_replace('_',' ',$a->status)) }}
                        </span>
                    </td>
                    <td class="p-3 pr-5 text-right">
                        <a href="{{ route('moha.show', $a->id) }}"
                           class="text-xs font-black text-gov-green hover:underline">Open →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-slate-400 font-bold">No cases at your stage.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
