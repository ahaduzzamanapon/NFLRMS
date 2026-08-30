@extends('layouts.app')
@section('title', 'Certificate Verification')

@section('content')
<div class="w-full space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">
                Certificate Verification
            </h2>
            <p class="text-xs text-slate-500 mt-1 font-normal">
                Real-time national firearms license &amp; permit verification registry &bull; BRS §7.4
            </p>
        </div>
        {{-- <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Registry Live</span>
            </span>
        </div> --}}
    </div>

    <!-- Search Form Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex items-center gap-2">
            <span class="text-lg"><i class="fa-solid fa-magnifying-glass text-gov-green"></i></span>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Verify License or Permit Authenticity</h3>
                <p class="text-xs text-slate-500 font-normal">Enter the reference number printed on the physical license document or QR code label to confirm real-time validity.</p>
            </div>
        </div>

        <form action="{{ route('dashboard.verify') }}" method="GET" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-sm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="license_number" id="license_number" required
                           value="{{ $licenseNumber }}"
                           placeholder="Enter License Reference Number (e.g. FL-0OJA0TX5-2026)"
                           class="w-full pl-10 pr-4 py-2.5 text-xs text-slate-900 bg-white rounded-lg font-mono font-semibold border border-slate-300 focus:ring-1 focus:ring-gov-green focus:border-gov-green outline-none uppercase placeholder:font-sans placeholder:font-normal">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg shadow-sm transition-colors flex items-center justify-center space-x-2 shrink-0">
                    <span><i class="fa-solid fa-magnifying-glass mr-1"></i> Verify Certificate</span>
                </button>
            </div>
        </form>
    </div>

    <!-- VERIFICATION RESULT DISPLAY -->
    @if($status)
        <div class="space-y-6">
            @if($status === 'valid' && $license)
                <!-- VALID LICENSE CARD -->
                <div class="bg-white rounded-xl border border-emerald-500/50 shadow-md overflow-hidden">
                    <!-- Top Banner -->
                    <div class="bg-emerald-700 text-white px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-white text-emerald-800 font-bold flex items-center justify-center text-lg shadow flex-shrink-0">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xs sm:text-sm uppercase tracking-wide">VERIFIED OFFICIAL LICENSE RECORD</h3>
                                <p class="text-[11px] text-emerald-100 font-medium">Valid Firearms License Registered in Government Database</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-white/20 text-white text-[10px] sm:text-[11px] font-bold uppercase rounded-lg border border-white/30 self-start sm:self-auto">
                            STATUS: VALID &amp; ACTIVE
                        </span>
                    </div>

                    <!-- Details Grid -->
                    <div class="p-5 grid grid-cols-1 md:grid-cols-12 gap-6 bg-slate-50/50">

                        <!-- Left: Certificate Details -->
                        <div class="md:col-span-8 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                    <span class="text-[10px] font-semibold text-slate-400 uppercase block">License Number</span>
                                    <span class="font-mono font-bold text-slate-900 text-xs sm:text-sm tracking-wide break-all">{{ $license->license_number }}</span>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                    <span class="text-[10px] font-semibold text-slate-400 uppercase block">License Category / Type</span>
                                    <span class="font-bold text-emerald-800 text-xs">{{ strtoupper($license->type ?? 'Personal Firearm') }}</span>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                    <span class="text-[10px] font-semibold text-slate-400 uppercase block">License Holder Name</span>
                                    <span class="font-bold text-slate-900 text-xs">{{ $license->user->name ?? 'N/A' }}</span>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                    <span class="text-[10px] font-semibold text-slate-400 uppercase block">Issuing Authority</span>
                                    <span class="font-bold text-slate-800 text-xs">
                                        {{ $license->application->district->name ?? 'District Commissioner' }} DC Office
                                    </span>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                    <span class="text-[10px] font-semibold text-slate-400 uppercase block">Issue Date</span>
                                    <span class="font-bold text-slate-800 text-xs">{{ optional($license->issue_date)->format('F d, Y') ?? 'N/A' }}</span>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                    <span class="text-[10px] font-semibold text-slate-400 uppercase block">Expiry Date</span>
                                    <span class="font-bold text-emerald-700 text-xs">{{ optional($license->expiry_date)->format('F d, Y') ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Weapon Specs -->
                            @if(!empty($license->firearm_details))
                                <div class="bg-emerald-50/70 p-4 rounded-xl border border-emerald-200/80 space-y-2">
                                    <h4 class="text-[11px] font-semibold uppercase text-emerald-900 tracking-wider"><i class="fa-solid fa-shield-halved text-emerald-800 mr-1"></i> Authorized Weapon Specifications</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                                        <div><span class="text-slate-400 text-[10px] block">Weapon Type:</span> <strong class="text-slate-800">{{ $license->firearm_details['weapon_type'] ?? 'N/A' }}</strong></div>
                                        <div><span class="text-slate-400 text-[10px] block">Bore Spec:</span> <strong class="text-slate-800">{{ $license->firearm_details['bore'] ?? 'N/A' }}</strong></div>
                                        <div><span class="text-slate-400 text-[10px] block">Purpose:</span> <strong class="text-slate-800">{{ $license->firearm_details['purpose'] ?? 'Personal Protection' }}</strong></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right: Security Verification & Digital Seal -->
                        <div class="md:col-span-4 bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                            <div class="text-center space-y-2 border-b border-slate-100 pb-3">
                                <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Govt Seal" class="w-12 h-12 object-contain mx-auto rounded-full">
                                <h4 class="text-xs font-bold text-slate-900 uppercase leading-snug">Government Registry Verification</h4>
                                <p class="text-[10px] text-slate-400">Ministry of Home Affairs &bull; Firearms Section</p>
                            </div>

                            <div class="space-y-2 text-[11px] text-slate-600">
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="text-slate-400">Digital Seal:</span>
                                    <span class="font-bold text-emerald-700">PASSED</span>
                                </div>
                                <div class="flex justify-between border-b border-slate-100 pb-1">
                                    <span class="text-slate-400">Verification Ref:</span>
                                    <span class="font-mono text-[10px]">REG-{{ time() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Timestamp:</span>
                                    <span class="font-mono text-[10px]">{{ now()->format('Y-m-d H:i:s') }}</span>
                                </div>
                            </div>

                            <div class="pt-2 text-center">
                                <span class="inline-block px-3 py-1 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider border border-emerald-300">
                                    <i class="fa-solid fa-check mr-1"></i> Authentic &amp; Valid
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

            @elseif($status === 'suspended' && $license)
                <!-- SUSPENDED LICENSE CARD -->
                <div class="bg-white rounded-xl border border-amber-500/50 shadow-md overflow-hidden">
                    <div class="bg-amber-600 text-white px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl"><i class="fa-solid fa-triangle-exclamation"></i></span>
                            <div>
                                <h3 class="font-bold text-xs sm:text-sm uppercase">LICENSE STATUS: SUSPENDED</h3>
                                <p class="text-[11px] text-amber-100">License Temporarily Suspended Pending Administrative Review</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-white/20 text-white text-[10px] sm:text-[11px] font-bold uppercase rounded-lg">SUSPENDED</span>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900 leading-relaxed font-normal">
                            Reference Number <strong>{{ $licenseNumber }}</strong> is currently <strong>SUSPENDED</strong>. License holder must report to the local District Commissioner's Office (JM Branch) for clearance.
                        </div>
                    </div>
                </div>

            @else
                <!-- NOT FOUND CARD -->
                <div class="bg-white rounded-xl border border-rose-400/50 shadow-md overflow-hidden">
                    <div class="bg-rose-700 text-white px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl"><i class="fa-solid fa-triangle-exclamation"></i></span>
                            <div>
                                <h3 class="font-bold text-xs sm:text-sm uppercase">REGISTRY RECORD NOT FOUND</h3>
                                <p class="text-[11px] text-rose-100">No active firearms license matching reference "{{ $licenseNumber }}"</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-white/20 text-white text-[10px] sm:text-[11px] font-bold uppercase rounded-lg">NOT FOUND</span>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="p-4 bg-rose-50 rounded-xl border border-rose-200 text-xs text-rose-900 leading-relaxed font-normal space-y-2">
                            <p><strong>Notice:</strong> The entered reference number <code>{{ $licenseNumber }}</code> could not be found in the Ministry of Home Affairs Firearms Registry.</p>
                            <ul class="list-disc pl-5 space-y-1 text-xs text-rose-800">
                                <li>Double check the reference code printed on the physical license document or QR label.</li>
                                <li>Ensure correct formatting without spaces or typos.</li>
                                <li>If you suspect a counterfeit certificate, report it immediately to the nearest District Commissioner (DC) Office.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Guidance Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 pt-2">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-9 h-9 rounded-lg bg-gov-green/10 text-gov-green flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <h3 class="font-bold text-xs text-slate-900">QR Code Instant Scanning</h3>
            <p class="text-xs text-slate-500 leading-relaxed font-normal">
                Scanning the encrypted QR Code on booklets or permits verifies authentic license data against the central registry.
            </p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-9 h-9 rounded-lg bg-gov-green/10 text-gov-green flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3 class="font-bold text-xs text-slate-900">Cryptographic Integrity</h3>
            <p class="text-xs text-slate-500 leading-relaxed font-normal">
                Verifies essential validity status directly from the Ministry of Home Affairs database under BRS §7.4.
            </p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-9 h-9 rounded-lg bg-gov-green/10 text-gov-green flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <h3 class="font-bold text-xs text-slate-900">Reporting Forgeries</h3>
            <p class="text-xs text-slate-500 leading-relaxed font-normal">
                If physical document parameters do not match registry records, notify the District Commissioner's Office immediately.
            </p>
        </div>
    </div>

</div>
@endsection
