<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification Portal - NFLRMS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;750;800&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
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
            background-color: #f4f6f5;
        }
        .text-serif {
            font-family: 'Lora', serif;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased text-slate-800">

    <!-- Top Tiny Bar -->
    <div class="bg-gov-deep text-white/90 text-[9px] font-black uppercase tracking-wider py-2.5 px-6 border-b border-white/5">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <span>Government of the People's Republic of Bangladesh &bull; Ministry of Home Affairs</span>
            <span>Public Certificate Registry &bull; GRS-Enabled</span>
        </div>
    </div>

    <!-- Main Navigation / Header -->
    <header class="bg-white border-b border-slate-200/80 px-6 py-4 flex-shrink-0 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="/" class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors flex items-center space-x-1.5">
                <span>&larr;</span>
                <span>Back to NFLRMS</span>
            </a>
            
            <div class="flex items-center space-x-3.5">
                <img src="https://flms.lovable.app/__l5e/assets-v1/acbf4783-ce0b-43bc-b0fd-4ba7908c84b3/govt-logo.png" alt="Seal" class="w-10 h-10 object-contain"/>
                <div>
                    <h1 class="text-xs font-black uppercase tracking-wider leading-none text-gov-deep">NFLRMS</h1>
                    <p class="text-[9px] text-slate-500 font-bold uppercase mt-1 leading-none">Certificate Verification Portal</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Body Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        <!-- Left Side: Informative Text -->
        <div class="lg:col-span-6 space-y-6">
            <span class="inline-block px-3 py-1 rounded-full border border-gov-accent/30 bg-gov-accent/5 text-gov-accent text-[9px] font-black uppercase tracking-wider">
                🛡️ Public Verification
            </span>
            
            <h2 class="text-4xl md:text-5xl font-bold text-serif text-gov-deep leading-[1.15]">
                Verify a Firearm or<br>Dealing Licence
            </h2>
            
            <p class="text-slate-500 text-xs md:text-sm leading-relaxed max-w-lg font-semibold">
                Confirm the authenticity of any certificate issued under the Arms Rules 1924 or Explosive Substances Rules 1981. Scan the QR on the printed certificate or enter the licence reference below.
            </p>

            <div class="flex flex-wrap gap-6 text-[10px] text-slate-400 font-bold pt-4">
                <span class="flex items-center space-x-2">
                    <span>👤</span> <span>Privacy-preserving</span>
                </span>
                <span class="flex items-center space-x-2">
                    <span>✓</span> <span>FR-CRT-05 compliant</span>
                </span>
                <span class="flex items-center space-x-2">
                    <span>🏛️</span> <span>MoHA registry</span>
                </span>
            </div>
        </div>

        <!-- Right Side: Lookup Card -->
        <div class="lg:col-span-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
                <!-- Card Header -->
                <div class="bg-gov-deep p-4 text-white flex items-center space-x-2">
                    <span class="text-sm">🛡️</span>
                    <span class="text-[10px] font-black uppercase tracking-wider">Certificate Lookup</span>
                </div>
                
                <div class="p-6 space-y-6">
                    <form action="/verify" method="GET" class="space-y-4">
                        <div>
                            <label for="license_number" class="block text-[10px] font-extrabold uppercase text-slate-400 mb-2">Licence / Reference Number</label>
                            <div class="flex space-x-2">
                                <div class="relative flex-grow">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">🔍</span>
                                    <input type="text" name="license_number" id="license_number" required
                                           class="w-full pl-9 pr-3.5 py-3 text-xs rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green font-mono font-bold"
                                           placeholder="e.g. BD-HND-DHK-004521" value="{{ $licenseNumber }}">
                                </div>
                                <button type="submit" class="px-5 py-3 bg-gov-green hover:bg-gov-light text-white text-xs font-bold rounded-lg flex items-center space-x-1.5 shadow transition-all">
                                    <span>✓</span>
                                    <span>Verify</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- SEARCH RESULT RESULT (If searched) -->
                    @if($status)
                        <div class="p-4 rounded-xl border {{ $status === 'valid' ? 'border-emerald-200 bg-emerald-500/5 text-emerald-800' : ($status === 'suspended' ? 'border-rose-200 bg-rose-500/5 text-rose-800' : 'border-slate-200 bg-slate-100 text-slate-500') }} text-xs">
                            <span class="text-[8px] font-extrabold uppercase tracking-wider block mb-1">Lookup Result</span>
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="font-mono font-bold">{{ $licenseNumber }}</span>
                                    @if($status === 'valid')
                                        <p class="text-[10px] font-semibold mt-1">Status: Valid Digital Firearm Certificate</p>
                                    @elseif($status === 'suspended')
                                        <p class="text-[10px] font-semibold mt-1">Status: Suspended (Security clearance review pending)</p>
                                    @else
                                        <p class="text-[10px] font-semibold mt-1">Status: Not Found (Invalid license reference)</p>
                                    @endif
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $status === 'valid' ? 'bg-emerald-50 border border-emerald-500/20 text-emerald-700' : ($status === 'suspended' ? 'bg-rose-50 border border-rose-500/20 text-rose-700' : 'bg-slate-200 border border-slate-300 text-slate-600') }}">
                                    {{ $status === 'valid' ? 'Valid' : ($status === 'suspended' ? 'Suspended' : 'Not Found') }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Try a Sample list -->
                    <div class="space-y-3 pt-2 border-t border-slate-100">
                        <span class="block text-[10px] font-extrabold uppercase text-slate-400">Try a Sample</span>
                        
                        <div class="space-y-2">
                            @if($sampleLicenses->isEmpty())
                                <p class="text-[10px] text-slate-400 font-semibold py-2">No licenses issued yet. Submit an application to get started.</p>
                            @else
                                @foreach($sampleLicenses as $sl)
                                    <button onclick="setSample('{{ $sl->license_number }}')" class="w-full text-left p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors flex items-center justify-between text-xs">
                                        <div>
                                            <span class="font-mono font-bold text-slate-800">{{ $sl->license_number }}</span>
                                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $sl->user->name ?? 'Unknown' }} &bull; {{ $sl->firearm_details['weapon_type'] ?? 'Firearm' }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase
                                            {{ $sl->status === 'active' ? 'bg-emerald-50 border border-emerald-500/20 text-emerald-700' : 'bg-rose-50 border border-rose-500/20 text-rose-700' }}">
                                            {{ ucfirst($sl->status) }}
                                        </span>
                                    </button>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Bottom Features (3 columns) -->
    <section class="bg-white border-t border-slate-200/80 py-10 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-2">
                <span class="text-lg">📱</span>
                <h4 class="text-xs font-bold text-slate-900 font-serif">Scan the QR</h4>
                <p class="text-[10px] text-slate-500 leading-relaxed font-semibold">Every printed certificate carries a QR code that opens this page pre-filled.</p>
            </div>
            <div class="space-y-2">
                <span class="text-lg">🛡️</span>
                <h4 class="text-xs font-bold text-slate-900 font-serif">Only public status</h4>
                <p class="text-[10px] text-slate-500 leading-relaxed font-semibold">We disclose only Valid, Expired, Suspended, or Not Found &mdash; no personal data leaks.</p>
            </div>
            <div class="space-y-2">
                <span class="text-lg">🏛️</span>
                <h4 class="text-xs font-bold text-slate-900 font-serif">Report a forgery</h4>
                <p class="text-[10px] text-slate-500 leading-relaxed font-semibold">If a physical certificate does not match this result, notify the nearest DC Office.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gov-deep text-slate-300 py-6 px-6 border-t border-white/5 flex-shrink-0 text-white">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between text-xs gap-4">
            <div class="text-[10px]">
                &copy; 2026 Ministry of Home Affairs &bull; Bangladesh Secretariat, Dhaka-1000
            </div>
            <div class="text-[10px] font-bold">
                nflrms.gov.bd
            </div>
        </div>
    </footer>

    <script>
        function setSample(ref) {
            document.getElementById('license_number').value = ref;
            // Auto submit
            document.getElementById('license_number').form.submit();
        }
    </script>

</body>
</html>
