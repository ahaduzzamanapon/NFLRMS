<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>National Firearms Licensing & Renewal Management System (NFLRMS)</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
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
                            gold: '#dfa32b',
                            accent: '#e8a838',
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
            background-color: #fafbfc;
        }
        .hero-bg {
            background: linear-gradient(rgba(3, 52, 37, 0.93), rgba(3, 52, 37, 0.95)), url('https://flms.lovable.app/__l5e/assets-v1/d9f2dc6d-2e28-4bf1-b5d5-e32f5583aae4/home.jpg');
            background-size: cover;
            background-position: center;
        }
        .text-serif {
            font-family: 'Lora', serif;
        }
    </style>
</head>
<body class="antialiased text-slate-800">

    <!-- Navbar -->
    <nav class="bg-gov-deep border-b border-white/5 text-white sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            
            <div class="flex items-center space-x-3.5">
                <img src="https://flms.lovable.app/__l5e/assets-v1/acbf4783-ce0b-43bc-b0fd-4ba7908c84b3/govt-logo.png" alt="Government of Bangladesh" width="40" height="40" class="w-10 h-10 object-contain"/>
                <div class="leading-tight">
                    <h1 class="text-xs font-black uppercase tracking-wider leading-none text-slate-100 text-accent">Govt. of Bangladesh</h1>
                    <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 leading-none">Ministry of Home Affairs</p>
                </div>
            </div>
            
            <!-- Nav Links -->
            <div class="hidden md:flex items-center space-x-10 text-xs font-semibold text-slate-300">
                <a href="#services" class="hover:text-white transition-colors">Services</a>
                <a href="#pricing" class="hover:text-white transition-colors">Pricing</a>
                <a href="{{ route('verify') }}" class="hover:text-white transition-colors">Verify Certificate</a>
            </div>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-3.5">
                <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold text-white hover:bg-white/10 rounded-lg transition-colors border border-white/10">
                    Login
                </a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-gov-accent hover:bg-amber-500 text-slate-950 font-black text-xs rounded-lg shadow-md transition-all">
                    Sign up
                </a>
            </div>

        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-bg text-white py-20 px-6 relative overflow-hidden">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Left Side -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Badge -->
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-gov-accent text-[9px] font-black uppercase tracking-wider">
                    <span>💎</span> <span>NFLRMS . PRODUCTION SYSTEM</span>
                </span>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-serif leading-[1.1]">
                    National Firearms<br>Licensing & Renewal<br>Management System
                </h1>
                
                <p class="text-slate-300 text-xs md:text-sm leading-relaxed max-w-xl font-medium">
                    A single, secure digital platform for citizens, arms dealers, District Commissioners, intelligence agencies, and the Ministry of Home Affairs — preserving every statutory approval of the Arms Rules 1924 & Explosive Substances Rules 1981.
                </p>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('register') }}" class="px-6 py-3.5 rounded-lg bg-gov-accent hover:bg-amber-500 text-slate-950 font-black text-xs shadow-lg transition-transform hover:scale-[1.02] flex items-center space-x-2">
                        <span>Create Citizen / Dealer Account</span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('login') }}" class="px-6 py-3.5 rounded-lg bg-white/10 hover:bg-white/15 border border-white/25 text-white font-bold text-xs transition-colors">
                        Executive Dashboard
                    </a>
                    <a href="{{ route('login') }}" class="px-6 py-3.5 rounded-lg bg-white/10 hover:bg-white/15 border border-white/25 text-white font-bold text-xs transition-colors">
                        Verify a Certificate
                    </a>
                </div>

                <!-- Stats Banner -->
                <div class="grid grid-cols-4 gap-4 pt-10 border-t border-white/10">
                    <div>
                        <h4 class="text-2xl font-bold text-serif text-gov-accent">{{ number_format($stats['total_licenses']) }}</h4>
                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-wider mt-1.5">Active licenses</p>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-serif text-gov-accent">{{ $stats['total_districts'] }}</h4>
                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-wider mt-1.5">Districts served</p>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-serif text-gov-accent">4</h4>
                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-wider mt-1.5">Vetting agencies</p>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-serif text-gov-accent">3</h4>
                        <p class="text-[9px] text-slate-400 uppercase font-black tracking-wider mt-1.5">MoHA tiers</p>
                    </div>
                </div>
            </div>

            <!-- Hero Right Side Graphics Mockup -->
            <div class="lg:col-span-5 relative hidden lg:block">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/20 rotate-1">
                    <img src="https://flms.lovable.app/assets/desk-officials-CIo_RzyK.jpg" alt="Official desk with seal and documents" width="1400" height="900" class="w-full h-72 object-cover" loading="lazy"/>
                </div>
                
                <div class="absolute -bottom-6 -left-6 w-64 bg-white text-slate-800 rounded-xl p-4 shadow-2xl ring-1 ring-black/5 -rotate-2">
                    <div class="flex items-center gap-2 text-xs text-emerald-600 font-bold">
                        <span>✓</span> Certificate Issued
                    </div>
                    <div class="mt-2 font-mono text-xs font-bold text-slate-900">BD-HND-DHK-004521</div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-1">Md. Nasrin Sultana · Revolver</div>
                    <div class="mt-3 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 w-[92%]"></div>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-1">92% of validity remaining</div>
                </div>
                
                <div class="absolute -top-4 -right-4 w-56 bg-white text-slate-800 rounded-xl p-4 shadow-2xl ring-1 ring-black/5 rotate-3">
                    <div class="text-[10px] uppercase text-slate-400 font-bold">Renewal reminder</div>
                    <div class="text-sm font-bold mt-1 text-slate-900">28 days remaining</div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-1">SMS + email dispatched at 60d / 30d / 15d</div>
                </div>
            </div>

        </div>
    </header>

    <!-- Why NFLRMS Features -->
    <section class="max-w-7xl mx-auto py-20 px-6">
        <span class="text-[10px] font-black uppercase text-gov-accent tracking-widest block text-center mb-2">Why NFLRMS</span>
        <h2 class="text-3xl font-bold text-serif text-slate-900 text-center max-w-xl mx-auto leading-tight mb-12">
            A statutory workflow, digitized without losing a single approval.
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="p-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm space-y-4 hover:shadow-md transition-shadow">
                <span class="p-3 rounded-xl bg-emerald-50 text-emerald-600 text-xl inline-block">🛡️</span>
                <h3 class="text-sm font-bold text-serif text-slate-900">Statutory Chain Preserved</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    DC Office &rarr; Police / SB (individuals) or Police/SB/NSI/DGFI (dealers) &rarr; MoHA Political-4 &rarr; Joint Secretary &rarr; Minister. Every signature, every register.
                </p>
            </div>
            <!-- Feature 2 -->
            <div class="p-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm space-y-4 hover:shadow-md transition-shadow">
                <span class="p-3 rounded-xl bg-emerald-50 text-emerald-600 text-xl inline-block">⏳</span>
                <h3 class="text-sm font-bold text-serif text-slate-900">Automatic Renewal Lifecycle</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    60/30/15-day reminders, grace period, 3-tier late-fine engine, auto-suspension, mandatory re-vetting — no case is forgotten.
                </p>
            </div>
            <!-- Feature 3 -->
            <div class="p-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm space-y-4 hover:shadow-md transition-shadow">
                <span class="p-3 rounded-xl bg-emerald-50 text-emerald-600 text-xl inline-block">🏛️</span>
                <h3 class="text-sm font-bold text-serif text-slate-900">Ministry-grade Oversight</h3>
                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Real-time revenue, quota, and heatmap dashboards for the Secretary and Minister — with an emergency national kill-switch.
                </p>
            </div>
        </div>
    </section>

    <!-- Services Workflows -->
    <section id="services" class="max-w-7xl mx-auto py-20 px-6 border-t border-slate-200">
        <div class="flex items-center justify-between mb-12">
            <div>
                <span class="text-[10px] font-black uppercase text-gov-green tracking-widest block mb-2">Services</span>
                <h2 class="text-3xl font-bold text-serif text-slate-900">Five end-to-end digital workflows</h2>
            </div>
            <a href="{{ route('register') }}" class="text-xs font-bold text-gov-green hover:underline">Create account to apply &rarr;</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between h-36">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">New Firearm License — Long Gun</h4>
                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed font-semibold">Shotgun / Rifle. DC-level approval.</p>
                </div>
                <div class="text-xs font-black text-slate-700">৳40,000 + ৳850</div>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between h-36">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">New Firearm License — Handgun</h4>
                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed font-semibold">Pistol / Revolver. MoHA approval chain.</p>
                </div>
                <div class="text-xs font-black text-slate-700">৳60,000 + ৳850</div>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between h-36">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">Renewal — Long Gun</h4>
                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed font-semibold">Annual, with automatic late-fine tiers.</p>
                </div>
                <div class="text-xs font-black text-slate-700">৳10,000 + ৳720</div>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between h-36">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">Renewal — Handgun</h4>
                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed font-semibold">Annual, DC + Political-4 verification.</p>
                </div>
                <div class="text-xs font-black text-slate-700">৳20,000 + ৳720</div>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between h-36">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">Arms Dealing License (Form K)</h4>
                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed font-semibold">4-agency vetting + Screening Committee.</p>
                </div>
                <div class="text-xs font-black text-slate-700">৳150,000 + ৳2,500</div>
            </div>
            <div class="p-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between h-36">
                <div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">Dealing License Renewal</h4>
                    <p class="text-[10px] text-slate-500 mt-1.5 leading-relaxed font-semibold">Stock ledger reconciliation + committee.</p>
                </div>
                <div class="text-xs font-black text-slate-700">৳75,000 + ৳2,500</div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="max-w-7xl mx-auto py-20 px-6 border-t border-slate-200">
        <span class="text-[10px] font-black uppercase text-gov-green tracking-widest block text-center mb-2">Transparent Pricing</span>
        <h2 class="text-3xl font-bold text-serif text-slate-900 text-center mb-2">Statutory fees & platform charges</h2>
        <p class="text-center text-[10px] text-slate-500 font-bold uppercase tracking-wider mb-10">Effective 2026 revision &bull; all amounts in BDT (৳)</p>

        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gov-deep text-white text-[10px] uppercase font-bold tracking-wider">
                        <th class="p-4 pl-6">Transaction</th>
                        <th class="p-4">Statutory Fee</th>
                        <th class="p-4">Platform Charge</th>
                        <th class="p-4 pr-6">Total Payable</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    <tr>
                        <td class="p-4 pl-6 font-semibold">New Licence &mdash; Pistol/Revolver</td>
                        <td class="p-4">৳60,000</td>
                        <td class="p-4">৳850</td>
                        <td class="p-4 pr-6 font-black text-gov-green">৳60,850</td>
                    </tr>
                    <tr>
                        <td class="p-4 pl-6 font-semibold">New Licence &mdash; Shotgun/Rifle</td>
                        <td class="p-4">৳40,000</td>
                        <td class="p-4">৳850</td>
                        <td class="p-4 pr-6 font-black text-gov-green">৳40,850</td>
                    </tr>
                    <tr>
                        <td class="p-4 pl-6 font-semibold">Renewal (on time) &mdash; Pistol/Revolver</td>
                        <td class="p-4">৳20,000</td>
                        <td class="p-4">৳720</td>
                        <td class="p-4 pr-6 font-black text-gov-green">৳20,720</td>
                    </tr>
                    <tr>
                        <td class="p-4 pl-6 font-semibold">Renewal (on time) &mdash; Shotgun/Rifle</td>
                        <td class="p-4">৳10,000</td>
                        <td class="p-4">৳720</td>
                        <td class="p-4 pr-6 font-black text-gov-green">৳10,720</td>
                    </tr>
                    <tr>
                        <td class="p-4 pl-6 font-semibold">Renewal (Late Tier 1) &mdash; Pistol/Revolver</td>
                        <td class="p-4">৳20,000 + ৳2,000</td>
                        <td class="p-4">৳720 + ৳250</td>
                        <td class="p-4 pr-6 font-black text-gov-green">৳22,970</td>
                    </tr>
                    <tr>
                        <td class="p-4 pl-6 font-semibold">Renewal (Late Tier 2) &mdash; Shotgun/Rifle</td>
                        <td class="p-4">৳10,000 + ৳5,000</td>
                        <td class="p-4">৳720 + ৳250</td>
                        <td class="p-4 pr-6 font-black text-gov-green">৳15,970</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gov-deep text-slate-350 py-12 px-6 border-t border-white/5 text-white">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between text-xs gap-6 text-slate-300">
            <div>
                <h4 class="font-extrabold text-white font-serif text-sm">Ministry of Home Affairs</h4>
                <p class="text-[10px] text-slate-400 mt-1">Bangladesh Secretariat, Dhaka-1000</p>
            </div>
            <div class="text-center md:text-right text-[10px]">
                <p>&copy; 2026 Government of the People's Republic of Bangladesh. All rights reserved.</p>
                <p class="text-slate-400 mt-1 font-semibold">Powered by Mysoft Heaven (BD) Ltd.</p>
            </div>
        </div>
    </footer>

</body>
</html>
