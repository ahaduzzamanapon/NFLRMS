@extends('layouts.app')

@section('title', 'Arms License Renewal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Title & Subtitle -->
    <div>
        <h2 class="text-2xl font-black font-serif text-slate-900 leading-tight">Arms License Renewal</h2>
        <p class="text-xs text-slate-500 mt-1 font-semibold">Annual renewal &mdash; automatic late-fine tier engine (BRS §7.4 &bull; Table 8.3)</p>
    </div>

    <!-- Stepper Navigation Header -->
    <div class="flex flex-wrap items-center justify-between gap-2 bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm text-[10px] font-bold">
        <div class="flex items-center space-x-1.5 step-indicator" data-step="1">
            <span class="w-5 h-5 rounded-full bg-gov-green text-white flex items-center justify-center font-bold text-[9px] step-number">1</span>
            <span class="text-slate-900 step-label">Select License</span>
        </div>
        <span class="text-slate-300 hidden sm:inline">&mdash;</span>

        <div class="flex items-center space-x-1.5 step-indicator" data-step="2">
            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number">2</span>
            <span class="text-slate-500 step-label">Compliance</span>
        </div>
        <span class="text-slate-300 hidden sm:inline">&mdash;</span>

        <div class="flex items-center space-x-1.5 step-indicator" data-step="3">
            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number">3</span>
            <span class="text-slate-500 step-label">Fee & Pay</span>
        </div>
        <span class="text-slate-300 hidden sm:inline">&mdash;</span>

        <div class="flex items-center space-x-1.5 step-indicator" data-step="4">
            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number">4</span>
            <span class="text-slate-500 step-label">Confirm</span>
        </div>
    </div>

    <!-- Multi-Step Renewal Form -->
    <form action="{{ route('citizen.renew', $license->id) }}" method="POST" id="renewal-multi-form" enctype="multipart/form-data" class="space-y-6" onsubmit="return validateStep(4);" novalidate>
        @csrf

        <!-- STEP 1: SELECT LICENSE -->
        <div class="step-panel bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4" id="panel-1">
            <span class="text-[10px] font-extrabold uppercase text-slate-400">Pick the license you wish to renew:</span>

            <div class="space-y-3">
                @foreach($licenses as $idx => $lic)
                    @php
                        $weapon = $lic->firearm_details['weapon_type'] ?? 'Handgun';
                        $isExpired = now()->isAfter($lic->expiry_date);
                        $daysDiff = (int) abs(now()->diffInDays($lic->expiry_date));
                    @endphp
                    <label id="label-lic-{{ $lic->id }}" class="flex items-center justify-between p-4 rounded-xl border {{ $lic->id === $license->id ? 'border-2 border-gov-green bg-emerald-50/5' : 'border-slate-200' }} hover:bg-slate-50 cursor-pointer transition-all">
                        <div class="flex items-start space-x-3">
                            <input type="radio" name="selected_licence" value="{{ $lic->license_number }}"
                                   data-id="{{ $lic->id }}"
                                   data-weapon-type="{{ $weapon }}"
                                   data-expiry-days="{{ (int) now()->diffInDays($lic->expiry_date, false) }}"
                                   {{ $lic->id === $license->id ? 'checked' : '' }}
                                   class="rounded text-gov-green focus:ring-0 mt-1"
                                   onchange="onLicenseSelected(this)">
                            <div>
                                <span class="text-xs font-black uppercase font-mono text-slate-900">{{ $lic->license_number }}</span>
                                <p class="text-[10px] text-slate-500 mt-1 font-semibold">
                                    {{ $weapon }} &bull;
                                    @if(!$isExpired)
                                        Valid &mdash; expires in {{ $daysDiff }} days
                                    @else
                                        Expired {{ $daysDiff }} days ago
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($isExpired)
                            <span class="text-[9px] font-black uppercase text-amber-600 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded">⚠️ Late fine applicable</span>
                        @endif
                    </label>
                @endforeach
            </div>
        </div>

        <!-- STEP 2: COMPLIANCE -->
        <div class="step-panel hidden bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4" id="panel-2">
            <div class="space-y-3">
                <div class="hidden p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-bold space-y-1" id="err-panel2-checks">
                    <span class="text-[12px] font-black font-serif">⚠️ Please confirm mandatory two checklist items above before continuing.</span>
                </div>
                <label id="lbl_chk_firing_ack" class="flex items-start space-x-2.5 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" id="chk_firing_ack" name="firing_report_ack" required class="rounded text-gov-green focus:ring-0 mt-0.5" onchange="clearFieldError('lbl_chk_firing_ack')">
                    <span class="text-xs text-slate-650 font-semibold leading-normal">Firing-range annual report attached (mandatory)</span>
                </label>
                <label id="lbl_chk_medical_ack" class="flex items-start space-x-2.5 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" id="chk_medical_ack" name="medical_ack" required class="rounded text-gov-green focus:ring-0 mt-0.5">
                    <span class="text-xs text-slate-650 font-semibold leading-normal">Medical fitness declaration (self + doctor)</span>
                </label>
                <label id="lbl_chk_police_ack" class="flex items-start space-x-2.5 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                    <input type="checkbox" id="chk_police_ack" name="police_ack" required class="rounded text-gov-green focus:ring-0 mt-0.5" onchange="clearFieldError('lbl_chk_police_ack')">
                    <span class="text-xs text-slate-600 font-semibold leading-normal">Local police station 'no adverse record' letter uploaded</span>
                </label>
            </div>

            <div>
                <label for="ammo_ledger" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Ammunition Ledger (Used / Issued in past year)</label>
                <input type="text" name="ammo_ledger" id="ammo_ledger" required value="18 / 24"
                       class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                <div class="hidden p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-bold space-y-1 mb-1" id="err-panel2-docs">
                    <span class="text-[12px] font-black font-serif">⚠️ Please upload the mandatory documents: Firing-range annual report and Local police station clearance letter.</span>
                </div>
                @php
                    $complianceDocs = [
                        'firing_report' => 'Firing-range annual report (mandatory)',
                        'medical_cert' => 'Medical fitness certificate (self + doctor)',
                        'police_clearance' => 'Local police station clearance letter'
                    ];
                    $mandatoryDocs = ['firing_report', 'police_clearance'];
                @endphp
                @foreach($complianceDocs as $key => $label)
                    <div class="flex items-center justify-between py-2.5 px-2 rounded-lg border border-transparent transition-colors" id="row-{{ $key }}">
                        <div class="flex items-center space-x-2">
                            <span>📄</span>
                            <span class="font-semibold text-slate-800">{{ $label }}</span>
                            @if(in_array($key, $mandatoryDocs))
                                <span class="text-[9px] font-black uppercase text-red-500">*</span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-3 text-[10px]">
                            <span id="status-{{ $key }}" class="text-amber-600 font-bold">⚠️ Not uploaded</span>
                            <input type="file" name="{{ $key }}" id="file-{{ $key }}" class="hidden" onchange="handleFileSelected('{{ $key }}')">
                            <button type="button" onclick="triggerUpload('{{ $key }}')" id="btn-{{ $key }}" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold border border-slate-200/50 transition-colors">Upload</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- STEP 3: FEE & PAY -->
        <div class="step-panel hidden bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-5" id="panel-3">
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs" id="status-badge-container">
                <span class="text-[8px] font-extrabold uppercase text-slate-900 block mb-0.5">Status</span>
                <span class="font-extrabold text-amber-800" id="status-badge-text">Tier 1 late (31-90d)</span>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                <div class="flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-semibold">Statutory renewal fee</span>
                    <span class="font-extrabold text-slate-900" id="fee-base">BDT 20,000</span>
                </div>
                <div class="flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-semibold">Platform service charge</span>
                    <span class="font-extrabold text-slate-900" id="fee-platform">BDT 720</span>
                </div>
                <div class="flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-semibold">Late fine (statutory)</span>
                    <span class="font-extrabold text-slate-900" id="fee-late">BDT 2,000</span>
                </div>
                <div class="flex justify-between items-center py-2.5">
                    <span class="text-slate-500 font-semibold">Platform late add-on</span>
                    <span class="font-extrabold text-slate-900" id="fee-platform-late">BDT 250</span>
                </div>
                <div class="flex justify-between items-center py-3.5 border-t border-slate-200 font-bold pt-3 mt-1">
                    <span class="text-slate-800 font-bold text-sm">Total payable</span>
                    <span class="font-black text-gov-green text-sm" id="fee-total">BDT 22,970</span>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-extrabold uppercase text-slate-900 mb-2">Select Payment Channel</label>
                <div class="grid grid-cols-3 gap-3" id="payment-channel-group">
                    <button type="button" id="pay-bkash" onclick="selectPayment('bkash')" class="py-2.5 rounded-lg border-2 border-gov-green bg-emerald-50/10 text-xs font-black text-gov-green transition-all">bKash</button>
                    <button type="button" id="pay-nagad" onclick="selectPayment('nagad')" class="py-2.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition-all">Nagad</button>
                    <button type="button" id="pay-card" onclick="selectPayment('card')" class="py-2.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition-all">Card / Bank</button>
                </div>
                <input type="hidden" name="payment_channel" id="payment_channel" value="bkash" required>
                <p id="err-panel3-payment" class="hidden text-[10px] text-red-600 font-bold pt-2">⚠️ Please select a payment channel to continue.</p>
            </div>
        </div>

        <!-- STEP 4: CONFIRM -->
        <div class="step-panel hidden bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4" id="panel-4">
            <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-500/5 text-xs text-emerald-800">
                <span class="text-[8px] font-extrabold uppercase text-slate-400 block mb-0.5">Ready to submit</span>
                <span class="font-extrabold" id="ready-submit-text">Renewing {{ $license->license_number }} (Revolver) &mdash; total <span class="font-black">BDT 22,970</span>.</span>
            </div>

            <label id="lbl_chk_confirm_declare" class="flex items-start space-x-2.5 p-3 bg-slate-50 rounded-lg border border-slate-200 cursor-pointer transition-colors">
                <input type="checkbox" id="chk_confirm_declare" name="declaration_ack" class="rounded text-gov-green focus:ring-0 mt-0.5" onchange="clearFieldError('lbl_chk_confirm_declare', 'err-panel4-confirm')">
                <span class="text-xs text-slate-650 font-semibold leading-normal">I declare the information is true. I understand that a false declaration will render the renewal void.</span>
            </label>
            <p id="err-panel4-confirm" class="hidden text-[10px] text-red-600 font-bold pl-1">⚠️ Please confirm the declaration before submitting.</p>
        </div>

        <!-- Wizard Navigation Bar -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-200">
            <button type="button" id="btn-prev" onclick="prevStep()" disabled
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200/80 text-slate-500 text-xs font-bold rounded-lg focus:outline-none transition-colors">
                &larr; Previous
            </button>
            <button type="button" id="btn-next" onclick="nextStep()"
                    class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white text-xs font-bold rounded-lg focus:outline-none transition-colors">
                Continue &rarr;
            </button>
            <button type="submit" id="btn-submit" class="hidden px-6 py-2.5 bg-gov-amber hover:bg-amber-500 text-slate-950 font-black text-xs rounded-lg transition-colors shadow-md">
                Submit Renewal
            </button>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 4;

    function updateStepIndicator() {
        document.querySelectorAll('.step-indicator').forEach(el => {
            const stepNum = parseInt(el.getAttribute('data-step'));
            const numSpan = el.querySelector('.step-number');
            const sisterLabel = el.querySelector('.step-label');

            if (!numSpan || !sisterLabel) {
                return;
            }

            if (stepNum < currentStep) {
                numSpan.className = 'w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[9px] step-number';
                numSpan.innerText = '✓';
                sisterLabel.className = 'text-slate-400 step-label';
            } else if (stepNum === currentStep) {
                numSpan.className = 'w-5 h-5 rounded-full bg-gov-green text-white flex items-center justify-center font-bold text-[9px] step-number';
                numSpan.innerText = stepNum;
                sisterLabel.className = 'text-slate-900 step-label';
            } else {
                numSpan.className = 'w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number';
                numSpan.innerText = stepNum;
                sisterLabel.className = 'text-slate-550 step-label';
            }
        });
    }

    function showStepPanel() {
        document.querySelectorAll('.step-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById(`panel-${currentStep}`).classList.remove('hidden');

        document.getElementById('btn-prev').disabled = currentStep === 1;

        if (currentStep === totalSteps) {
            document.getElementById('btn-next').classList.add('hidden');
            document.getElementById('btn-submit').classList.remove('hidden');
        } else {
            document.getElementById('btn-next').classList.remove('hidden');
            document.getElementById('btn-submit').classList.add('hidden');
        }
    }

    function nextStep() {
        if (!validateStep(currentStep)) {
            return;
        }
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepIndicator();
            showStepPanel();
        }
    }

    function clearFieldError(labelId, errorId) {
        const label = document.getElementById(labelId);
        if (label) {
            label.classList.remove('border-red-400', 'bg-red-50/40');
        }
        if (errorId) {
            const err = document.getElementById(errorId);
            if (err) err.classList.add('hidden');
        }
    }

    function validateStep(step) {
        let isValid = true;

        if (step === 2) {
            // Compliance checklist checkboxes must all be checked
            const checklistItems = [
                { inputId: 'chk_firing_ack', labelId: 'lbl_chk_firing_ack' },
                { inputId: 'chk_police_ack', labelId: 'lbl_chk_police_ack' }
            ];
            let checksOk = true;
            checklistItems.forEach(item => {
                const input = document.getElementById(item.inputId);
                const label = document.getElementById(item.labelId);
                if (!input || !input.checked) {
                    checksOk = false;
                    if (label) label.classList.add('border-red-400', 'bg-red-50/40');
                } else if (label) {
                    label.classList.remove('border-red-400', 'bg-red-50/40');
                }
            });
            document.getElementById('err-panel2-checks').classList.toggle('hidden', checksOk);
            if (!checksOk) isValid = false;

            // Mandatory document uploads: Firing-range annual report & Local police station clearance letter
            const mandatoryDocs = ['firing_report', 'police_clearance'];
            let docsOk = true;
            mandatoryDocs.forEach(key => {
                const fileInput = document.getElementById(`file-${key}`);
                const row = document.getElementById(`row-${key}`);
                const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
                if (!hasFile) {
                    docsOk = false;
                    if (row) row.classList.add('border-red-400', 'bg-red-50/40');
                } else if (row) {
                    row.classList.remove('border-red-400', 'bg-red-50/40');
                }
            });
            document.getElementById('err-panel2-docs').classList.toggle('hidden', docsOk);
            if (!docsOk) isValid = false;
        }

        if (step === 3) {
            const channel = document.getElementById('payment_channel').value;
            const group = document.getElementById('payment-channel-group');
            const errPay = document.getElementById('err-panel3-payment');
            if (!channel) {
                isValid = false;
                errPay.classList.remove('hidden');
                if (group) group.classList.add('ring-1', 'ring-red-400', 'rounded-lg', 'p-1');
            } else {
                errPay.classList.add('hidden');
                if (group) group.classList.remove('ring-1', 'ring-red-400', 'rounded-lg', 'p-1');
            }
        }

        if (step === 4) {
            const confirmChk = document.getElementById('chk_confirm_declare');
            const label = document.getElementById('lbl_chk_confirm_declare');
            const errConfirm = document.getElementById('err-panel4-confirm');
            if (!confirmChk || !confirmChk.checked) {
                isValid = false;
                if (label) label.classList.add('border-red-400', 'bg-red-50/40');
                errConfirm.classList.remove('hidden');
            } else {
                if (label) label.classList.remove('border-red-400', 'bg-red-50/40');
                errConfirm.classList.add('hidden');
            }
        }

        return isValid;
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            updateStepIndicator();
            showStepPanel();
        }
    }

    function selectPayment(channel) {
        document.getElementById('payment_channel').value = channel;
        document.getElementById('err-panel3-payment').classList.add('hidden');
        const group = document.getElementById('payment-channel-group');
        if (group) group.classList.remove('ring-1', 'ring-red-400', 'rounded-lg', 'p-1');

        // Reset classes
        ['bkash', 'nagad', 'card'].forEach(c => {
            const btn = document.getElementById(`pay-${c}`);
            if (btn) {
                btn.className = 'py-2.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition-all';
            }
        });

        // Style selected button
        const selectedBtn = document.getElementById(`pay-${channel}`);
        if (selectedBtn) {
            selectedBtn.className = 'py-2.5 rounded-lg border-2 border-gov-green bg-emerald-50/10 text-xs font-black text-gov-green transition-all';
        }
    }

    function onLicenseSelected(radio) {
        const weaponType = radio.getAttribute('data-weapon-type');
        const expiryDays = parseInt(radio.getAttribute('data-expiry-days'));
        const licenseNumber = radio.value;
        const licId = radio.getAttribute('data-id');

        // Update form action route dynamically to match target license
        const form = document.getElementById('renewal-multi-form');
        form.action = `/citizen/licenses/${licId}/renew`;

        // Highlight selected label border
        document.querySelectorAll('[id^="label-lic-"]').forEach(label => {
            label.className = 'flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition-all';
        });
        const activeLabel = document.getElementById(`label-lic-${licId}`);
        if (activeLabel) {
            activeLabel.className = 'flex items-center justify-between p-4 rounded-xl border-2 border-gov-green bg-emerald-50/5 hover:bg-slate-50 cursor-pointer transition-all';
        }

        // Calculate statutory fee
        // Handguns: 20000 BDT, Long Guns: 10000 BDT
        const isHandgun = (weaponType === 'Pistol' || weaponType === 'Revolver');
        const baseFee = isHandgun ? 20000 : 10000;
        const platformCharge = 720;

        let lateFine = 0;
        let latePlatform = 0;
        let statusText = 'On Time';

        if (expiryDays < 0) {
            const daysOverdue = Math.abs(expiryDays);
            if (daysOverdue <= 30) {
                lateFine = 1000;
                latePlatform = 100;
                statusText = 'Tier 1 late (1-30d)';
            } else if (daysOverdue <= 90) {
                lateFine = 2000;
                latePlatform = 250;
                statusText = 'Tier 1 late (31-90d)';
            } else {
                lateFine = 5000;
                latePlatform = 250;
                statusText = 'Tier 2 late (>90d)';
            }
        }

        const total = baseFee + platformCharge + lateFine + latePlatform;

        // Update DOM element values
        document.getElementById('status-badge-text').innerText = statusText;

        const badgeContainer = document.getElementById('status-badge-container');
        if (expiryDays < 0) {
            badgeContainer.className = 'p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs';
            document.getElementById('status-badge-text').className = 'font-extrabold text-amber-800';
        } else {
            badgeContainer.className = 'p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs';
            document.getElementById('status-badge-text').className = 'font-extrabold text-emerald-800';
        }

        document.getElementById('fee-base').innerText = `BDT ${baseFee.toLocaleString()}`;
        document.getElementById('fee-late').innerText = `BDT ${lateFine.toLocaleString()}`;
        document.getElementById('fee-platform-late').innerText = `BDT ${latePlatform.toLocaleString()}`;
        document.getElementById('fee-total').innerText = `BDT ${total.toLocaleString()}`;

        document.getElementById('ready-submit-text').innerHTML = `Renewing <span class="font-mono font-bold">${licenseNumber}</span> (${weaponType}) &mdash; total <span class="font-black">BDT ${total.toLocaleString()}</span>.`;
    }

    // Trigger initial calculation on load
    window.addEventListener('DOMContentLoaded', () => {
        const checkedRadio = document.querySelector('input[name="selected_licence"]:checked');
        if (checkedRadio) {
            onLicenseSelected(checkedRadio);
        }
    });

    function triggerUpload(key) {
        document.getElementById(`file-${key}`).click();
    }

    function handleFileSelected(key) {
        const fileInput = document.getElementById(`file-${key}`);
        const statusSpan = document.getElementById(`status-${key}`);
        const btn = document.getElementById(`btn-${key}`);

        if (fileInput.files && fileInput.files.length > 0) {
            const fileName = fileInput.files[0].name;
            statusSpan.className = 'text-emerald-600 font-bold';
            statusSpan.innerText = `✓ Uploaded (${fileName})`;
            btn.innerText = 'Replace';
        } else {
            statusSpan.className = 'text-amber-600 font-bold';
            statusSpan.innerText = '⚠️ Not uploaded';
            btn.innerText = 'Upload';
        }

        // Live-clear the mandatory document error state for this row
        const row = document.getElementById(`row-${key}`);
        if (row) row.classList.remove('border-red-400', 'bg-red-50/40');
        const mandatoryDocs = ['firing_report', 'police_clearance'];
        const stillMissing = mandatoryDocs.some(k => {
            const fi = document.getElementById(`file-${k}`);
            return !(fi && fi.files && fi.files.length > 0);
        });
        const errDocs = document.getElementById('err-panel2-docs');
        if (errDocs) errDocs.classList.toggle('hidden', !stillMissing);
    }
</script>
@endsection
