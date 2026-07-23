@extends('layouts.app')
@section('title', 'New Dealing Licence — Form K')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-black font-serif text-slate-900">New Dealing Licence (Form K)</h2>
        <p class="text-xs text-slate-500 mt-1 font-semibold">
            Arms Dealing Authorization · Appendix B, BRS §7.2 · 4-agency vetting required
        </p>
    </div>

    <!-- Fee Summary -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 flex items-center justify-between">
        <div class="text-xs font-bold text-amber-800">
            Statutory Fee: <span class="font-black">৳1,50,000</span> &bull; Platform Charge: <span class="font-black">৳2,500</span>
        </div>
        <span class="text-[10px] font-black text-amber-600 uppercase tracking-wider">Total: ৳1,52,500</span>
    </div>

    <!-- Validation Summary Alert -->
    <div id="formValidationAlert" class="{{ $errors->any() ? '' : 'hidden' }} p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-bold space-y-1">
        <span class="block text-sm font-black font-serif">
            ⚠️ Please fill in the highlighted required field(s) above before continuing.
        </span>
    </div>

    <form action="{{ route('dealer.apply.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="formKForm" novalidate>
        @csrf
        <input type="hidden" name="type" value="new_dealing_license">

        <!-- Section 1: Business Information -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-900">1. Business Information</span>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Firm / Business Name <span class="text-rose-500 font-semibold">*</span></label>
                    <input type="text" name="firm_name" id="firm_name" required minlength="3" value="{{ old('firm_name') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="e.g. Karim Arms & Ammunition">
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="firm_name"></span>
                    @error('firm_name')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Trade License Number <span class="text-rose-500 font-semibold">*</span></label>
                    <input type="text" name="trade_license" id="trade_license" required
                           pattern="^TL-[A-Z]{2,4}-\d{4}-\d{3,6}$" value="{{ old('trade_license') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="TL-DHK-2024-XXXXX">
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="trade_license"></span>
                    @error('trade_license')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Business Address <span class="text-rose-500 font-semibold">*</span></label>
                    <input type="text" name="business_address" id="business_address" required minlength="10" value="{{ old('business_address') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('business_address') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="Full address of premises">
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="business_address"></span>
                    @error('business_address')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">District <span class="text-rose-500 font-semibold">*</span></label>
                    <select name="district_id" id="dealer_district_id" required
                            class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('district_id') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                        <option value="">— Select District —</option>
                        @foreach($districts as $d)
                        <option value="{{ $d->id }}" {{ old('district_id')==$d->id?'selected':'' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="district_id"></span>
                    @error('district_id')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Upazila / Thana <span class="text-rose-500 font-semibold">*</span></label>
                    <select name="upazila_id" id="dealer_upazila_id" required disabled
                            class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('upazila_id') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                        <option value="">— Select District First —</option>
                    </select>
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="upazila_id"></span>
                    @error('upazila_id')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Licence Class <span class="text-rose-500 font-semibold">*</span></label>
                    <select name="license_class" id="license_class" required
                            class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('license_class') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                        <option value="">— Select Class —</option>
                        <option value="A" {{ old('license_class')=='A'?'selected':'' }}>Class A — Retail Sale</option>
                        <option value="B" {{ old('license_class')=='B'?'selected':'' }}>Class B — Wholesale</option>
                        <option value="C" {{ old('license_class')=='C'?'selected':'' }}>Class C — Import / Export</option>
                    </select>
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="license_class"></span>
                    @error('license_class')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Proprietor Details -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-900">2. Proprietor / Responsible Person</span>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Full Name <span class="text-rose-500 font-semibold">*</span></label>
                    <input type="text" name="proprietor_name" id="proprietor_name" required value="{{ old('proprietor_name', auth()->user()->name) }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('proprietor_name') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green">
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="proprietor_name"></span>
                    @error('proprietor_name')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">National ID (NID) <span class="text-rose-500 font-semibold">*</span></label>
                    <input type="text" name="nid" id="nid" required pattern="^\d{10}(\d{7})?$" inputmode="numeric"
                           value="{{ old('nid', auth()->user()->nid) }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('nid') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="10 or 17-digit NID number">
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="nid"></span>
                    @error('nid')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Mobile Number <span class="text-rose-500 font-semibold">*</span></label>
                    <input type="text" name="mobile" id="mobile" required pattern="^01[3-9]\d{8}$" inputmode="numeric"
                           value="{{ old('mobile', auth()->user()->phone) }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('mobile') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="01XXXXXXXXX">
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="mobile"></span>
                    @error('mobile')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Annual Income (BDT) <span class="text-rose-500 font-semibold">*</span></label>
                    <input type="number" name="annual_income" id="annual_income" required min="1" step="1" value="{{ old('annual_income') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('annual_income') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="0">
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="annual_income"></span>
                    @error('annual_income')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Stock Categories -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">3. Arms Categories to be Dealt</span>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-2 rounded-lg border {{ $errors->has('categories') ? 'border-rose-400 bg-rose-50/40' : 'border-transparent' }} js-error-wrapper" data-wrapper-for="categories">
                    @foreach(['Pistol','Revolver','Shotgun','Rifle','Air Gun','Ammunition'] as $cat)
                    <label class="flex items-center space-x-2.5 p-3 rounded-lg border border-slate-200 cursor-pointer hover:border-gov-green hover:bg-emerald-50/50 transition-colors">
                        <input type="checkbox" name="categories[]" value="{{ $cat }}"
                               class="rounded border-slate-300 text-gov-green focus:ring-gov-green"
                               {{ in_array($cat, old('categories', [])) ? 'checked' : '' }}>
                        <span class="text-xs font-semibold text-slate-700">{{ $cat }}</span>
                    </label>
                    @endforeach
                </div>
                <span class="text-[10px] text-rose-500 font-semibold mt-2 block js-error" data-for="categories"></span>
                @error('categories')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <!-- Section 4: Documents -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">4. Required Documents</span>
            </div>
            <div class="p-5 space-y-3">
                @foreach([
                    ['name'=>'nid_copy','label'=>'NID Copy (Front & Back)'],
                    ['name'=>'trade_license_doc','label'=>'Trade License (Current Year)'],
                    ['name'=>'premises_photo','label'=>'Premises Photograph'],
                    ['name'=>'bank_statement','label'=>'Bank Statement (Last 6 months)'],
                ] as $doc)
                <div>
                    <div class="flex items-center justify-between p-3 rounded-lg border {{ $errors->has($doc['name']) ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} js-error-wrapper" data-wrapper-for="{{ $doc['name'] }}">
                        <div>
                            <div class="text-xs font-bold text-slate-900">{{ $doc['label'] }}</div>
                            <div class="text-[10px] text-slate-400 font-medium">PDF or JPG/PNG · Max 5MB</div>
                        </div>
                        <input type="file" name="{{ $doc['name'] }}" id="{{ $doc['name'] }}" required
                               accept=".pdf,.jpg,.jpeg,.png" data-max-size="5242880"
                               class="text-[10px] text-slate-600 js-file-input">
                    </div>
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="{{ $doc['name'] }}"></span>
                    @error($doc['name'])<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                @endforeach
            </div>
        </div>

        <!-- Declaration -->
        <div class="bg-white rounded-xl border {{ $errors->has('declaration') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} shadow-sm p-5 js-error-wrapper" data-wrapper-for="declaration">
            <label class="flex items-start space-x-3 cursor-pointer">
                <input type="checkbox" name="declaration" id="declaration" required value="1"
                       class="mt-0.5 rounded border-slate-300 text-gov-green focus:ring-gov-green">
                <span class="text-xs text-slate-600 leading-relaxed font-medium">
                    I declare that all information provided above is true and accurate to the best of my knowledge.
                    I understand that submission of false information may result in rejection of my application and legal prosecution
                    under the Arms Act 1878 and applicable provisions of the Penal Code.
                </span>
            </label>
            <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error" data-for="declaration"></span>
            @error('declaration')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
        </div>

        <!-- Submit -->
        <div class="flex gap-3 justify-end">
            <a href="{{ route('dealer.dashboard') }}"
               class="px-5 py-2.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-gov-green hover:bg-gov-light text-white font-black text-xs shadow-md transition-colors">
                Submit Form K Application →
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('dealer_district_id')?.addEventListener('change', function () {
        const districtId = this.value;
        const upazilaSelect = document.getElementById('dealer_upazila_id');
        upazilaSelect.innerHTML = '<option value="">Loading...</option>';
        upazilaSelect.disabled = true;

        if (!districtId) {
            upazilaSelect.innerHTML = '<option value="">— Select District First —</option>';
            return;
        }

        fetch(`/api/districts/${districtId}/upazilas`)
            .then(response => response.json())
            .then(data => {
                upazilaSelect.innerHTML = '<option value="">— Select Upazila / Thana —</option>';
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

    // ---- Form K client-side validation ----
    (function () {
        const form = document.getElementById('formKForm');
        if (!form) return;

        const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
        const ALLOWED_EXT = ['pdf', 'jpg', 'jpeg', 'png'];

        function showError(fieldName, message) {
            const span = form.querySelector(`.js-error[data-for="${fieldName}"]`);
            if (span) span.textContent = message || '';
            const input = form.querySelector(`[name="${fieldName}"]`) || document.getElementById(fieldName);
            if (input) input.classList.toggle('border-rose-400', !!message);

            // Checkbox groups, file inputs, and the declaration checkbox render
            // without a visible border on the control itself, so highlight the
            // surrounding card instead.
            const wrapper = form.querySelector(`.js-error-wrapper[data-wrapper-for="${fieldName}"]`);
            if (wrapper) {
                wrapper.classList.toggle('border-rose-400', !!message);
                wrapper.classList.toggle('bg-rose-50/40', !!message);
                wrapper.classList.toggle('border-slate-200', !message && wrapper.dataset.wrapperFor !== 'categories');
                wrapper.classList.toggle('border-transparent', !message && wrapper.dataset.wrapperFor === 'categories');
            }
        }

        function clearAllErrors() {
            form.querySelectorAll('.js-error').forEach(s => s.textContent = '');
            form.querySelectorAll('input, select').forEach(el => el.classList.remove('border-rose-400'));
        }

        function validateFile(input) {
            const file = input.files && input.files[0];
            if (!file) {
                showError(input.name, 'This document is required.');
                return false;
            }
            const ext = file.name.split('.').pop().toLowerCase();
            if (!ALLOWED_EXT.includes(ext)) {
                showError(input.name, 'Only PDF, JPG or PNG files are allowed.');
                return false;
            }
            if (file.size > MAX_FILE_SIZE) {
                showError(input.name, 'File exceeds the 5MB size limit.');
                return false;
            }
            showError(input.name, '');
            return true;
        }

        function validateField(input, message) {
            if (!input.checkValidity()) {
                showError(input.name, message);
                return false;
            }
            showError(input.name, '');
            return true;
        }

        function validateCategories() {
            const checked = form.querySelectorAll('input[name="categories[]"]:checked');
            if (checked.length === 0) {
                showError('categories', 'Select at least one arms category.');
                return false;
            }
            showError('categories', '');
            return true;
        }

        function validateForm() {
            clearAllErrors();
            let valid = true;

            valid = validateField(document.getElementById('firm_name'), 'Firm / business name must be at least 3 characters.') && valid;
            valid = validateField(document.getElementById('trade_license'), 'Enter a valid trade license number, e.g. TL-DHK-2024-00123.') && valid;
            valid = validateField(document.getElementById('business_address'), 'Enter the full business address (min. 10 characters).') && valid;
            valid = validateField(document.getElementById('dealer_district_id'), 'Please select a district.') && valid;
            valid = validateField(document.getElementById('dealer_upazila_id'), 'Please select an upazila / thana.') && valid;
            valid = validateField(document.querySelector('select[name="license_class"]'), 'Please select a licence class.') && valid;
            valid = validateField(document.getElementById('proprietor_name'), 'Proprietor name is required.') && valid;
            valid = validateField(document.getElementById('nid'), 'Enter a valid 10 or 17-digit NID number.') && valid;
            valid = validateField(document.getElementById('mobile'), 'Enter a valid Bangladeshi mobile number, e.g. 01712345678.') && valid;
            valid = validateField(document.getElementById('annual_income'), 'Enter a valid annual income greater than 0.') && valid;

            valid = validateCategories() && valid;

            form.querySelectorAll('.js-file-input').forEach(input => {
                valid = validateFile(input) && valid;
            });

            const declaration = document.getElementById('declaration');
            if (!declaration.checked) {
                showError('declaration', 'You must accept the declaration to proceed.');
                valid = false;
            }

            return valid;
        }

        function maybeHideAlert() {
            const alertBox = document.getElementById('formValidationAlert');
            const hasVisibleError = Array.from(form.querySelectorAll('.js-error')).some(s => s.textContent.trim() !== '');
            if (!hasVisibleError) alertBox?.classList.add('hidden');
        }

        // Live validation on blur/change for a smoother experience
        // ['firm_name', 'trade_license', 'business_address', 'nid', 'mobile', 'annual_income', 'proprietor_name'].forEach(id => {
        //     const el = document.getElementById(id);
        //     el?.addEventListener('blur', () => { validateField(el, el.title || 'This field is invalid.'); maybeHideAlert(); });
        // });
        document.getElementById('dealer_district_id')?.addEventListener('change', () => { showError('district_id', ''); maybeHideAlert(); });
        document.getElementById('dealer_upazila_id')?.addEventListener('change', () => { showError('upazila_id', ''); maybeHideAlert(); });
        document.querySelector('select[name="license_class"]')?.addEventListener('change', function () { showError('license_class', ''); maybeHideAlert(); });
        form.querySelectorAll('input[name="categories[]"]').forEach(cb => cb.addEventListener('change', () => { validateCategories(); maybeHideAlert(); }));
        form.querySelectorAll('.js-file-input').forEach(input => input.addEventListener('change', () => { validateFile(input); maybeHideAlert(); }));
        document.getElementById('declaration')?.addEventListener('change', function () {
            showError('declaration', this.checked ? '' : 'You must accept the declaration to proceed.');
            maybeHideAlert();
        });

        form.addEventListener('submit', function (e) {
            const alertBox = document.getElementById('formValidationAlert');
            if (!validateForm()) {
                e.preventDefault();
                alertBox?.classList.remove('hidden');
                const firstError = form.querySelector('.js-error:not(:empty)');
                firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                alertBox?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                alertBox?.classList.add('hidden');
            }
        });
    })();
</script>
@endsection
