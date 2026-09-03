@extends('layouts.app')
@section('title', 'Registered Arms Dealer Dossier · ' . $dealer['name'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Top Navigation & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ $backRoute }}" class="p-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 transition-colors shadow-sm flex items-center justify-center">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Arms Dealer Dossier</span>
                    <span class="text-slate-300">•</span>
                    <span class="font-mono text-xs font-semibold text-gov-green">{{ $dealer['trade_license'] }}</span>
                </div>
                <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">{{ $dealer['name'] }}</h2>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-print text-slate-400"></i> Print Dossier
            </button>
            <a href="{{ $backRoute }}" class="px-3.5 py-2 bg-gov-green hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Back to Dealers List
            </a>
        </div>
    </div>

    <!-- Overview Banner Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                <i class="fa-solid fa-store"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-lg font-bold text-slate-900">{{ $dealer['name'] }}</h3>
                    @if($dealer['status'] === 'Active')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <i class="fa-solid fa-circle-check text-[8px] mr-1"></i> Active / Licensed
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                            <i class="fa-solid fa-hourglass-half text-[8px] mr-1"></i> Pending Renewal
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">Trade Licence: <span class="font-mono font-semibold text-slate-700">{{ $dealer['trade_license'] }}</span> • Classification: <span class="font-semibold text-slate-700">{{ $dealer['dealer_class'] }}</span></p>
            </div>
        </div>
        <div class="text-left sm:text-right">
            <div class="text-xs font-semibold text-slate-500">Audited Total Stock</div>
            <div class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">{{ number_format($dealer['total_stock']) }} <span class="text-xs font-normal text-slate-400">units</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Valid until {{ $dealer['expiry_date'] }}</div>
        </div>
    </div>

    <!-- Structured Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Card 1: Commercial Credentials -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-file-contract"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Commercial Dealership Credentials</h3>
                    <p class="text-[10px] text-slate-400">Statutory trade and dealing license authorization</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Trade License No.</span>
                    <span class="font-mono font-bold text-gov-green text-xs mt-0.5 block">{{ $dealer['trade_license'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">License Category</span>
                    <span class="font-bold text-slate-800 text-xs mt-0.5 block">{{ $dealer['dealer_class'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Issue Date</span>
                    <span class="font-mono font-semibold text-slate-700 text-xs mt-0.5 block">{{ $dealer['issue_date'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Renewal Deadline</span>
                    <span class="font-mono font-semibold text-slate-700 text-xs mt-0.5 block">{{ $dealer['expiry_date'] }}</span>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs flex justify-between items-center">
                <span class="text-slate-500 font-medium">Dealership Standing:</span>
                <span class="font-semibold text-slate-900">{{ $dealer['status'] }} Regulatory Standing</span>
            </div>
        </div>

        <!-- Card 2: Proprietorship & Premises -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Proprietor &amp; Armoury Premises</h3>
                    <p class="text-[10px] text-slate-400">Management signatory and commercial location records</p>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Proprietor / Managing Partner:</span>
                    <span class="font-bold text-slate-900">{{ $dealer['proprietor'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Corporate Telephone:</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $dealer['phone'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Corporate Email:</span>
                    <span class="font-mono font-medium text-slate-800">{{ $dealer['email'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">District Jurisdiction:</span>
                    <span class="font-bold text-slate-900">{{ $dealer['district'] }} ({{ $dealer['thana'] }})</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-slate-500 font-medium">Premises / Vault Address:</span>
                    <span class="font-medium text-slate-800 text-right max-w-[280px]">{{ $dealer['address'] }}</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Audited Stock Inventory -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Audited Inventory &amp; Quota Stock</h3>
                    <p class="text-[10px] text-slate-400">Physical stock audit and approved quota holdings</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 text-xs text-center">
                <div class="p-3 bg-cyan-50/50 rounded-xl border border-cyan-100">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block tracking-wider">Firearms</span>
                    <span class="font-mono font-extrabold text-cyan-800 text-lg mt-1 block">{{ number_format($dealer['total_firearms']) }}</span>
                    <span class="text-[9px] text-slate-400">units</span>
                </div>
                <div class="p-3 bg-indigo-50/50 rounded-xl border border-indigo-100">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block tracking-wider">Ammunition</span>
                    <span class="font-mono font-extrabold text-indigo-800 text-lg mt-1 block">{{ number_format($dealer['total_ammo']) }}</span>
                    <span class="text-[9px] text-slate-400">rounds</span>
                </div>
                <div class="p-3 bg-slate-100/70 rounded-xl border border-slate-200">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block tracking-wider">Total Units</span>
                    <span class="font-mono font-extrabold text-slate-900 text-lg mt-1 block">{{ number_format($dealer['total_stock']) }}</span>
                    <span class="text-[9px] text-slate-400">items</span>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Vault Security Certification:</span>
                    <span class="font-semibold text-emerald-700">Class 1 Safe / Double-Lock Verified</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Last Stock Audit Reconciliation:</span>
                    <span class="font-mono text-slate-700 font-medium">Reconciled &amp; Signed</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Compliance & Regulatory Actions -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Regulatory Standing &amp; Oversight</h3>
                    <p class="text-[10px] text-slate-400">MoHA and District Commissioner oversight status</p>
                </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Regulatory Status:</span>
                    @if($dealer['status'] === 'Active')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            Authorized Dealer
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                            Renewal Due
                        </span>
                    @endif
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">District Firearms Registry:</span>
                    <span class="font-semibold text-slate-800">Synchronized (DC {{ $dealer['district'] }})</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">MoHA Oversight:</span>
                    <span class="font-semibold text-slate-800">Clearance Current</span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ $backRoute }}" class="text-xs text-gov-green hover:underline font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i> Return to Dealers Register
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
