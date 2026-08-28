<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in — NFLRMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">

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
                            ink: '#0c1b14',
                        },
                        gold: {
                            DEFAULT: '#c9a24b',
                            soft: '#e3cd93',
                        },
                        paper: {
                            DEFAULT: '#faf8f2',
                            line: '#e8e2d0',
                        }
                    },
                    fontFamily: {
                        sans: ['"Poppins"', 'sans-serif'],
                        serif: ['"Poppins"', 'sans-serif'],
                        bn: ['"Nikosh"', '"Noto Sans Bengali"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @font-face {
            font-family: 'Nikosh';
            src: url('{{ asset('fonts/Nikosh.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        body { font-family: 'Poppins', sans-serif; font-size: 14px; line-height: 1.5; }
        h1, h2, h3, h4, h5, h6, .font-serif { font-family: 'Poppins', sans-serif; }
        .font-bn, [lang="bn"] { font-family: 'Nikosh', 'Noto Sans Bengali', sans-serif; }
        .seal-ring { box-shadow: 0 0 0 1px rgba(201,162,75,0.55), 0 0 0 4px rgba(201,162,75,0.12); }
        .sec-chip { font-variant-numeric: tabular-nums; }
        .drawer-transition { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Global Typography Standards */
        input, select, textarea, button { font-size: 0.875rem; }
        label { font-size: 0.75rem; }
        .text-\[9px\] { font-size: 11px !important; }
        .text-\[10px\] { font-size: 11.5px !important; }
        .text-\[11px\] { font-size: 12px !important; }
    </style>
</head>
<body class="min-h-full font-sans antialiased text-slate-800 flex flex-col lg:flex-row">

    <!-- Left Pane: Ceremonial / letterhead side -->
    <div class="w-full lg:w-1/2 bg-gov-deep text-white relative">

        <!-- Guilloché-style security pattern, signature background element (spans the full column height) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <svg class="w-full h-full opacity-[0.10]" preserveAspectRatio="xMidYMid slice" viewBox="0 0 800 800" aria-hidden="true">
                <defs>
                    <pattern id="guilloche" width="80" height="80" patternUnits="userSpaceOnUse">
                        <circle cx="40" cy="40" r="38" fill="none" stroke="#e3cd93" stroke-width="0.6"/>
                        <circle cx="40" cy="40" r="26" fill="none" stroke="#e3cd93" stroke-width="0.6"/>
                        <circle cx="0" cy="0" r="38" fill="none" stroke="#e3cd93" stroke-width="0.6"/>
                        <circle cx="80" cy="0" r="38" fill="none" stroke="#e3cd93" stroke-width="0.6"/>
                        <circle cx="0" cy="80" r="38" fill="none" stroke="#e3cd93" stroke-width="0.6"/>
                        <circle cx="80" cy="80" r="38" fill="none" stroke="#e3cd93" stroke-width="0.6"/>
                    </pattern>
                </defs>
                <rect width="800" height="800" fill="url(#guilloche)"/>
            </svg>
        </div>

        <!-- Sticky content layer: pins to the viewport while the outer panel stretches to match the form's height -->
        <div class="relative z-10 lg:sticky lg:top-0 lg:h-screen flex flex-col justify-between p-5 sm:p-8 lg:p-14 lg:pb-16">

            <!-- Top: back link + form reference -->
            <div class="flex items-center justify-between">
                <a href="/" class="text-xs font-semibold text-slate-300 hover:text-white flex items-center gap-2 transition-colors">
                    <span>&larr;</span>
                    <span>Back to home</span>
                </a>
                <span class="text-[11px] tracking-[0.2em] uppercase text-gold-soft font-semibold">Form NFLRMS&ndash;02</span>
            </div>

            <!-- Middle Content -->
            <div class="my-10 space-y-7 max-w-lg">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0">
                        <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Government of Bangladesh" class="w-15 h-15 object-contain rounded-full"/>
                    </div>
                    {{-- <div class="w-16 h-16 rounded-full seal-ring flex items-center justify-center bg-white/5 shrink-0">
                        <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Government of Bangladesh" class="w-11 h-11 object-contain"/>
                    </div> --}}
                    <div class="text-xs leading-snug text-slate-300 font-semibold uppercase tracking-wider">
                        <p>Government of the People&rsquo;s Republic of Bangladesh</p>
                        <p class="font-bn normal-case text-slate-400 mt-0.5">বাংলাদেশ গণপ্রজাতন্ত্রী সরকার</p>
                    </div>
                </div>

                <div class="h-px w-16 bg-gold/60"></div>

                <h2 class="text-3xl lg:text-[2.0rem] font-extrabold font-serif leading-[1.1]">
                    Welcome back to NFLRMS
                </h2>
                <p class="text-slate-300 text-xs md:text-sm leading-relaxed font-medium max-w-sm">
                    Sign in to manage your firearm license or dealing license applications.
                </p>

                <ul class="space-y-3 text-xs text-slate-200 font-medium pt-2">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-4 h-4 rounded-full border border-gold/70 flex items-center justify-center shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                        </span>
                        <span>Mobile number <span class="text-slate-400 font-normal">or</span> email sign-in</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-4 h-4 rounded-full border border-gold/70 flex items-center justify-center shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                        </span>
                        <span>Role-based secure access</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-4 h-4 rounded-full border border-gold/70 flex items-center justify-center shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                        </span>
                        <span>Every session logged for audit</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-4 h-4 rounded-full border border-gold/70 flex items-center justify-center shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                        </span>
                        <span>Bilingual (Bangla + English) notifications</span>
                    </li>
                </ul>
            </div>

            <!-- Bottom Footer -->
            <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium pt-6 border-t border-white/10">
                <span class="flex items-center gap-2">
                    <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Ministry of Home Affairs" class="w-4 h-4 object-contain opacity-90 rounded-full">
                    <span>Ministry of Home Affairs &bull; Government of Bangladesh</span>
                </span>
                <span class="text-slate-500">v1.0</span>
            </div>
        </div>
    </div>

    <!-- Right Pane: Sign In Form -->
    <div class="w-full lg:w-1/2 bg-slate-50 flex flex-col p-5 sm:p-6 lg:px-8 lg:py-8">
        <div class="max-w-xl w-full mx-auto my-auto space-y-6 bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_32px_-16px_rgba(3,52,37,0.25)]">

            <div>
                <h3 class="text-2xl font-bold font-serif text-slate-900 leading-none">Sign in</h3>
                <p class="text-xs text-slate-500 mt-1.5 font-medium">Use your mobile number <span class="text-slate-400">or</span> email address and password.</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-4" id="main-login-form">
                @csrf

                <!-- Validation Summary Alert -->
                <div id="loginValidationAlert" class="{{ $errors->any() ? '' : 'hidden' }} p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-semibold space-y-1">
                    <span class="block text-sm font-bold font-serif">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Please fill in the highlighted required field(s) below before continuing.
                    </span>
                </div>

                <!-- Section A: Access credentials -->
                <div class="space-y-3">
                    <p class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-950">
                        <span class="sec-chip inline-flex w-4 h-4 rounded-[4px] bg-gov-deep text-white items-center justify-center text-[10px]">A</span>
                        Access Credentials
                    </p>

                    <div>
                        <label for="login-identifier" class="block text-[11px] font-semibold text-slate-600 mb-1.5">
                            Mobile <span class="text-slate-400 font-normal">/</span> Email
                        </label>
                        <div class="relative">
                            {{-- Dynamic icon: switches between phone & email based on input --}}
                            <span id="icon-phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                            </span>
                            <span id="icon-email" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 hidden">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                            </span>
                            <input type="text" name="phone" id="login-identifier"
                                   class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border bg-white outline-none focus:ring-2 transition-all @error('phone') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror"
                                   placeholder="Enter mobile or email" value="{{ old('phone') }}" autocomplete="username">
                        </div>
                        @if ($errors->has('phone') || $errors->has('email'))
                            <span class="text-[11px] text-rose-500 font-semibold mt-1 block">
                                {{ $errors->first('phone') ?: $errors->first('email') }}
                            </span>
                        @endif
                        <span id="js-error-login-identifier" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Enter a valid mobile number (01XXXXXXXXX) or email address.</span>
                    </div>

                    <div>
                        <label for="login-password" class="block text-[11px] font-semibold text-slate-600 mb-1.5">Password</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                            <input type="password" name="password" id="login-password"
                                   class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border bg-white outline-none focus:ring-2 transition-all @error('password') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror"
                                   placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
                        </div>
                        @error('password')
                            <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                        <span id="js-error-login-password" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Password is required.</span>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-lg bg-gov-green hover:bg-gov-light text-white font-bold text-xs shadow-md shadow-gov-green/20 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H3" /></svg>
                    <span>Sign in</span>
                </button>
            </form>

            <div class="text-center space-y-2">
                {{-- <p class="text-[11px] text-slate-500 leading-relaxed font-medium bg-slate-50 border border-slate-100 rounded-lg px-3 py-2">
                    Demo credentials: 01711234567 / demo1234 (citizen), 01711000111 / demo1234 (dealer)
                </p> --}}
                <p class="text-xs text-slate-500">
                    No account? <a href="{{ route('register') }}" class="text-gov-green hover:underline font-semibold">Sign up</a>
                </p>
                <div class="flex items-center justify-center gap-2 text-[11px] text-slate-400 font-medium pt-3 border-t border-slate-100">
                    <span>Designed & Developed By</span>
                    <img src="{{ asset('assets/brand/mysoft-with-background.jpg') }}" alt="Mysoft Heaven (BD) Ltd." class="h-6 w-auto object-contain rounded">
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Roles Floating Button -->
    {{-- <button type="button" onclick="toggleDrawer(true)"
            class="fixed bottom-6 right-6 px-4 py-2.5 bg-gov-deep hover:bg-gov-green text-white text-xs font-bold rounded-full shadow-lg shadow-gov-deep/30 flex items-center gap-2 z-40 transition-transform active:scale-95 border border-gold/40">
        <svg class="w-4 h-4 text-gold-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
        <span>Quick Login</span>
    </button> --}}

    <!-- Backdrop Blur for Drawer -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-45 hidden transition-opacity opacity-0" onclick="toggleDrawer(false)"></div>

    <!-- Quick Roles Sliding Drawer (Right Side) -->
    <div id="quick-roles-drawer" class="fixed top-0 right-0 h-full w-[320px] sm:w-[350px] max-w-[90vw] bg-white shadow-2xl z-50 transform translate-x-full drawer-transition flex flex-col justify-between border-l border-slate-200">
        <!-- Header -->
        <div class="p-5 bg-gov-deep text-white flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-white">Registered Accounts</h3>
                <p class="text-[10px] text-slate-300 font-medium mt-0.5">Click to sign in instantly with mock profile</p>
            </div>
            <button type="button" onclick="toggleDrawer(false)" class="text-slate-300 hover:text-white text-sm font-bold p-1">&#10005;</button>
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
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-1">{{ $group }}</h4>
                <div class="space-y-1">
                    @foreach($rolesList as $roleVal => $details)
                    <button type="button" onclick="quickLogin('{{ $details[0] }}')"
                            class="w-full flex items-center justify-between p-2.5 rounded-lg border border-slate-200 hover:border-gov-green hover:bg-emerald-50/20 text-left transition-all group">
                        <div class="flex items-center gap-3.5">
                            <span class="w-1.5 h-1.5 rounded-full group-hover:scale-125 transition-transform" style="background: {{ $details[2] }}"></span>
                            <div>
                                <div class="text-[11px] font-bold text-slate-800 leading-none">{{ $details[1] }}</div>
                                <div class="text-[10px] text-slate-400 font-medium mt-1 leading-none">{{ $details[0] }}</div>
                            </div>
                        </div>
                        <span class="text-[10px] text-gov-green font-semibold opacity-0 group-hover:opacity-100 transition-opacity">Login &rarr;</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 text-center text-[9px] text-slate-400 font-semibold uppercase tracking-wider">
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
        // ---------- Client-side validation (login form) ----------
        const LOGIN_ERROR_BORDER = ['border-rose-500', 'focus:ring-rose-200'];
        const LOGIN_NORMAL_BORDER = ['border-slate-200', 'focus:ring-gov-green/25', 'focus:border-gov-green'];
        const PHONE_PATTERN = /^01[0-9]{9}$/;
        const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        function markLoginInvalid(fieldId) {
            const el = document.getElementById(fieldId);
            const msg = document.getElementById('js-error-' + fieldId);
            if (el) {
                el.classList.remove(...LOGIN_NORMAL_BORDER);
                el.classList.add(...LOGIN_ERROR_BORDER);
            }
            if (msg) msg.classList.remove('hidden');
        }

        function markLoginValid(fieldId) {
            const el = document.getElementById(fieldId);
            const msg = document.getElementById('js-error-' + fieldId);
            if (el) {
                el.classList.remove(...LOGIN_ERROR_BORDER);
                el.classList.add(...LOGIN_NORMAL_BORDER);
            }
            if (msg) msg.classList.add('hidden');
        }

        /** Switch the left icon based on whether user is typing phone vs email */
        function updateIdentifierIcon(value) {
            const isEmail = value.includes('@') || EMAIL_PATTERN.test(value);
            document.getElementById('icon-phone').classList.toggle('hidden', isEmail);
            document.getElementById('icon-email').classList.toggle('hidden', !isEmail);
        }

        function isValidIdentifier(value) {
            return PHONE_PATTERN.test(value.trim()) || EMAIL_PATTERN.test(value.trim());
        }

        function validateLoginForm() {
            let isValid = true;
            const alertBox = document.getElementById('loginValidationAlert');

            const identifier = document.getElementById('login-identifier');
            if (!isValidIdentifier(identifier.value)) {
                markLoginInvalid('login-identifier');
                isValid = false;
            } else {
                markLoginValid('login-identifier');
            }

            const password = document.getElementById('login-password');
            if (!password.value) { markLoginInvalid('login-password'); isValid = false; } else { markLoginValid('login-password'); }

            if (alertBox) {
                isValid ? alertBox.classList.add('hidden') : alertBox.classList.remove('hidden');
            }

            return isValid;
        }

        document.getElementById('main-login-form').addEventListener('submit', function (e) {
            if (!validateLoginForm()) {
                e.preventDefault();
                const firstInvalid = document.querySelector('#main-login-form .border-rose-500');
                if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Live validation & icon switching for identifier field
        const identifierEl = document.getElementById('login-identifier');
        if (identifierEl) {
            identifierEl.addEventListener('input', function () {
                updateIdentifierIcon(this.value);
                if (isValidIdentifier(this.value)) markLoginValid('login-identifier');
            });
        }

        // Live validation for password
        const passwordEl = document.getElementById('login-password');
        if (passwordEl) {
            passwordEl.addEventListener('input', function () {
                if (this.value) markLoginValid('login-password');
            });
        }
        // ---------- End client-side validation ----------

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
