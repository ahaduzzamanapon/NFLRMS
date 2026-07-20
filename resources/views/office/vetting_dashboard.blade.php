@extends('layouts.app')
@section('title', 'Vetting Queue')

@section('content')
<div class="max-w-5xl space-y-5">

    <div>
        <h2 class="text-2xl font-black font-serif text-slate-900">
            @if(auth()->user()->hasRole(\App\Enums\Role::PoliceOfficer)) Police (SP Office / Thana)
            @elseif(auth()->user()->hasRole(\App\Enums\Role::SpecialBranch)) Special Branch (SB)
            @elseif(auth()->user()->hasRole(\App\Enums\Role::NsiOfficer)) NSI
            @else DGFI
            @endif
            — Vetting Queue
        </h2>
        <p class="text-xs text-slate-500 mt-1">
            {{ $vettings->where('status','pending')->count() }} pending &bull;
            {{ $vettings->whereIn('status',['cleared','flagged'])->count() }} completed
        </p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center space-x-3">
            <div class="w-9 h-9 rounded-full border-2 border-slate-300 flex items-center justify-center text-slate-500">🛡</div>
            <div>
                <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">Pending Clearance</div>
                <div class="text-2xl font-black text-slate-900">{{ $vettings->where('status','pending')->count() }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center space-x-3">
            <div class="w-9 h-9 rounded-full border-2 border-emerald-400 flex items-center justify-center text-emerald-600">✓</div>
            <div>
                <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">Cleared This Month</div>
                <div class="text-2xl font-black text-emerald-600">{{ $vettings->where('status','cleared')->count() }}</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center space-x-3">
            <div class="w-9 h-9 rounded-full border-2 border-rose-400 flex items-center justify-center text-rose-600">✗</div>
            <div>
                <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">Not Cleared</div>
                <div class="text-2xl font-black text-rose-600">{{ $vettings->where('status','flagged')->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Pending Cases -->
    @php $pending = $vettings->where('status','pending'); @endphp
    @if($pending->count())
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Pending Cases</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">
                    <th class="p-3 pl-5">Reference</th>
                    <th class="p-3">Applicant</th>
                    <th class="p-3">Service</th>
                    <th class="p-3">District</th>
                    <th class="p-3 pr-5 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @foreach($pending as $v)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5 font-bold font-mono text-gov-green text-[10px]">
                        {{ $v->application->application_number }}
                    </td>
                    <td class="p-3 font-bold text-slate-900">{{ strtoupper($v->application->user->name) }}</td>
                    <td class="p-3 font-semibold text-slate-600">
                        {{ ucfirst(str_replace('_',' ',$v->application->type)) }} &bull;
                        {{ $v->application->firearm_details['weapon_type'] ?? 'N/A' }}
                    </td>
                    <td class="p-3 text-slate-600">{{ $v->application->district->name ?? 'N/A' }}</td>
                    <td class="p-3 pr-5 text-right">
                        <a href="{{ route('vetting.show', $v->id) }}"
                           class="text-xs font-black text-gov-green hover:underline">Submit report →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Completed Cases -->
    @php $done = $vettings->whereIn('status',['cleared','flagged']); @endphp
    @if($done->count())
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Completed</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">
                    <th class="p-3 pl-5">Reference</th>
                    <th class="p-3">Applicant</th>
                    <th class="p-3">Service</th>
                    <th class="p-3">District</th>
                    <th class="p-3 pr-5">Verdict</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @foreach($done as $v)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5 font-bold font-mono text-gov-green text-[10px]">
                        {{ $v->application->application_number }}
                    </td>
                    <td class="p-3 font-bold text-slate-900">{{ $v->application->user->name }}</td>
                    <td class="p-3 font-semibold text-slate-600">
                        {{ $v->application->firearm_details['weapon_type'] ?? 'N/A' }}
                    </td>
                    <td class="p-3 text-slate-600">{{ $v->application->district->name ?? 'N/A' }}</td>
                    <td class="p-3 pr-5">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border
                            {{ $v->status === 'cleared' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700' }}">
                            {{ ucfirst($v->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($vettings->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-400 font-bold">
        No vetting cases assigned to your agency.
    </div>
    @endif
</div>
@endsection
