@extends('layouts.app')
@section('title', 'Firearm License Dossier · ' . $license['license_number'])

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
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Official Dossier</span>
                    <span class="text-slate-300">•</span>
                    <span class="font-mono text-xs font-semibold text-gov-green">{{ $license['license_number'] }}</span>
                </div>
                <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">{{ $license['name'] }}</h2>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-print text-slate-400"></i> Print Dossier
            </button>
            <a href="{{ $backRoute }}" class="px-3.5 py-2 bg-gov-green hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Status Overview Banner Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-gov-green flex items-center justify-center text-2xl font-bold flex-shrink-0">
                <i class="fa-solid fa-certificate"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono font-bold text-sm text-slate-900">{{ $license['license_number'] }}</span>
                    @if($license['user_type'] === 'Citizen')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                            <i class="fa-solid fa-user text-[8px] mr-1"></i> Citizen Licensee
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100">
                            <i class="fa-solid fa-store text-[8px] mr-1"></i> Commercial Dealer
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">Application Reference: <span class="font-mono font-semibold text-slate-700">{{ $license['reference'] }}</span> • Jurisdiction: <span class="font-semibold text-slate-700">{{ $license['thana'] }}, {{ $license['district'] }}</span></p>
            </div>
        </div>
        <div class="text-left sm:text-right">
            @if($license['status'] === 'Approved')
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <i class="fa-solid fa-circle-check text-[10px]"></i> Active &amp; Approved
                </div>
            @elseif($license['status'] === 'Pending')
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <i class="fa-solid fa-hourglass-half text-[10px]"></i> Pending Review
                </div>
            @else
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Suspended
                </div>
            @endif
            <div class="text-[11px] text-slate-400 mt-1">Verified on {{ $license['verified_date'] }}</div>
        </div>
    </div>

    <!-- Structured Dossier Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Card 1: Official License Credentials -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-stamp"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">License Credentials &amp; Authorization</h3>
                    <p class="text-[10px] text-slate-400">Statutory licensing details under Arms Act 1878</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">License Number</span>
                    <span class="font-mono font-bold text-gov-green text-xs mt-0.5 block">{{ $license['license_number'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Application Tracking Ref</span>
                    <span class="font-mono font-bold text-slate-800 text-xs mt-0.5 block">{{ $license['reference'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Date of Issuance</span>
                    <span class="font-mono font-semibold text-slate-700 text-xs mt-0.5 block">{{ $license['issue_date'] }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Date of Expiry</span>
                    <span class="font-mono font-semibold text-slate-700 text-xs mt-0.5 block">{{ $license['expiry_date'] }}</span>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs flex justify-between items-center">
                <span class="text-slate-500 font-medium">Issuing Government Authority:</span>
                <span class="font-semibold text-slate-900">{{ $license['issuer'] }}</span>
            </div>
        </div>

        <!-- Card 2: Licensee & Premises Record -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Licensee Identity &amp; Location</h3>
                    <p class="text-[10px] text-slate-400">Jurisdiction, thana and registered address records</p>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Holder / Legal Entity:</span>
                    <span class="font-bold text-slate-900">{{ $license['name'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Applicant Classification:</span>
                    <span class="font-semibold text-slate-800">{{ $license['user_type'] }} Entity</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Administrative District:</span>
                    <span class="font-bold text-slate-900">{{ $license['district'] }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Police Station (Thana):</span>
                    <span class="font-semibold text-slate-800">{{ $license['thana'] }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-slate-500 font-medium">Registered Premises Address:</span>
                    <span class="font-medium text-slate-800 text-right max-w-[280px]">{{ $license['address'] }}</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Firearm Specifications -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-gun"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Authorized Weapon Specifications</h3>
                    <p class="text-[10px] text-slate-400">Firearm model, bore and ballistic parameters</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3 bg-cyan-50/40 rounded-xl border border-cyan-100">
                    <span class="text-[10px] uppercase font-bold text-cyan-700 block tracking-wider">Authorized Weapon</span>
                    <span class="font-bold text-slate-900 text-sm mt-1 block">{{ $license['weapon_type'] }}</span>
                </div>
                <div class="p-3 bg-cyan-50/40 rounded-xl border border-cyan-100">
                    <span class="text-[10px] uppercase font-bold text-cyan-700 block tracking-wider">Calibre / Bore</span>
                    <span class="font-mono font-bold text-cyan-800 text-sm mt-1 block">{{ $license['bore'] }}</span>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Security Clearance Level:</span>
                    <span class="font-semibold text-slate-800">MoHA Verified Level 1</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Ballistics Verification:</span>
                    <span class="font-mono text-slate-700 font-medium">Completed &amp; Registered</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Compliance & Regulatory Log -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Regulatory Audit &amp; Compliance Notes</h3>
                    <p class="text-[10px] text-slate-400">Official observations and enforcement standing</p>
                </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-3">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 block tracking-wider">Status Observation</span>
                    <p class="text-slate-700 font-medium mt-1 leading-relaxed">{{ $license['status_note'] }}</p>
                </div>
                <div class="pt-3 border-t border-slate-200/80 flex justify-between items-center text-slate-500">
                    <span>Audit Record Date:</span>
                    <span class="font-mono font-semibold text-slate-800">{{ $license['verified_date'] }}</span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ $backRoute }}" class="text-xs text-gov-green hover:underline font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i> Return to License Register
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
