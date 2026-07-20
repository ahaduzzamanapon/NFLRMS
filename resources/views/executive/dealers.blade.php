@extends('layouts.app')
@section('title', 'Dealers & Stock')

@section('content')
<div class="max-w-6xl space-y-5">

    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-black font-serif text-slate-900">Registered Arms Dealers</h2>
            <p class="text-xs text-slate-500 mt-1">National dealer registry · Stock cross-verification · Anomaly detection</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">Registered Dealers</div>
            <div class="text-3xl font-black text-slate-900 mt-1">{{ $totalDealers }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">Total Arms in Dealer Stock</div>
            <div class="text-3xl font-black text-gov-green mt-1">{{ number_format($totalArmsInStock) }}</div>
            <div class="text-[9px] text-slate-400 mt-0.5">Across {{ $totalDealers }} dealer(s)</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">Anomaly Alerts</div>
            <div class="text-3xl font-black text-rose-600 mt-1">{{ $dealers->sum('anomalyAlerts') }}</div>
        </div>
    </div>

    <!-- Dealers Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Dealer Registry</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 text-[10px] font-extrabold uppercase text-slate-400 tracking-wider bg-slate-50">
                    <th class="p-3 pl-5">Dealer Name</th>
                    <th class="p-3">District</th>
                    <th class="p-3">Firearms</th>
                    <th class="p-3">Ammo</th>
                    <th class="p-3">Total Stock</th>
                    <th class="p-3 pr-5">Anomalies</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($dealers as $d)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5">
                        <div class="font-bold text-slate-900">{{ $d->name }}</div>
                        <div class="text-[9px] text-slate-400">{{ $d->email }}</div>
                    </td>
                    <td class="p-3 text-slate-600">{{ $d->district->name ?? '—' }}</td>
                    <td class="p-3 font-black text-slate-900">{{ number_format($d->totalFirearms) }}</td>
                    <td class="p-3 font-bold text-slate-600">{{ number_format($d->totalAmmo) }}</td>
                    <td class="p-3 font-bold text-slate-900">{{ number_format($d->totalStock) }}</td>
                    <td class="p-3 pr-5">
                        @if($d->anomalyAlerts > 0)
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black border border-rose-200 bg-rose-50 text-rose-700">
                                ⚑ {{ $d->anomalyAlerts }} alert(s)
                            </span>
                        @else
                            <span class="text-[9px] text-gov-green font-black">✓ Clear</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-slate-400 font-bold">No registered dealers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
