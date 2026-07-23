@extends('layouts.app')
@section('title', 'Renew Dealing Licence')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-black font-serif text-slate-900">Renew Dealing Licence</h2>
        <p class="text-xs text-slate-500 mt-1 font-semibold">
            Stock ledger reconciliation required · Committee review · BRS §8.3
        </p>
    </div>

    <!-- Fee -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 flex items-center justify-between">
        <div class="text-xs font-bold text-amber-800">
            Renewal Fee: <span class="font-black">৳75,000</span> &bull; Platform Charge: <span class="font-black">৳2,500</span>
        </div>
        <span class="text-[10px] font-black text-amber-600 uppercase tracking-wider">Total: ৳77,500</span>
    </div>

    @if($licenses->isEmpty())
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-10 text-center space-y-3">
        <div class="text-3xl">📋</div>
        <div class="text-sm font-bold text-slate-700">No active dealing licences found</div>
        <p class="text-xs text-slate-500">You need an issued dealing licence before you can renew. Apply for a new licence first.</p>
        <a href="{{ route('dealer.apply') }}"
           class="inline-block mt-2 px-5 py-2.5 rounded-lg bg-gov-green hover:bg-gov-light text-white font-bold text-xs shadow transition-colors">
            Apply for New Dealing Licence →
        </a>
    </div>
    @else
    <form id="renewal-form" method="POST" action="" class="space-y-6" novalidate>
        @csrf

        <!-- Validation Summary Alert -->
        <div id="formValidationAlert" class="{{ $errors->any() ? '' : 'hidden' }} p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-bold space-y-1">
            <span class="block text-sm font-black font-serif">
                ⚠️ Please fill in the highlighted required field(s) above before continuing.
            </span>
        </div>

        <!-- Select Licence -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">Select Dealing Licence to Renew</span>
            </div>
            <div class="p-5 space-y-3 rounded-lg border border-transparent js-error-wrapper" data-wrapper-for="license_id">
                @foreach($licenses as $lic)
                <label class="flex items-center space-x-3 p-3.5 rounded-lg border border-slate-200 cursor-pointer hover:border-gov-green hover:bg-emerald-50/50 transition-colors">
                    <input type="radio" name="license_id" value="{{ $lic->id }}" required
                           data-action="{{ route('citizen.renew', $lic->id) }}"
                           {{ (old('license_id', $loop->first ? $lic->id : null)) == $lic->id ? 'checked' : '' }}
                           class="border-slate-300 text-gov-green focus:ring-gov-green">
                    <div>
                        <div class="text-xs font-bold text-slate-900">{{ $lic->license_number }}</div>
                        <div class="text-[10px] text-slate-500 font-medium">
                            Expires: {{ $lic->expiry_date?->format('d M Y') ?? 'N/A' }} &bull;
                            Status: <span class="font-black text-{{ $lic->status === 'active' ? 'gov-green' : 'amber-600' }}">{{ ucfirst($lic->status) }}</span>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            <div class="px-5 pb-4">
                <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error" data-for="license_id"></span>
                @error('license_id')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
            </div>
        </div>

        <!-- Stock Declaration -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">Stock Declaration (Year-end)</span>
            </div>
            <div class="p-5 space-y-3">
                <p class="text-xs text-slate-500 font-medium">
                    Declare your closing stock for the year. This will be cross-checked against your submitted Stock Ledger.
                    <a href="{{ route('dealer.stock_ledger') }}" class="text-gov-green font-bold hover:underline">View Stock Ledger →</a>
                </p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Total Firearms in Stock</label>
                        <input type="number" name="declared_firearms" id="declared_firearms" min="0" required step="1" value="{{ old('declared_firearms') }}"
                               class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('declared_firearms') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green"
                               placeholder="0">
                        <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="declared_firearms"></span>
                        @error('declared_firearms')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Total Ammunition Rounds</label>
                        <input type="number" name="declared_ammo" id="declared_ammo" min="0" required step="1" value="{{ old('declared_ammo') }}"
                               class="w-full px-3 py-2.5 text-xs rounded-lg border {{ $errors->has('declared_ammo') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green"
                               placeholder="0">
                        <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="declared_ammo"></span>
                        @error('declared_ammo')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Declaration -->
        <div class="bg-white rounded-xl border {{ $errors->has('declaration') ? 'border-rose-400' : 'border-slate-200' }} shadow-sm p-5 js-error-wrapper" data-wrapper-for="declaration">
            <label class="flex items-start space-x-3 cursor-pointer">
                <input type="checkbox" name="declaration" id="declaration" required value="1"
                       class="mt-0.5 rounded border-slate-300 text-gov-green focus:ring-gov-green">
                <span class="text-xs text-slate-600 leading-relaxed font-medium">
                    I confirm that the declared stock is accurate and matches the physical inventory at my premises.
                    I understand that discrepancies may result in an anomaly audit by MoHA.
                </span>
            </label>
            <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error" data-for="declaration"></span>
            @error('declaration')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('citizen.dashboard') }}"
               class="px-5 py-2.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-gov-green hover:bg-gov-light text-white font-black text-xs shadow-md transition-colors">
                Submit Renewal Application →
            </button>
        </div>
    </form>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('renewal-form');
        if (!form) return;
        const radios = document.querySelectorAll('input[name="license_id"]');

        function updateAction() {
            const checkedRadio = document.querySelector('input[name="license_id"]:checked');
            if (checkedRadio) {
                form.action = checkedRadio.dataset.action;
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', updateAction);
        });

        // Initial set
        updateAction();

        // ---- Renewal form client-side validation ----
        function showError(fieldName, message) {
            const span = form.querySelector(`.js-error[data-for="${fieldName}"]`);
            if (span) span.textContent = message || '';
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) input.classList.toggle('border-rose-400', !!message);

            const wrapper = form.querySelector(`.js-error-wrapper[data-wrapper-for="${fieldName}"]`);
            if (wrapper) {
                wrapper.classList.toggle('border-rose-400', !!message);
                wrapper.classList.toggle('bg-rose-50/40', !!message);
                wrapper.classList.toggle('border-slate-200', !message && wrapper.dataset.wrapperFor !== 'license_id');
                wrapper.classList.toggle('border-transparent', !message && wrapper.dataset.wrapperFor === 'license_id');
            }
        }

        function clearAllErrors() {
            form.querySelectorAll('.js-error').forEach(s => s.textContent = '');
            form.querySelectorAll('input').forEach(el => el.classList.remove('border-rose-400'));
        }

        function maybeHideAlert() {
            const alertBox = document.getElementById('formValidationAlert');
            const hasVisibleError = Array.from(form.querySelectorAll('.js-error')).some(s => s.textContent.trim() !== '');
            if (!hasVisibleError) alertBox?.classList.add('hidden');
        }

        function validateLicense() {
            const checked = form.querySelector('input[name="license_id"]:checked');
            if (!checked) {
                showError('license_id', 'Please select a licence to renew.');
                return false;
            }
            showError('license_id', '');
            return true;
        }

        function validateDeclaration() {
            const declaration = document.getElementById('declaration');
            if (!declaration.checked) {
                showError('declaration', 'You must accept the declaration to proceed.');
                return false;
            }
            showError('declaration', '');
            return true;
        }

        function validateStockField(input, label) {
            const value = input.value.trim();
            if (value === '') {
                showError(input.name, `${label} is required.`);
                return false;
            }
            const num = Number(value);
            if (!Number.isInteger(num) || num < 0) {
                showError(input.name, `${label} must be a whole number of 0 or more.`);
                return false;
            }
            showError(input.name, '');
            return true;
        }

        function validateFirearms() {
            return validateStockField(document.getElementById('declared_firearms'), 'Total firearms in stock');
        }

        function validateAmmo() {
            return validateStockField(document.getElementById('declared_ammo'), 'Total ammunition rounds');
        }

        function validateForm() {
            clearAllErrors();
            let valid = true;
            valid = validateLicense() && valid;
            valid = validateFirearms() && valid;
            valid = validateAmmo() && valid;
            valid = validateDeclaration() && valid;
            return valid;
        }

        radios.forEach(radio => radio.addEventListener('change', () => { validateLicense(); maybeHideAlert(); }));
        document.getElementById('declared_firearms')?.addEventListener('blur', () => { validateFirearms(); maybeHideAlert(); });
        document.getElementById('declared_ammo')?.addEventListener('blur', () => { validateAmmo(); maybeHideAlert(); });
        document.getElementById('declaration')?.addEventListener('change', () => { validateDeclaration(); maybeHideAlert(); });

        form.addEventListener('submit', function (e) {
            const alertBox = document.getElementById('formValidationAlert');
            if (!validateForm()) {
                e.preventDefault();
                alertBox?.classList.remove('hidden');
                const firstError = form.querySelector('.js-error:not(:empty)');
                firstError?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alertBox?.classList.add('hidden');
            }
        });
    });
</script>
@endsection
