<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in — NFLRMS</title>
    
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
        }
        .text-serif {
            font-family: 'Lora', serif;
        }
        .drawer-transition {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="h-full antialiased text-slate-800 bg-slate-50 flex overflow-hidden">

    <!-- Split Layout Grid -->
    <div class="w-full grid grid-cols-1 md:grid-cols-12 min-h-screen">
        
        <!-- Left Banner: Forest Green (Col Span 5) -->
        <div class="hidden md:flex md:col-span-5 bg-gov-green text-white p-16 flex-col justify-between relative">
            <!-- Back to Home -->
            <div>
                <a href="/" class="inline-flex items-center space-x-2 text-xs font-semibold text-emerald-100 hover:text-white transition-colors">
                    <span>←</span> <span>Back to home</span>
                </a>
            </div>

            <!-- Center Welcome -->
            <div class="space-y-6 my-auto">
                <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center border border-white/10 shadow-lg p-2.5">
                    <img src="https://flms.lovable.app/__l5e/assets-v1/acbf4783-ce0b-43bc-b0fd-4ba7908c84b3/govt-logo.png" 
                         alt="Government Seal" class="w-full h-full object-contain">
                </div>
                
                <h1 class="text-4xl font-extrabold text-serif tracking-tight leading-[1.1] text-white">
                    Welcome back to NFLRMS
                </h1>
                
                <p class="text-emerald-100/70 text-xs leading-relaxed max-w-sm font-semibold">
                    Sign in to manage your firearm license or dealing license applications.
                </p>
            </div>

            <!-- Footer info -->
            <div class="text-[10px] text-emerald-100/40 font-bold uppercase tracking-wider">
                Ministry of Home Affairs &bull; Government of Bangladesh
            </div>
        </div>

        <!-- Right Form: Off-white (Col Span 7) -->
        <div class="col-span-12 md:col-span-7 bg-[#FAF9F6] flex flex-col justify-center px-8 sm:px-16 py-12 relative">
            <!-- Mobile Navigation fallback -->
            <div class="absolute top-6 left-8 md:hidden">
                <a href="/" class="inline-flex items-center space-x-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors">
                    <span>←</span> <span>Back to home</span>
                </a>
            </div>

            <div class="max-w-[400px] w-full mx-auto space-y-8">
                <!-- Header -->
                <div class="space-y-1">
                    <h2 class="text-4xl font-bold text-serif text-slate-950">Sign in</h2>
                    <p class="text-slate-500 text-xs font-semibold">Use your mobile number and password.</p>
                </div>

                <!-- Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-5" id="main-login-form">
                    @csrf
                    
                    <!-- Mobile Field -->
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500">Mobile Number</label>
                        <input type="text" name="phone" id="login-phone" required
                               class="w-full px-4 py-3 text-xs font-bold text-slate-800 rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green transition-all"
                               placeholder="01711234567" value="{{ old('phone') }}">
                        @error('phone')
                            <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500">Password</label>
                        <input type="password" name="password" id="login-password" required
                               class="w-full px-4 py-3 text-xs font-bold text-slate-800 rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green transition-all"
                               placeholder="••••••••">
                        @error('password')
                            <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Button -->
                    <button type="submit"
                            class="w-full py-3 rounded-lg bg-gov-green hover:bg-[#08402e] text-white font-bold text-xs shadow-md transition-colors flex items-center justify-center space-x-1.5">
                        <span>→]</span> <span>Sign in</span>
                    </button>
                </form>

                <!-- Mockup details -->
                <div class="text-[10px] text-slate-500 leading-relaxed font-semibold pt-2">
                    Demo credentials: 01711234567 / demo1234 (citizen), 01711000111 / demo1234 (dealer)
                </div>

                <!-- Sign up Link -->
                <div class="text-xs text-slate-500 font-semibold text-center pt-2">
                    No account? <a href="{{ route('register') }}" class="text-gov-green hover:underline font-extrabold">Sign up</a>
                </div>
            </div>

            <!-- Footer for mobile view -->
            <div class="absolute bottom-6 left-8 right-8 text-center md:hidden text-[9px] text-slate-400 font-bold uppercase tracking-wider">
                Ministry of Home Affairs &bull; GoB
            </div>
        </div>
    </div>

    <!-- Quick Roles Floating Button -->
    <button type="button" onclick="toggleDrawer(true)"
            class="fixed bottom-6 right-6 px-4 py-2.5 bg-gov-green hover:bg-[#08402e] text-white text-xs font-black rounded-full shadow-lg flex items-center space-x-2 z-40 transition-transform active:scale-95 border border-white/10">
        <span>🔑</span> <span>Quick Login</span>
    </button>

    <!-- Backdrop Blur for Drawer -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-45 hidden transition-opacity opacity-0" onclick="toggleDrawer(false)"></div>

    <!-- Quick Roles Sliding Drawer (Right Side) -->
    <div id="quick-roles-drawer" class="fixed top-0 right-0 h-full w-[350px] bg-white shadow-2xl z-50 transform translate-x-full drawer-transition flex flex-col justify-between border-l border-slate-200">
        <!-- Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Registered Accounts</h3>
                <p class="text-[9px] text-slate-500 font-semibold mt-0.5">Click to sign in instantly with mock profile</p>
            </div>
            <button type="button" onclick="toggleDrawer(false)" class="text-slate-400 hover:text-slate-600 text-sm font-black p-1">✕</button>
        </div>

        <!-- Scrollable accounts list -->
        <div class="p-5 overflow-y-auto flex-grow space-y-4">
            @php
                $roleGroups = [
                    'Public' => [
                        \App\Enums\Role::CitizenApplicant->value => ['01711234567', 'Citizen Applicant', '#0b523a'],
                        \App\Enums\Role::DealerApplicant->value => ['01711000111', 'Dealer Applicant', '#07805c']
                    ],
                    'DC Office' => [
                        \App\Enums\Role::DcFrontDesk->value => ['01711000222', 'Front Desk Office', '#1e40af'],
                        \App\Enums\Role::DcJmBranch->value => ['01711000333', 'JM Branch Officer', '#1d4ed8'],
                        \App\Enums\Role::DistrictCommissioner->value => ['01711000444', 'District Commissioner', '#2563eb']
                    ],
                    'Vetting' => [
                        \App\Enums\Role::PoliceOfficer->value => ['01711000555', 'Police Officer', '#7c3aed'],
                        \App\Enums\Role::SpecialBranch->value => ['01711000666', 'Special Branch', '#6d28d9'],
                        \App\Enums\Role::NsiOfficer->value => ['01711000777', 'NSI Officer', '#5b21b6'],
                        \App\Enums\Role::DgfiOfficer->value => ['01711000888', 'DGFI Officer', '#4c1d95']
                    ],
                    'MoHA Ministry' => [
                        \App\Enums\Role::MohaDesk->value => ['01711000999', 'MoHA Desk Officer', '#b45309'],
                        \App\Enums\Role::JointSecretary->value => ['01711000123', 'Joint Secretary', '#d97706'],
                        \App\Enums\Role::NationalScreeningCommittee->value => ['01711000789', 'National Screening Committee', '#ea580c'],
                        \App\Enums\Role::SeniorSecretary->value => ['01711000456', 'Senior Secretary', '#f59e0b']
                    ],
                    'Executive' => [
                        \App\Enums\Role::Executive->value => ['01711000987', 'Executive / Oversight', '#0d9488']
                    ],
                    'Sysops' => [
                        \App\Enums\Role::SystemAdmin->value => ['01711000654', 'System Admin', '#374151']
                    ]
                ];
            @endphp

            @foreach($roleGroups as $group => $rolesList)
            <div class="space-y-1.5">
                <h4 class="text-[9px] font-black uppercase tracking-wider text-slate-400 px-1">{{ $group }}</h4>
                <div class="space-y-1">
                    @foreach($rolesList as $roleVal => $details)
                    <button type="button" onclick="quickLogin('{{ $details[0] }}')"
                            class="w-full flex items-center justify-between p-2.5 rounded-lg border border-slate-200 hover:border-gov-green hover:bg-emerald-50/20 text-left transition-all group">
                        <div class="flex items-center space-x-3.5">
                            <span class="w-1.5 h-1.5 rounded-full group-hover:scale-125 transition-transform" style="background: {{ $details[2] }}"></span>
                            <div>
                                <div class="text-[10px] font-bold text-slate-800 leading-none">{{ $details[1] }}</div>
                                <div class="text-[9px] text-slate-400 font-bold mt-1 leading-none">{{ $details[0] }}</div>
                            </div>
                        </div>
                        <span class="text-[9px] text-gov-green font-extrabold opacity-0 group-hover:opacity-100 transition-opacity">Login &rarr;</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 text-center text-[8px] text-slate-400 font-bold uppercase tracking-wider">
            NFLRMS &bull; GRS Verified Session
        </div>
    </div>

    <!-- Hidden quick-login form -->
    <form id="quick-login-form" action="{{ route('login') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="phone" id="ql-phone">
        <input type="hidden" name="password" value="demo1234">
    </form>

    <script>
        function toggleDrawer(isOpen) {
            const drawer = document.getElementById('quick-roles-drawer');
            const backdrop = document.getElementById('drawer-backdrop');
            
            if (isOpen) {
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.add('opacity-100');
                    drawer.classList.remove('translate-x-full');
                }, 10);
            } else {
                backdrop.classList.remove('opacity-100');
                drawer.classList.add('translate-x-full');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }
        }

        function quickLogin(phone) {
            document.getElementById('ql-phone').value = phone;
            document.getElementById('quick-login-form').submit();
        }
    </script>
</body>
</html>
