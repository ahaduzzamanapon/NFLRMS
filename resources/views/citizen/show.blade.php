@extends('layouts.app')

@section('title', 'Application Tracking')

@section('content')
<div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-5 my-2">

    <!-- Left Column: Details & Logs -->
    <div class="md:col-span-8 space-y-4">

        <!-- Application Summary Card -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <div>
                    <h3 class="text-xs font-black uppercase text-gov-green tracking-wider font-outfit">Application File Summary</h3>
                    <p class="text-[9px] text-slate-400 font-semibold mt-0.5">Tracking Code: <span class="font-bold text-slate-800">{{ $application->application_number }}</span></p>
                </div>
                <a href="{{ route('citizen.dashboard') }}" class="text-[10px] font-bold text-slate-555 hover:text-slate-700">&larr; Back</a>
            </div>

            <div class="grid grid-cols-2 gap-4 text-[11px] mb-4">
                <div>
                    <span class="text-slate-500 block">Applicant Profile:</span>
                    <span class="font-bold text-slate-900">{{ $application->user->name }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Registration Type:</span>
                    <span class="font-bold text-slate-900 capitalize">{{ $application->applicant_type }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Application Type:</span>
                    <span class="font-bold text-slate-900 capitalize">{{ $application->type }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Jurisdiction:</span>
                    <span class="font-bold text-slate-900">{{ $application->district->name ?? 'N/A' }} Office</span>
                </div>
            </div>

            <!-- Firearm Card -->
            <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 text-[11px] grid grid-cols-3 gap-2">
                <div>
                    <span class="text-slate-500 block">Weapon Type:</span>
                    <span class="font-bold text-slate-900">{{ $application->firearm_details['weapon_type'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Bore Spec:</span>
                    <span class="font-bold text-slate-900">{{ $application->firearm_details['bore'] ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Purpose:</span>
                    <span class="font-bold text-slate-900">{{ $application->firearm_details['purpose'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Tracking Timeline -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <h3 class="text-xs font-black uppercase text-gov-green tracking-wider font-outfit border-b border-slate-100 pb-2 mb-4">
                🔄 Workflow Processing Timeline
            </h3>

            <div class="relative pl-5 border-l-2 border-slate-200 ml-2.5 space-y-4">
                @foreach($application->logs as $log)
                    <div class="relative">
                        <!-- Bullet point icon -->
                        <span class="absolute -left-[27px] top-1 w-3.5 h-3.5 rounded-full border-2 bg-white
                            @if(str_contains($log->action, 'approved')) border-emerald-500 bg-emerald-50
                            @elseif(str_contains($log->action, 'reject')) border-rose-500 bg-rose-50
                            @else border-gov-green bg-emerald-50 @endif">
                        </span>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-1">
                            <span class="font-bold text-slate-900 capitalize">{{ str_replace('_', ' ', $log->action) }}</span>
                            <span class="text-[9px] text-slate-400 font-semibold">{{ $log->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        <p class="text-[10px] text-slate-800 mt-1 leading-normal">{{ $log->remarks }}</p>
                        @if($log->actor)
                            <div class="text-[9px] text-slate-400 font-bold uppercase mt-1">Processed By: {{ $log->actor->roleLabel() }} ({{ $log->actor->name }})</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Right Column: Status & Security Clearance -->
    <div class="md:col-span-4 space-y-4">

        <!-- Status Panel -->
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm text-center">
            <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Current File Status</h3>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider inline-block
                @if($application->status === 'payment_pending') bg-amber-500/10 text-amber-600 border border-amber-500/20
                @elseif($application->status === 'waiting_for_license_fee') bg-indigo-500/10 text-indigo-600 border border-indigo-500/20
                @elseif(str_contains($application->status, 'approved')) bg-emerald-500/10 text-emerald-600 border border-emerald-500/20
                @elseif(str_contains($application->status, 'reject')) bg-rose-500/10 text-rose-600 border border-rose-500/20
                @else bg-amber-500/10 text-amber-600 border border-amber-500/20 @endif">
                {{ str_replace('_', ' ', $application->status) }}
            </span>
            <p class="text-[10px] text-slate-500 mt-2 font-semibold">Active Desk: {{ is_string($application->current_actor_role) ? ucwords(str_replace('_', ' ', $application->current_actor_role)) : (\App\Enums\Role::tryFrom($application->current_actor_role)?->label() ?? 'Applicant') }}</p>

            @if($application->status === 'payment_pending')
                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                    <p class="text-[10px] text-slate-500 font-bold uppercase">Platform Service Charge Pending</p>
                    <a href="{{ route('payment.initiate', [$application->id, 'type' => 'service_fee']) }}" class="w-full block py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-black shadow-sm transition-colors">
                        💳 Pay Platform Fee (PayStation)
                    </a>
                    <button onclick="checkPaymentStatus('{{ $application->id }}', this)" class="w-full block py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold border border-slate-300 transition-colors">
                        🔍 Verify Payment Status
                    </button>
                </div>
            @elseif($application->status === 'waiting_for_license_fee')
                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                    <p class="text-[10px] text-slate-500 font-bold uppercase block">Approved &bull; Waiting for License Fee</p>
                    <p class="text-base font-black text-slate-800">৳{{ number_format($application->license_fee_amount ?? 0) }}</p>
                    <a href="{{ route('payment.initiate', [$application->id, 'type' => 'license_fee']) }}" class="w-full block py-2 bg-gov-green hover:bg-gov-light text-white rounded-lg text-xs font-black shadow-sm transition-colors animate-pulse">
                        💳 Pay License Fee (PayStation)
                    </a>
                    <button onclick="checkPaymentStatus('{{ $application->id }}', this)" class="w-full block py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold border border-slate-300 transition-colors">
                        🔍 Verify Payment Status
                    </button>
                </div>
            @endif
        </div>

        <!-- Stored Payment Details Breakdown -->
        @if($application->payment_details || $application->service_fee_paid || $application->license_fee_paid)
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-black uppercase text-gov-green tracking-wider font-outfit border-b border-slate-100 pb-2 mb-3">
                    💳 Payment Transaction Records
                </h3>
                <div class="space-y-3 text-[11px]">
                    <!-- Platform Fee Record -->
                    <div class="p-2.5 rounded bg-slate-50 border border-slate-200/60 space-y-1">
                        <div class="flex justify-between items-center font-bold">
                            <span class="text-slate-700">Platform Service Charge</span>
                            @if($application->service_fee_paid)
                                <span class="text-[9px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-extrabold uppercase">Paid</span>
                            @else
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-extrabold uppercase">Pending</span>
                            @endif
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Amount:</span>
                            <span class="font-bold text-slate-800">৳{{ number_format($application->service_fee_amount ?? 850) }}</span>
                        </div>
                        @if(isset($application->payment_details['service_fee_trx_id']))
                            <div class="flex justify-between text-slate-500">
                                <span>Trx ID:</span>
                                <span class="font-mono font-bold text-slate-800">{{ $application->payment_details['service_fee_trx_id'] }}</span>
                            </div>
                        @endif
                        @if(isset($application->payment_details['service_fee_date']))
                            <div class="flex justify-between text-slate-400 text-[9px]">
                                <span>Paid Date:</span>
                                <span>{{ $application->payment_details['service_fee_date'] }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- License Fee Record -->
                    <div class="p-2.5 rounded bg-slate-50 border border-slate-200/60 space-y-1">
                        <div class="flex justify-between items-center font-bold">
                            <span class="text-slate-700">Statutory License Fee</span>
                            @if($application->license_fee_paid)
                                <span class="text-[9px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-extrabold uppercase">Paid</span>
                            @elseif($application->license_fee_amount)
                                <span class="text-[9px] px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 font-extrabold uppercase">Awaiting Payment</span>
                            @else
                                <span class="text-[9px] px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-extrabold uppercase">Not Due Yet</span>
                            @endif
                        </div>
                        @if($application->license_fee_amount)
                            <div class="flex justify-between text-slate-500">
                                <span>Amount:</span>
                                <span class="font-bold text-slate-800">৳{{ number_format($application->license_fee_amount) }}</span>
                            </div>
                        @endif
                        @if(isset($application->payment_details['license_fee_trx_id']))
                            <div class="flex justify-between text-slate-500">
                                <span>Trx ID:</span>
                                <span class="font-mono font-bold text-slate-800">{{ $application->payment_details['license_fee_trx_id'] }}</span>
                            </div>
                        @endif
                        @if(isset($application->payment_details['license_fee_date']))
                            <div class="flex justify-between text-slate-400 text-[9px]">
                                <span>Paid Date:</span>
                                <span>{{ $application->payment_details['license_fee_date'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Security Clearances -->
        @if($application->vettings->isNotEmpty())
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
                <h3 class="text-xs font-black uppercase text-gov-green tracking-wider font-outfit border-b border-slate-100 pb-2 mb-3">
                    🛡️ Agency Vetting Clearances
                </h3>
                <div class="space-y-2.5">
                    @foreach($application->vettings as $v)
                        <div class="flex justify-between items-center text-[11px] p-2 rounded bg-slate-50 border border-slate-200/50">
                            <span class="font-bold uppercase tracking-wider">{{ $v->agency }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wide
                                @if($v->status === 'cleared') bg-emerald-500/15 text-emerald-600
                                @elseif($v->status === 'flagged') bg-rose-500/15 text-rose-600
                                @else bg-amber-500/15 text-amber-600 @endif">
                                {{ $v->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</div>
@endsection

@section('scripts')
<script>
    function checkPaymentStatus(appId, btnElement) {
        if (btnElement) {
            btnElement.disabled = true;
            btnElement.innerText = '⏳ Verifying with PayStation...';
        }

        fetch('/payment/check-status/' + appId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Success: ' + data.message);
                window.location.reload();
            } else if (data.status === 'failed') {
                alert('Payment Notice: ' + data.message);
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerText = '🔍 Verify Payment Status';
                }
            } else {
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerText = '🔍 Verify Payment Status';
                }
                alert(data.message || 'Status check complete.');
            }
        })
        .catch(err => {
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerText = '🔍 Verify Payment Status';
            }
        });
    }

    @if(in_array($application->status, ['payment_pending', 'waiting_for_license_fee']))
    (function autoPollPayment() {
        const appId = '{{ $application->id }}';
        let checkCount = 0;
        const maxChecks = 24; // 24 * 10s = 240 seconds = 4 minutes max limit

        const pollInterval = setInterval(() => {
            checkCount++;
            if (checkCount > maxChecks) {
                clearInterval(pollInterval);
                console.log('Payment auto-polling stopped after 4 minutes max limit.');
                return;
            }

            fetch('/payment/check-status/' + appId, {
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
        }, 10000);
    })();
    @endif
</script>
@endsection
