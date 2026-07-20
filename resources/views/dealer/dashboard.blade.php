@extends('layouts.app')
@section('title', 'Dealer Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Top Profile & Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black font-serif text-slate-900 leading-tight">
                Dealer Portal
            </h2>
            <p class="text-xs text-slate-500 mt-1 font-semibold">
                {{ auth()->user()->name }} &bull; {{ auth()->user()->district->name ?? 'N/A' }} &bull;
                <span class="text-gov-green font-black">Dealer Applicant</span>
            </p>
        </div>
        <div class="flex-shrink-0">
            <a href="{{ route('dealer.apply') }}" class="px-4 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors flex items-center space-x-1.5 shadow-sm">
                <span>+</span>
                <span>New Dealer Application (Form K)</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-24">
            <h4 class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Dealer Licences</h4>
            <p class="text-3xl font-black font-serif text-emerald-600 mt-1">{{ $licenses->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-24">
            <h4 class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">In Progress</h4>
            <p class="text-3xl font-black font-serif text-blue-600 mt-1">{{ $applications->whereNotIn('status', ['approved', 'rejected', 'suspended'])->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-24">
            <h4 class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Needs Attention</h4>
            <p class="text-3xl font-black font-serif text-amber-500 mt-1">{{ $applications->where('status', 'suspended')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between h-24">
            <h4 class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Total Applications</h4>
            <p class="text-3xl font-black font-serif text-slate-900 mt-1">{{ $applications->count() }}</p>
        </div>
    </div>

    <!-- My Dealer Licence Section -->
    <div class="space-y-3">
        <h3 class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">
            My Dealer Licence
        </h3>

        @if($licenses->isEmpty())
            <div class="max-w-xl p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md">
                <p class="text-xs text-slate-400 font-semibold text-center py-4">
                    No active dealer license found. <a href="{{ route('dealer.apply') }}" class="text-gov-green font-black hover:underline">Apply for a new dealer license (Form K) →</a>
                </p>
            </div>
        @else
            @foreach($licenses as $l)
            <div class="max-w-xl p-5 rounded-2xl bg-white border border-slate-200/80 shadow-md flex flex-col sm:flex-row justify-between gap-6">
                <div class="flex-grow space-y-4">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">🏪</span>
                            <div>
                                <h4 class="text-[9px] font-black uppercase text-slate-500 leading-none">
                                    Government of Bangladesh &bull; MoHA
                                </h4>
                                <h3 class="text-xs font-bold text-slate-900 mt-1 leading-none">
                                    Dealer Dealing Licence
                                </h3>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase
                            {{ $l->status === 'active' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/25' : 'bg-rose-500/10 text-rose-600 border border-rose-500/25' }}">
                            {{ ucfirst($l->status) }}
                        </span>
                    </div>

                    <!-- Fields -->
                    <div class="grid grid-cols-2 gap-4 text-[10px]">
                        <div>
                            <span class="text-slate-400 block font-bold uppercase tracking-wider text-[8px]">Firm Name</span>
                            <span class="font-extrabold text-slate-900">{{ auth()->user()->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-bold uppercase tracking-wider text-[8px]">Licence Class</span>
                            <span class="font-extrabold text-slate-900">Class A Dealer</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-bold uppercase tracking-wider text-[8px]">Licence No.</span>
                            <span class="font-extrabold text-slate-900 uppercase font-mono">{{ $l->license_number }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-bold uppercase tracking-wider text-[8px]">District</span>
                            <span class="font-extrabold text-slate-900">{{ auth()->user()->district->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-bold uppercase tracking-wider text-[8px]">Issued</span>
                            <span class="font-extrabold text-slate-900">{{ $l->issue_date->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-bold uppercase tracking-wider text-[8px]">Expires</span>
                            <span class="font-extrabold text-slate-900 {{ $l->expiry_date->isPast() ? 'text-rose-600' : '' }}">
                                {{ $l->expiry_date->format('d M Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-2.5 flex items-center justify-between">
                        <a href="{{ route('dealer.renew') }}"
                           class="text-[9px] font-black text-gov-green hover:underline">🔄 Renew License</a>
                        <a href="{{ route('verify', ['license_number' => $l->license_number]) }}"
                           class="text-[9px] font-black text-gov-green hover:underline">⬇ Download / Verify</a>
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
                    <span class="text-[7px] text-slate-400 font-bold uppercase mt-2 leading-tight">Scan to verify<br>on NFLRMS portal</span>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- Dealer Stock Status Panel -->
    @if($licenses->isNotEmpty())
        @php
            $stocks = auth()->user()->dealerStocks()->latest()->get();
        @endphp
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 font-serif">Stock Ledger Summary</h3>
                <a href="{{ route('dealer.stock_ledger') }}" class="text-[10px] font-extrabold text-gov-green hover:underline">Manage Stock Ledger &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Firearms in Stock</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $stocks->where('item_type', 'firearm')->sum('quantity') }} items</div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Ammunition in Stock</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $stocks->where('item_type', 'ammunition')->sum('quantity') }} rds</div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Stock Anomalies</div>
                    <div class="text-2xl font-black text-gov-green mt-1">✓ Verified Clear</div>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">
                            <th class="p-3 pl-5">Item Name</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Bore / Caliber</th>
                            <th class="p-3">Quantity</th>
                            <th class="p-3 pr-5 text-right">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs divide-y divide-slate-100">
                        @forelse($stocks->take(5) as $stk)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 pl-5 font-bold text-slate-900">{{ $stk->item_name }}</td>
                            <td class="p-3 font-semibold text-slate-500 uppercase">{{ $stk->item_type }}</td>
                            <td class="p-3 font-semibold text-slate-600">{{ $stk->bore ?? 'N/A' }}</td>
                            <td class="p-3 font-bold text-slate-800">{{ number_format($stk->quantity) }}</td>
                            <td class="p-3 pr-5 text-right text-slate-400 font-semibold">{{ $stk->remarks ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 font-bold">No stock ledger entries found.</td>
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
            <h3 class="text-sm font-bold text-slate-900 font-serif">My Applications</h3>
            <button onclick="window.location.reload()" class="text-[10px] font-extrabold text-slate-400 hover:text-slate-600 flex items-center space-x-1">
                <span>🔄</span>
                <span>Refresh</span>
            </button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">
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
                            <td class="p-3 pl-5 font-bold font-mono text-slate-900">{{ $a->application_number }}</td>
                            <td class="p-3">
                                <span class="font-bold text-slate-800">
                                    @if($a->type === 'renewal')
                                        Dealing Licence Renewal
                                    @else
                                        New Dealing Licence (Form K)
                                    @endif
                                </span>
                            </td>
                            <td class="p-3 font-semibold text-slate-500">
                                {{ $a->created_at->format('d M Y') }}
                            </td>
                            <td class="p-3">
                                @php
                                    $badgeStyles = match($a->status) {
                                        'submitted' => 'bg-blue-500/10 text-blue-600 border-blue-500/20',
                                        'received' => 'bg-indigo-500/10 text-indigo-600 border-indigo-500/20',
                                        'pending_vetting' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
                                        'vetted_cleared' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
                                        'vetted_flagged' => 'bg-red-500/10 text-red-600 border-red-500/20',
                                        'approved' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
                                        'rejected' => 'bg-red-500/10 text-red-600 border-red-500/20',
                                        'suspended' => 'bg-rose-500/10 text-rose-600 border-rose-500/20',
                                        default => 'bg-slate-500/10 text-slate-600 border-slate-500/20',
                                    };
                                    $statusLabel = match($a->status) {
                                        'submitted' => 'Awaiting Verification',
                                        'received' => 'Under Review',
                                        'pending_vetting' => 'Awaiting Vetting Clearance',
                                        'vetted_cleared' => 'Vetted: Passed',
                                        'vetted_flagged' => 'Vetted: Flagged',
                                        'approved' => 'Certificate Issued',
                                        'rejected' => 'Rejected',
                                        'suspended' => 'Suspended',
                                        default => ucfirst($a->status),
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $badgeStyles }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="p-3 pr-5 text-right">
                                <a href="{{ route('citizen.show', $a->id) }}" class="text-gov-green hover:underline font-black">
                                    View &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-8 text-slate-400 font-bold">
                                No application records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
</script>
@endsection
