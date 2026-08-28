@extends('layouts.app')
@section('title', 'Senior Secretary Home Dashboard')

@section('content')
<div class="max-w-full space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">Senior Secretary Dashboard</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">Ministry of Home Affairs &bull; High-level licensing overview &amp; regional analytics</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap self-start sm:self-auto">
            <a href="{{ route('moha.dashboard') }}" class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-xl shadow-sm transition-colors flex items-center gap-1.5">
                <span><i class="fa-solid fa-building-columns"></i></span><span>Approval Queue</span>
            </a>
            {{-- <a href="{{ route('custom_comment.index') }}" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1.5">
                <span><i class="fa-solid fa-comments text-slate-500"></i></span><span>Directives &amp; Notes</span>
            </a> --}}
        </div>
    </div>

    <!-- ===== 1. DASHBOARD STATISTICS CARDS (6 Count Cards) ===== -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">

        <!-- Card 1: Total Licenses -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 hover:border-emerald-300 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Licenses</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-gov-green flex items-center justify-center text-sm font-bold group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-certificate"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                {{ number_format($stats['total_licenses']) }}
            </div>
            <div class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-md px-2 py-0.5 mt-2 inline-block">
                All Categories Issued
            </div>
        </div>

        <!-- Card 2: Approved Licenses -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 hover:border-emerald-400 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Approved Licenses</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-100/70 text-emerald-600 flex items-center justify-center text-sm font-bold group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">
                {{ number_format($stats['total_approved_licenses']) }}
            </div>
            <div class="text-[10px] font-semibold text-slate-500 mt-2 flex items-center gap-1">
                <span class="text-emerald-600 font-bold">83.6%</span> active status
            </div>
        </div>

        <!-- Card 3: Pending Licenses -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 hover:border-amber-300 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Pending Licenses</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 tracking-tight">
                {{ number_format($stats['total_pending_licenses']) }}
            </div>
            <div class="text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-100 rounded-md px-2 py-0.5 mt-2 inline-block">
                In Workflow Queues
            </div>
        </div>

        <!-- Card 4: Suspended Licenses -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 hover:border-rose-300 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Suspended Licenses</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-rose-600 tracking-tight">
                {{ number_format($stats['total_suspended_licenses']) }}
            </div>
            <div class="text-[10px] font-semibold text-rose-700 bg-rose-50 border border-rose-100 rounded-md px-2 py-0.5 mt-2 inline-block">
                Non-Compliant / Flagged
            </div>
        </div>

        <!-- Card 5: Total Citizens -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 hover:border-blue-300 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Citizens</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-blue-600 tracking-tight">
                {{ number_format($stats['total_citizens']) }}
            </div>
            <div class="text-[10px] font-semibold text-slate-500 mt-2">
                Individual Licensees
            </div>
        </div>

        <!-- Card 6: Total Dealers -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 hover:border-purple-300 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Dealers</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-store"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-purple-600 tracking-tight">
                {{ number_format($stats['total_dealers']) }}
            </div>
            <div class="text-[10px] font-semibold text-purple-700 bg-purple-50 border border-purple-100 rounded-md px-2 py-0.5 mt-2 inline-block">
                Commercial Stockers
            </div>
        </div>

    </div>

    <!-- ===== 2. CHARTS SECTION (District-wise & Thana-wise) ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- District-wise License Statistics -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-gov-green/10 text-gov-green flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">District-wise License Statistics</h3>
                        <p class="text-[10px] text-slate-400">Distribution of active &amp; pending licenses across top divisions</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">Top 8 Districts</span>
            </div>
            <div class="p-5">
                <div class="relative w-full h-[260px]">
                    <canvas id="districtChart"></canvas>
                </div>
            </div>
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-[11px]">
                <div class="border-r border-slate-200/60 pr-2">
                    <span class="text-slate-400 block text-[10px]">Highest Volume</span>
                    <strong class="text-slate-800">Dhaka (4,850)</strong>
                </div>
                <div class="border-r border-slate-200/60 pr-2">
                    <span class="text-slate-400 block text-[10px]">Second Highest</span>
                    <strong class="text-slate-800">Chattogram (2,940)</strong>
                </div>
                <div class="border-r border-slate-200/60 pr-2">
                    <span class="text-slate-400 block text-[10px]">Northern Zone</span>
                    <strong class="text-slate-800">Rajshahi (1,820)</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Southern Zone</span>
                    <strong class="text-slate-800">Khulna (1,460)</strong>
                </div>
            </div>
        </div>

        <!-- Thana-wise License Statistics -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">Thana-wise License Statistics</h3>
                        <p class="text-[10px] text-slate-400">High-density administrative police stations (Thanas)</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">Metropolitan Thanas</span>
            </div>
            <div class="p-5">
                <div class="relative w-full h-[260px]">
                    <canvas id="thanaChart"></canvas>
                </div>
            </div>
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-[11px]">
                <span class="text-slate-500">Gulshan Thana accounts for <strong class="text-slate-800">8.3%</strong> of total national licenses</span>
                <span class="text-gov-green font-bold flex items-center gap-1"><i class="fa-solid fa-shield text-[10px]"></i> High Density Security</span>
            </div>
        </div>

    </div>

    <!-- ===== 3. MIDDLE OVERVIEW & SUMMARY METRICS ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- License Status Summary & Processing Progress -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-gov-green"></i>
                    <span>Processing &amp; Status Metrics</span>
                </h3>
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">National Summary</span>
            </div>

            <div class="space-y-3.5">
                <!-- Vetting Completion -->
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-medium text-slate-700">Security Vetting Clearance Rate</span>
                        <span class="font-bold text-emerald-700">{{ $licenseStatusSummary['vetting_completed'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full bg-emerald-500" style="width: {{ $licenseStatusSummary['vetting_completed'] }}%"></div>
                    </div>
                </div>

                <!-- MoHA Review -->
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-medium text-slate-700">MoHA Approval Processing Progress</span>
                        <span class="font-bold text-gov-green">{{ $licenseStatusSummary['moha_reviewed'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full bg-gov-green" style="width: {{ $licenseStatusSummary['moha_reviewed'] }}%"></div>
                    </div>
                </div>

                <!-- Approved vs Total -->
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-medium text-slate-700">Active License Ratio</span>
                        <span class="font-bold text-blue-600">{{ $licenseStatusSummary['approved_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full bg-blue-500" style="width: {{ $licenseStatusSummary['approved_rate'] }}%"></div>
                    </div>
                </div>

                <!-- Restricted Weapons -->
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-medium text-slate-700">Restricted Category Share (.9mm / Magnums)</span>
                        <span class="font-bold text-purple-600">{{ $licenseStatusSummary['restricted_weapon_share'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full bg-purple-500" style="width: {{ $licenseStatusSummary['restricted_weapon_share'] }}%"></div>
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Standard Review SLAs: <strong>14 Days</strong></span>
                <span class="text-emerald-600 font-semibold flex items-center gap-1"><i class="fa-solid fa-check text-[10px]"></i> System Optimal</span>
            </div>
        </div>

        <!-- Recent Activities Feed -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">Recent Senior Secretary Level Activities</h3>
                        <p class="text-[10px] text-slate-400">Chronological activity stream of high-level ministerial decisions</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Live Log</span>
            </div>

            <div class="p-5 divide-y divide-slate-100 space-y-3">
                @foreach($recentActivities as $act)
                <div class="pt-3 first:pt-0 flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-sm border {{ $act['color'] }}">
                        <i class="{{ $act['icon'] }}"></i>
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-xs font-bold text-slate-900 truncate">{{ $act['action'] }}</h4>
                            <span class="text-[10px] text-slate-400 font-medium whitespace-nowrap">{{ $act['time'] }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">{{ $act['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-[11px]">
                <span class="text-slate-500">All administrative operations logged &amp; timestamped for audit integrity</span>
                <span class="text-gov-green font-bold">Audit Level 1</span>
            </div>
        </div>

    </div>

    <!-- ===== 4. RECENT APPLICATIONS TABLE ===== -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gov-green/10 text-gov-green flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">Recent Applications &amp; Licenses Overview</h3>
                    <p class="text-[11px] text-slate-400">High-priority applications undergoing MoHA review or recent issuance</p>
                </div>
            </div>
            <a href="{{ route('moha.dashboard') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors self-start sm:self-auto flex items-center gap-1.5">
                <span>View Full Approval Queue</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[10px] font-bold uppercase text-slate-400 tracking-wider">
                        <th class="py-3 px-5">Application / License No</th>
                        <th class="py-3 px-4">Applicant &amp; Category</th>
                        <th class="py-3 px-4">Firearm Type</th>
                        <th class="py-3 px-4">District / Thana</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach($recentApplications as $app)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3.5 px-5">
                            <div class="font-bold text-slate-900">{{ $app['app_no'] }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $app['license_no'] }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-800">{{ $app['applicant_name'] }}</div>
                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $app['category'] === 'Dealer' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                {{ $app['category'] }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            {{ $app['type'] }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-medium text-slate-800">{{ $app['district'] }}</div>
                            <div class="text-[10px] text-slate-400">{{ $app['thana'] }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            @if($app['status'] === 'approved')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                </span>
                            @elseif($app['status'] === 'suspended')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Suspended
                                </span>
                            @elseif($app['status'] === 'pending_screening')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Nat. Screening
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> MoHA Review
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                            {{ $app['date'] }}
                        </td>
                        <td class="py-3.5 px-5 text-right">
                            <a href="{{ route('moha.dashboard') }}" class="px-3 py-1 bg-gov-green/10 hover:bg-gov-green hover:text-white text-gov-green font-bold text-[11px] rounded-lg transition-colors inline-flex items-center gap-1">
                                <span>Inspect</span>
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart.js Script Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. District-wise License Statistics (Bar Chart)
        const districtCtx = document.getElementById('districtChart').getContext('2d');
        const districtData = @json($districtStats);
        
        new Chart(districtCtx, {
            type: 'bar',
            data: {
                labels: districtData.map(d => d.name),
                datasets: [
                    {
                        label: 'Approved Licenses',
                        data: districtData.map(d => d.approved),
                        backgroundColor: '#1a7a52',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pending Applications',
                        data: districtData.map(d => d.pending),
                        backgroundColor: '#d97706',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Poppins', size: 11, weight: '500' },
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        titleFont: { family: 'Poppins', size: 12, weight: '700' },
                        bodyFont: { family: 'Poppins', size: 11 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Poppins', size: 10 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { family: 'Poppins', size: 10 } }
                    }
                }
            }
        });

        // 2. Thana-wise License Statistics (Doughnut / Horizontal Bar Chart)
        const thanaCtx = document.getElementById('thanaChart').getContext('2d');
        const thanaData = @json($thanaStats);

        new Chart(thanaCtx, {
            type: 'doughnut',
            data: {
                labels: thanaData.map(t => t.name + ' (' + t.district + ')'),
                datasets: [{
                    data: thanaData.map(t => t.count),
                    backgroundColor: [
                        '#1a7a52',
                        '#2563eb',
                        '#059669',
                        '#d97706',
                        '#9333ea',
                        '#0284c7',
                        '#e11d48',
                        '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { family: 'Poppins', size: 10, weight: '500' },
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' licenses (' + thanaData[context.dataIndex].percentage + '%)';
                                }
                                return label;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    });
</script>
@endsection
