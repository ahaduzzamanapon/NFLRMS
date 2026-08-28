@extends('layouts.app')
@section('title', 'Fee & Fine Configuration')

@section('content')
@php
    // Fee config summary stats
    $totalStatutory = ($settings['fee_pistol_new'] ?? 0) + ($settings['fee_pistol_renewal'] ?? 0) + ($settings['fee_longgun_new'] ?? 0) + ($settings['fee_longgun_renewal'] ?? 0);
    $totalPlatform = ($settings['fee_platform_new'] ?? 0) + ($settings['fee_platform_renewal'] ?? 0) + ($settings['fee_platform_late'] ?? 0);
    $totalFines = ($settings['fine_t1_pistol'] ?? 0) + ($settings['fine_t1_longgun'] ?? 0) + ($settings['fine_t2_pistol'] ?? 0) + ($settings['fine_t2_longgun'] ?? 0) + ($settings['fine_t3_pistol'] ?? 0) + ($settings['fine_t3_longgun'] ?? 0);
    $totalSla = ($settings['sla_vetting'] ?? 0) + ($settings['sla_moha'] ?? 0) + ($settings['sla_committee'] ?? 0);

    // Field definitions (used across tabs)
    $feeFields = [
        'fee_pistol_new'      => 'Pistol/Revolver — New',
        'fee_pistol_renewal'  => 'Pistol/Revolver — Renewal',
        'fee_longgun_new'     => 'Shotgun/Rifle — New',
        'fee_longgun_renewal' => 'Shotgun/Rifle — Renewal',
    ];
    $platformFields = [
        'fee_platform_new'     => 'New Registration',
        'fee_platform_renewal' => 'Annual Renewal',
        'fee_platform_late'    => 'Late Add-on',
    ];
    $fineFields = [
        'fine_t1_pistol'  => 'Tier 1 (31–90d) · Pistol',
        'fine_t1_longgun' => 'Tier 1 · Long Gun',
        'fine_t2_pistol'  => 'Tier 2 (91–180d) · Pistol',
        'fine_t2_longgun' => 'Tier 2 · Long Gun',
        'fine_t3_pistol'  => 'Tier 3 (180d+) · Pistol',
        'fine_t3_longgun' => 'Tier 3 · Long Gun',
    ];
    $slaFields = [
        'sla_vetting'   => ['label' => 'Vetting (each agency)', 'icon' => '<i class="fa-solid fa-shield-halved text-gov-green"></i>', 'desc' => 'Police / SB / NSI / DGFI'],
        'sla_moha'      => ['label' => 'MoHA (per tier)', 'icon' => '<i class="fa-solid fa-building text-gov-green"></i>', 'desc' => 'Desk → Joint Secretary → Minister'],
        'sla_committee' => ['label' => 'Committee Review', 'icon' => '<i class="fa-solid fa-users text-gov-green"></i>', 'desc' => 'National Screening Committee'],
    ];

    // Dealer fee fields grouped by license class
    $dealerClasses = [
        'A' => ['label' => 'Class A — Retail Sale', 'keys' => ['dealer_fee_class_a_new' => 'New Dealer License', 'dealer_fee_class_a_renewal' => 'Renewal Dealer License']],
        'B' => ['label' => 'Class B — Wholesale', 'keys' => ['dealer_fee_class_b_new' => 'New Dealer License', 'dealer_fee_class_b_renewal' => 'Renewal Dealer License']],
        'C' => ['label' => 'Class C — Import / Export', 'keys' => ['dealer_fee_class_c_new' => 'New Dealer License', 'dealer_fee_class_c_renewal' => 'Renewal Dealer License']],
    ];

    $dealerPlatformFields = [
        'dealer_platform_new'     => 'New Dealer Registration',
        'dealer_platform_renewal' => 'Dealer Annual Renewal',
    ];
@endphp

<div class="max-w-full space-y-4">

    <!-- Back to dashboard (top-left, outside header) -->
    <a href="{{ route('admin.dashboard') }}"
       class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-sm text-[11px] font-extrabold text-slate-500 hover:text-gov-green hover:border-gov-green/40 transition-all">
        <span><i class="fa-solid fa-arrow-left"></i></span><span>Back to Dashboard</span>
    </a>

    <!-- Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900">Fee & Fine Configuration</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                Fees, fines, quotas & SLAs &bull; BRS §5.12 &bull; FR-ADM-02
            </p>
        </div>
        <button form="fee-form" type="submit"
                class="px-4 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-2 shadow-sm self-start sm:self-auto">
            <i class="fa-solid fa-floppy-disk mr-1"></i> Save Configuration
        </button>
    </div>

    {{-- @if(session('success'))
    <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-700 flex items-center gap-2">
        <span><i class="fa-solid fa-check mr-1"></i></span><span>{{ session('success') }}</span>
    </div>
    @endif --}}

    <!-- ===== TAB NAVIGATION ===== -->
    <div class="flex flex-wrap items-center gap-1.5 bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
        <button type="button" data-tab="citizen" onclick="switchConfigTab('citizen')"
                class="config-tab flex items-center space-x-1.5 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-lg text-[11px] font-extrabold uppercase transition-all focus:outline-none bg-gov-green text-white shadow-sm">
            <span><i class="fa-solid fa-user"></i></span><span>Citizen Statutory Fees</span>
        </button>
        <button type="button" data-tab="dealer" onclick="switchConfigTab('dealer')"
                class="config-tab flex items-center space-x-1.5 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-lg text-[11px] font-extrabold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span><i class="fa-solid fa-store"></i></span><span>Dealer Statutory Fees</span>
        </button>
        <button type="button" data-tab="fines" onclick="switchConfigTab('fines')"
                class="config-tab flex items-center space-x-1.5 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-lg text-[11px] font-extrabold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span><i class="fa-solid fa-triangle-exclamation"></i></span><span>Late Fines</span>
        </button>
        <button type="button" data-tab="sla" onclick="switchConfigTab('sla')"
                class="config-tab flex items-center space-x-1.5 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-lg text-[11px] font-extrabold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span><i class="fa-solid fa-hourglass-half"></i></span><span>SLA Timers</span>
        </button>
        <button type="button" data-tab="summary" onclick="switchConfigTab('summary')"
                class="config-tab flex items-center space-x-1.5 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-lg text-[11px] font-extrabold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span><i class="fa-solid fa-chart-pie"></i></span><span>Summary</span>
        </button>
    </div>

    <form id="fee-form" action="{{ route('admin.fee_config.save') }}" method="POST">
        @csrf
        <input type="hidden" name="active_tab" id="active_tab" value="{{ request('tab', 'citizen') }}">

        <!-- ===== TAB: CITIZEN STATUTORY FEES ===== -->
        <div class="config-panel" id="panel-citizen">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Statutory License Fees -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-slate-900">Statutory License Fees</div>
                            <div class="text-[11px] text-gov-green font-semibold">Government revenue — Table 8.1</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">BDT</span>
                    </div>
                    <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($feeFields as $key => $label)
                        <div>
                            <label class="text-[10px] font-bold uppercase text-slate-900 tracking-widest block mb-1.5">{{ $label }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400">৳</span>
                                <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                                       class="w-full pl-7 pr-3 py-2 text-xs font-semibold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Platform Service Charges -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-slate-900">Platform Service Charges</div>
                            <div class="text-[11px] text-gov-green font-semibold">Table 8.2</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-700">BDT</span>
                    </div>
                    <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($platformFields as $key => $label)
                        <div>
                            <label class="text-[10px] font-bold uppercase text-slate-900 tracking-widest block mb-1.5">{{ $label }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400">৳</span>
                                <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                                       class="w-full pl-7 pr-3 py-2 text-xs font-semibold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: DEALER STATUTORY FEES ===== -->
        <div class="config-panel hidden" id="panel-dealer">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Dealer Statutory License Fees (all classes shown) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-slate-900">Statutory License Fees</div>
                            <div class="text-[11px] text-gov-green font-semibold">Arms Dealing License (Form K) — Table 8.4</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">BDT</span>
                    </div>
                    <div class="p-4 sm:p-5 space-y-4">
                        @foreach($dealerClasses as $classKey => $classInfo)
                        <!-- Per-class fee input group -->
                        <div class="rounded-xl border border-slate-200 overflow-hidden">
                            <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-600">{{ $classInfo['label'] }}</span>
                            </div>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                @foreach($classInfo['keys'] as $fieldKey => $fieldLabel)
                                <div>
                                    <label class="text-[10px] font-bold uppercase text-slate-900 tracking-widest block mb-1.5">{{ $fieldLabel }}</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400">৳</span>
                                        <input type="number" name="{{ $fieldKey }}" value="{{ $settings[$fieldKey] ?? '' }}"
                                               class="w-full pl-7 pr-3 py-2 text-xs font-semibold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Dealer Platform Service Charges -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-slate-900">Platform Service Charges</div>
                            <div class="text-[11px] text-gov-green font-semibold">Dealer Portal — Table 8.5</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-700">BDT</span>
                    </div>
                    <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($dealerPlatformFields as $key => $label)
                        <div>
                            <label class="text-[10px] font-bold uppercase text-slate-900 tracking-widest block mb-1.5">{{ $label }}</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400">৳</span>
                                <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                                       class="w-full pl-7 pr-3 py-2 text-xs font-semibold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: LATE FINES ===== -->
        <div class="config-panel hidden" id="panel-fines">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-bold text-slate-900">Late-Fine Tiers</div>
                        <div class="text-[11px] text-gov-green font-semibold">Statutory late-fine framework — Table 8.3</div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-700">BDT</span>
                </div>
                <div class="p-4 sm:p-5">
                    <!-- Tier headers -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-4 mb-3">
                        <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-center">
                            <span class="text-[10px] font-black uppercase text-amber-700 tracking-wider block">Tier 1</span>
                            <span class="text-[9px] text-amber-600 font-semibold">31–90 days late</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-orange-50 border border-orange-200 text-center">
                            <span class="text-[10px] font-black uppercase text-orange-700 tracking-wider block">Tier 2</span>
                            <span class="text-[9px] text-orange-600 font-semibold">91–180 days late</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-center">
                            <span class="text-[10px] font-black uppercase text-rose-700 tracking-wider block">Tier 3</span>
                            <span class="text-[9px] text-rose-600 font-semibold">180+ days late</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php
                            $fineGroups = [
                                'Tier 1' => ['fine_t1_pistol' => 'Pistol/Revolver', 'fine_t1_longgun' => 'Shotgun/Rifle'],
                                'Tier 2' => ['fine_t2_pistol' => 'Pistol/Revolver', 'fine_t2_longgun' => 'Shotgun/Rifle'],
                                'Tier 3' => ['fine_t3_pistol' => 'Pistol/Revolver', 'fine_t3_longgun' => 'Shotgun/Rifle'],
                            ];
                        @endphp
                        @foreach($fineGroups as $tier => $fields)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 block">{{ $tier }} Fines</span>
                            @foreach($fields as $key => $label)
                            <div>
                                <label class="text-[10px] font-bold uppercase text-slate-900 tracking-widest block mb-1.5">{{ $label }}</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400">৳</span>
                                    <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                                           class="w-full pl-7 pr-3 py-2 text-xs font-semibold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: SLA TIMERS ===== -->
        <div class="config-panel hidden" id="panel-sla">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-bold text-slate-900">SLA Timers</div>
                        <div class="text-[11px] text-gov-green font-semibold">FR-VET-04 & MoHA workflow</div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-100 text-blue-700">Business Days</span>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($slaFields as $key => $field)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-xl">{{ $field['icon'] }}</span>
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 block">{{ $field['label'] }}</span>
                                    <span class="text-[9px] text-slate-400 font-semibold">{{ $field['desc'] }}</span>
                                </div>
                            </div>
                            <div class="relative">
                                <input type="number" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                                       class="w-full pl-3 pr-10 py-2 text-xs font-semibold text-slate-900 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-gov-green/30 focus:border-gov-green bg-white transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold text-slate-400">days</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TAB: SUMMARY ===== -->
        <div class="config-panel hidden" id="panel-summary">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Citizen Statutory Fees Summary -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                        <span class="text-[11px] font-extrabold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-user text-gov-green mr-1"></i> Citizen Statutory License Fees</span>
                    </div>
                    <div class="p-5 space-y-2 text-xs">
                        @foreach($feeFields as $key => $label)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="font-semibold text-slate-700">{{ $label }}</span>
                            <span class="font-black text-slate-900">৳{{ number_format($settings[$key] ?? 0) }}</span>
                        </div>
                        @endforeach
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-emerald-50 border border-emerald-200">
                            <span class="font-bold text-emerald-700">Total Citizen Statutory</span>
                            <span class="font-black text-emerald-700">৳{{ number_format($totalStatutory) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Dealer Statutory Fees Summary (only classes with data) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                        <span class="text-[11px] font-extrabold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-store text-gov-green mr-1"></i> Dealer Statutory License Fees</span>
                    </div>
                    <div class="p-5 space-y-2 text-xs">
                        @php
                            $hasDealerClassData = false;
                        @endphp
                        @foreach($dealerClasses as $classKey => $classInfo)
                            @php
                                $classHasData = collect($classInfo['keys'])->keys()->contains(fn($k) => isset($settings[$k]) && $settings[$k] !== '' && $settings[$k] !== null);
                                if (!$classHasData) continue;
                                $hasDealerClassData = true;
                            @endphp
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="font-bold text-slate-800 block text-[11px] uppercase tracking-wider mb-1.5">{{ $classInfo['label'] }}</span>
                                @foreach($classInfo['keys'] as $fieldKey => $fieldLabel)
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="font-normal text-slate-600">{{ $fieldLabel }}</span>
                                    <span class="font-bold text-slate-900">৳{{ number_format($settings[$fieldKey] ?? 0) }}</span>
                                </div>
                                @endforeach
                            </div>
                        @endforeach
                        @if(!$hasDealerClassData)
                            <div class="p-4 text-center text-slate-400 font-semibold">
                                No dealer fees configured yet. Fill in the Dealer Statutory Fees tab.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Platform + Fines Summary -->
                <div class="space-y-4 lg:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                                <span class="text-[11px] font-extrabold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-credit-card text-gov-green mr-1"></i> Citizen Platform Charges</span>
                            </div>
                            <div class="p-5 space-y-2 text-xs">
                                @foreach($platformFields as $key => $label)
                                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                    <span class="font-semibold text-slate-700">{{ $label }}</span>
                                    <span class="font-black text-slate-900">৳{{ number_format($settings[$key] ?? 0) }}</span>
                                </div>
                                @endforeach
                                <div class="flex items-center justify-between p-2.5 rounded-lg bg-amber-50 border border-amber-200">
                                    <span class="font-bold text-amber-700">Total Citizen Platform</span>
                                    <span class="font-black text-amber-700">৳{{ number_format($totalPlatform) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                                <span class="text-[11px] font-extrabold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-credit-card text-gov-green mr-1"></i> Dealer Platform Charges</span>
                            </div>
                            <div class="p-5 space-y-2 text-xs">
                                @foreach($dealerPlatformFields as $key => $label)
                                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                    <span class="font-semibold text-slate-700">{{ $label }}</span>
                                    <span class="font-black text-slate-900">৳{{ number_format($settings[$key] ?? 0) }}</span>
                                </div>
                                @endforeach
                                <div class="flex items-center justify-between p-2.5 rounded-lg bg-emerald-50 border border-emerald-200">
                                    <span class="font-bold text-emerald-700">Total Dealer Platform</span>
                                    <span class="font-black text-emerald-700">৳{{ number_format(($settings['dealer_platform_new'] ?? 0) + ($settings['dealer_platform_renewal'] ?? 0)) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                                <span class="text-[11px] font-extrabold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1"></i> Late-Fine Tiers</span>
                            </div>
                            <div class="p-5 space-y-2 text-xs">
                                @foreach($fineFields as $key => $label)
                                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                    <span class="font-semibold text-slate-700">{{ $label }}</span>
                                    <span class="font-black text-slate-900">৳{{ number_format($settings[$key] ?? 0) }}</span>
                                </div>
                                @endforeach
                                <div class="flex items-center justify-between p-2.5 rounded-lg bg-rose-50 border border-rose-200">
                                    <span class="font-bold text-rose-700">Total Fines</span>
                                    <span class="font-black text-rose-700">৳{{ number_format($totalFines) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    // ===== TAB SWITCHING =====
    function switchConfigTab(tabName) {
        // Persist the active tab in the hidden input (sent with form on save)
        document.getElementById('active_tab').value = tabName;

        document.querySelectorAll('.config-panel').forEach(p => p.classList.add('hidden'));
        const panel = document.getElementById(`panel-${tabName}`);
        if (panel) panel.classList.remove('hidden');

        document.querySelectorAll('.config-tab').forEach(btn => {
            const isActive = btn.dataset.tab === tabName;
            btn.className = isActive
                ? 'config-tab flex items-center space-x-1.5 px-4 py-2.5 rounded-lg text-[11px] font-extrabold uppercase transition-all focus:outline-none bg-gov-green text-white shadow-sm'
                : 'config-tab flex items-center space-x-1.5 px-4 py-2.5 rounded-lg text-[11px] font-extrabold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50';
        });
    }

    // ===== RESTORE SAVED TAB ON PAGE LOAD =====
    document.addEventListener('DOMContentLoaded', function () {
        const savedTab = document.getElementById('active_tab')?.value || 'citizen';
        // Switch to the saved tab (also persists to hidden input + updates button styles)
        switchConfigTab(savedTab);
    });
</script>
@endsection
