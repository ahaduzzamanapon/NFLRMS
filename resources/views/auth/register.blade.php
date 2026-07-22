<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up - NFLRMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;750;800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

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
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans antialiased text-slate-800 flex flex-col lg:flex-row">

    <!-- Left Pane: Split Screen Hero -->
    <div class="w-full lg:w-1/2 bg-gov-green text-white p-8 lg:p-16 flex flex-col justify-between relative">
        <!-- Top Back to home link -->
        <div>
            <a href="/" class="text-xs font-semibold text-slate-200 hover:text-white flex items-center space-x-2 transition-colors">
                <span>&larr;</span>
                <span>Back to home</span>
            </a>
        </div>

        <!-- Middle Content -->
        <div class="my-10 space-y-6 max-w-md">
            <!-- Bangladesh Gov Seal -->
            <img src="https://flms.lovable.app/__l5e/assets-v1/acbf4783-ce0b-43bc-b0fd-4ba7908c84b3/govt-logo.png" alt="Government of Bangladesh" class="w-16 h-16 object-contain"/>

            <h2 class="text-3xl lg:text-4xl font-extrabold font-serif leading-tight">
                Create your NFLRMS account
            </h2>
            <p class="text-slate-300 text-xs md:text-sm leading-relaxed font-semibold">
                Citizens and arms dealers register once with a verified mobile number and NID, then apply for any license service.
            </p>

            <ul class="space-y-2.5 text-xs text-slate-205 font-medium pt-4">
                <li class="flex items-center space-x-2">
                    <span class="text-amber-400">&bull;</span>
                    <span>Mobile OTP verification (compulsory)</span>
                </li>
                <li class="flex items-center space-x-2">
                    <span class="text-amber-400">&bull;</span>
                    <span>NID cross-validation (mocked)</span>
                </li>
                <li class="flex items-center space-x-2">
                    <span class="text-amber-400">&bull;</span>
                    <span>Bilingual (Bangla + English) notifications</span>
                </li>
                <li class="flex items-center space-x-2">
                    <span class="text-amber-400">&bull;</span>
                    <span>eKYC digital signature accepted as wet-ink</span>
                </li>
            </ul>
        </div>

        <!-- Bottom Footer -->
        <div class="text-[10px] text-slate-355 font-medium">
            Ministry of Home Affairs &bull; Government of Bangladesh
        </div>
    </div>

    <!-- Right Pane: Sign Up Form -->
    <div class="w-full lg:w-1/2 bg-slate-50 flex flex-col justify-center p-8 lg:p-12 overflow-y-auto">
        <div class="max-w-md w-full mx-auto space-y-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm my-4">
            <div>
                <h3 class="text-2xl font-black font-serif text-slate-900 leading-none">Sign up</h3>
                <p class="text-[11px] text-slate-500 mt-2 font-medium">Step 1 of 2 . Personal details</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Account Type Selector (Tabs) -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Account Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="selectRole('citizen_applicant')" id="btn-citizen"
                                class="py-2.5 rounded-lg border-2 text-center text-xs font-bold transition-all focus:outline-none bg-emerald-50/20 border-gov-green text-gov-green">
                            Citizen (Individual)
                        </button>
                        <button type="button" onclick="selectRole('dealer_applicant')" id="btn-dealer"
                                class="py-2.5 rounded-lg border border-slate-200 text-center text-xs font-bold transition-all focus:outline-none text-slate-650 hover:bg-slate-50">
                            Arms Dealer / Firm
                        </button>
                    </div>
                    <input type="hidden" name="role" id="role-field" value="citizen_applicant">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="name" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Full Name (English)</label>
                        <input type="text" name="name" id="name" required
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green transition-all"
                               placeholder="Md. Rafikul Islam" value="{{ old('name') }}">
                        @error('name')
                            <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="name_bn" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Full Name (Bengali)</label>
                        <input type="text" name="name_bn" id="name_bn" required
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green transition-all"
                               placeholder="মো: রফিকুল ইসলাম" value="{{ old('name_bn') }}">
                        @error('name_bn')
                            <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="district_id" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">District</label>
                        <select name="district_id" id="district_id" required
                                class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green transition-all">
                            <option value="">Select District</option>
                            @foreach(\App\Models\District::orderBy('name')->get() as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="upazila_id" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Upazila / Thana</label>
                        <select name="upazila_id" id="upazila_id" required disabled
                                class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green transition-all">
                            <option value="">Select District First</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Email</label>
                    <input type="email" name="email" id="email" required
                           class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green transition-all"
                           placeholder="name@example.bd" value="{{ old('email') }}">
                    @error('email')
                        <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="nid" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">National ID (NID)</label>
                    <input type="text" name="nid" id="nid" required
                           class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green transition-all"
                           placeholder="10 or 13-digit NID">
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white outline-none focus:ring-1 focus:ring-gov-green transition-all"
                           placeholder="Minimum 8 characters">
                    <input type="hidden" name="password_confirmation" id="password_confirmation">
                </div>

                <button type="submit" onclick="document.getElementById('password_confirmation').value = document.getElementById('password').value"
                        class="w-full py-3 rounded-lg bg-gov-green hover:bg-gov-light text-white font-bold text-xs shadow-md transition-all flex items-center justify-center space-x-1.5">
                    <span>👤+</span>
                    <span>Create account & send OTP</span>
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-500">
                    Already registered? <a href="{{ route('login') }}" class="text-gov-green hover:underline font-bold">Sign in</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function selectRole(role) {
            document.getElementById('role-field').value = role;
            const btnCitizen = document.getElementById('btn-citizen');
            const btnDealer = document.getElementById('btn-dealer');

            if (role === 'citizen_applicant') {
                btnCitizen.className = 'py-2.5 rounded-lg border-2 text-center text-xs font-bold transition-all focus:outline-none bg-emerald-50/20 border-gov-green text-gov-green';
                btnDealer.className = 'py-2.5 rounded-lg border border-slate-200 text-center text-xs font-bold transition-all focus:outline-none text-slate-650 hover:bg-slate-50';
            } else {
                btnDealer.className = 'py-2.5 rounded-lg border-2 text-center text-xs font-bold transition-all focus:outline-none bg-emerald-50/20 border-gov-green text-gov-green';
                btnCitizen.className = 'py-2.5 rounded-lg border border-slate-200 text-center text-xs font-bold transition-all focus:outline-none text-slate-650 hover:bg-slate-50';
            }
        }

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
