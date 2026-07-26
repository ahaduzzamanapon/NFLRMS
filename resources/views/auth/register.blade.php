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
    <div class="w-full lg:w-1/2 bg-slate-50 flex flex-col p-8 lg:p-12 overflow-y-auto">
        <div class="max-w-md w-full m-auto space-y-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm my-4">

            <div>
                <h3 class="text-2xl font-black font-serif text-slate-900 leading-none">Sign up</h3>
                <p class="text-[11px] text-slate-500 mt-2 font-medium">Step 1 of 2 . Personal details</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Account Type Selector (Tabs) -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Account Type</label>
                    <div id="role-wrap" class="grid grid-cols-2 gap-3 @error('role') ring-1 ring-rose-500 rounded-lg p-0.5 @enderror">
                        <button type="button" onclick="selectRole('citizen_applicant')" id="btn-citizen"
                                class="py-2.5 rounded-lg border-2 text-center text-xs font-bold transition-all focus:outline-none bg-emerald-50/20 border-gov-green text-gov-green">
                            Citizen (Individual)
                        </button>
                        <button type="button" onclick="selectRole('dealer_applicant')" id="btn-dealer"
                                class="py-2.5 rounded-lg border border-slate-200 text-center text-xs font-bold transition-all focus:outline-none text-slate-650 hover:bg-slate-50">
                            Arms Dealer / Firm
                        </button>
                    </div>
                    <input type="hidden" name="role" id="role-field" value="{{ old('role', 'citizen_applicant') }}">
                    @error('role')
                        <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                    <span id="js-error-role" class="text-[10px] text-rose-500 font-semibold mt-1 hidden">Please select an account type.</span>
                </div>

                <!-- Validation Summary Alert -->
                <div id="formValidationAlert" class="{{ $errors->any() ? '' : 'hidden' }} p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-bold space-y-1">
                    <span class="block text-sm font-black font-serif">
                        ⚠️ Please fill in the highlighted required field(s) above before continuing.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="name" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Full Name (English)</label>
                        <input type="text" name="name" id="name"
                               class="w-full px-3 py-2 text-xs rounded-lg border bg-white outline-none focus:ring-1 transition-all @error('name') border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:ring-gov-green @enderror"
                               placeholder="Md. Rafikul Islam" value="{{ old('name') }}">
                        @error('name')
                            <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                        <span id="js-error-name" class="text-[10px] text-rose-500 font-semibold mt-1 hidden">Full name (English) is required.</span>
                    </div>
                    <div>
                        <label for="name_bn" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Full Name (Bengali)</label>
                        <input type="text" name="name_bn" id="name_bn"
                               class="w-full px-3 py-2 text-xs rounded-lg border bg-white outline-none focus:ring-1 transition-all @error('name_bn') border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:ring-gov-green @enderror"
                               placeholder="মো: রফিকুল ইসলাম" value="{{ old('name_bn') }}">
                        @error('name_bn')
                            <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                        <span id="js-error-name_bn" class="text-[10px] text-rose-500 font-semibold mt-1 hidden">Full name (Bengali) is required.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="district_id" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">District</label>
                        <select name="district_id" id="district_id"
                                class="w-full px-3 py-2 text-xs rounded-lg border bg-white outline-none focus:ring-1 transition-all @error('district_id') border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:ring-gov-green @enderror">
                            <option value="">Select District</option>
                            @foreach(\App\Models\District::orderBy('name')->get() as $d)
                                <option value="{{ $d->id }}" @selected(old('district_id') == $d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                        @error('district_id')
                            <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                        <span id="js-error-district_id" class="text-[10px] text-rose-500 font-semibold mt-1 hidden">Please select a district.</span>
                    </div>
                    <div>
                        <label for="upazila_id" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Upazila / Thana</label>
                        <select name="upazila_id" id="upazila_id" disabled
                                class="w-full px-3 py-2 text-xs rounded-lg border bg-white outline-none focus:ring-1 transition-all @error('upazila_id') border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:ring-gov-green @enderror">
                            <option value="">Select District First</option>
                        </select>
                        @error('upazila_id')
                            <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror
                        <span id="js-error-upazila_id" class="text-[10px] text-rose-500 font-semibold mt-1 hidden">Please select an upazila / thana.</span>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Email</label>
                    <input type="email" name="email" id="email"
                           class="w-full px-3 py-2 text-xs rounded-lg border bg-white outline-none focus:ring-1 transition-all @error('email') border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:ring-gov-green @enderror"
                           placeholder="name@example.bd" value="{{ old('email') }}">
                    @error('email')
                        <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                    <span id="js-error-email" class="text-[10px] text-rose-500 font-semibold mt-1 hidden">Enter a valid email address.</span>
                </div>

                <div>
                    <label for="nid" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">National ID (NID)</label>
                    <input type="text" name="nid" id="nid"
                           class="w-full px-3 py-2 text-xs rounded-lg border bg-white outline-none focus:ring-1 transition-all @error('nid') border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:ring-gov-green @enderror"
                           placeholder="10 or 13-digit NID" value="{{ old('nid') }}">
                    @error('nid')
                        <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                    <span id="js-error-nid" class="text-[10px] text-rose-500 font-semibold mt-1 hidden">Enter a valid 10 or 13-digit NID.</span>
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-950 mb-1.5">Password</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-3 py-2 text-xs rounded-lg border bg-white outline-none focus:ring-1 transition-all @error('password') border-rose-500 focus:ring-rose-500 @else border-slate-200 focus:ring-gov-green @enderror"
                           placeholder="Minimum 6 characters">
                    @error('password')
                        <span class="text-[10px] text-rose-500 font-semibold mt-1 block">{{ $message }}</span>
                    @enderror
                    <span id="js-error-password" class="text-[10px] text-rose-500 font-semibold mt-1 hidden">Password must be at least 6 characters.</span>
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
        // ---------- Client-side validation ----------
        const ERROR_BORDER = ['border-rose-500', 'focus:ring-rose-500'];
        const NORMAL_BORDER = ['border-slate-200', 'focus:ring-gov-green'];

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

            const nid = document.getElementById('nid');
            const nidPattern = /^\d{10}$|^\d{13}$|^\d{17}$/;
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
        ['name', 'name_bn', 'district_id', 'upazila_id', 'email', 'nid', 'password'].forEach(function (id) {
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

            if (role === 'citizen_applicant') {
                btnCitizen.className = 'py-2.5 rounded-lg border-2 text-center text-xs font-bold transition-all focus:outline-none bg-emerald-50/20 border-gov-green text-gov-green';
                btnDealer.className = 'py-2.5 rounded-lg border border-slate-200 text-center text-xs font-bold transition-all focus:outline-none text-slate-650 hover:bg-slate-50';
            } else {
                btnDealer.className = 'py-2.5 rounded-lg border-2 text-center text-xs font-bold transition-all focus:outline-none bg-emerald-50/20 border-gov-green text-gov-green';
                btnCitizen.className = 'py-2.5 rounded-lg border border-slate-200 text-center text-xs font-bold transition-all focus:outline-none text-slate-650 hover:bg-slate-50';
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
