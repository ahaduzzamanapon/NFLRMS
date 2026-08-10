<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up - NFLRMS</title>

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
        body { font-family: 'Poppins', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-serif { font-family: 'Poppins', sans-serif; }
        .font-bn, [lang="bn"] { font-family: 'Nikosh', 'Noto Sans Bengali', sans-serif; }
        /* Fine hairline seal-ring used behind the emblem */
        .seal-ring { box-shadow: 0 0 0 1px rgba(201,162,75,0.55), 0 0 0 4px rgba(201,162,75,0.12); }
        /* Section letter chip */
        .sec-chip { font-variant-numeric: tabular-nums; }
        select { -webkit-appearance: none; appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.85rem center; }
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
        <div class="relative z-10 lg:top-0 lg:h-screen flex flex-col justify-between p-8 pb-12 lg:p-16 lg:pb-20">

            <!-- Top: back link + form reference -->
            <div class="flex items-center justify-between">
                <a href="/" class="text-xs font-semibold text-slate-300 hover:text-white flex items-center gap-2 transition-colors">
                    <span>&larr;</span>
                    <span>Back to home</span>
                </a>
                <span class="text-[11px] tracking-[0.2em] uppercase text-gold-soft font-semibold">Form NFLRMS&ndash;01</span>
            </div>

            <!-- Middle Content -->
            <div class="my-10 space-y-7 max-w-lg">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full seal-ring flex items-center justify-center bg-white/5 shrink-0">
                        <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Government of Bangladesh" class="w-11 h-11 object-contain"/>
                    </div>
                    <div class="text-xs leading-snug text-slate-300 font-semibold uppercase tracking-wider">
                        <p>Government of the People&rsquo;s Republic of Bangladesh</p>
                        <p class="font-bn normal-case text-slate-400 mt-0.5">বাংলাদেশ গণপ্রজাতন্ত্রী সরকার</p>
                    </div>
                </div>

                <div class="h-px w-16 bg-gold/60"></div>

                <h2 class="text-3xl lg:text-[1.9rem] font-extrabold font-serif leading-[1.1]">
                    Create your NFLRMS account
                </h2>
                <p class="text-slate-300 text-xs md:text-sm leading-relaxed font-medium max-w-sm">
                    Citizens and arms dealers register once with a verified mobile number and NID, then apply for any license service.
                </p>

                <ul class="space-y-3 text-xs text-slate-200 font-medium pt-2">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-4 h-4 rounded-full border border-gold/70 flex items-center justify-center shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                        </span>
                        <span>Mobile OTP verification <span class="text-slate-400 font-normal">(compulsory)</span></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-4 h-4 rounded-full border border-gold/70 flex items-center justify-center shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                        </span>
                        <span>NID cross-validation <span class="text-slate-400 font-normal">(mocked)</span></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-4 h-4 rounded-full border border-gold/70 flex items-center justify-center shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                        </span>
                        <span>Bilingual (Bangla + English) notifications</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 w-4 h-4 rounded-full border border-gold/70 flex items-center justify-center shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                        </span>
                        <span>eKYC digital signature accepted as wet-ink</span>
                    </li>
                </ul>
            </div>

            <!-- Bottom Footer -->
            <div class="flex items-center justify-between text-[11px] text-slate-400 font-medium pt-6 border-t border-white/10">
                <span class="flex items-center gap-2">
                    <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Ministry of Home Affairs" class="w-4 h-4 object-contain opacity-90">
                    <span>Ministry of Home Affairs &bull; Government of Bangladesh</span>
                </span>
                <span class="text-slate-500">v1.0</span>
            </div>
        </div>
    </div>

    <!-- Right Pane: Sign Up Form -->
    <div class="w-full lg:w-1/2 bg-slate-50 flex flex-col p-5 sm:p-6 lg:px-8 lg:py-8">
        <div class="max-w-xl w-full mx-auto my-auto space-y-6 bg-white rounded-2xl p-6 sm:p-9 border border-slate-200/80 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_32px_-16px_rgba(3,52,37,0.25)]">

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold font-serif text-slate-800 leading-none">Sign up</h3>
                    <p class="text-xs text-slate-500 mt-2 font-medium">Step 1 of 2 &middot; Personal details</p>
                </div>
                <div class="flex items-center gap-1.5 pt-1" aria-hidden="true">
                    <span id="dot-role-citizen" class="w-5 h-1.5 rounded-full bg-gov-green transition-colors"></span>
                    <span id="dot-role-dealer" class="w-5 h-1.5 rounded-full bg-slate-200 transition-colors"></span>
                </div>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Account Type Selector (Tabs) -->
                <div>
                    <label class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-950 mb-1.5">
                        <span class="sec-chip inline-flex w-4 h-4 rounded-[4px] bg-gov-deep text-white items-center justify-center text-[10px]">A</span>
                        Account Type
                    </label>
                    <div id="role-wrap" class="grid grid-cols-2 gap-3 @error('role') ring-1 ring-rose-500 rounded-lg p-0.5 @enderror">
                        <button type="button" onclick="selectRole('citizen_applicant')" id="btn-citizen"
                                class="py-2.5 px-2 rounded-lg border-2 text-center text-xs font-semibold transition-all focus:outline-none bg-emerald-50/40 border-gov-green text-gov-green flex flex-col items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" /></svg>
                            <span>Citizen (Individual)</span>
                        </button>
                        <button type="button" onclick="selectRole('dealer_applicant')" id="btn-dealer"
                                class="py-2.5 px-2 rounded-lg border border-slate-200 text-center text-xs font-semibold transition-all focus:outline-none text-slate-600 hover:bg-slate-50 flex flex-col items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.098a2.25 2.25 0 01-2.25 2.25h-12a2.25 2.25 0 01-2.25-2.25v-4.098M20.25 14.15L12 15.75 3.75 14.15M20.25 14.15V9.6a2.25 2.25 0 00-1.183-1.98l-6.75-3.6a2.25 2.25 0 00-2.134 0l-6.75 3.6A2.25 2.25 0 003.75 9.6v4.55" /></svg>
                            <span>Arms Dealer / Firm</span>
                        </button>
                    </div>
                    <input type="hidden" name="role" id="role-field" value="{{ old('role', 'citizen_applicant') }}">
                    @error('role')
                        <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                    <span id="js-error-role" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Please select an account type.</span>
                </div>

                <!-- Validation Summary Alert -->
                <div id="formValidationAlert" class="{{ $errors->any() ? '' : 'hidden' }} p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-semibold space-y-1">
                    <span class="block text-sm font-bold font-serif">
                        <span>⚠️</span> Please fill in the highlighted required field(s) below before continuing.
                    </span>
                </div>

                <!-- Section B: Personal information -->
                <div class="space-y-3">
                    <p class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-950">
                        <span class="sec-chip inline-flex w-4 h-4 rounded-[4px] bg-gov-deep text-white items-center justify-center text-[10px]">B</span>
                        Personal Information
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="name" class="block text-[11px] font-semibold text-slate-600 mb-1.5">Full Name (English)</label>
                            <input type="text" name="name" id="name"
                                   class="w-full px-3 py-2.5 text-xs rounded-lg border bg-white outline-none focus:ring-2 transition-all @error('name') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror"
                                   placeholder="Md. Rafikul Islam" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                            <span id="js-error-name" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Full name (English) is required.</span>
                        </div>
                        <div>
                            <label for="name_bn" class="block text-[11px] font-semibold text-slate-600 mb-1.5">Full Name (Bengali)</label>
                            <input type="text" name="name_bn" id="name_bn"
                                   class="w-full px-3 py-2.5 text-xs font-bn rounded-lg border bg-white outline-none focus:ring-2 transition-all @error('name_bn') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror"
                                   placeholder="মো: রফিকুল ইসলাম" value="{{ old('name_bn') }}">
                            @error('name_bn')
                                <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                            <span id="js-error-name_bn" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Full name (Bengali) is required.</span>
                        </div>
                    </div>
                </div>

                <!-- Section C: Location -->
                <div class="space-y-3">
                    <p class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-950">
                        <span class="sec-chip inline-flex w-4 h-4 rounded-[4px] bg-gov-deep text-white items-center justify-center text-[10px]">C</span>
                        Location
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="district_id" class="block text-[11px] font-semibold text-slate-600 mb-1.5">District</label>
                            <select name="district_id" id="district_id"
                                    class="w-full px-3 py-2.5 text-xs rounded-lg border bg-white outline-none focus:ring-2 transition-all @error('district_id') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror">
                                <option value="">Select District</option>
                                @foreach(\App\Models\District::orderBy('name')->get() as $d)
                                    <option value="{{ $d->id }}" @selected(old('district_id') == $d->id)>{{ $d->name }}</option>
                                @endforeach
                            </select>
                            @error('district_id')
                                <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                            <span id="js-error-district_id" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Please select a district.</span>
                        </div>
                        <div>
                            <label for="upazila_id" class="block text-[11px] font-semibold text-slate-600 mb-1.5">Upazila / Thana</label>
                            <select name="upazila_id" id="upazila_id" disabled
                                    class="w-full px-3 py-2.5 text-xs rounded-lg border bg-white outline-none focus:ring-2 transition-all disabled:bg-slate-50 disabled:text-slate-400 @error('upazila_id') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror">
                                <option value="">Select District First</option>
                            </select>
                            @error('upazila_id')
                                <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                            <span id="js-error-upazila_id" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Please select an upazila / thana.</span>
                        </div>
                    </div>
                </div>

                <!-- Section D: Contact & identity -->
                <div class="space-y-3">
                    <p class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-950">
                        <span class="sec-chip inline-flex w-4 h-4 rounded-[4px] bg-gov-deep text-white items-center justify-center text-[10px]">D</span>
                        Contact &amp; Identity
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="email" class="block text-[11px] font-semibold text-slate-600 mb-1.5">Email</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0-.414.336-.75.75-.75h18a.75.75 0 01.75.75v10.5a.75.75 0 01-.75.75h-18a.75.75 0 01-.75-.75V6.75zm0 0l9.75 6.75 9.75-6.75" /></svg>
                                <input type="email" name="email" id="email"
                                       class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border bg-white outline-none focus:ring-2 transition-all @error('email') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror"
                                       placeholder="name@example.bd" value="{{ old('email') }}">
                            </div>
                            @error('email')
                                <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                            <span id="js-error-email" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Enter a valid email address.</span>
                        </div>

                        <div>
                            <label for="phone" class="block text-[11px] font-semibold text-slate-600 mb-1.5">Mobile Phone (11 Digits)</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                                <input type="text" name="phone" id="phone" maxlength="11"
                                       class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border bg-white outline-none focus:ring-2 transition-all @error('phone') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror"
                                       placeholder="01712345678" value="{{ old('phone') }}">
                            </div>
                            @error('phone')
                                <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                            <span id="js-error-phone" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Enter a valid 11-digit Bangladeshi mobile number (e.g. 01712345678).</span>
                        </div>
                    </div>

                    <div>
                        <label for="nid" class="block text-[11px] font-semibold text-slate-600 mb-1.5">National ID (NID)</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 15.75h19.5M6 4.5h12A2.25 2.25 0 0120.25 6.75v10.5A2.25 2.25 0 0118 19.5H6a2.25 2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 016 4.5z" /></svg>
                            <input type="text" name="nid" id="nid"
                                   class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border bg-white outline-none focus:ring-2 transition-all @error('nid') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror"
                                   placeholder="10 or 17-digit NID" value="{{ old('nid') }}">
                        </div>
                        @error('nid')
                            <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                        <span id="js-error-nid" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Enter a valid 10 or 17-digit NID.</span>
                    </div>
                </div>

                <!-- Section E: Security -->
                <div class="space-y-3">
                    <p class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-slate-950">
                        <span class="sec-chip inline-flex w-4 h-4 rounded-[4px] bg-gov-deep text-white items-center justify-center text-[10px]">E</span>
                        Security
                    </p>
                    <div>
                        <label for="password" class="block text-[11px] font-semibold text-slate-600 mb-1.5">Password</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                            <input type="password" name="password" id="password"
                                   class="w-full pl-9 pr-3 py-2.5 text-xs rounded-lg border bg-white outline-none focus:ring-2 transition-all @error('password') border-rose-500 focus:ring-rose-200 @else border-slate-200 focus:ring-gov-green/25 focus:border-gov-green @enderror"
                                   placeholder="Minimum 6 characters">
                        </div>
                        @error('password')
                            <span class="text-[11px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                        <span id="js-error-password" class="text-[11px] text-rose-500 font-semibold mt-1 hidden">Password must be at least 6 characters.</span>
                        <input type="hidden" name="password_confirmation" id="password_confirmation">
                    </div>
                </div>

                <button type="submit" onclick="document.getElementById('password_confirmation').value = document.getElementById('password').value"
                        class="w-full py-3.5 rounded-lg bg-gov-green hover:bg-gov-light text-white font-bold text-xs shadow-md shadow-gov-green/20 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3M4.5 19.5a7.5 7.5 0 0115 0m-15 0h15m-15 0v-1.5A2.25 2.25 0 016.75 15.75h4.5A2.25 2.25 0 0113.5 18v1.5M12 12a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" /></svg>
                    <span>Create account &amp; send OTP</span>
                </button>
            </form>

            <div class="pt-3 text-center space-y-3">
                <p class="text-xs text-slate-500">
                    Already registered? <a href="{{ route('login') }}" class="text-gov-green hover:underline font-semibold">Sign in</a>
                </p>
                <div class="flex items-center justify-center gap-2 text-[11px] text-slate-400 font-medium pt-3 border-t border-slate-100">
                    <span>Developed By</span>
                    <img src="{{ asset('assets/brand/mysoft-with-background.jpg') }}" alt="Mysoft Heaven (BD) Ltd." class="h-6 w-auto object-contain rounded">
                </div>
            </div>
        </div>
    </div>

    <script>
        // ---------- Client-side validation ----------
        const ERROR_BORDER = ['border-rose-500', 'focus:ring-rose-200'];
        const NORMAL_BORDER = ['border-slate-200', 'focus:ring-gov-green/25', 'focus:border-gov-green'];

        function markInvalid(fieldId) {
            const el = document.getElementById(fieldId);
            const msg = document.getElementById('js-error-' + fieldId);
            if (el) {
                el.classList.remove(...NORMAL_BORDER);
                el.classList.add(...ERROR_BORDER);
            }
            if (msg) msg.classList.remove('hidden');
        }

        function markValid(fieldId) {
            const el = document.getElementById(fieldId);
            const msg = document.getElementById('js-error-' + fieldId);
            if (el) {
                el.classList.remove(...ERROR_BORDER);
                el.classList.add(...NORMAL_BORDER);
            }
            if (msg) msg.classList.add('hidden');
        }

        function validateForm() {
            let isValid = true;
            const alertBox = document.getElementById('formValidationAlert');

            const name = document.getElementById('name');
            if (!name.value.trim()) { markInvalid('name'); isValid = false; } else { markValid('name'); }

            const nameBn = document.getElementById('name_bn');
            if (!nameBn.value.trim()) { markInvalid('name_bn'); isValid = false; } else { markValid('name_bn'); }

            const role = document.getElementById('role-field').value;
            const roleWrap = document.getElementById('role-wrap');
            const roleMsg = document.getElementById('js-error-role');
            if (!role) {
                if (roleWrap) roleWrap.classList.add('ring-1', 'ring-rose-500');
                if (roleMsg) roleMsg.classList.remove('hidden');
                isValid = false;
            } else {
                if (roleWrap) roleWrap.classList.remove('ring-1', 'ring-rose-500');
                if (roleMsg) roleMsg.classList.add('hidden');
            }

            const district = document.getElementById('district_id');
            if (!district.value) { markInvalid('district_id'); isValid = false; } else { markValid('district_id'); }

            const upazila = document.getElementById('upazila_id');
            if (!upazila.value) { markInvalid('upazila_id'); isValid = false; } else { markValid('upazila_id'); }

            const email = document.getElementById('email');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim() || !emailPattern.test(email.value.trim())) { markInvalid('email'); isValid = false; } else { markValid('email'); }

            const phone = document.getElementById('phone');
            const phonePattern = /^01[3-9]\d{8}$/;
            if (!phonePattern.test(phone.value.trim())) { markInvalid('phone'); isValid = false; } else { markValid('phone'); }

            const nid = document.getElementById('nid');
            const nidPattern = /^\d{10}$|^\d{17}$/;
            if (!nidPattern.test(nid.value.trim())) { markInvalid('nid'); isValid = false; } else { markValid('nid'); }

            const password = document.getElementById('password');
            if (password.value.length < 6) { markInvalid('password'); isValid = false; } else { markValid('password'); }

            if (alertBox) {
                isValid ? alertBox.classList.add('hidden') : alertBox.classList.remove('hidden');
            }

            return isValid;
        }

        document.querySelector('form').addEventListener('submit', function (e) {
            document.getElementById('password_confirmation').value = document.getElementById('password').value;

            if (!validateForm()) {
                e.preventDefault();
                const firstInvalid = document.querySelector('.border-rose-500, .ring-rose-500');
                if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Clear error state as the user fixes each field
        ['name', 'name_bn', 'district_id', 'upazila_id', 'email', 'phone', 'nid', 'password'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', function () { validateFieldOnBlur(id); });
            if (el) el.addEventListener('change', function () { validateFieldOnBlur(id); });
        });

        function validateFieldOnBlur(id) {
            const el = document.getElementById(id);
            if (!el) return;
            if (id === 'email') {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                emailPattern.test(el.value.trim()) ? markValid(id) : null;
            } else if (id === 'phone') {
                const phonePattern = /^01[3-9]\d{8}$/;
                phonePattern.test(el.value.trim()) ? markValid(id) : null;
            } else if (id === 'nid') {
                const nidPattern = /^\d{10}$|^\d{13}$|^\d{17}$/;
                nidPattern.test(el.value.trim()) ? markValid(id) : null;
            } else if (id === 'password') {
                if (el.value.length >= 6) markValid(id);
            } else if (el.value && el.value.trim()) {
                markValid(id);
            }
        }
        // ---------- End client-side validation ----------

        function selectRole(role) {
            document.getElementById('role-field').value = role;
            const btnCitizen = document.getElementById('btn-citizen');
            const btnDealer = document.getElementById('btn-dealer');
            const dotCitizen = document.getElementById('dot-citizen');
            const dotDealer = document.getElementById('dot-dealer');
            const dotRoleCitizen = document.getElementById('dot-role-citizen');
            const dotRoleDealer = document.getElementById('dot-role-dealer');

            const ACTIVE = 'py-2.5 px-2 rounded-lg border-2 text-center text-xs font-semibold transition-all focus:outline-none bg-emerald-50/40 border-gov-green text-gov-green flex flex-col items-center gap-1.5';
            const INACTIVE = 'py-2.5 px-2 rounded-lg border border-slate-200 text-center text-xs font-semibold transition-all focus:outline-none text-slate-600 hover:bg-slate-50 flex flex-col items-center gap-1.5';
            const DOT_ACTIVE = 'w-5 h-1.5 rounded-full bg-gov-green transition-colors';
            const DOT_INACTIVE = 'w-5 h-1.5 rounded-full bg-slate-200 transition-colors';

            if (role === 'citizen_applicant') {
                btnCitizen.className = ACTIVE;
                btnDealer.className = INACTIVE;
                if (dotCitizen) dotCitizen.className = DOT_ACTIVE;
                if (dotDealer) dotDealer.className = DOT_INACTIVE;
                if (dotRoleCitizen) dotRoleCitizen.className = DOT_ACTIVE;
                if (dotRoleDealer) dotRoleDealer.className = DOT_INACTIVE;
            } else {
                btnDealer.className = ACTIVE;
                btnCitizen.className = INACTIVE;
                if (dotDealer) dotDealer.className = DOT_ACTIVE;
                if (dotCitizen) dotCitizen.className = DOT_INACTIVE;
                if (dotRoleDealer) dotRoleDealer.className = DOT_ACTIVE;
                if (dotRoleCitizen) dotRoleCitizen.className = DOT_INACTIVE;
            }
        }

        // Restore correct tab highlight after a failed submit (old('role'))
        selectRole(document.getElementById('role-field').value);

        document.getElementById('district_id').addEventListener('change', function () {
            const districtId = this.value;
            const upazilaSelect = document.getElementById('upazila_id');

            upazilaSelect.innerHTML = '<option value="">Loading...</option>';
            upazilaSelect.disabled = true;

            if (!districtId) {
                upazilaSelect.innerHTML = '<option value="">Select District First</option>';
                return;
            }

            // Fetch upazilas dynamically via AJAX
            fetch(`/api/districts/${districtId}/upazilas`)
                .then(response => response.json())
                .then(data => {
                    upazilaSelect.innerHTML = '<option value="">Select Upazila / Thana</option>';
                    data.forEach(upazila => {
                        upazilaSelect.innerHTML += `<option value="${upazila.id}">${upazila.name}</option>`;
                    });
                    upazilaSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching upazilas:', error);
                    upazilaSelect.innerHTML = '<option value="">Error loading upazilas</option>';
                });
        });
    </script>

</body>
</html>
