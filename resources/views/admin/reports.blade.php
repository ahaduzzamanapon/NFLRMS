@extends('layouts.app')
@section('title', 'Reports & Analytics')

@section('content')
<div class="max-w-6xl space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black font-serif text-slate-900">Reports & Analytics</h2>
            <p class="text-xs text-slate-500 mt-1">10 statutory + operational reports · exportable to PDF / Excel / CSV (BRS §9.2)</p>
        </div>
        <button class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg flex items-center space-x-1.5 shadow-sm">
            <span>⬇</span><span>Export all</span>
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $kpis = [
            ['label' => 'Total Licenses', 'value' => number_format($stats['total_licenses']), 'sub' => 'Active in system', 'color' => 'text-gov-green'],
            ['label' => 'Applications Processed', 'value' => number_format($stats['total_apps']), 'sub' => 'All time', 'color' => 'text-gov-green'],
            ['label' => 'Active Licenses', 'value' => number_format($stats['active_licenses']), 'sub' => 'Currently valid', 'color' => 'text-gov-green'],
            ['label' => 'Pending Applications', 'value' => number_format($stats['pending_apps']), 'sub' => 'Awaiting action', 'color' => 'text-amber-600'],
        ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">{{ $kpi['label'] }}</div>
            <div class="text-3xl font-black {{ $kpi['color'] }} mt-1">{{ $kpi['value'] }}</div>
            <div class="text-[9px] text-slate-400 mt-0.5">{{ $kpi['sub'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Charts row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <!-- By District -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold text-slate-900">R-01 · Active Licenses by District</div>
                </div>
                <span class="text-[9px] text-slate-400 font-bold">Total {{ number_format($stats['active_licenses']) }}</span>
            </div>
            <div class="p-5 space-y-2.5">
                @forelse($byDistrict as $d)
                @php $pct = $stats['active_licenses'] > 0 ? round(($d->applications_count / max($byDistrict->max('applications_count'), 1)) * 100) : 0; @endphp
                <div class="flex items-center space-x-3">
                    <span class="text-[10px] font-bold text-slate-700 w-28 truncate">{{ $d->name }}</span>
                    <div class="flex-grow bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-gov-green" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500 w-8 text-right">{{ $d->applications_count }}</span>
                </div>
                @empty
                <p class="text-xs text-slate-400 font-semibold text-center py-4">No license data available yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Report Catalog -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="text-xs font-bold text-slate-900">Report Catalog</div>
                <span class="text-[9px] text-slate-400 font-bold">10 reports</span>
            </div>
            <div class="divide-y divide-slate-100">
                @php
                $reports = [
                    ['id'=>'R-01','name'=>'Revenue collection by district','category'=>'Financial'],
                    ['id'=>'R-02','name'=>'Monthly application volume (new / renewal / dealing)','category'=>'Operations'],
                    ['id'=>'R-03','name'=>'Vetting SLA compliance (Police, SB, NSI, DGFI)','category'=>'SLA'],
                    ['id'=>'R-04','name'=>'Rejection analytics by cause','category'=>'Compliance'],
                    ['id'=>'R-05','name'=>'Late renewal ageing (Tier 1 / 2 / 3)','category'=>'Compliance'],
                    ['id'=>'R-06','name'=>'MoHA approval lead-time (Political-4 → Minister)','category'=>'SLA'],
                    ['id'=>'R-07','name'=>'Dealer stock ledger reconciliation exceptions','category'=>'Audit'],
                    ['id'=>'R-08','name'=>'District quota utilisation vs cap','category'=>'Governance'],
                    ['id'=>'R-09','name'=>'Certificate issuance & downloads','category'=>'Operations'],
                    ['id'=>'R-10','name'=>'User activity & audit trail export','category'=>'Audit'],
                ];
                @endphp
                @foreach($reports as $r)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-center space-x-3">
                        <span class="text-[9px] font-extrabold text-slate-400 font-mono w-8">{{ $r['id'] }}</span>
                        <span class="text-xs font-semibold text-slate-700">{{ $r['name'] }}</span>
                    </div>
                    <div class="flex items-center space-x-3 flex-shrink-0">
                        <span class="text-[9px] text-slate-400 font-bold">{{ $r['category'] }}</span>
                        <button class="text-[9px] font-black text-gov-green hover:underline flex items-center space-x-0.5">
                            <span>📄</span><span>Run</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
