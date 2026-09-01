@extends('layouts.app')
@section('title', 'Reports & Analytics')

@section('content')
<div class="max-w-full space-y-6">

    <!-- Top Header & Export All -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900">Reports & Analytics</h2>
            <p class="text-xs text-slate-500 mt-1">10 statutory &amp; operational reports &bull; exportable to Excel and PDF (BRS §9.2)</p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <a href="{{ route('admin.reports.export_all', 'excel') }}" class="px-3.5 py-2 bg-emerald-800 hover:bg-emerald-900 text-white font-bold text-xs rounded-lg flex items-center space-x-1.5 shadow-sm transition-colors">
                <i class="fa-solid fa-file-excel"></i><span>Export All (Excel)</span>
            </a>
            <a href="{{ route('admin.reports.export_all', 'pdf') }}" target="_blank" class="px-3.5 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg flex items-center space-x-1.5 shadow-sm transition-colors">
                <i class="fa-solid fa-file-pdf"></i><span>Export All (PDF)</span>
            </a>
        </div>
    </div>

    <!-- Stats KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @php
        $kpis = [
            ['label' => 'Total Licenses', 'value' => number_format($stats['total_licenses']), 'sub' => 'Active in system', 'color' => 'text-gov-green'],
            ['label' => 'Applications Processed', 'value' => number_format($stats['total_apps']), 'sub' => 'All time', 'color' => 'text-gov-green'],
            ['label' => 'Active Licenses', 'value' => number_format($stats['active_licenses']), 'sub' => 'Currently valid', 'color' => 'text-gov-green'],
            ['label' => 'Pending Applications', 'value' => number_format($stats['pending_apps']), 'sub' => 'Awaiting action', 'color' => 'text-amber-600'],
        ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3.5 sm:p-4">
            <div class="text-[10px] font-semibold uppercase text-slate-400 tracking-widest">{{ $kpi['label'] }}</div>
            <div class="text-2xl sm:text-3xl font-bold {{ $kpi['color'] }} mt-1">{{ $kpi['value'] }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">{{ $kpi['sub'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Charts & Catalog Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">

        <!-- By District -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold text-slate-900">R-01 &middot; Active Licenses by District</div>
                </div>
                <span class="text-[10px] text-slate-500 font-semibold font-mono">Total {{ number_format($byDistrict->sum('applications_count')) }}</span>
            </div>
            <div class="p-5 space-y-2.5">
                @forelse($byDistrict as $d)
                @php $pct = $byDistrict->max('applications_count') > 0 ? round(($d->applications_count / max($byDistrict->max('applications_count'), 1)) * 100) : 0; @endphp
                <div class="flex items-center space-x-3">
                    <span class="text-[11px] font-semibold text-slate-700 w-28 truncate">{{ $d->name }}</span>
                    <div class="flex-grow bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-gov-green" style="width: {{ max($pct, 6) }}%"></div>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 w-8 text-right font-mono">{{ $d->applications_count }}</span>
                </div>
                @empty
                <p class="text-xs text-slate-400 font-semibold text-center py-4">No district license data available yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Report Catalog -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="text-xs font-semibold text-slate-900">Report Catalog</div>
                <span class="text-[10px] text-slate-500 font-semibold font-mono">{{ count($reportsCatalog) }} reports available</span>
            </div>
            <div class="divide-y divide-slate-100 max-h-[380px] overflow-y-auto">
                @foreach($reportsCatalog as $r)
                @php $isActive = isset($activeReportData) && $activeReportData['meta']['id'] === $r['id']; @endphp
                <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50/70 transition-colors {{ $isActive ? 'bg-emerald-50/40 border-l-4 border-gov-green' : '' }}">
                    <div class="flex items-center space-x-3 min-w-0">
                        <span class="text-[10px] font-semibold text-slate-500 font-mono w-8 flex-shrink-0">{{ $r['id'] }}</span>
                        <div class="min-w-0">
                            <a href="{{ route('admin.reports.export', [$r['id'], 'pdf']) }}" target="_blank" class="text-xs font-semibold text-slate-800 hover:text-rose-700 transition-colors block truncate">
                                {{ $r['name'] }}
                            </a>
                            <span class="text-[10px] text-slate-400 font-normal block truncate">{{ $r['desc'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 flex-shrink-0 ml-2">
                        <span class="text-[10px] text-slate-400 font-semibold px-2 py-0.5 rounded bg-slate-100">{{ $r['category'] }}</span>
                        <a href="{{ route('admin.reports.export', [$r['id'], 'pdf']) }}" target="_blank" class="px-2.5 py-1 text-[10px] font-bold rounded bg-rose-700 hover:bg-rose-800 text-white transition-colors flex items-center space-x-1 shadow-sm">
                            <i class="fa-solid fa-file-pdf text-[10px]"></i><span>Export PDF</span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
    </div>
</div>
@endsection
