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
    <form id="renewal-form" method="POST" action="" class="space-y-6">
        @csrf

        <!-- Select Licence -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">Select Dealing Licence to Renew</span>
            </div>
            <div class="p-5 space-y-3">
                @foreach($licenses as $lic)
                <label class="flex items-center space-x-3 p-3.5 rounded-lg border border-slate-200 cursor-pointer hover:border-gov-green hover:bg-emerald-50/50 transition-colors">
                    <input type="radio" name="license_id" value="{{ $lic->id }}" required
                           data-action="{{ route('citizen.renew', $lic->id) }}"
                           {{ $loop->first ? 'checked' : '' }}
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
                        <input type="number" name="declared_firearms" min="0"
                               class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                               placeholder="0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mb-1">Total Ammunition Rounds</label>
                        <input type="number" name="declared_ammo" min="0"
                               class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                               placeholder="0">
                    </div>
                </div>
            </div>
        </div>

        <!-- Declaration -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <label class="flex items-start space-x-3 cursor-pointer">
                <input type="checkbox" name="declaration" required value="1"
                       class="mt-0.5 rounded border-slate-300 text-gov-green focus:ring-gov-green">
                <span class="text-xs text-slate-600 leading-relaxed font-medium">
                    I confirm that the declared stock is accurate and matches the physical inventory at my premises.
                    I understand that discrepancies may result in an anomaly audit by MoHA.
                </span>
            </label>
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
    });
</script>
@endsection
