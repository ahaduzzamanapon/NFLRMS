@extends('layouts.app')
@section('title', 'Dealing License Central')

@section('content')
<div class="max-w-6xl space-y-5">

    <div>
<h2 class="text-2xl font-bold font-serif text-slate-900">Dealing License — Central Dashboard</h2>
        <p class="text-xs text-slate-500 mt-1">All dealing licence applications · New applications & renewals</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach([
            ['label'=>'Total Applications','value'=>$stats['total'],'color'=>'text-slate-900'],
            ['label'=>'Pending Review','value'=>$stats['pending'],'color'=>'text-amber-600'],
            ['label'=>'Approved','value'=>$stats['approved'],'color'=>'text-gov-green'],
            ['label'=>'Rejected','value'=>$stats['rejected'],'color'=>'text-rose-600'],
        ] as $stat)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3.5 sm:p-4">
            <div class="text-[10px] font-semibold uppercase text-slate-400 tracking-widest">{{ $stat['label'] }}</div>
            <div class="text-2xl sm:text-3xl font-bold {{ $stat['color'] }} mt-1">{{ $stat['value'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- New Applications -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 min-w-[560px]">
            <span class="text-[11px] font-semibold uppercase text-slate-500 tracking-widest">New Dealing Licence Applications</span>
        </div>
        <table class="w-full text-left border-collapse min-w-[560px]">
            <thead>
                <tr class="border-b border-slate-100 text-[11px] font-semibold uppercase text-slate-400 tracking-wider bg-slate-50">
                    <th class="p-3 pl-5">Reference</th>
                    <th class="p-3">Applicant</th>
                    <th class="p-3">District</th>
                    <th class="p-3">Applied</th>
                    <th class="p-3 pr-5">Status</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($dealingApps as $a)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5 font-semibold font-mono text-gov-green text-[11px]">{{ $a->application_number }}</td>
                    <td class="p-3">
                        <div class="font-semibold text-slate-900">{{ $a->user->name }}</div>
                        <div class="text-[10px] text-slate-400">NID {{ $a->user->nid ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3 text-slate-600">{{ optional($a->user->district)->name ?? '—' }}</td>
                    <td class="p-3 text-slate-500">{{ $a->created_at->format('d M Y') }}</td>
                    <td class="p-3 pr-5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase border
                            @if(in_array($a->status,['approved','license_issued'])) border-emerald-200 bg-emerald-50 text-emerald-700
                            @elseif($a->status === 'rejected') border-rose-200 bg-rose-50 text-rose-700
                            @else border-amber-200 bg-amber-50 text-amber-700 @endif">
                            {{ ucfirst(str_replace('_',' ',$a->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-400 font-normal">No new dealing applications.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Renewal Applications -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 min-w-[560px]">
            <span class="text-[11px] font-semibold uppercase text-slate-500 tracking-widest">Dealing Licence Renewals</span>
        </div>
        <table class="w-full text-left border-collapse min-w-[560px]">
            <thead>
                <tr class="border-b border-slate-100 text-[11px] font-semibold uppercase text-slate-400 tracking-wider bg-slate-50">
                    <th class="p-3 pl-5">Reference</th>
                    <th class="p-3">Applicant</th>
                    <th class="p-3">District</th>
                    <th class="p-3">Applied</th>
                    <th class="p-3 pr-5">Status</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($dealingRenewals as $a)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5 font-semibold font-mono text-gov-green text-[11px]">{{ $a->application_number }}</td>
                    <td class="p-3">
                        <div class="font-semibold text-slate-900">{{ $a->user->name }}</div>
                        <div class="text-[10px] text-slate-400">NID {{ $a->user->nid ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3 text-slate-600">{{ optional($a->user->district)->name ?? '—' }}</td>
                    <td class="p-3 text-slate-500">{{ $a->created_at->format('d M Y') }}</td>
                    <td class="p-3 pr-5">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase border
                            @if(in_array($a->status,['approved','license_issued'])) border-emerald-200 bg-emerald-50 text-emerald-700
                            @elseif($a->status === 'rejected') border-rose-200 bg-rose-50 text-rose-700
                            @else border-amber-200 bg-amber-50 text-amber-700 @endif">
                            {{ ucfirst(str_replace('_',' ',$a->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-400 font-normal">No renewal applications.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
