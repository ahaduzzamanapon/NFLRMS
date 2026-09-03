@extends('layouts.app')
@section('title', 'Citizen Licensee Dossier · ' . $citizen['name'])

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
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Citizen Dossier</span>
                    <span class="text-slate-300">•</span>
                    <span class="font-mono text-xs font-semibold text-blue-600">NID: {{ $citizen['nid'] }}</span>
                </div>
                <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">{{ $citizen['name'] }}</h2>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-print text-slate-400"></i> Print Dossier
            </button>
            <a href="{{ $backRoute }}" class="px-3.5 py-2 bg-gov-green hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Back to Citizens List
            </a>
        </div>
    </div>

    <!-- Overview Banner Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-lg font-bold text-slate-900">{{ $citizen['name'] }}</h3>
                    @if($citizen['status'] === 'Active')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <i class="fa-solid fa-circle-check text-[8px] mr-1"></i> Active Licensee
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                            <i class="fa-solid fa-clock text-[8px] mr-1"></i> Pending Verification
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">National ID: <span class="font-mono font-semibold text-slate-700">{{ $citizen['nid'] }}</span> • Jurisdiction: <span class="font-semibold text-slate-700">{{ $citizen['thana'] }}, {{ $citizen['district'] }}</span></p>
            </div>
        </div>
        <div class="text-left sm:text-right">
            <div class="text-xs font-semibold text-slate-500">Firearms in Possession</div>
            <div class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">{{ $citizen['total_firearms'] }} <span class="text-xs font-normal text-slate-400">units</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Enrolled on {{ $citizen['registration_date'] }}</div>
        </div>
    </div>

    <!-- Structured Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Card 1: Identification & Contact -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-id-badge"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Personal Identity &amp; Contact Records</h3>
                    <p class="text-[10px] text-slate-400">Verified Citizen Identification and direct contact channels</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Full Legal Name</span>
                    <span class="font-bold text-slate-900 text-xs mt-0.5 block">{{ $citizen['name'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">National ID (NID)</span>
                    <span class="font-mono font-bold text-blue-700 text-xs mt-0.5 block">{{ $citizen['nid'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Contact Phone</span>
                    <span class="font-mono font-semibold text-slate-800 text-xs mt-0.5 block">{{ $citizen['phone'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Email Address</span>
                    <span class="font-medium text-slate-800 text-xs mt-0.5 block truncate">{{ $citizen['email'] }}</span>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs flex justify-between items-center">
                <span class="text-slate-500 font-medium">Initial System Registration:</span>
                <span class="font-mono font-semibold text-slate-800">{{ $citizen['registration_date'] }}</span>
            </div>
        </div>

        <!-- Card 2: Jurisdiction & Residence -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Residential Address &amp; Jurisdiction</h3>
                    <p class="text-[10px] text-slate-400">Police station jurisdiction and permanent address record</p>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">District Authority:</span>
                    <span class="font-bold text-slate-900">{{ $citizen['district'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Police Station (Thana):</span>
                    <span class="font-semibold text-slate-800">{{ $citizen['thana'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Present Residence Address:</span>
                    <span class="font-medium text-slate-800 text-right max-w-[280px]">{{ $citizen['address'] }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-slate-500 font-medium">Special Branch (SB) Verification:</span>
                    <span class="font-semibold text-emerald-700">Jurisdiction Cleared</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Economic & Professional Standing -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Occupational &amp; Financial Standing</h3>
                    <p class="text-[10px] text-slate-400">Professional status and declared income eligibility</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Profession / Sector</span>
                    <span class="font-bold text-slate-900 text-xs mt-1 block">{{ $citizen['occupation'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Declared Annual Income</span>
                    <span class="font-mono font-bold text-slate-900 text-xs mt-1 block">BDT {{ number_format($citizen['annual_income']) }}</span>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tax Returns Assessment:</span>
                    <span class="font-semibold text-emerald-700">Compliant (3 Years Filed)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Eligibility Minimum:</span>
                    <span class="text-slate-700 font-medium">Exceeds Statutory Threshold</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Authorized Firearms Holdings -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-gun"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Authorized Firearm Holdings</h3>
                    <p class="text-[10px] text-slate-400">Personal protection firearm and ballistics record</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-cyan-50/40 rounded-xl border border-cyan-100">
                    <span class="text-[10px] uppercase font-bold text-cyan-700 block tracking-wider">Weapon Model</span>
                    <span class="font-bold text-slate-900 text-sm mt-1 block">{{ $citizen['licensed_weapon'] }}</span>
                </div>
                <div class="p-3 bg-cyan-50/40 rounded-xl border border-cyan-100">
                    <span class="text-[10px] uppercase font-bold text-cyan-700 block tracking-wider">Bore / Calibre</span>
                    <span class="font-mono font-bold text-cyan-800 text-sm mt-1 block">{{ $citizen['bore'] }}</span>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs flex justify-between items-center">
                <span class="text-slate-500 font-medium">Total Registered Firearms in Possession:</span>
                <span class="inline-block px-3 py-1 rounded-lg bg-slate-100 text-slate-900 font-extrabold font-mono text-sm border border-slate-200">
                    {{ $citizen['total_firearms'] }}
                </span>
            </div>
        </div>

    </div>
</div>
@endsection
