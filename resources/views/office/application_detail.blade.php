@extends('layouts.app')
@section('title', 'Case Detail')

@section('content')
@php
    $role = auth()->user()->role->value;
    $backRoute = match(true) {
        $role === 'dc_front_desk'       => route('front_desk.dashboard'),
        $role === 'dc_jm_branch'        => route('jm_branch.dashboard'),
        $role === 'district_commissioner' => route('dc.dashboard'),
        in_array($role, ['moha_desk','joint_secretary','senior_secretary','national_screening_committee']) => route('moha.dashboard'),
        default => url()->previous(),
    };
    $actionRoute = match(true) {
        $role === 'dc_front_desk'       => route('front_desk.action', $application->id),
        $role === 'dc_jm_branch'        => route('jm_branch.action', $application->id),
        $role === 'district_commissioner' => route('dc.action', $application->id),
        in_array($role, ['moha_desk','joint_secretary','senior_secretary','national_screening_committee']) => route('moha.action', $application->id),
        default => '#',
    };
    $actions = match(true) {
        $role === 'dc_front_desk'       => ['forward' => 'Accept & Screen', 'reject' => 'Reject'],
        $role === 'dc_jm_branch'        => ['trigger_vetting' => 'Trigger Vetting', 'forward_dc' => 'Forward to DC', 'reject' => 'Reject'],
        $role === 'district_commissioner' => ['approve' => 'Approve & Issue License', 'forward_moha' => 'Refer to MoHA', 'reject' => 'Reject'],
        in_array($role, ['moha_desk','joint_secretary','national_screening_committee']) => ['forward' => 'Forward Up', 'reject' => 'Reject'],
        $role === 'senior_secretary'    => ['approve' => 'Final Approve & Issue', 'reject' => 'Reject'],
        default => [],
    };
@endphp

<div class="max-w-5xl space-y-5">
    <!-- Back + Title -->
    <div>
        <a href="{{ $backRoute }}" class="text-[10px] font-extrabold text-slate-400 hover:text-gov-green flex items-center space-x-1 mb-3">
            <span>←</span><span>Back to queue</span>
        </a>
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-black font-serif text-slate-900">Case {{ $application->application_number }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ ucfirst(str_replace('_', ' ', $application->type)) }} &bull;
                    {{ $application->firearm_details['weapon_type'] ?? 'N/A' }} &bull;
                    {{ $application->user->name }}
                </p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase border
                @if(in_array($application->status, ['approved','license_issued','vetted_cleared'])) border-emerald-500/30 bg-emerald-50 text-emerald-700
                @elseif(str_contains($application->status,'rejected') || $application->status === 'vetted_flagged') border-rose-500/30 bg-rose-50 text-rose-700
                @else border-amber-500/30 bg-amber-50 text-amber-700 @endif">
                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Left: Application Info -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Applicant & Application -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Applicant & Application</span>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Name</span>
                        <span class="font-bold text-slate-900">{{ $application->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">NID</span>
                        <span class="font-bold text-slate-900">{{ $application->applicant_details['nid'] ?? $application->user->nid ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Date of Birth</span>
                        <span class="font-bold text-slate-900">
                            @php
                                $dobVal = $application->applicant_details['dob'] ?? $application->user->dob ?? null;
                            @endphp
                            {{ $dobVal ? (\Illuminate\Support\Carbon::parse($dobVal)->format('Y-m-d')) : 'N/A' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Phone</span>
                        <span class="font-bold text-slate-900">{{ $application->user->phone ?? $application->applicant_details['phone'] ?? 'N/A' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Present Address</span>
                        <span class="font-bold text-slate-900">{{ $application->applicant_details['present_address'] ?? $application->user->present_address ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">District</span>
                        <span class="font-bold text-slate-900">{{ $application->user->district->name ?? $application->district->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Occupation</span>
                        <span class="font-bold text-slate-900">{{ $application->applicant_details['occupation'] ?? $application->user->occupation ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Annual Income</span>
                        <span class="font-bold text-slate-900">
                            @php
                                $income = $application->applicant_details['annual_income'] ?? $application->user->annual_income ?? null;
                            @endphp
                            {{ $income ? 'BDT ' . number_format($income) : 'N/A' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Weapon Type</span>
                        <span class="font-bold text-slate-900">{{ $application->firearm_details['weapon_type'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Bore / Calibre</span>
                        <span class="font-bold text-slate-900">{{ $application->firearm_details['bore'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Purpose</span>
                        <span class="font-bold text-slate-900">{{ $application->firearm_details['purpose'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Vetting Reports -->
            @if($application->vettings->count())
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Vetting Reports</span>
                </div>
                <div class="p-5 grid grid-cols-2 gap-3">
                    @foreach($application->vettings as $v)
                    <div class="flex items-center justify-between px-3 py-2.5 rounded-lg border border-slate-100 bg-slate-50">
                        <span class="text-xs font-bold text-slate-700 uppercase">{{ $v->agency }}</span>
                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full
                            @if($v->status === 'cleared') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($v->status === 'flagged') bg-rose-50 text-rose-700 border border-rose-200
                            @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                            {{ $v->status }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Case Timeline -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Case Timeline (audit trail)</span>
                </div>
                <div class="p-5 space-y-3">
                    @forelse($application->logs as $log)
                    <div class="flex space-x-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="text-[9px] text-slate-400 font-bold">{{ $log->created_at->format('d M Y · h:i A') }}</div>
                            <div class="text-xs font-bold text-slate-900 mt-0.5">{{ $log->remarks }}</div>
                            @if($log->actor)
                            <div class="text-[9px] text-slate-500">by {{ $log->actor->name }}</div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 font-semibold">No timeline entries yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Officer Actions -->
        <div class="space-y-4">
            @if(!empty($actions) && $actionRoute !== '#')
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Officer Actions</span>
                </div>
                @if ($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-bold space-y-1">
                        <span class="block text-sm font-black font-serif">⚠️ Please resolve the following errors:</span>
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="p-5 space-y-3">
                    <form action="{{ $actionRoute }}" method="POST" class="space-y-3">
                        @csrf
                        <textarea name="remarks" rows="3" placeholder="Remarks (mandatory)"
                                  class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white resize-none"></textarea>

                        @foreach($actions as $value => $label)
                        <button type="submit" name="action" value="{{ $value }}"
                                class="w-full py-2.5 rounded-lg text-xs font-black transition-colors
                                {{ in_array($value, ['approve','forward','trigger_vetting','forward_dc','forward_moha']) ? 'bg-gov-green hover:bg-gov-light text-white' : 'border border-rose-300 text-rose-600 hover:bg-rose-50' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </form>
                </div>
            </div>
            @endif

            <!-- Routing Info -->
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest mb-2">Routing Rule</div>
                <p class="text-xs text-slate-600 font-semibold">
                    @if(in_array($application->firearm_details['weapon_type'] ?? '', ['Pistol','Revolver']))
                        Handgun cases → MoHA approval required.
                    @else
                        Long-gun cases → DC direct approval.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
