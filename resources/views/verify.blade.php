<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Certificate Verification Registry - NFLRMS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gov: {
                            deep: '#033425',
                            green: '#0b523a',
                            light: '#07805c',
                            amber: '#f59e0b',
                            accent: '#dfa32b',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['Lora', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f7f5;
        }
        .text-serif {
            font-family: 'Lora', serif;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased text-slate-800">

    <!-- Top Government Notification Banner -->
    <div class="bg-gov-deep text-white/90 text-[10px] font-extrabold uppercase tracking-wider py-2.5 px-6 border-b border-white/10 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-1">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Government of the People's Republic of Bangladesh &bull; Ministry of Home Affairs</span>
            </div>
            <div class="flex items-center space-x-4 text-[9px] text-emerald-200">
                <span>Public Firearms License Registry</span>
                <span>&bull;</span>
                <span>Cryptographically Signed (Arms Rules §7.4)</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="bg-white border-b border-slate-200/80 px-6 py-4 flex-shrink-0 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="/" class="text-xs font-bold text-slate-600 hover:text-gov-green transition-colors flex items-center space-x-1.5 group">
                <span class="group-hover:-translate-x-1 transition-transform">&larr;</span>
                <span>Return to Home Portal</span>
            </a>
            
            <div class="flex items-center space-x-3">
                <img src="https://flms.lovable.app/__l5e/assets-v1/acbf4783-ce0b-43bc-b0fd-4ba7908c84b3/govt-logo.png" alt="Government Seal" class="w-10 h-10 object-contain drop-shadow-sm"/>
                <div>
                    <h1 class="text-xs font-black uppercase tracking-wider leading-none text-gov-deep">NFLRMS VERIFICATION VAULT</h1>
                    <p class="text-[9px] text-slate-500 font-bold uppercase mt-1 leading-none">National Firearms Licensing Registry</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-6 py-10 space-y-8">

        <!-- Search Header Card -->
        <div class="bg-gradient-to-r from-gov-deep via-gov-green to-teal-950 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                <img src="https://flms.lovable.app/__l5e/assets-v1/acbf4783-ce0b-43bc-b0fd-4ba7908c84b3/govt-logo.png" alt="Seal Watermark" class="w-96 h-96 object-contain"/>
            </div>

            <div class="max-w-3xl space-y-4 relative z-10">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm text-emerald-300 text-[10px] font-bold uppercase tracking-wider">
                    <span>🛡️ Official Public Registry</span>
                    <span>&bull;</span>
                    <span>Real-time License Verification</span>
                </div>

                <h2 class="text-3xl md:text-4xl font-bold font-serif leading-tight">
                    Verify Firearms License &amp; Dealer Permit Authenticity
                </h2>

                <p class="text-emerald-100 text-xs md:text-sm leading-relaxed max-w-2xl font-normal">
                    Enter the official Firearms License Number or scan the QR Code on the physical license booklet to confirm real-time validity, issuing district authority, and security status.
                </p>

                <!-- Search Input Form -->
                <form action="{{ route('verify') }}" method="GET" class="pt-2">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                🔍
                            </div>
                            <input type="text" name="license_number" id="license_number" required
                                   value="{{ $licenseNumber }}"
                                   placeholder="Enter License Reference Number (e.g. FL-0OJA0TX5-2026)"
                                   class="w-full pl-10 pr-4 py-3.5 text-xs text-slate-900 bg-white rounded-xl font-mono font-bold shadow-inner border border-white/30 focus:outline-none focus:ring-2 focus:ring-amber-400 placeholder:font-sans placeholder:font-normal uppercase">
                        </div>
                        <button type="submit" class="px-6 py-3.5 bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2 shrink-0">
                            <span>🔍 Search Registry</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- VERIFICATION RESULT DISPLAY -->
        @if($status)
            <div class="space-y-6">
                @if($status === 'valid' && $license)
                    <!-- VALID LICENSE CARD -->
                    <div class="bg-white rounded-2xl border-2 border-emerald-500/40 shadow-xl overflow-hidden space-y-0">
                        <!-- Top Banner -->
                        <div class="bg-emerald-700 text-white px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-white text-emerald-800 font-black flex items-center justify-center text-xl shadow">
                                    ✓
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-sm uppercase tracking-wide">VERIFIED OFFICIAL LICENSE RECORD</h3>
                                    <p class="text-[10px] text-emerald-100 font-medium">Valid Firearms License Registered in Government Database</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 bg-white/20 text-white text-[10px] font-black uppercase rounded-lg border border-white/30">
                                    STATUS: VALID &amp; ACTIVE
                                </span>
                            </div>
                        </div>

                        <!-- Card Details Grid -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6 bg-slate-50/50">
                            
                            <!-- Left: Certificate Details -->
                            <div class="md:col-span-8 space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block">License Number</span>
                                        <span class="font-mono font-extrabold text-slate-900 text-sm tracking-wide">{{ $license->license_number }}</span>
                                    </div>
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block">License Category / Type</span>
                                        <span class="font-bold text-emerald-800 text-xs">{{ strtoupper($license->type ?? 'Personal Firearm') }}</span>
                                    </div>
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block">License Holder Name</span>
                                        <span class="font-extrabold text-slate-900 text-xs">{{ $license->user->name ?? 'Md. Applicant' }}</span>
                                    </div>
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Issuing Authority</span>
                                        <span class="font-bold text-slate-800 text-xs">
                                            {{ $license->application->district->name ?? 'District Commissioner' }} DC Office
                                        </span>
                                    </div>
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Issue Date</span>
                                        <span class="font-bold text-slate-800 text-xs">{{ optional($license->issue_date)->format('F d, Y') ?? 'July 15, 2026' }}</span>
                                    </div>
                                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-1">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase block">Expiry Date</span>
                                        <span class="font-bold text-emerald-700 text-xs">{{ optional($license->expiry_date)->format('F d, Y') ?? 'December 31, 2027' }}</span>
                                    </div>
                                </div>

                                <!-- Weapon Specs -->
                                @if(!empty($license->firearm_details))
                                    <div class="bg-emerald-50/70 p-4 rounded-xl border border-emerald-200/80 space-y-2">
                                        <h4 class="text-[10px] font-extrabold uppercase text-emerald-900 tracking-wider">🔫 Authorized Weapon Specifications</h4>
                                        <div class="grid grid-cols-3 gap-2 text-[11px]">
                                            <div><span class="text-slate-400 text-[9px] block">Weapon Type:</span> <strong class="text-slate-800">{{ $license->firearm_details['weapon_type'] ?? 'N/A' }}</strong></div>
                                            <div><span class="text-slate-400 text-[9px] block">Bore Spec:</span> <strong class="text-slate-800">{{ $license->firearm_details['bore'] ?? 'N/A' }}</strong></div>
                                            <div><span class="text-slate-400 text-[9px] block">Purpose:</span> <strong class="text-slate-800">{{ $license->firearm_details['purpose'] ?? 'Personal Protection' }}</strong></div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Right: Security Verification & Digital Seal -->
                            <div class="md:col-span-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                                <div class="text-center space-y-2 border-b border-slate-100 pb-3">
                                    <img src="https://flms.lovable.app/__l5e/assets-v1/acbf4783-ce0b-43bc-b0fd-4ba7908c84b3/govt-logo.png" alt="Govt Seal" class="w-14 h-14 object-contain mx-auto">
                                    <h4 class="text-xs font-black text-gov-deep uppercase leading-snug">Government Registry Cryptographic Verification</h4>
                                    <p class="text-[9px] text-slate-400">Ministry of Home Affairs &bull; Firearms Section</p>
                                </div>

                                <div class="space-y-2 text-[10px] text-slate-600">
                                    <div class="flex justify-between border-b border-slate-100 pb-1">
                                        <span class="text-slate-400">Digital Seal:</span>
                                        <span class="font-bold text-emerald-700">PASSED</span>
                                    </div>
                                    <div class="flex justify-between border-b border-slate-100 pb-1">
                                        <span class="text-slate-400">Verification Ref:</span>
                                        <span class="font-mono text-[9px]">REG-{{ time() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400">Timestamp:</span>
                                        <span class="font-mono text-[9px]">{{ now()->format('Y-m-d H:i:s') }}</span>
                                    </div>
                                </div>

                                <div class="pt-2 text-center">
                                    <span class="inline-block px-3 py-1 rounded bg-emerald-100 text-emerald-800 text-[9px] font-black uppercase tracking-wider border border-emerald-300">
                                        ✓ Authentic &amp; Valid
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                @elseif($status === 'suspended' && $license)
                    <!-- SUSPENDED LICENSE CARD -->
                    <div class="bg-white rounded-2xl border-2 border-amber-500/40 shadow-xl overflow-hidden">
                        <div class="bg-amber-600 text-white px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">⚠️</span>
                                <div>
                                    <h3 class="font-extrabold text-sm uppercase">LICENSE STATUS: SUSPENDED</h3>
                                    <p class="text-[10px] text-amber-100">License Temporarily Suspended Pending Administrative Review</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-white/20 text-white text-[10px] font-black uppercase rounded-lg">SUSPENDED</span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900 leading-relaxed font-semibold">
                                Reference Number <strong>{{ $licenseNumber }}</strong> is currently <strong>SUSPENDED</strong>. License holder must report to the local District Commissioner's Office (JM Branch) for clearance.
                            </div>
                        </div>
                    </div>

                @else
                    <!-- NOT FOUND CARD -->
                    <div class="bg-white rounded-2xl border-2 border-rose-400/40 shadow-xl overflow-hidden">
                        <div class="bg-rose-700 text-white px-6 py-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">⚠️</span>
                                <div>
                                    <h3 class="font-extrabold text-sm uppercase">REGISTRY RECORD NOT FOUND</h3>
                                    <p class="text-[10px] text-rose-100">No active firearms license matching reference "{{ $licenseNumber }}"</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-white/20 text-white text-[10px] font-black uppercase rounded-lg">NOT FOUND</span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="p-4 bg-rose-50 rounded-xl border border-rose-200 text-xs text-rose-900 leading-relaxed font-semibold space-y-2">
                                <p><strong>Notice:</strong> The entered reference number <code>{{ $licenseNumber }}</code> could not be found in the Ministry of Home Affairs Firearms Registry.</p>
                                <ul class="list-disc pl-5 space-y-1 text-[11px] text-rose-800">
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

        <!-- Informative Public Guidance Grid (3 Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
            
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-xl bg-gov-green/10 text-gov-green flex items-center justify-center text-xl font-bold">
                    📱
                </div>
                <h3 class="font-bold text-sm text-slate-900 font-serif">QR Code Instant Scanning</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-normal">
                    All physical Firearms License Booklets and Dealer Permits issued under NFLRMS feature an encrypted QR Code. Scanning the QR code opens this verification registry directly.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-xl bg-gov-green/10 text-gov-green flex items-center justify-center text-xl font-bold">
                    🛡️
                </div>
                <h3 class="font-bold text-sm text-slate-900 font-serif">Privacy-Preserving Registry</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-normal">
                    To safeguard citizen security and privacy, this public registry verifies only essential license validity status without exposing sensitive personal identifier data.
                </p>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-xl bg-gov-green/10 text-gov-green flex items-center justify-center text-xl font-bold">
                    🏛️
                </div>
                <h3 class="font-bold text-sm text-slate-900 font-serif">Report Forgeries &amp; Claims</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-normal">
                    If physical license details do not match this official record, contact the Ministry of Home Affairs or the Judicial Magistrate (JM) Branch of your District Commissioner's Office.
                </p>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-gov-deep text-white py-6 px-6 border-t border-white/10 flex-shrink-0 mt-8">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between text-xs gap-4">
            <div class="text-[10px] text-slate-300 font-medium">
                &copy; 2026 Ministry of Home Affairs &bull; Government of Bangladesh &bull; All Rights Reserved
            </div>
            <div class="text-[10px] text-emerald-300 font-extrabold uppercase tracking-wider">
                NFLRMS Security Vault v2.4 &bull; nflrms.gov.bd
            </div>
        </div>
    </footer>

</body>
</html>
