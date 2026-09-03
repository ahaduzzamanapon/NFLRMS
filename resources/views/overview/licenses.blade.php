@extends('layouts.app')
@section('title', $pageTitle)

@section('content')
<div class="max-w-full space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                @php
                    $backRoute = auth()->user()->hasRole('system_admin') ? route('admin.dashboard') : route('senior_secretary.dashboard');
                @endphp
                <a href="{{ $backRoute }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors text-xs">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">{{ $pageTitle }}</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1 font-medium">{{ $pageSubtitle }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $backRoute }}" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-gauge text-slate-500"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards (4 Status Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Licenses -->
        <a href="{{ route('overview.licenses') }}" class="bg-white rounded-2xl border {{ empty($statusFilter) || $statusFilter === 'all' ? 'border-gov-green ring-2 ring-gov-green/20' : 'border-slate-200/80' }} shadow-sm p-4 hover:border-gov-green transition-all group block">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Licenses</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-gov-green flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-certificate"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $totalCount }}</div>
            <span class="text-[10px] text-slate-500 font-medium">All categories issued</span>
        </a>

        <!-- Approved -->
        <a href="{{ route('overview.licenses.approved') }}" class="bg-white rounded-2xl border {{ strtolower((string)$statusFilter) === 'approved' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80' }} shadow-sm p-4 hover:border-emerald-400 transition-all group block">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Approved</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-100/70 text-emerald-600 flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600 tracking-tight">{{ $approvedCount }}</div>
            <span class="text-[10px] text-emerald-700 font-medium">Active &amp; valid status</span>
        </a>

        <!-- Pending -->
        <a href="{{ route('overview.licenses.pending') }}" class="bg-white rounded-2xl border {{ strtolower((string)$statusFilter) === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/80' }} shadow-sm p-4 hover:border-amber-400 transition-all group block">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Pending</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-amber-600 tracking-tight">{{ $pendingCount }}</div>
            <span class="text-[10px] text-amber-700 font-medium">In vetting / review</span>
        </a>

        <!-- Suspended -->
        <a href="{{ route('overview.licenses.suspended') }}" class="bg-white rounded-2xl border {{ strtolower((string)$statusFilter) === 'suspended' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200/80' }} shadow-sm p-4 hover:border-rose-400 transition-all group block">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Suspended</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-rose-600 tracking-tight">{{ $suspendedCount }}</div>
            <span class="text-[10px] text-rose-700 font-medium">Under regulatory inquiry</span>
        </a>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Search and Filter Bar -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('overview.licenses') }}" class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search licence, holder, weapon..." class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-gov-green/20 focus:border-gov-green">
                </div>
                <select name="status" onchange="this.form.submit()" class="w-full sm:w-36 py-1.5 px-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-gov-green/20 focus:border-gov-green">
                    <option value="">All Statuses</option>
                    <option value="Approved" {{ (request('status') ?? $statusFilter) === 'Approved' || (request('status') ?? $statusFilter) === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Pending" {{ (request('status') ?? $statusFilter) === 'Pending' || (request('status') ?? $statusFilter) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Suspended" {{ (request('status') ?? $statusFilter) === 'Suspended' || (request('status') ?? $statusFilter) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <select name="user_type" onchange="this.form.submit()" class="w-full sm:w-36 py-1.5 px-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-gov-green/20 focus:border-gov-green">
                    <option value="">All User Types</option>
                    <option value="Citizen" {{ request('user_type') === 'Citizen' ? 'selected' : '' }}>Citizen</option>
                    <option value="Dealer" {{ request('user_type') === 'Dealer' ? 'selected' : '' }}>Dealer</option>
                </select>
                @if(request('search') || request('status') || request('user_type') || $statusFilter)
                    <a href="{{ route('overview.licenses') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1">Reset</a>
                @endif
            </form>
            <div class="text-xs text-slate-500 font-medium">
                Showing <span class="font-bold text-slate-800">{{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-800">{{ $items->total() }}</span> licenses
            </div>
        </div>

        <!-- Clean Streamlined Table (Essential Columns Only) -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold uppercase text-slate-500 tracking-wider">
                        <th class="p-3.5 pl-5">Licence No.</th>
                        <th class="p-3.5">Holder Name</th>
                        <th class="p-3.5">District</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-3.5 pl-5 font-mono text-gov-green font-semibold whitespace-nowrap">
                            {{ $item['license_number'] }}
                        </td>
                        <td class="p-3.5">
                            <div class="font-semibold text-slate-900 flex items-center gap-2">
                                <span>{{ $item['name'] }}</span>
                                @if($item['user_type'] === 'Citizen')
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-100 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-user text-[7px]"></i> Citizen
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-100 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-store text-[7px]"></i> Dealer
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="p-3.5 text-slate-700 font-medium">
                            {{ $item['district'] }}
                        </td>
                        <td class="p-3.5">
                            @if($item['status'] === 'Approved')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-[8px]"></i> Approved
                                </span>
                            @elseif($item['status'] === 'Pending')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-hourglass-half text-[8px]"></i> Pending
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-triangle-exclamation text-[8px]"></i> Suspended
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 pr-5 text-right">
                            <a href="{{ route('overview.licenses.show', ['id' => $item['id'], 'from' => $statusFilter]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gov-green/10 hover:bg-gov-green hover:text-white text-gov-green text-xs font-bold rounded-lg transition-colors">
                                <span>View Details</span> <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 font-medium">No license records found matching your search criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($items->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
