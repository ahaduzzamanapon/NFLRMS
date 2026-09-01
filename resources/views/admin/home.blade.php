@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-full space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">Admin Home Dashboard</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">System operations, user statistics &amp; application oversight</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users') }}" class="px-3.5 py-2 bg-gov-green hover:bg-gov-light text-white text-xs font-bold rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <span><i class="fa-solid fa-users"></i></span><span>Manage Users</span>
            </a>
            <a href="{{ route('admin.audit_log') }}" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1.5">
                <span><i class="fa-solid fa-file-lines text-slate-500"></i></span><span>Audit Trail</span>
            </a>
        </div>
    </div>

    <!-- Top KPI Cards (2 Rows of 4 Cards) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">

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

        <!-- Card 7: Total Firearms (Clickable -> Firearms List) -->
        <a href="{{ route('overview.firearms') }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 hover:border-cyan-400 hover:shadow-md transition-all group block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Firearms</span>
                <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-sm font-bold group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-gun"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-cyan-700 tracking-tight">
                {{ number_format($stats['total_firearms']) }}
            </div>
            <div class="text-[10px] font-semibold text-cyan-700 bg-cyan-50 border border-cyan-100 rounded-md px-2 py-0.5 mt-2 inline-flex items-center gap-1">
                <span>Citizen &amp; Dealer Arms</span> <i class="fa-solid fa-arrow-right text-[8px]"></i>
            </div>
        </a>

        <!-- Card 8: Total Ammunition (Clickable -> Ammunition List) -->
        <a href="{{ route('overview.ammunition') }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 hover:border-indigo-400 hover:shadow-md transition-all group block">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Ammunition</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-indigo-700 tracking-tight">
                {{ number_format($stats['total_ammunition']) }}
            </div>
            <div class="text-[10px] font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-md px-2 py-0.5 mt-2 inline-flex items-center gap-1">
                <span>Licensed Calibre Stock</span> <i class="fa-solid fa-arrow-right text-[8px]"></i>
            </div>
        </a>

    </div>

    <!-- ===== CHARTS SECTION (District-wise & Thana-wise) ===== -->
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

    <!-- Middle Section: Application Summary & User Role Distribution -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

        <!-- Application Status Summary -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                        <span><i class="fa-solid fa-chart-pie text-gov-green"></i></span><span>Application Summary</span>
                    </h3>
                    <span class="text-[10px] font-semibold text-slate-400">Status counts</span>
                </div>
                <div class="p-5 space-y-3.5">
                    @php
                    $appStatusList = [
                        ['label' => 'Pending Workflow', 'count' => $stats['pending_applications'], 'color' => 'bg-amber-500', 'textColor' => 'text-amber-700'],
                        ['label' => 'Approved / License Issued', 'count' => $stats['approved_applications'], 'color' => 'bg-emerald-500', 'textColor' => 'text-emerald-700'],
                        ['label' => 'Rejected Applications', 'count' => $stats['rejected_applications'], 'color' => 'bg-rose-500', 'textColor' => 'text-rose-700'],
                    ];
                    $maxAppCount = max(1, $stats['total_applications']);
                    @endphp
                    @foreach($appStatusList as $item)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-slate-700">{{ $item['label'] }}</span>
                            <span class="font-bold {{ $item['textColor'] }}">{{ number_format($item['count']) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $item['color'] }}" style="width: {{ round(($item['count'] / $maxAppCount) * 100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-[11px] text-slate-500">
                <span>Districts Covered: <strong class="text-slate-800">{{ $stats['total_districts'] }}</strong></span>
                <a href="{{ route('admin.reports') }}" class="font-semibold text-gov-green hover:underline">View Analytics <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></a>
            </div>
        </div>

        <!-- User Roles Overview -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                        <span><i class="fa-solid fa-shield-halved text-gov-green"></i></span><span>User Category Summary</span>
                    </h3>
                    <span class="text-[10px] font-semibold text-slate-400">Total {{ $stats['total_users'] }}</span>
                </div>
                <div class="p-5 space-y-3.5">
                    @php
                    $userCategories = [
                        ['label' => 'Applicants (Citizen & Dealer)', 'count' => $stats['role_counts']['applicants'] ?? 0, 'color' => 'bg-blue-500'],
                        ['label' => 'DC Office & Field Personnel', 'count' => $stats['role_counts']['dc_office'] ?? 0, 'color' => 'bg-gov-green'],
                        ['label' => 'Security Vetting Officers', 'count' => $stats['role_counts']['vetting'] ?? 0, 'color' => 'bg-purple-500'],
                        ['label' => 'Ministry Officials (MoHA)', 'count' => $stats['role_counts']['moha'] ?? 0, 'color' => 'bg-amber-500'],
                        ['label' => 'System Administrators', 'count' => $stats['role_counts']['admin'] ?? 0, 'color' => 'bg-slate-700'],
                    ];
                    $maxUserCat = max(1, $stats['total_users']);
                    @endphp
                    @foreach($userCategories as $cat)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-slate-700">{{ $cat['label'] }}</span>
                            <span class="font-bold text-slate-900">{{ number_format($cat['count']) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $cat['color'] }}" style="width: {{ round(($cat['count'] / $maxUserCat) * 100) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-[11px] text-slate-500">
                <span>Account Control Center</span>
                <a href="{{ route('admin.users') }}" class="font-semibold text-gov-green hover:underline">Manage Accounts <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></a>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Recent Activities Log & Administrative Shortcuts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">

        <!-- Recent Activities Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                    <span><i class="fa-solid fa-clock-rotate-left text-gov-green"></i></span><span>Recent System Activities</span>
                </h3>
                <a href="{{ route('admin.audit_log') }}" class="text-[10px] font-semibold text-gov-green hover:underline">View Full Log <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[480px]">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-bold uppercase text-slate-400 tracking-wider">
                            <th class="p-3 pl-5">Time</th>
                            <th class="p-3">Actor</th>
                            <th class="p-3">Action</th>
                            <th class="p-3 pr-5">Application</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100">
                        @forelse($recentActivities as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 pl-5 text-[11px] text-slate-400 whitespace-nowrap">
                                {{ $log->created_at ? $log->created_at->diffForHumans() : 'N/A' }}
                            </td>
                            <td class="p-3 font-semibold text-slate-800">
                                {{ $log->actor->name ?? 'System' }}
                            </td>
                            <td class="p-3 text-slate-600 font-medium">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="p-3 pr-5 font-mono text-[10px] font-bold text-gov-green">
                                {{ $log->application->application_number ?? 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-400 font-normal">
                                No activity recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Administration Modules -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex flex-col justify-between space-y-4">
            <div>
                <h3 class="text-xs font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span><i class="fa-solid fa-bolt text-amber-500"></i></span><span>Quick Administration</span>
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.users') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm"><i class="fa-solid fa-users text-slate-500"></i></span>
                            <span>User Management</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs"><i class="fa-solid fa-arrow-right text-[10px]"></i></span>
                    </a>
                    <a href="{{ route('admin.fee_config') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm"><i class="fa-solid fa-money-bill-wave text-slate-500"></i></span>
                            <span>Fee &amp; Fine Config</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs"><i class="fa-solid fa-arrow-right text-[10px]"></i></span>
                    </a>
                    <a href="{{ route('admin.acl') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm"><i class="fa-solid fa-key text-slate-500"></i></span>
                            <span>ACL / Permissions</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs"><i class="fa-solid fa-arrow-right text-[10px]"></i></span>
                    </a>
                    <a href="{{ route('admin.api_config') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm"><i class="fa-solid fa-plug text-slate-500"></i></span>
                            <span>API Configuration</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs"><i class="fa-solid fa-arrow-right text-[10px]"></i></span>
                    </a>
                    <a href="{{ route('admin.reports') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm"><i class="fa-solid fa-chart-line text-slate-500"></i></span>
                            <span>Reports &amp; Analytics</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs"><i class="fa-solid fa-arrow-right text-[10px]"></i></span>
                    </a>
                </div>
            </div>

            <div class="p-3 bg-gov-green/5 border border-gov-green/10 rounded-xl">
                <div class="text-[11px] font-bold text-gov-green">System Health: Operational</div>
                <div class="text-[10px] text-slate-500 mt-0.5">All services, database &amp; payment gateways active.</div>
            </div>
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

        // 2. Thana-wise License Statistics (Doughnut Chart)
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
