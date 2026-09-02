@extends('layouts.app')
@section('title', 'My Applications')

@section('content')
<div class="space-y-6">

    <!-- Top Profile & Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">
                Welcome, {{ auth()->user()->name }}
            </h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                NID {{ auth()->user()->nid ?? '—' }} &bull; {{ auth()->user()->district->name ?? 'Dhaka' }} District
            </p>
        </div>
        <div class="flex-shrink-0">
            <a href="{{ route('citizen.apply') }}" class="px-4 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors flex items-center space-x-1.5 shadow-sm">
                <span>+</span>
                <span>New Application</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[5.5rem] h-auto">
            <h4 class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Active Licenses</h4>
            <p class="text-2xl sm:text-3xl font-bold font-serif text-emerald-600 mt-1">{{ $licenses->count() }}</p>
        </div>
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[5.5rem] h-auto">
            <h4 class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">In Progress</h4>
            <p class="text-2xl sm:text-3xl font-bold font-serif text-blue-600 mt-1">{{ $applications->whereNotIn('status', ['approved', 'rejected', 'suspended'])->count() }}</p>
        </div>
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[5.5rem] h-auto">
            <h4 class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Needs Attention</h4>
            <p class="text-2xl sm:text-3xl font-bold font-serif text-amber-500 mt-1">{{ $applications->where('status', 'suspended')->count() }}</p>
        </div>
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[5.5rem] h-auto">
            <h4 class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Total Applications</h4>
            <p class="text-2xl sm:text-3xl font-bold font-serif text-slate-900 mt-1">{{ $applications->count() }}</p>
        </div>
    </div>

    <!-- Warning Banner (Conditional) -->
    @if($applications->where('status', 'suspended')->isNotEmpty())
        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start space-x-2.5">
            <span class="text-base mt-0.5"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <div>
                <span class="font-bold">You have {{ $applications->where('status', 'suspended')->count() }} application(s) needing action.</span>
                <p class="text-slate-600 mt-0.5">Complete payment or begin re-vetting to reactivate a suspended license.</p>
            </div>
        </div>
    @endif

    <!-- My Active Licence Section -->
    <div class="space-y-3">
        <h3 class="text-[11px] font-semibold uppercase text-slate-400 tracking-wider">
            My Active Licence
        </h3>

        @php
            $activeLicenses = $licenses->where('status', 'active');
        @endphp

        @if($activeLicenses->isEmpty())
            <div class="max-w-xl p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md">
                <p class="text-xs text-slate-400 font-medium text-center py-4">
                    No active license yet. <a href="{{ route('citizen.apply') }}" class="text-gov-green font-bold hover:underline">Apply for a new license <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></a>
                </p>
            </div>
        @else
            @foreach($activeLicenses as $l)
            <div class="max-w-xl p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md flex flex-col sm:flex-row justify-between gap-4 sm:gap-6">
                <div class="flex-grow space-y-4">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Government of Bangladesh" class="w-8 h-8 object-contain shrink-0">
                            <div>
                                <h4 class="text-[10px] font-semibold uppercase text-slate-500 leading-none">
                                    Government of Bangladesh &bull; MoHA
                                </h4>
                                <h3 class="text-xs font-bold text-slate-900 mt-1 leading-none">
                                    Firearm Licence
                                </h3>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                            {{ $l->status === 'active' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/25' : 'bg-rose-500/10 text-rose-600 border border-rose-500/25' }}">
                            {{ ucfirst($l->status) }}
                        </span>
                    </div>

                    <!-- Fields -->
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 text-[11px]">
                        <div>
                            <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">Holder</span>
                            <span class="font-bold text-slate-900">{{ auth()->user()->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">Weapon</span>
                            <span class="font-bold text-slate-900">{{ $l->firearm_details['weapon_type'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">Licence No.</span>
                            <span class="font-bold text-slate-900 uppercase font-mono break-all">{{ $l->license_number }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">District</span>
                            <span class="font-bold text-slate-900">{{ auth()->user()->district->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">Issued</span>
                            <span class="font-bold text-slate-900">{{ $l->issue_date->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">Expires</span>
                            <span class="font-bold text-slate-900 {{ $l->expiry_date->isPast() ? 'text-rose-600' : '' }}">
                                {{ $l->expiry_date->format('d M Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-2.5 flex items-center justify-between flex-wrap gap-2">
                        <a href="{{ route('citizen.renew', Crypt::encryptString($l->id)) }}"
                           class="text-[10px] font-semibold text-gov-green hover:underline"><i class="fa-solid fa-arrows-rotate mr-1"></i> Renew License</a>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('license.download', Crypt::encryptString($l->id)) }}"
                               target="_blank"
                               class="text-[10px] font-semibold text-gov-green hover:underline flex items-center">
                                <i class="fa-solid fa-file-pdf mr-1"></i> Download PDF
                            </a>
                            <a href="{{ route('verify', ['license_number' => $l->license_number]) }}"
                               class="text-[10px] font-semibold text-gov-green hover:underline flex items-center">
                                <i class="fa-solid fa-circle-check mr-1"></i> Verify
                            </a>
                        </div>
                    </div>
                </div>

                <!-- QR Code Side -->
                <div class="flex-shrink-0 flex flex-col items-center justify-between sm:border-l border-slate-100 sm:pl-6 text-center">
                    <div class="w-24 h-24 bg-white border border-slate-200 rounded-xl p-1.5 flex items-center justify-center shadow-sm">
                        <div id="qr-{{ $l->id }}"
                             data-url="{{ route('verify', ['license_number' => $l->license_number]) }}"
                             class="w-full h-full">
                        </div>
                    </div>
                    <span class="text-[8px] text-slate-400 font-medium uppercase mt-2 leading-tight">Scan to verify<br>on NFLRMS portal</span>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- My Applications Table Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="text-sm font-bold text-slate-900 font-serif">My Applications</h3>
            <button onclick="window.location.reload()" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 flex items-center space-x-1">
                <span><i class="fa-solid fa-arrows-rotate"></i></span>
                <span>Refresh</span>
            </button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto shadow-sm">
            <table class="w-full text-left border-collapse min-w-[680px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold uppercase text-slate-500 tracking-wider">
                        <th class="p-3 pl-5">Reference</th>
                        <th class="p-3">Service</th>
                        <th class="p-3">Submitted</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-[11px] divide-y divide-slate-100">
                    @forelse($applications as $a)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 pl-5 font-semibold font-mono text-slate-900 whitespace-nowrap">{{ $a->application_number }}</td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="font-semibold text-slate-800">
                                    @if($a->type === 'renewal')
                                        Renewal &bull; {{ in_array($a->firearm_details['weapon_type'] ?? '', ['Pistol', 'Revolver']) ? 'Handgun' : 'Long Gun' }}
                                    @else
                                        New License &bull; {{ in_array($a->firearm_details['weapon_type'] ?? '', ['Pistol', 'Revolver']) ? 'Handgun' : 'Long Gun' }}
                                    @endif
                                </span>
                                <span class="text-slate-400 font-normal">&bull; {{ $a->firearm_details['weapon_type'] ?? 'Revolver' }} ({{ $a->firearm_details['bore'] ?? '12 Bore' }})</span>
                            </td>
                            <td class="p-3 font-normal text-slate-500 whitespace-nowrap">
                                {{ $a->created_at->format('d M Y') }}
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                @php
                                    $badgeStyles = match($a->status) {
                                        'payment_pending' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
                                        'waiting_for_license_fee' => 'bg-indigo-500/10 text-indigo-600 border-indigo-500/20',
                                        'submitted' => 'bg-blue-500/10 text-blue-600 border-blue-500/20',
                                        'received' => 'bg-indigo-500/10 text-indigo-600 border-indigo-500/20',
                                        'pending_vetting' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
                                        'vetted_cleared' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
                                        'vetted_flagged' => 'bg-red-500/10 text-red-600 border-red-500/20',
                                        'approved', 'license_issued' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
                                        'rejected' => 'bg-red-500/10 text-red-600 border-red-500/20',
                                        'suspended' => 'bg-rose-500/10 text-rose-600 border-rose-500/20',
                                        default => 'bg-slate-500/10 text-slate-600 border-slate-500/20',
                                    };
                                    $statusLabel = match($a->status) {
                                        'payment_pending' => 'Payment Pending',
                                        'waiting_for_license_fee' => 'Waiting for License Fee',
                                        'submitted' => 'Awaiting Verification',
                                        'received' => 'Under Review',
                                        'pending_vetting' => 'Awaiting Vetting Clearance',
                                        'vetted_cleared' => 'Vetted: Passed',
                                        'vetted_flagged' => 'Vetted: Flagged',
                                        'approved' => 'Certificate Issued',
                                        'license_issued' => 'License Issued',
                                        'rejected' => 'Rejected',
                                        'suspended' => 'Suspended',
                                        default => ucfirst(str_replace('_', ' ', $a->status ?? '')),
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border uppercase tracking-wider {{ $badgeStyles }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="p-3 pr-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end space-x-1.5">
                                    @if($a->status === 'payment_pending')
                                        <a href="{{ route('payment.initiate', [Crypt::encryptString($a->id), 'type' => 'service_fee']) }}" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-[11px] font-bold shadow-sm transition-colors">
                                            Pay Platform Fee
                                        </a>
                                        <button onclick="checkPaymentStatus('{{ Crypt::encryptString($a->id) }}', this)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-bold border border-slate-300 transition-colors" title="Check PayStation gateway for payment status">
                                            <i class="fa-solid fa-magnifying-glass mr-1"></i> Verify
                                        </button>
                                    @elseif($a->status === 'waiting_for_license_fee')
                                        <a href="{{ route('payment.initiate', [Crypt::encryptString($a->id), 'type' => 'license_fee']) }}" class="px-2.5 py-1 text-white rounded text-[11px] font-bold shadow-sm transition-all animate-pay-license">
                                            Pay License Fee
                                        </a>
                                        <button onclick="checkPaymentStatus('{{ Crypt::encryptString($a->id) }}', this)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-bold border border-slate-300 transition-colors" title="Check PayStation gateway for payment status">
                                            <i class="fa-solid fa-magnifying-glass mr-1"></i> Verify
                                        </button>
                                    @endif
                                    <a href="{{ route('citizen.show', Crypt::encryptString($a->id)) }}" class="text-gov-green hover:underline font-semibold ml-1.5">
                                        View <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-8 text-slate-400 font-normal">
                                No application records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Verification Alert Modal -->
<div id="verify-alert-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-sm w-full p-5 text-center transform transition-all">
        <div id="verify-alert-icon-container" class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold mx-auto mb-3">
            <i id="verify-alert-icon" class="fa-solid fa-circle-check"></i>
        </div>
        <h3 id="verify-alert-title" class="text-sm font-bold text-slate-900 font-serif">Payment Status</h3>
        <p id="verify-alert-message" class="text-xs text-slate-600 mt-1.5 leading-relaxed"></p>
        <div class="mt-5">
            <button id="verify-alert-ok-btn" onclick="closeVerifyAlertModal()" class="w-full py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                OK
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[id^="qr-"]').forEach(function (el) {
            var url = el.getAttribute('data-url');
            if (url) {
                new QRCode(el, {
                    text: url,
                    width: 80,
                    height: 80,
                    colorDark: '#0f2a1f',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        });
    });

    let verifyModalReloadOnClose = false;

    function showVerifyAlertModal(title, message, type = 'info', shouldReload = false) {
        const modal = document.getElementById('verify-alert-modal');
        const titleEl = document.getElementById('verify-alert-title');
        const msgEl = document.getElementById('verify-alert-message');
        const iconContainer = document.getElementById('verify-alert-icon-container');
        const iconEl = document.getElementById('verify-alert-icon');

        titleEl.innerText = title;
        msgEl.innerText = message;
        verifyModalReloadOnClose = shouldReload;

        if (type === 'success') {
            iconContainer.className = 'w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold mx-auto mb-3';
            iconEl.className = 'fa-solid fa-circle-check';
        } else if (type === 'warning' || type === 'failed') {
            iconContainer.className = 'w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold mx-auto mb-3';
            iconEl.className = 'fa-solid fa-triangle-exclamation';
        } else {
            iconContainer.className = 'w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold mx-auto mb-3';
            iconEl.className = 'fa-solid fa-circle-info';
        }

        modal.classList.remove('hidden');
    }

    function closeVerifyAlertModal() {
        document.getElementById('verify-alert-modal').classList.add('hidden');
        if (verifyModalReloadOnClose) {
            window.location.reload();
        }
    }

    function checkPaymentStatus(appId, btnElement) {
        if (btnElement) {
            btnElement.disabled = true;
            btnElement.innerHTML = '<i class="fa-solid fa-hourglass-half mr-1"></i> Verifying...';
        }

        fetch('/payment/check-status/' + encodeURIComponent(appId), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showVerifyAlertModal('Payment Verified', data.message, 'success', true);
            } else if (data.status === 'failed') {
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = '<i class="fa-solid fa-magnifying-glass mr-1"></i> Verify';
                }
                showVerifyAlertModal('Payment Notice', data.message, 'failed', false);
            } else {
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = '<i class="fa-solid fa-magnifying-glass mr-1"></i> Verify';
                }
                showVerifyAlertModal('Verification Status', data.message || 'Status check complete.', 'info', false);
            }
        })
        .catch(err => {
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerHTML = '<i class="fa-solid fa-magnifying-glass mr-1"></i> Verify';
            }
            showVerifyAlertModal('Verification Notice', 'Unable to verify payment status at this moment. Please try again.', 'warning', false);
        });
    }

    // Smart Auto-Polling (Polls pending payments every 10 sec, up to 4 mins = 24 checks max)
    @php
        $pendingAppIds = array_map(fn($id) => Crypt::encryptString($id), $applications->whereIn('status', ['payment_pending', 'waiting_for_license_fee'])->pluck('id')->toArray());
    @endphp
    @if(!empty($pendingAppIds))
    (function autoPollPendingPayments() {
        const pendingIds = @json($pendingAppIds);
        let checkCount = 0;
        const maxChecks = 24; // 24 * 10s = 240 seconds = 4 minutes max limit

        const pollInterval = setInterval(() => {
            checkCount++;
            if (checkCount > maxChecks) {
                clearInterval(pollInterval);
                console.log('Payment auto-polling stopped after 4 minutes max limit.');
                return;
            }

            pendingIds.forEach(id => {
                fetch('/payment/check-status/' + id, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        clearInterval(pollInterval);
                        window.location.reload();
                    }
                })
                .catch(err => {});
            });
        }, 10000);
    })();
    @endif
</script>

<style>
@keyframes payLicensePulse {
    0%, 100% {
        background-color: #047857; /* Deep Emerald Green Main Color */
        box-shadow: 0 0 0 0 rgba(4, 120, 87, 0.4);
    }
    50% {
        background-color: #d97706; /* Vibrant Amber Gold Blinking Color */
        box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.25);
    }
}
.animate-pay-license {
    animation: payLicensePulse 1.8s infinite ease-in-out;
}
</style>
@endsection
