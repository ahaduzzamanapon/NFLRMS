@extends('layouts.app')
@section('title', 'Case Detail')

@section('content')
@php
    $role = auth()->user()->role->value;
    $backRoute = match(true) {
        $role === 'dc_front_desk'       => route('front_desk.dashboard'),
        $role === 'dc_jm_branch'        => route('jm_branch.dashboard'),
        $role === 'district_commissioner' => route('dc.dashboard'),
        in_array($role, ['moha_desk','joint_secretary','senior_secretary','national_screening_committee']) => route('moha.dashboard'),
        default => url()->previous(),
    };
    $actionRoute = match(true) {
        $role === 'dc_front_desk'       => route('front_desk.action', $application->id),
        $role === 'dc_jm_branch'        => route('jm_branch.action', $application->id),
        $role === 'district_commissioner' => route('dc.action', $application->id),
        in_array($role, ['moha_desk','joint_secretary','senior_secretary','national_screening_committee']) => route('moha.action', $application->id),
        default => '#',
    };
    $actions = match(true) {
        $role === 'dc_front_desk'       => ['forward' => 'Accept & Screen', 'reject' => 'Reject'],
        $role === 'dc_jm_branch'        => ['trigger_vetting' => 'Trigger Vetting', 'forward_dc' => 'Forward to DC', 'reject' => 'Reject'],
        $role === 'district_commissioner' => ['approve' => 'Approve & Issue License', 'forward_moha' => 'Refer to MoHA', 'reject' => 'Reject'],
        in_array($role, ['moha_desk','joint_secretary','national_screening_committee']) => ['forward' => 'Forward Up', 'reject' => 'Reject'],
        $role === 'senior_secretary'    => ['approve' => 'Final Approve & Issue', 'reject' => 'Reject'],
        default => [],
    };

    // Application tracker pipeline
    $pipeline = [
        'submitted' => ['label' => 'Submitted', 'icon' => '📝'],
        'received' => ['label' => 'Received', 'icon' => '📥'],
        'pending_vetting' => ['label' => 'Vetting', 'icon' => '🛡️'],
        'recommended' => ['label' => 'Recommended', 'icon' => '✅'],
        'approved' => ['label' => 'Approved', 'icon' => '🏛️'],
        'license_issued' => ['label' => 'Issued', 'icon' => '📜'],
    ];

    $status = $application->status;
    $isRejected = str_contains($status, 'rejected');

    $statusMap = [
        'submitted' => 0,
        'received' => 1,
        'pending_vetting' => 2,
        'vetted_cleared' => 2,
        'vetted_flagged' => 2,
        'recommended' => 3,
        'referred_moha' => 3,
        'moha_processing' => 3,
        'pending_screening' => 3,
        'screened' => 3,
        'waiting_for_license_fee' => 4,
        'approved' => 4,
        'license_issued' => 5,
    ];
    $currentStepIndex = $statusMap[$status] ?? 0;

    // Document data
    $userUploadedDocs = $application->documents;
    $hasUploadedDocs = !empty($userUploadedDocs) && is_array($userUploadedDocs) && count($userUploadedDocs) > 0;

    $standardDocList = [
        'nid' => ['name' => 'National ID Card Copy', 'icon' => '🆔', 'keys' => ['nid', 'nid_copy', 'nid_card'], 'default_file' => 'nid_card_copy.pdf', 'size' => '1.2 MB'],
        'birth_cert' => ['name' => 'Birth Certificate', 'icon' => '👶', 'keys' => ['birth_cert', 'birth_certificate'], 'default_file' => 'birth_cert.pdf', 'size' => '950 KB'],
        'edu_cert' => ['name' => 'Educational Certificate', 'icon' => '🎓', 'keys' => ['edu_cert', 'edu', 'educational_cert'], 'default_file' => 'educational_cert.pdf', 'size' => '1.1 MB'],
        'tin' => ['name' => 'TIN / Tax Return', 'icon' => '🧾', 'keys' => ['tin', 'tin_certificate', 'tax_yr1', 'tax_yr2', 'tax_yr3', 'tax_return'], 'default_file' => 'tin_return_ack.pdf', 'size' => '850 KB'],
        'affidavit' => ['name' => 'Notarized Affidavit', 'icon' => '📜', 'keys' => ['affidavit', 'affidavit_copy'], 'default_file' => 'notarized_affidavit.pdf', 'size' => '1.8 MB'],
        'nationality_cert' => ['name' => 'Nationality Certificate', 'icon' => '🇧🇩', 'keys' => ['nationality_cert', 'nationality'], 'default_file' => 'nationality_certificate.pdf', 'size' => '720 KB'],
        'photo' => ['name' => 'Passport-size Photo', 'icon' => '📸', 'keys' => ['photo', 'passport_photo', 'profile_photo'], 'default_file' => 'passport_photo.jpg', 'size' => '650 KB'],
        'firing_report' => ['name' => 'Firing Range Report', 'icon' => '🎯', 'keys' => ['firing_report', 'firing_cert'], 'default_file' => 'firing_range_report.pdf', 'size' => '1.3 MB'],
        'medical' => ['name' => 'Medical Fitness', 'icon' => '🏥', 'keys' => ['medical', 'medical_cert', 'fitness_cert'], 'default_file' => 'medical_fitness_civil_surgeon.pdf', 'size' => '1.4 MB'],
        'police_clearance' => ['name' => 'Police Clearance', 'icon' => '👮', 'keys' => ['police_clearance', 'police'], 'default_file' => 'police_clearance.pdf', 'size' => '1.5 MB'],
        'bank' => ['name' => 'Bank Solvency', 'icon' => '🏦', 'keys' => ['bank', 'bank_solvency'], 'default_file' => 'bank_solvency.pdf', 'size' => '2.1 MB'],
        'safe' => ['name' => 'Safe Storage Photo', 'icon' => '🔐', 'keys' => ['safe', 'safe_photo'], 'default_file' => 'gun_safe_photo.jpg', 'size' => '3.4 MB'],
    ];

    if ($application->applicant_type === 'dealer') {
        $standardDocList['trade'] = ['name' => 'Trade License & Warehouse', 'icon' => '🏪', 'keys' => ['trade', 'trade_cert', 'trade_license'], 'default_file' => 'trade_license_warehouse.pdf', 'size' => '4.2 MB'];
    }

    $matchedUploadedKeys = [];
    $uploadedCount = 0;
    $docItems = [];

    foreach ($standardDocList as $specKey => $spec) {
        $uploadedItem = null;
        $foundKey = null;

        if ($hasUploadedDocs) {
            foreach ($spec['keys'] as $searchKey) {
                if (isset($userUploadedDocs[$searchKey])) {
                    $uploadedItem = $userUploadedDocs[$searchKey];
                    $foundKey = $searchKey;
                    $matchedUploadedKeys[] = $searchKey;
                    break;
                }
            }
        }

        $isUploaded = !empty($uploadedItem);
        if ($isUploaded) $uploadedCount++;
        $fileName = $isUploaded ? ($uploadedItem['file'] ?? $uploadedItem['name'] ?? $spec['default_file']) : 'File Not Found';
        $fileSize = $isUploaded ? ($uploadedItem['size'] ?? '1.5 MB') : 'N/A';

        $docItems[] = [
            'name' => $spec['name'],
            'icon' => $spec['icon'],
            'is_uploaded' => $isUploaded,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'key' => $foundKey ?? $specKey,
        ];
    }

    if ($hasUploadedDocs) {
        foreach ($userUploadedDocs as $uploadedKey => $item) {
            if (!in_array($uploadedKey, $matchedUploadedKeys) && is_array($item)) {
                $fileName = $item['file'] ?? $item['name'] ?? 'Attached Document';
                $fileSize = $item['size'] ?? '1.0 MB';
                $displayName = $item['name'] ?? ucfirst(str_replace('_', ' ', $uploadedKey));
                $uploadedCount++;

                $docItems[] = [
                    'name' => $displayName,
                    'icon' => '📄',
                    'is_uploaded' => true,
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                    'key' => $uploadedKey,
                ];
            }
        }
    }
@endphp

<div class="max-w-7xl space-y-4">

    <!-- Back to queue (top-left, outside header) -->
    <a href="{{ $backRoute }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-sm text-[10px] font-semibold text-slate-500 hover:text-gov-green hover:border-gov-green/40 transition-all">
        <span>←</span><span>Back to queue</span>
    </a>

    <!-- ===== COMPACT HEADER ===== -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 flex flex-wrap items-start justify-between gap-3">
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gov-green/10 border border-gov-green/20 flex items-center justify-center text-xl flex-shrink-0">📋</div>
                <div>
                    <h2 class="text-base font-bold font-serif text-slate-900 leading-tight">Case {{ $application->application_number }}</h2>
                    <p class="text-[10px] text-slate-500 font-medium">
                        {{ ucfirst(str_replace('_', ' ', $application->type)) }} &bull;
                        {{ $application->firearm_details['weapon_type'] ?? 'N/A' }} &bull;
                        {{ $application->user->name }}
                    </p>
                </div>
            </div>
            <div class="flex flex-col items-end gap-1.5">
                <span class="px-2.5 py-1 rounded-full text-[9px] font-semibold uppercase border
                    @if(in_array($status, ['approved','license_issued','vetted_cleared'])) border-emerald-500/30 bg-emerald-50 text-emerald-700
                    @elseif($isRejected || $status === 'vetted_flagged') border-rose-500/30 bg-rose-50 text-rose-700
                    @else border-amber-500/30 bg-amber-50 text-amber-700 @endif">
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </span>
                <span class="text-[9px] text-slate-400 font-normal">Updated {{ $application->updated_at->diffForHumans() }}</span>
            </div>
        </div>

        <!-- Compact Tracker (Horizontal Stepper) -->
        <div class="px-5 pb-3">
            <div class="flex items-center">
                @foreach($pipeline as $key => $step)
                    @php
                        $stepIdx = $loop->index;
                        $isDone = $stepIdx < $currentStepIndex;
                        $isCurrent = $stepIdx === $currentStepIndex;
                        $isRejectedStep = $isRejected && $stepIdx === $currentStepIndex;
                    @endphp
                    <div class="flex-1 relative">
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all
                                @if($isDone) bg-emerald-500 border-emerald-500 text-white
                                @elseif($isCurrent) @if($isRejectedStep) bg-rose-500 border-rose-500 text-white @else bg-gov-green border-gov-green text-white ring-4 ring-gov-green/20 @endif
                                @else bg-white border-slate-200 text-slate-300 @endif">
                                @if($isDone) ✓ @else {{ $step['icon'] }} @endif
                            </div>
                            <span class="mt-1 text-[8px] font-semibold uppercase tracking-wider
                                @if($isDone) text-emerald-600
                                @elseif($isCurrent) @if($isRejectedStep) text-rose-600 @else text-gov-green @endif
                                @else text-slate-400 @endif">
                                {{ $step['label'] }}
                            </span>
                        </div>
                        @if(!$loop->last)
                            <div class="absolute top-[14px] left-[calc(50%+14px)] right-[calc(-50%+14px)] h-0.5
                                @if($stepIdx < $currentStepIndex) bg-emerald-400 @else bg-slate-200 @endif"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ===== TAB NAVIGATION ===== -->
    <div class="flex flex-wrap items-center gap-1.5 bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
        <button type="button" data-tab="overview" onclick="switchDetailTab('overview')"
                class="detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[10px] font-semibold uppercase transition-all focus:outline-none bg-gov-green text-white shadow-sm">
            <span>👤</span><span>Overview</span>
        </button>
        <button type="button" data-tab="documents" onclick="switchDetailTab('documents')"
                class="detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[10px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span>📎</span><span>Documents</span>
            <span class="px-1.5 py-0.5 rounded-full text-[8px] font-semibold {{ $uploadedCount === count($standardDocList) ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $uploadedCount }}/{{ count($standardDocList) }}</span>
        </button>
        <button type="button" data-tab="timeline" onclick="switchDetailTab('timeline')"
                class="detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[10px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span>🕐</span><span>Activity Log</span>
            <span class="px-1.5 py-0.5 rounded-full text-[8px] font-semibold bg-slate-100 text-slate-500">{{ $application->logs->count() }}</span>
        </button>
        @if($application->vettings->count())
        <button type="button" data-tab="vetting" onclick="switchDetailTab('vetting')"
                class="detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[10px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span>🛡️</span><span>Vetting</span>
            <span class="px-1.5 py-0.5 rounded-full text-[8px] font-semibold bg-slate-100 text-slate-500">{{ $application->vettings->count() }}</span>
        </button>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- ===== LEFT: TAB CONTENT (internal scroll, no page scroll) ===== -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- TAB: OVERVIEW -->
                <div class="detail-panel" id="panel-overview">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">👤 Applicant Summary</span>
                        <button type="button" onclick="openApplicantModal()"
                                class="px-3 py-1.5 rounded-lg bg-gov-green hover:bg-gov-light text-white text-[10px] font-semibold transition-colors shadow-sm flex items-center space-x-1">
                            <span>🔍</span><span>View Full Details</span>
                        </button>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center space-x-4">
                            <div class="w-14 h-14 rounded-full bg-gov-green/10 border-2 border-gov-green/30 flex items-center justify-center text-gov-green font-bold text-xl flex-shrink-0">
                                {{ strtoupper(substr($application->user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-900 text-sm truncate">{{ $application->user->name }}
                                    @if($application->user->name_bn) <span class="text-[11px] text-slate-500 font-normal">({{ $application->user->name_bn }})</span> @endif
                                </div>
                                <div class="text-[10px] text-slate-500 font-normal mt-0.5">
                                    NID: {{ $application->applicant_details['nid'] ?? $application->user->nid ?? 'N/A' }}
                                </div>
                                <div class="flex items-center space-x-3 mt-1.5 text-[10px] text-slate-500 font-normal">
                                    <span class="flex items-center space-x-1"><span>📱</span><span>{{ $application->user->phone ?? 'N/A' }}</span></span>
                                    <span class="flex items-center space-x-1"><span>📍</span><span>{{ $application->user->district->name ?? $application->district->name ?? 'N/A' }}</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-slate-100">
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Weapon</span>
                                <span class="text-xs font-semibold text-slate-800">{{ $application->firearm_details['weapon_type'] ?? 'N/A' }}</span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Bore</span>
                                <span class="text-xs font-semibold text-slate-800">{{ $application->firearm_details['bore'] ?? 'N/A' }}</span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Occupation</span>
                                <span class="text-xs font-semibold text-slate-800 truncate">{{ $application->applicant_details['occupation'] ?? $application->user->occupation ?? 'N/A' }}</span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Income</span>
                                <span class="text-xs font-semibold text-slate-800">
                                    @php $income = $application->applicant_details['annual_income'] ?? $application->user->annual_income ?? null; @endphp
                                    {{ $income ? 'BDT ' . number_format($income) : 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <!-- Routing info inside overview -->
                        <div class="mt-4 p-3 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200">
                            <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-wider block mb-1">🧭 Routing Rule</span>
                            <p class="text-xs text-slate-600 font-normal">
                                @if(in_array($application->firearm_details['weapon_type'] ?? '', ['Pistol','Revolver']))
                                    <span class="text-rose-600 font-semibold">Handgun case</span> → MoHA approval required.
                                @else
                                    <span class="text-emerald-700 font-semibold">Long-gun case</span> → DC direct approval.
                                @endif
                            </p>
                        </div>

                        <!-- Quick stats -->
                        <div class="grid grid-cols-4 gap-3 mt-4">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="text-lg font-bold text-gov-green block">{{ $application->logs->count() }}</span>
                                <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider">Events</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="text-lg font-bold text-amber-600 block">{{ $application->vettings->count() }}</span>
                                <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider">Vettings</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="text-lg font-bold text-emerald-600 block">{{ $uploadedCount }}</span>
                                <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider">Docs</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="text-lg font-bold text-blue-600 block">{{ $application->created_at->format('d M') }}</span>
                                <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider">Filed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: DOCUMENTS -->
                <div class="detail-panel hidden" id="panel-documents">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">📎 Attached Documents</span>
                        @if($hasUploadedDocs)
                            <span class="text-[9px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">✓ Uploaded & Verified</span>
                        @else
                            <span class="text-[9px] font-semibold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full">⚠️ Pending Upload</span>
                        @endif
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach($docItems as $doc)
                            <div class="p-3 rounded-xl border transition-all group
                                {{ $doc['is_uploaded'] ? 'border-slate-200 bg-slate-50/70 hover:bg-white hover:shadow-sm' : 'border-rose-200/70 bg-rose-50/40 hover:bg-rose-50/70' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <span class="text-xl flex-shrink-0">{{ $doc['icon'] }}</span>
                                        <div class="min-w-0">
                                            <span class="font-semibold text-slate-800 block text-[11px] leading-tight truncate">{{ $doc['name'] }}</span>
                                            @if(!$doc['is_uploaded'])
                                                <span class="text-[9px] text-rose-600 font-semibold">Not uploaded</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1.5 flex-shrink-0">
                                        @if($doc['is_uploaded'])
                                            <span class="w-2 h-2 rounded-full bg-emerald-500" title="Attached"></span>
                                            <button type="button" onclick="openOfficeDocumentViewer('{{ addslashes($doc['name']) }}', '{{ $doc['file_name'] }}', '{{ $doc['file_size'] }}', true, '{{ $doc['key'] }}')"
                                                    class="px-2 py-1 rounded-lg bg-gov-green hover:bg-gov-light text-white text-[10px] font-semibold transition-all shadow-sm">
                                                👁️ View
                                            </button>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-rose-400" title="Missing"></span>
                                            <button type="button" onclick="openOfficeDocumentViewer('{{ addslashes($doc['name']) }}', 'No file uploaded', '0 KB', false, '{{ $doc['key'] }}')"
                                                    class="px-2 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-[10px] font-semibold transition-all">
                                                👁️ Inspect
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Document progress bar -->
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <div class="flex items-center justify-between text-[10px] font-semibold text-slate-500 mb-1.5">
                                <span>Document Completion</span>
                                <span class="text-gov-green">{{ $uploadedCount }}/{{ count($standardDocList) }} uploaded</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gov-green rounded-full transition-all duration-500"
                                     style="width: {{ count($standardDocList) > 0 ? round(($uploadedCount / count($standardDocList)) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: TIMELINE -->
                <div class="detail-panel hidden" id="panel-timeline">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">🕐 Case Activity Log</span>
                        <span class="text-[9px] font-normal text-slate-400">{{ $application->logs->count() }} events</span>
                    </div>
                    <div class="p-5">
                        @forelse($application->logs as $log)
                        <div class="relative flex space-x-4 pb-6 last:pb-0">
                            @if(!$loop->last)
                                <div class="absolute left-[7px] top-5 bottom-0 w-px bg-slate-200"></div>
                            @endif
                            <div class="relative flex-shrink-0">
                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center
                                    @if($loop->first) border-gov-green bg-gov-green
                                    @elseif(str_contains($log->action, 'reject')) border-rose-400 bg-rose-100
                                    @else border-amber-400 bg-amber-100 @endif">
                                    <div class="w-1.5 h-1.5 rounded-full
                                        @if($loop->first) bg-white
                                        @elseif(str_contains($log->action, 'reject')) bg-rose-500
                                        @else bg-amber-500 @endif"></div>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[9px] font-semibold uppercase tracking-wider
                                        @if($loop->first) text-gov-green
                                        @elseif(str_contains($log->action, 'reject')) text-rose-600
                                        @else text-amber-600 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                    <span class="text-[9px] text-slate-400 font-normal flex-shrink-0">{{ $log->created_at->format('d M Y · h:i A') }}</span>
                                </div>
                                <p class="text-xs text-slate-700 font-medium leading-relaxed mt-1">{{ $log->remarks }}</p>
                                @if($log->actor)
                                <div class="flex items-center space-x-1.5 mt-1.5">
                                    <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[8px] font-semibold text-slate-600 flex-shrink-0">
                                        {{ strtoupper(substr($log->actor->name, 0, 1)) }}
                                    </span>
                                    <span class="text-[9px] text-slate-500 font-normal">by {{ $log->actor->name }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 space-y-2">
                            <span class="text-3xl block">🕐</span>
                            <p class="text-xs text-slate-400 font-normal">No activity entries yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- TAB: VETTING -->
                @if($application->vettings->count())
                <div class="detail-panel hidden" id="panel-vetting">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                        <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">🛡️ Security Vetting Reports</span>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($application->vettings as $v)
                            <div class="rounded-xl border transition-all overflow-hidden
                                @if($v->status === 'cleared') border-emerald-200 bg-emerald-50/50
                                @elseif($v->status === 'flagged') border-rose-200 bg-rose-50/50
                                @else border-amber-200 bg-amber-50/50 @endif">
                                <div class="flex items-center justify-between px-3.5 py-3">
                                    <div class="flex items-center space-x-2.5">
                                        <span class="text-lg">
                                            @if($v->status === 'cleared') ✅
                                            @elseif($v->status === 'flagged') ⚠️
                                            @else ⏳ @endif
                                        </span>
                                        <div>
                                            <span class="text-xs font-semibold text-slate-800 uppercase">{{ $v->agency }}</span>
                                            @if($v->vetted_at)
                                                <span class="text-[9px] text-slate-400 font-normal block">{{ $v->vetted_at->format('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-[9px] font-semibold uppercase px-2.5 py-1 rounded-full
                                        @if($v->status === 'cleared') bg-emerald-100 text-emerald-700
                                        @elseif($v->status === 'flagged') bg-rose-100 text-rose-700
                                        @else bg-amber-100 text-amber-700 @endif">
                                        {{ $v->status }}
                                    </span>
                                </div>
                                @if($v->remarks)
                                <div class="px-3.5 py-2.5 border-t bg-white/60
                                    @if($v->status === 'cleared') border-emerald-100
                                    @elseif($v->status === 'flagged') border-rose-100
                                    @else border-amber-100 @endif">
                                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block mb-1">📝 Remarks</span>
                                    <p class="text-[11px] text-slate-700 font-normal leading-relaxed">{{ $v->remarks }}</p>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- ===== RIGHT: OFFICER ACTIONS (always visible, sticky) ===== -->
        <div class="space-y-4">
            @if(!empty($actions) && $actionRoute !== '#')
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden lg:sticky lg:top-4">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">⚡ Officer Actions</span>
                </div>
                @if ($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-normal space-y-1">
                        <span class="block text-sm font-bold font-serif">⚠️ Please resolve the following errors:</span>
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="p-5 space-y-3">
                    <form action="{{ $actionRoute }}" method="POST" class="space-y-3">
                        @csrf

                        @if($customComments->isNotEmpty())
                        <div>
                            <label for="custom_comment_select" class="block text-[9px] font-semibold uppercase text-slate-700 tracking-wider mb-1.5">💬 Quick Fill</label>
                            <select id="custom_comment_select" onchange="fillRemarksFromCustomComment(this)"
                                    class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                                <option value="">— Select a saved comment —</option>
                                @foreach($customComments as $cc)
                                    <option value="{{ $cc->comment }}">{{ $cc->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div>
                            <label for="remarks" class="block text-[9px] font-semibold uppercase text-slate-700 tracking-wider mb-1.5">📝 Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" placeholder="Remarks (mandatory)"
                                      class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white resize-none"></textarea>
                        </div>

                        @foreach($actions as $value => $label)
                        <button type="submit" name="action" value="{{ $value }}"
                                class="w-full py-2.5 rounded-lg text-xs font-bold transition-colors shadow-sm
                                {{ in_array($value, ['approve','forward','trigger_vetting','forward_dc','forward_moha']) ? 'bg-gov-green hover:bg-gov-light text-white shadow-sm' : 'border border-rose-300 text-rose-600 hover:bg-rose-50' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- ===== APPLICANT FULL DETAILS MODAL ===== -->
<div id="applicantModal" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl overflow-hidden border border-slate-200 max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-gov-green text-white flex items-center justify-between flex-shrink-0">
            <div class="flex items-center space-x-2.5">
                <span class="text-xl">👤</span>
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider">Applicant Full Details</h3>
                    <p class="text-[10px] text-white/80 font-normal">Case {{ $application->application_number }}</p>
                </div>
            </div>
            <button type="button" onclick="closeApplicantModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-sm flex items-center justify-center transition-colors">
                ✕
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto flex-grow">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <!-- Identity -->
                <div class="sm:col-span-2">
                    <span class="text-[9px] font-semibold uppercase text-gov-green tracking-wider block mb-2 border-b border-slate-100 pb-1.5">🪪 Identity</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Full Name</span>
                    <span class="font-semibold text-slate-900">{{ $application->user->name }}</span>
                    @if($application->user->name_bn) <span class="text-[11px] text-slate-500 font-normal block">({{ $application->user->name_bn }})</span> @endif
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">National ID (NID)</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['nid'] ?? $application->user->nid ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Date of Birth</span>
                    <span class="font-semibold text-slate-900">
                        @php $dobVal = $application->applicant_details['dob'] ?? $application->user->dob ?? null; @endphp
                        {{ $dobVal ? (\Illuminate\Support\Carbon::parse($dobVal)->format('d M Y')) : 'N/A' }}
                    </span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Phone</span>
                    <span class="font-semibold text-slate-900">{{ $application->user->phone ?? $application->applicant_details['phone'] ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Marital Status</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['marital_status'] ?? $application->user->marital_status ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Nationality</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['nationality'] ?? $application->user->nationality ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Religion</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['religion'] ?? $application->user->religion ?? 'N/A' }}</span>
                </div>

                <!-- Family -->
                <div class="sm:col-span-2 mt-2">
                    <span class="text-[9px] font-semibold uppercase text-gov-green tracking-wider block mb-2 border-b border-slate-100 pb-1.5">👨‍👩‍👧 Family</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Father's Name</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['father_name'] ?? $application->user->father_name ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Mother's Name</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['mother_name'] ?? $application->user->mother_name ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Spouse Name</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['spouse_name'] ?? $application->user->spouse_name ?? 'N/A' }}</span>
                </div>

                <!-- Address -->
                <div class="sm:col-span-2 mt-2">
                    <span class="text-[9px] font-semibold uppercase text-gov-green tracking-wider block mb-2 border-b border-slate-100 pb-1.5">📍 Address</span>
                </div>
                <div class="sm:col-span-2 p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Present Address</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['present_address'] ?? $application->user->present_address ?? 'N/A' }}</span>
                </div>
                <div class="sm:col-span-2 p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Permanent Address</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['permanent_address'] ?? $application->user->permanent_address ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">District</span>
                    <span class="font-semibold text-slate-900">{{ $application->user->district->name ?? $application->district->name ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Upazila / Thana</span>
                    <span class="font-semibold text-slate-900">{{ $application->user->upazila->name ?? $application->upazila->name ?? 'N/A' }}</span>
                </div>

                <!-- Occupation & Income -->
                <div class="sm:col-span-2 mt-2">
                    <span class="text-[9px] font-semibold uppercase text-gov-green tracking-wider block mb-2 border-b border-slate-100 pb-1.5">💼 Occupation & Income</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Occupation</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['occupation'] ?? $application->user->occupation ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Employer Address</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['employer_address'] ?? $application->user->employer_address ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Annual Income</span>
                    <span class="font-semibold text-slate-900">
                        @php $income2 = $application->applicant_details['annual_income'] ?? $application->user->annual_income ?? null; @endphp
                        {{ $income2 ? 'BDT ' . number_format($income2) : 'N/A' }}
                    </span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">TIN Number</span>
                    <span class="font-semibold text-slate-900">{{ $application->applicant_details['tin_number'] ?? $application->user->tin_number ?? 'N/A' }}</span>
                </div>

                <!-- Firearm Details -->
                <div class="sm:col-span-2 mt-2">
                    <span class="text-[9px] font-semibold uppercase text-gov-green tracking-wider block mb-2 border-b border-slate-100 pb-1.5">🔫 Firearm Details</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Weapon Type</span>
                    <span class="font-semibold text-slate-900">{{ $application->firearm_details['weapon_type'] ?? 'N/A' }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Bore / Calibre</span>
                    <span class="font-semibold text-slate-900">{{ $application->firearm_details['bore'] ?? 'N/A' }}</span>
                </div>
                <div class="sm:col-span-2 p-3 bg-slate-50 rounded-lg border border-slate-100">
                    <span class="text-[8px] font-semibold uppercase text-slate-400 tracking-wider block">Purpose</span>
                    <span class="font-semibold text-slate-900">{{ $application->firearm_details['purpose'] ?? 'N/A' }}</span>
                </div>
                <div class="sm:col-span-2 p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                    <span class="text-[8px] font-semibold uppercase text-emerald-600 tracking-wider block">Licensed Arms Dealer / Sourcing Store</span>
                    <span class="font-semibold text-emerald-800 text-xs">{{ $application->firearm_details['dealer_name'] ?? 'M/S Metropolitan Arms Store (Govt. Reg #AD-1029)' }}</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between flex-shrink-0">
            <span class="text-[10px] text-slate-400 font-medium">NFLRMS Official Case File</span>
            <button type="button" onclick="closeApplicantModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Interactive Document Preview Modal -->
<div id="officeDocumentViewerModal" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200">
        <!-- Modal Header -->
        <div class="px-5 py-4 bg-gov-green text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <span class="text-xl">📄</span>
                <div>
                    <h3 id="officeModalDocTitle" class="text-xs font-bold uppercase tracking-wider">Document Title</h3>
                    <p id="officeModalDocMeta" class="text-[10px] text-white/80 font-normal">filename.pdf &bull; 1.5 MB</p>
                </div>
            </div>
            <button type="button" onclick="closeOfficeDocumentViewer()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-sm flex items-center justify-center transition-colors">
                ✕
            </button>
        </div>

        <!-- Modal Document Viewer Content Area -->
        <div class="p-6 bg-slate-100 max-h-[70vh] overflow-y-auto">
            <div class="bg-white p-6 rounded-xl border border-slate-300 shadow-inner space-y-4 font-sans text-xs">
                <div class="space-y-3 py-2">
                    <div class="bg-slate-50 p-3 rounded border border-slate-200 text-[11px] space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Document Type:</span>
                            <span id="officeDocTypeLabel" class="font-semibold text-slate-900">National Identity Document</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Case Tracking Code:</span>
                            <span class="font-mono font-semibold text-slate-800">{{ $application->application_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Verification Hash:</span>
                            <span class="font-mono text-[9px] text-slate-600">SHA256: 8f92a10b4c892e104f81a7b...</span>
                        </div>
                    </div>

                    <!-- Visual Rendered Document Container -->
                    <div id="officePreviewDocumentBody" class="space-y-3">
                        <!-- Dynamic rendered document content injected via JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <span class="text-[10px] text-slate-400 font-medium">NFLRMS Official Inspection Vault</span>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="closeOfficeDocumentViewer()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition-colors">
                    Close
                </button>
                <button type="button" onclick="triggerOfficeDocDownload()" class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg shadow-sm transition-colors flex items-center space-x-1">
                    <span>⬇ Download PDF</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // ===== TAB SWITCHING =====
    function switchDetailTab(tabName) {
        // Hide all panels
        document.querySelectorAll('.detail-panel').forEach(p => p.classList.add('hidden'));
        // Show selected panel
        const panel = document.getElementById(`panel-${tabName}`);
        if (panel) panel.classList.remove('hidden');

        // Update tab button styles
        document.querySelectorAll('.detail-tab').forEach(btn => {
            const isActive = btn.dataset.tab === tabName;
            btn.className = isActive
                ? 'detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[10px] font-semibold uppercase transition-all focus:outline-none bg-gov-green text-white shadow-sm'
                : 'detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[10px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50';
        });
    }

    // Fill the remarks textarea with the selected custom comment
    function fillRemarksFromCustomComment(selectEl) {
        const remarks = document.getElementById('remarks');
        if (remarks && selectEl.value) {
            remarks.value = selectEl.value;
        }
    }

    // Applicant Full Details Modal
    function openApplicantModal() {
        document.getElementById('applicantModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeApplicantModal() {
        document.getElementById('applicantModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal on backdrop click
    document.getElementById('applicantModal')?.addEventListener('click', function (e) {
        if (e.target === this) closeApplicantModal();
    });

    // Close modals on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeApplicantModal();
            closeOfficeDocumentViewer();
        }
    });

    // ===== DOCUMENT VIEWER =====
    let currentOfficeDocTitle = '';
    let currentOfficeDocKey = '';
    let isCurrentOfficeDocUploaded = true;
    const currentOfficeAppNo = '{{ $application->application_number }}';

    function openOfficeDocumentViewer(title, filename, size, isUploaded = true, key = '') {
        currentOfficeDocTitle = title;
        currentOfficeDocKey = key;
        isCurrentOfficeDocUploaded = isUploaded;

        document.getElementById('officeModalDocTitle').innerText = title;
        document.getElementById('officeModalDocMeta').innerText = filename + (size !== '0 KB' ? ' • ' + size : '');
        document.getElementById('officeDocTypeLabel').innerText = title;

        const previewContainer = document.getElementById('officePreviewDocumentBody');

        if (!isUploaded) {
            previewContainer.innerHTML = `
                <div class="bg-rose-50 border-2 border-dashed border-rose-300 rounded-xl p-8 text-center text-rose-800 space-y-3 my-2">
                    <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-3xl font-bold shadow-sm">
                        ⚠️
                    </div>
                    <h5 class="font-bold text-slate-900 text-base font-serif">File Not Found</h5>
                    <p class="text-xs text-rose-700 max-w-md mx-auto leading-relaxed font-normal">
                        No statutory document file was uploaded by the applicant for <strong>${title}</strong>.
                    </p>
                    <div class="pt-2 flex justify-center space-x-2">
                        <span class="px-3 py-1 bg-rose-200 text-rose-900 text-[10px] font-bold rounded uppercase">Status: Not Uploaded</span>
                    </div>
                </div>
            `;
        } else {
            const streamUrl = '{{ route("document.download") }}?key=' + encodeURIComponent(key) + '&title=' + encodeURIComponent(title) + '&app=' + encodeURIComponent(currentOfficeAppNo) + '&inline=1';
            const isImage = filename.match(/\.(jpg|jpeg|png|webp)$/i);

            let realViewerHTML = '';
            if (isImage) {
                realViewerHTML = `
                    <div class="p-3 bg-slate-100 rounded-xl border border-slate-200 text-center mb-3">
                        <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider block mb-2">📸 Uploaded Attachment Image Preview</span>
                        <img src="${streamUrl}" alt="${title}" class="max-h-96 mx-auto rounded-lg shadow-md object-contain border border-slate-300">
                    </div>
                `;
            } else {
                realViewerHTML = `
                    <div class="mb-3 rounded-xl border border-slate-200 overflow-hidden shadow-inner bg-slate-950">
                        <div class="bg-slate-900 px-3 py-1.5 flex justify-between items-center text-white text-[10px] border-b border-slate-800">
                            <span class="font-semibold text-emerald-400">📄 Attached File: ${filename}</span>
                            <a href="${streamUrl}" target="_blank" class="text-amber-300 hover:underline font-semibold">Open Fullscreen ↗</a>
                        </div>
                        <iframe src="${streamUrl}" class="w-full h-80 bg-white"></iframe>
                    </div>
                `;
            }
            previewContainer.innerHTML = realViewerHTML;
        }

        document.getElementById('officeDocumentViewerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeOfficeDocumentViewer() {
        document.getElementById('officeDocumentViewerModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function triggerOfficeDocDownload() {
        if (!isCurrentOfficeDocUploaded) {
            alert('File Not Found: No document uploaded for ' + currentOfficeDocTitle + ' by applicant.');
            return;
        }
        const downloadUrl = '{{ route("document.download") }}?key=' + encodeURIComponent(currentOfficeDocKey) + '&title=' + encodeURIComponent(currentOfficeDocTitle) + '&app=' + encodeURIComponent(currentOfficeAppNo);
        window.location.href = downloadUrl;
    }
</script>
@endsection
