@extends('layouts.app')
@section('title', 'Fee & Fine Configuration')

@section('content')
<div class="max-w-5xl space-y-6">

    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-black font-serif text-slate-900">Administration &amp; Configuration</h2>
            <p class="text-xs text-slate-500 mt-1">Fees, fines, quotas &amp; SLAs · BRS §5.12 · FR-ADM-02</p>
        </div>
        <button form="fee-form" type="submit"
                class="px-4 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-2 shadow-sm">
            💾 Save Configuration
        </button>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-700">✓ {{ session('success') }}</div>
    @endif

    <form id="fee-form" action="{{ route('admin.fee_config.save') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Statutory License Fees -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <div class="text-sm font-bold text-slate-900">Statutory License Fees (BDT)</div>
                    <div class="text-[10px] text-gov-green font-semibold">Government revenue — Table 8.1</div>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    @php $feeFields = [
                        'fee_pistol_new'      => 'Pistol/Revolver — New',
                        'fee_pistol_renewal'  => 'Pistol/Revolver — Renewal',
                        'fee_longgun_new'     => 'Shotgun/Rifle — New',
                        'fee_longgun_renewal' => 'Shotgun/Rifle — Renewal',
                    ]; @endphp
                    @foreach($feeFields as $key => $label)
                    <div>
                        <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">{{ $label }}</label>
                        <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                               class="w-full px-3 py-2 text-xs font-bold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Platform Service Charges -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <div class="text-sm font-bold text-slate-900">Platform Service Charges (BDT)</div>
                    <div class="text-[10px] text-gov-green font-semibold">Table 8.2</div>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    @php $platformFields = [
                        'fee_platform_new'     => 'New Registration',
                        'fee_platform_renewal' => 'Annual Renewal',
                        'fee_platform_late'    => 'Late Add-on',
                    ]; @endphp
                    @foreach($platformFields as $key => $label)
                    <div>
                        <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">{{ $label }}</label>
                        <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                               class="w-full px-3 py-2 text-xs font-bold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Late-Fine Tiers -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <div class="text-sm font-bold text-slate-900">Late-Fine Tiers (BDT)</div>
                    <div class="text-[10px] text-gov-green font-semibold">Statutory late-fine framework — Table 8.3</div>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    @php $fineFields = [
                        'fine_t1_pistol'  => 'Tier 1 (31–90d) · Pistol',
                        'fine_t1_longgun' => 'Tier 1 · Long Gun',
                        'fine_t2_pistol'  => 'Tier 2 (91–180d) · Pistol',
                        'fine_t2_longgun' => 'Tier 2 · Long Gun',
                        'fine_t3_pistol'  => 'Tier 3 (180d+) · Pistol',
                        'fine_t3_longgun' => 'Tier 3 · Long Gun',
                    ]; @endphp
                    @foreach($fineFields as $key => $label)
                    <div>
                        <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">{{ $label }}</label>
                        <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                               class="w-full px-3 py-2 text-xs font-bold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- SLA Timers -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <div class="text-sm font-bold text-slate-900">SLA Timers (business days)</div>
                    <div class="text-[10px] text-gov-green font-semibold">FR-VET-04 &amp; MoHA workflow</div>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    @php $slaFields = [
                        'sla_vetting'   => 'Vetting (each agency)',
                        'sla_moha'      => 'MoHA (per tier)',
                        'sla_committee' => 'Committee Review',
                    ]; @endphp
                    @foreach($slaFields as $key => $label)
                    <div>
                        <label class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block mb-1.5">{{ $label }}</label>
                        <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                               class="w-full px-3 py-2 text-xs font-bold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
