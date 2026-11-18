@extends('layouts.app')
@section('title', 'Citizen Licensees Overview')

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
                <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">Citizen Licensees Overview</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Directory of verified individual citizen licensees and personal firearm holders</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $backRoute }}" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-gauge text-slate-500"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Citizens</span>
                <div class="text-2xl font-extrabold text-blue-600 tracking-tight mt-0.5">{{ $totalCitizens }}</div>
                <span class="text-[10px] text-slate-500 font-medium">Enrolled citizen applicants</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Active Licensees</span>
                <div class="text-2xl font-extrabold text-emerald-600 tracking-tight mt-0.5">{{ $activeCitizens }}</div>
                <span class="text-[10px] text-emerald-700 font-medium">Cleared and actively licensed</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Firearms in Possession</span>
                <div class="text-2xl font-extrabold text-cyan-700 tracking-tight mt-0.5">{{ $totalFirearmsHeld }}</div>
                <span class="text-[10px] text-slate-500 font-medium">Personally held authorized arms</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-gun"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Search and Filter Bar -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('overview.citizens') }}" class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, NID, weapon..." class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-gov-green/20 focus:border-gov-green">
                </div>
                <select name="district" onchange="this.form.submit()" class="w-full sm:w-40 py-1.5 px-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-gov-green/20 focus:border-gov-green">
                    <option value="">All Districts</option>
                    @foreach(['Dhaka', 'Chattogram', 'Sylhet', 'Khulna', 'Barisal', 'Rangpur'] as $d)
                        <option value="{{ $d }}" {{ request('district') === $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
                @if(request('search') || request('district'))
                    <a href="{{ route('overview.citizens') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1">Reset</a>
                @endif
            </form>
            <div class="text-xs text-slate-500 font-medium">
                Showing <span class="font-bold text-slate-800">{{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-800">{{ $items->total() }}</span> citizens
            </div>
        </div>

        <!-- Clean Streamlined Table (Essential Columns Only) -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold uppercase text-slate-500 tracking-wider">
                        <th class="p-3.5 pl-5">Citizen Name</th>
                        <th class="p-3.5">National ID (NID)</th>
                        <th class="p-3.5">District</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @forelse($items as $c)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-3.5 pl-5 font-semibold text-slate-900">
                            {{ $c['name'] }}
                            <div class="text-[10px] text-slate-400 font-normal">{{ $c['email'] }}</div>
                        </td>
                        <td class="p-3.5 font-mono font-semibold text-slate-700 text-xs">{{ $c['nid'] }}</td>
                        <td class="p-3.5 text-slate-700 font-medium">{{ $c['district'] }}</td>
                        <td class="p-3.5">
                            @if($c['status'] === 'Active')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-[8px]"></i> Active
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-clock text-[8px]"></i> Pending Verification
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 pr-5 text-right">
                            <a href="{{ route('overview.citizens.show', $c['id']) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gov-green/10 hover:bg-gov-green hover:text-white text-gov-green text-xs font-bold rounded-lg transition-colors">
                                <span>View Details</span> <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 font-medium">No citizen records found matching your search.</td>
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
