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
                <span>👤</span><span>Manage Users</span>
            </a>
            <a href="{{ route('admin.audit_log') }}" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1.5">
                <span>📝</span><span>Audit Trail</span>
            </a>
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">System Users</div>
                <span class="text-base sm:text-lg">👥</span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900">{{ number_format($stats['total_users']) }}</div>
            <div class="text-[10px] text-slate-500 mt-1 flex flex-wrap items-center gap-1.5 sm:gap-2">
                <span class="text-emerald-600 font-semibold">✓ {{ $stats['active_users'] }} active</span>
                @if($stats['inactive_users'] > 0)
                <span class="text-rose-500 font-semibold">• {{ $stats['inactive_users'] }} inactive</span>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Applications</div>
                <span class="text-base sm:text-lg">📄</span>
            </div>
            <div class="text-2xl font-extrabold text-gov-green">{{ number_format($stats['total_applications']) }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Submitted nationwide</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Active Licenses</div>
                <span class="text-base sm:text-lg">📜</span>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600">{{ number_format($stats['active_licenses']) }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Issued &amp; verified</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-3.5 sm:p-4 hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Pending Action</div>
                <span class="text-base sm:text-lg">⏳</span>
            </div>
            <div class="text-2xl font-extrabold text-amber-600">{{ number_format($stats['pending_applications']) }}</div>
            <div class="text-[10px] text-slate-500 mt-1">In workflow queues</div>
        </div>
    </div>

    <!-- Middle Section: Application Summary & User Role Distribution -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

        <!-- Application Status Summary -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                        <span>📊</span><span>Application Summary</span>
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
                <a href="{{ route('admin.reports') }}" class="font-semibold text-gov-green hover:underline">View Analytics →</a>
            </div>
        </div>

        <!-- User Roles Overview -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                        <span>🛡️</span><span>User Category Summary</span>
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
                <a href="{{ route('admin.users') }}" class="font-semibold text-gov-green hover:underline">Manage Accounts →</a>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Recent Activities Log & Administrative Shortcuts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">

        <!-- Recent Activities Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-900 flex items-center gap-2">
                    <span>📝</span><span>Recent System Activities</span>
                </h3>
                <a href="{{ route('admin.audit_log') }}" class="text-[10px] font-semibold text-gov-green hover:underline">View Full Log →</a>
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
                    <span>⚡</span><span>Quick Administration</span>
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.users') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">👤</span>
                            <span>User Management</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs">→</span>
                    </a>
                    <a href="{{ route('admin.fee_config') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">💵</span>
                            <span>Fee &amp; Fine Config</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs">→</span>
                    </a>
                    <a href="{{ route('admin.acl') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">🔑</span>
                            <span>ACL / Permissions</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs">→</span>
                    </a>
                    <a href="{{ route('admin.api_config') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">🔌</span>
                            <span>API Configuration</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs">→</span>
                    </a>
                    <a href="{{ route('admin.reports') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-200 transition-all text-xs font-semibold text-slate-700 group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">📊</span>
                            <span>Reports &amp; Analytics</span>
                        </div>
                        <span class="text-slate-400 group-hover:translate-x-0.5 transition-transform text-xs">→</span>
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
@endsection
