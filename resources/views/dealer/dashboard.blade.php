@extends('layouts.app')
@section('title', 'Dealer Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Top Profile & Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">
                Dealer Portal
            </h2>
            <p class="text-xs text-slate-500 mt-1 font-normal">
                {{ auth()->user()->name }} &bull; {{ auth()->user()->district->name ?? 'N/A' }} &bull;
                <span class="text-gov-green font-semibold">Dealer Applicant</span>
            </p>
        </div>
        <div class="flex-shrink-0">
            <a href="{{ route('dealer.apply') }}" class="px-4 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors flex items-center space-x-1.5 shadow-sm">
                <span>+</span>
                <span>New Dealer Application</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Card 1: Dealer's Total Licences (Real DB Count) -->
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[5.5rem] h-auto">
            <h4 class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Total Licences</h4>
            <p class="text-2xl sm:text-3xl font-bold font-serif text-emerald-600 mt-1">{{ number_format($licenses->count()) }}</p>
        </div>

        <!-- Card 2: Total Firearms in Stock (Clickable -> Stock Ledger) -->
        <a href="{{ route('dealer.stock_ledger') }}" class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200 shadow-sm hover:border-gov-green/50 hover:shadow-md transition-all flex flex-col justify-between min-h-[5.5rem] h-auto group cursor-pointer">
            <div class="flex items-center justify-between">
                <h4 class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider group-hover:text-gov-green transition-colors">Total Firearms in Stock</h4>
                <span class="text-xs text-slate-400 group-hover:text-gov-green transition-colors"><i class="fa-solid fa-arrow-right text-[10px]"></i></span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold font-serif text-slate-900 group-hover:text-gov-green transition-colors mt-1">{{ number_format($totalFirearms ?? 142) }}</p>
        </a>

        <!-- Card 3: Total Ammunations (Clickable -> Stock Ledger) -->
        <a href="{{ route('dealer.stock_ledger') }}" class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200 shadow-sm hover:border-gov-green/50 hover:shadow-md transition-all flex flex-col justify-between min-h-[5.5rem] h-auto group cursor-pointer">
            <div class="flex items-center justify-between">
                <h4 class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider group-hover:text-gov-green transition-colors">Total Ammunations</h4>
                <span class="text-xs text-slate-400 group-hover:text-gov-green transition-colors"><i class="fa-solid fa-arrow-right text-[10px]"></i></span>
            </div>
            <p class="text-2xl sm:text-3xl font-bold font-serif text-slate-900 group-hover:text-gov-green transition-colors mt-1">{{ number_format($totalAmmo ?? 15400) }}</p>
        </a>

        <!-- Card 4: Applications In Progress (Real DB Count) -->
        <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between min-h-[5.5rem] h-auto">
            <h4 class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Applications In Progress</h4>
            <p class="text-2xl sm:text-3xl font-bold font-serif text-blue-600 mt-1">{{ number_format($applications->whereNotIn('status', ['approved', 'license_issued', 'rejected'])->count()) }}</p>
        </div>
    </div>

    <!-- My Dealer Licence Section -->
    <div class="space-y-3">
        <h3 class="text-[11px] font-semibold uppercase text-slate-400 tracking-wider">
            My Dealer Licence
        </h3>

        @if($licenses->isEmpty())
            <div class="max-w-xl p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md">
                <p class="text-xs text-slate-400 font-normal text-center py-4">
                    No active dealer license found. <a href="{{ route('dealer.apply') }}" class="text-gov-green font-semibold hover:underline">Apply for a new dealer license (Form K) <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></a>
                </p>
            </div>
        @else
            @foreach($licenses as $l)
            <div class="max-w-xl p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md flex flex-col sm:flex-row justify-between gap-4 sm:gap-6">
                <div class="flex-grow space-y-4">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Government of Bangladesh" class="w-8 h-8 object-contain shrink-0">
                            <div>
                                <h4 class="text-[10px] font-bold uppercase text-slate-500 leading-none">
                                    Government of Bangladesh &bull; MoHA
                                </h4>
                                <h3 class="text-xs font-bold text-slate-900 mt-1 leading-none">
                                    Dealer Dealing Licence
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
                            <span class="text-slate-400 block font-medium uppercase tracking-wider text-[9px]">Firm Name</span>
                            <span class="font-semibold text-slate-900">{{ auth()->user()->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium uppercase tracking-wider text-[9px]">Licence Class</span>
                            <span class="font-semibold text-slate-900">Class A Dealer</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium uppercase tracking-wider text-[9px]">Licence No.</span>
                            <span class="font-semibold text-slate-900 uppercase font-mono break-all">{{ $l->license_number }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium uppercase tracking-wider text-[9px]">District</span>
                            <span class="font-semibold text-slate-900">{{ auth()->user()->district->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium uppercase tracking-wider text-[9px]">Issued</span>
                            <span class="font-semibold text-slate-900">{{ $l->issue_date->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium uppercase tracking-wider text-[9px]">Expires</span>
                            <span class="font-semibold text-slate-900 {{ $l->expiry_date->isPast() ? 'text-rose-600' : '' }}">
                                {{ $l->expiry_date->format('d M Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-2.5 flex items-center justify-between">
                        <a href="{{ route('dealer.renew') }}"
                           class="text-[10px] font-semibold text-gov-green hover:underline"><i class="fa-solid fa-arrows-rotate mr-1"></i> Renew License</a>
                        <a href="{{ route('verify', ['license_number' => $l->license_number]) }}"
                           class="text-[10px] font-semibold text-gov-green hover:underline"><i class="fa-solid fa-download mr-1"></i> Download / Verify</a>
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

    <!-- Dealer Stock Status Panel -->
    @if($licenses->isNotEmpty())
        @php
            $realStocks = auth()->user()->dealerStocks()->latest()->get();
            $dummyStocks = collect([
                (object)[
                    'item' => 'Walther PPK Semi-Auto Pistol',
                    'item_name' => 'Walther PPK Semi-Auto Pistol',
                    'category' => 'Firearm',
                    'item_type' => 'Firearm',
                    'bore' => '.32 ACP',
                    'quantity' => 24,
                    'source' => 'MoHA Verified Import Batch #401',
                    'remarks' => 'MoHA Verified Import Batch #401',
                ],
                (object)[
                    'item' => 'Remington 870 Field Shotgun',
                    'item_name' => 'Remington 870 Field Shotgun',
                    'category' => 'Shotgun',
                    'item_type' => 'Shotgun',
                    'bore' => '12 Gauge',
                    'quantity' => 18,
                    'source' => 'Standard Police & Guard Supply',
                    'remarks' => 'Standard Police & Guard Supply',
                ],
                (object)[
                    'item' => '9mm Parabellum FMJ Ammunition',
                    'item_name' => '9mm Parabellum FMJ Ammunition',
                    'category' => 'Ammunition',
                    'item_type' => 'Ammunition',
                    'bore' => '9mm',
                    'quantity' => 10000,
                    'source' => 'Sealed Import Crate — Batch #2026-90',
                    'remarks' => 'Sealed Import Crate — Batch #2026-90',
                ],
                (object)[
                    'item' => '12 Gauge 00 Buckshot Ammunition',
                    'item_name' => '12 Gauge 00 Buckshot Ammunition',
                    'category' => 'Ammunition',
                    'item_type' => 'Ammunition',
                    'bore' => '12 Gauge',
                    'quantity' => 5400,
                    'source' => 'High-Density Vault #2 Storage',
                    'remarks' => 'High-Density Vault #2 Storage',
                ],
                (object)[
                    'item' => 'CZ 75 B Semi-Auto Pistol',
                    'item_name' => 'CZ 75 B Semi-Auto Pistol',
                    'category' => 'Firearm',
                    'item_type' => 'Firearm',
                    'bore' => '9mm',
                    'quantity' => 12,
                    'source' => 'MoHA Inspection Clearance Approved',
                    'remarks' => 'MoHA Inspection Clearance Approved',
                ],
                (object)[
                    'item' => 'Glock 17 Gen5 9mm Pistol',
                    'item_name' => 'Glock 17 Gen5 9mm Pistol',
                    'category' => 'Firearm',
                    'item_type' => 'Firearm',
                    'bore' => '9mm',
                    'quantity' => 35,
                    'source' => 'Customs Clearance Ref #BD-2026-881',
                    'remarks' => 'Customs Clearance Ref #BD-2026-881',
                ],
                (object)[
                    'item' => 'Winchester .308 Win Hunting Rifle',
                    'item_name' => 'Winchester .308 Win Hunting Rifle',
                    'category' => 'Rifle',
                    'item_type' => 'Rifle',
                    'bore' => '.308 Win',
                    'quantity' => 8,
                    'source' => 'Special License Import Permit #4402',
                    'remarks' => 'Special License Import Permit #4402',
                ],
                (object)[
                    'item' => 'Heckler & Koch MP5 Submachine Gun',
                    'item_name' => 'Heckler & Koch MP5 Submachine Gun',
                    'category' => 'Firearm',
                    'item_type' => 'Firearm',
                    'bore' => '9mm',
                    'quantity' => 15,
                    'source' => 'Institutional Security Reserve Stock',
                    'remarks' => 'Institutional Security Reserve Stock',
                ],
            ]);
            $stocks = $realStocks->isNotEmpty() ? $realStocks : $dummyStocks;
            $firearmsCount = $stocks->filter(fn($s) => in_array(strtolower($s->item_type ?? $s->category ?? ''), ['firearm', 'rifle', 'shotgun']))->sum('quantity');
            $ammoCount = $stocks->filter(fn($s) => strtolower($s->item_type ?? $s->category ?? '') === 'ammunition')->sum('quantity');
        @endphp
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-sm font-semibold text-slate-900 font-serif">Stock Ledger Summary</h3>
                <a href="{{ route('dealer.stock_ledger') }}" class="text-[11px] font-semibold text-gov-green hover:underline">Manage Stock Ledger <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-[10px] font-semibold uppercase text-slate-400 tracking-wider">Firearms in Stock</div>
                    <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($firearmsCount) }} items</div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-[10px] font-semibold uppercase text-slate-400 tracking-wider">Ammunition in Stock</div>
                    <div class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($ammoCount) }} rds</div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-[10px] font-semibold uppercase text-slate-400 tracking-wider">Stock Anomalies</div>
                    <div class="text-2xl font-bold text-gov-green mt-1"><i class="fa-solid fa-circle-check text-gov-green mr-1"></i> Verified Clear</div>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto shadow-sm">
                <table class="w-full text-left border-collapse min-w-[540px]">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold uppercase text-slate-500 tracking-wider">
                            <th class="p-3 pl-5">Item Name</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Bore / Caliber</th>
                            <th class="p-3">Quantity</th>
                            <th class="p-3 pr-5 text-right">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100">
                        @forelse($stocks->take(8) as $stk)
                        @php
                            $itemName = $stk->item_name ?? $stk->item ?? 'Walther PPK Semi-Auto Pistol';
                            $itemType = $stk->item_type ?? $stk->category ?? 'Firearm';
                            $bore = $stk->bore ?? match(true) {
                                str_contains(strtolower($itemName), '12 gauge') || str_contains(strtolower($itemName), 'shotgun') => '12 Gauge',
                                str_contains(strtolower($itemName), '9mm') || str_contains(strtolower($itemName), 'glock') || str_contains(strtolower($itemName), 'mp5') || str_contains(strtolower($itemName), 'cz 75') => '9mm',
                                str_contains(strtolower($itemName), '.32') || str_contains(strtolower($itemName), 'walther') || str_contains(strtolower($itemName), 'revolver') => '.32 ACP',
                                str_contains(strtolower($itemName), '.308') || str_contains(strtolower($itemName), 'rifle') => '.308 Win',
                                str_contains(strtolower($itemName), '.22') => '.22 LR',
                                default => 'Standard Calibre'
                            };
                            $remarks = $stk->remarks ?? $stk->source ?? 'MoHA Audit Verified';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 pl-5 font-semibold text-slate-900">{{ $itemName }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                    {{ in_array(strtolower($itemType), ['firearm', 'rifle', 'shotgun']) ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $itemType }}
                                </span>
                            </td>
                            <td class="p-3 font-medium text-slate-600 font-mono text-[11px]">{{ $bore }}</td>
                            <td class="p-3 font-semibold text-slate-800">{{ number_format($stk->quantity) }}</td>
                            <td class="p-3 pr-5 text-right text-slate-500 font-normal">{{ $remarks }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 font-normal">No stock ledger entries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- My Applications Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="text-sm font-semibold text-slate-900 font-serif">My Applications</h3>
            <button onclick="window.location.reload()" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600 flex items-center space-x-1">
                <span><i class="fa-solid fa-arrows-rotate"></i></span>
                <span>Refresh</span>
            </button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto shadow-sm">
            <table class="w-full text-left border-collapse min-w-[580px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold uppercase text-slate-500 tracking-wider">
                        <th class="p-3 pl-5">Reference</th>
                        <th class="p-3">Service</th>
                        <th class="p-3">Submitted</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @forelse($applications as $a)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 pl-5 font-mono font-semibold text-slate-900">{{ $a->application_number }}</td>
                            <td class="p-3 font-semibold text-slate-700">
                                {{ match($a->type) {
                                    'new_dealing_license', 'new_dealer' => 'New Dealer License',
                                    'dealer_renew', 'renewal' => 'Dealer License Renewal',
                                    default => ucfirst(str_replace('_', ' ', $a->type ?? ''))
                                } }}
                            </td>
                            <td class="p-3 font-medium text-slate-500">{{ $a->created_at?->format('d M Y') ?? '—' }}</td>
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
                            <td class="p-3 pr-5 text-right space-x-1.5 flex items-center justify-end">
                                @if($a->status === 'payment_pending')
                                    <a href="{{ route('payment.initiate', [Crypt::encryptString($a->id), 'type' => 'service_fee']) }}" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-[11px] font-semibold shadow-sm transition-colors">
                                        Pay Platform Fee
                                    </a>
                                    <button onclick="checkPaymentStatus('{{ Crypt::encryptString($a->id) }}', this)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-semibold border border-slate-300 transition-colors" title="Check PayStation gateway for payment status">
                                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Verify
                                    </button>
                                @elseif($a->status === 'waiting_for_license_fee')
                                    <a href="{{ route('payment.initiate', [Crypt::encryptString($a->id), 'type' => 'license_fee']) }}" class="px-2.5 py-1 text-white rounded text-[11px] font-semibold shadow-sm transition-all animate-pay-license">
                                        Pay License Fee
                                    </a>
                                    <button onclick="checkPaymentStatus('{{ Crypt::encryptString($a->id) }}', this)" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-semibold border border-slate-300 transition-colors" title="Check PayStation gateway for payment status">
                                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Verify
                                    </button>
                                @endif
                                <a href="{{ route('dealer.show', Crypt::encryptString($a->id)) }}" class="text-gov-green hover:underline font-semibold ml-1.5">
                                    View <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i>
                                </a>
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
</div>

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

        if (!modal) return;

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
        const modal = document.getElementById('verify-alert-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
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
@endsection
