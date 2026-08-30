@extends('layouts.app')

@section('title', 'Application Tracking')

@section('content')
@php
    $status = $application->status;
    $isRejected = str_contains($status, 'rejected');

    // Application tracker pipeline
    $pipeline = [
        'submitted' => ['label' => 'Submitted', 'icon' => 'fa-solid fa-file-lines'],
        'received' => ['label' => 'Received', 'icon' => 'fa-solid fa-inbox'],
        'pending_vetting' => ['label' => 'Vetting', 'icon' => 'fa-solid fa-shield-halved'],
        'recommended' => ['label' => 'Recommended', 'icon' => 'fa-solid fa-check'],
        'approved' => ['label' => 'Approved', 'icon' => 'fa-solid fa-building-columns'],
        'license_issued' => ['label' => 'Issued', 'icon' => 'fa-solid fa-scroll'],
    ];

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
        'payment_pending' => 0,
    ];
    $currentStepIndex = $statusMap[$status] ?? 0;

    // Document data
    $userUploadedDocs = $application->documents;
    $hasUploadedDocs = !empty($userUploadedDocs) && is_array($userUploadedDocs) && count($userUploadedDocs) > 0;

    // Citizen apply page document order (citizen/apply.blade.php Step 5)
    $citizenDocList = [
        'nid_copy'         => ['name' => 'National ID Copy',                    'icon' => 'fa-solid fa-id-card', 'keys' => ['nid_copy', 'nid', 'nid_card'],                          'default_file' => 'nid_copy.pdf',          'size' => '1.2 MB'],
        'tin_certificate'  => ['name' => 'TIN Certificate',                     'icon' => 'fa-solid fa-receipt', 'keys' => ['tin_certificate', 'tin'],                               'default_file' => 'tin_certificate.pdf',   'size' => '850 KB'],
        'birth_cert'       => ['name' => 'Birth Certificate',                   'icon' => 'fa-solid fa-baby', 'keys' => ['birth_cert', 'birth_certificate'],                       'default_file' => 'birth_cert.pdf',        'size' => '950 KB'],
        'edu_cert'         => ['name' => 'Educational Certificate',             'icon' => 'fa-solid fa-graduation-cap', 'keys' => ['edu_cert', 'edu', 'educational_cert'],                   'default_file' => 'educational_cert.pdf',  'size' => '1.1 MB'],
        'tax_yr1'          => ['name' => 'Income Tax Return · Year 1',          'icon' => 'fa-solid fa-chart-line', 'keys' => ['tax_yr1', 'tax_return_yr1'],                             'default_file' => 'tax_return_year1.pdf',  'size' => '1.0 MB'],
        'tax_yr2'          => ['name' => 'Income Tax Return · Year 2',          'icon' => 'fa-solid fa-chart-line', 'keys' => ['tax_yr2', 'tax_return_yr2'],                             'default_file' => 'tax_return_year2.pdf',  'size' => '1.0 MB'],
        'tax_yr3'          => ['name' => 'Income Tax Return · Year 3',          'icon' => 'fa-solid fa-chart-line', 'keys' => ['tax_yr3', 'tax_return_yr3'],                             'default_file' => 'tax_return_year3.pdf',  'size' => '1.0 MB'],
        'affidavit'        => ['name' => 'Notarized Affidavit (BDT 300 stamp)', 'icon' => 'fa-solid fa-file-contract', 'keys' => ['affidavit', 'affidavit_copy'],                           'default_file' => 'notarized_affidavit.pdf','size' => '1.8 MB'],
        'nationality_cert' => ['name' => 'Nationality Certificate',             'icon' => 'fa-solid fa-flag', 'keys' => ['nationality_cert', 'nationality'],                      'default_file' => 'nationality_cert.pdf',  'size' => '720 KB'],
        'photo'            => ['name' => 'Passport-size Photograph',            'icon' => 'fa-solid fa-camera', 'keys' => ['photo', 'passport_photo', 'profile_photo'],              'default_file' => 'passport_photo.jpg',    'size' => '650 KB'],
    ];

    // Dealer apply page document order (dealer/apply.blade.php Section 4)
    $dealerDocList = [
        'nid_copy'          => ['name' => 'NID Copy (Front & Back)',           'icon' => 'fa-solid fa-id-card', 'keys' => ['nid_copy', 'nid', 'nid_card'],                            'default_file' => 'nid_copy.pdf',          'size' => '1.2 MB'],
        'trade_license_doc' => ['name' => 'Trade License (Current Year)',      'icon' => 'fa-solid fa-store', 'keys' => ['trade_license_doc', 'trade_license', 'trade', 'trade_cert'],'default_file' => 'trade_license.pdf',    'size' => '2.5 MB'],
        'premises_photo'    => ['name' => 'Premises Photograph',               'icon' => 'fa-solid fa-building', 'keys' => ['premises_photo', 'premises'],                             'default_file' => 'premises_photo.jpg',    'size' => '3.1 MB'],
        'bank_statement'    => ['name' => 'Bank Statement (Last 6 months)',    'icon' => 'fa-solid fa-building-columns', 'keys' => ['bank_statement', 'bank', 'bank_solvency'],                'default_file' => 'bank_statement.pdf',    'size' => '1.8 MB'],
    ];

    $standardDocList = ($application->applicant_type === 'dealer') ? $dealerDocList : $citizenDocList;

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
                    'icon' => 'fa-solid fa-file-lines',
                    'is_uploaded' => true,
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                    'key' => $uploadedKey,
                ];
            }
        }
    }
@endphp

<div class="max-w-full space-y-4">

    <!-- Back to dashboard (top-left, outside header) -->
    <a href="{{ $application->applicant_type === 'dealer' ? route('dealer.dashboard') : route('citizen.dashboard') }}"
       class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-sm text-[11px] font-semibold text-slate-500 hover:text-gov-green hover:border-gov-green/40 transition-all">
        <span><i class="fa-solid fa-arrow-left"></i></span><span>Back to Dashboard</span>
    </a>

    <!-- ===== COMPACT HEADER ===== -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 flex flex-wrap items-start justify-between gap-3">
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gov-green/10 border border-gov-green/20 flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-clipboard-list text-gov-green"></i></div>
                <div>
                    <h2 class="text-base font-bold font-serif text-slate-900 leading-tight">Application {{ $application->application_number }}</h2>
                    <p class="text-[11px] text-slate-500 font-normal">
                        {{ ucfirst(str_replace('_', ' ', $application->type)) }} &bull;
                        {{ $application->firearm_details['weapon_type'] ?? 'N/A' }} &bull;
                        {{ $application->user->name }}
                    </p>
                </div>
            </div>
            <div class="flex flex-col items-end gap-1.5">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase border
                    @if(in_array($status, ['approved','license_issued','vetted_cleared'])) border-emerald-500/30 bg-emerald-50 text-emerald-700
                    @elseif($isRejected || $status === 'vetted_flagged') border-rose-500/30 bg-rose-50 text-rose-700
                    @else border-amber-500/30 bg-amber-50 text-amber-700 @endif">
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </span>
                <span class="text-[10px] text-slate-400 font-normal">Updated {{ $application->updated_at->diffForHumans() }}</span>
            </div>
        </div>

        <!-- Compact Tracker (Horizontal Stepper) -->
        <div class="px-4 sm:px-5 pb-3 overflow-x-auto no-scrollbar">
            <div class="flex items-center min-w-[500px]">
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
                                @if($isDone) <i class="fa-solid fa-check"></i> @else <i class="{{ $step['icon'] }}"></i> @endif
                            </div>
                            <span class="mt-1 text-[9px] font-semibold uppercase tracking-wider
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
                class="detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none bg-gov-green text-white shadow-sm">
            <span><i class="fa-solid fa-user"></i></span><span>Overview</span>
        </button>
        <button type="button" data-tab="documents" onclick="switchDetailTab('documents')"
                class="detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span><i class="fa-solid fa-paperclip"></i></span><span>Documents</span>
            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold {{ $uploadedCount === count($standardDocList) ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $uploadedCount }}/{{ count($standardDocList) }}</span>
        </button>
        {{-- <button type="button" data-tab="timeline" onclick="switchDetailTab('timeline')"
                class="detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span><i class="fa-solid fa-clock"></i></span><span>Timeline</span>
            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-slate-100 text-slate-500">{{ $application->logs->count() }}</span>
        </button> --}}
        {{-- @if($application->vettings->count())
        <button type="button" data-tab="vetting" onclick="switchDetailTab('vetting')"
                class="detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
            <span><i class="fa-solid fa-shield-halved"></i></span><span>Vetting</span>
            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-semibold bg-slate-100 text-slate-500">{{ $application->vettings->count() }}</span>
        </button>
        @endif --}}
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- ===== LEFT: TAB CONTENT ===== -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <!-- TAB: OVERVIEW -->
                <div class="detail-panel" id="panel-overview">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                        <span class="text-[11px] font-semibold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-user text-gov-green mr-1"></i> Application Summary</span>
                    </div>
                    <div class="p-5">
                        <!-- Applicant Particulars -->
                        <div class="mb-5">
                            <span class="text-[10px] font-semibold uppercase text-gov-green tracking-widest block mb-2 border-b border-slate-100 pb-1.5">Applicant Particulars</span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                     <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Full Name</span>
                                     <span class="font-semibold text-slate-900 break-words block">{{ $application->user->name }} @if($application->user->name_bn) <span class="text-[11px] text-slate-500 font-normal">({{ $application->user->name_bn }})</span> @endif</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                     <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Applicant Role</span>
                                     <span class="font-semibold text-slate-900 capitalize break-words block">{{ str_replace('_', ' ', $application->applicant_type) }}</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                     <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">NID Number</span>
                                     <span class="font-semibold text-slate-900 break-words [overflow-wrap:anywhere] block">{{ $application->applicant_details['nid'] ?? $application->user->nid ?? 'N/A' }}</span>
                                </div>

                                @if($application->applicant_type === 'dealer')
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                         <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Firm / Business Name</span>
                                         <span class="font-semibold text-slate-900 break-words block">{{ $application->applicant_details['firm_name'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                         <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Trade License</span>
                                         <span class="font-semibold text-slate-900 break-words [overflow-wrap:anywhere] block">{{ $application->applicant_details['trade_license'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                         <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">License Class</span>
                                         <span class="font-semibold text-slate-900 capitalize break-words block">{{ $application->applicant_details['license_class'] ?? 'Class A' }}</span>
                                    </div>
                                @else
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                         <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Date of Birth</span>
                                         <span class="font-semibold text-slate-900 break-words block">{{ $application->applicant_details['dob'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                         <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Father's Name</span>
                                         <span class="font-semibold text-slate-900 break-words block">{{ $application->applicant_details['father_name'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                         <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Mobile & Email</span>
                                         <span class="font-normal text-slate-900 break-words [overflow-wrap:anywhere] block">{{ $application->user->phone ?? 'N/A' }} &bull; {{ $application->user->email }}</span>
                                    </div>
                                @endif

                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                     <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Annual Income</span>
                                     <span class="font-semibold text-slate-900 break-words block">৳{{ number_format($application->applicant_details['annual_income'] ?? 0) }}</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                     <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Jurisdiction Office</span>
                                     <span class="font-semibold text-slate-900 break-words block">{{ $application->district->name ?? 'District' }} DC Office</span>
                                </div>
                            </div>
                        </div>

                        <!-- Firearm Particulars -->
                        <div class="mb-5">
                            <span class="text-[10px] font-semibold uppercase text-gov-green tracking-widest block mb-2 border-b border-slate-100 pb-1.5"><i class="fa-solid fa-shield-halved text-gov-green mr-1"></i> Firearm & License Specifications</span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                     <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Weapon Type</span>
                                     <span class="font-semibold text-slate-900 break-words block">{{ $application->firearm_details['weapon_type'] ?? 'N/A' }}</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                     <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Bore / Calibre</span>
                                     <span class="font-semibold text-slate-900 break-words block">{{ $application->firearm_details['bore'] ?? 'N/A' }}</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                     <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Purpose</span>
                                     <span class="font-normal text-slate-900 break-words block">{{ $application->firearm_details['purpose'] ?? 'N/A' }}</span>
                                </div>
                                <div class="sm:col-span-3 p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 min-w-0 break-words">
                                     <span class="text-[9px] font-semibold uppercase text-emerald-600 tracking-widest block">Sourcing Licensed Arms Dealer</span>
                                     <span class="font-semibold text-emerald-800 break-words [overflow-wrap:anywhere] block">{{ $application->firearm_details['dealer_name'] ?? 'M/S Metropolitan Arms Store (Govt. Reg #AD-1029)' }}</span>
                                </div>
                                @if(isset($application->firearm_details['categories']))
                                    <div class="sm:col-span-3 p-2.5 rounded-lg bg-slate-50 border border-slate-100 min-w-0 break-words">
                                         <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-widest block">Authorized Categories</span>
                                         <span class="font-semibold text-slate-900 break-words block">{{ is_array($application->firearm_details['categories']) ? implode(', ', $application->firearm_details['categories']) : $application->firearm_details['categories'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick stats -->
                        <div class="grid grid-cols-4 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                 <span class="text-lg font-bold text-gov-green block">{{ $application->logs->count() }}</span>
                                 <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-wider">Events</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                 <span class="text-lg font-bold text-amber-600 block">{{ $application->vettings->count() }}</span>
                                 <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-wider">Vettings</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                 <span class="text-lg font-bold text-emerald-600 block">{{ $uploadedCount }}</span>
                                 <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-wider">Docs</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                 <span class="text-lg font-bold text-blue-600 block">{{ $application->created_at->format('d M') }}</span>
                                 <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-wider">Filed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: DOCUMENTS -->
                <div class="detail-panel hidden" id="panel-documents">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <span class="text-[11px] font-semibold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-paperclip text-gov-green mr-1"></i> Attached Documents</span>
                        @if($hasUploadedDocs)
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full"><i class="fa-solid fa-check mr-1"></i> Uploaded & Verified</span>
                        @else
                            <span class="text-[10px] font-semibold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Pending Upload</span>
                        @endif
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach($docItems as $doc)
                            <div class="p-3 rounded-xl border transition-all group
                                {{ $doc['is_uploaded'] ? 'border-slate-200 bg-slate-50/70 hover:bg-white hover:shadow-sm' : 'border-rose-200/70 bg-rose-50/40 hover:bg-rose-50/70' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <span class="text-[16px] text-gov-green flex-shrink-0"><i class="{{ $doc['icon'] }}"></i></span>
                                        <div class="min-w-0">
                                             <span class="font-semibold text-slate-800 block text-xs leading-tight truncate">{{ $doc['name'] }}</span>
                                            @if(!$doc['is_uploaded'])
                                                 <span class="text-[10px] text-rose-600 font-normal">Not uploaded</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1.5 flex-shrink-0">
                                        @if($doc['is_uploaded'])
                                            <span class="w-2 h-2 rounded-full bg-emerald-500" title="Attached"></span>
                                            <button type="button" onclick="openDocumentViewer('{{ addslashes($doc['name']) }}', '{{ $doc['file_name'] }}', '{{ $doc['file_size'] }}', true, '{{ $doc['key'] }}')"
                                                     class="px-2 py-1 rounded-lg bg-gov-green hover:bg-gov-light text-white text-[11px] font-semibold transition-all shadow-sm">
                                                <i class="fa-solid fa-eye mr-1"></i> View
                                            </button>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-rose-400" title="Missing"></span>
                                            <button type="button" onclick="openDocumentViewer('{{ addslashes($doc['name']) }}', 'No file uploaded', '0 KB', false, '{{ $doc['key'] }}')"
                                                     class="px-2 py-1 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 text-[11px] font-semibold transition-all">
                                                <i class="fa-solid fa-eye mr-1"></i> Check Status
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Document progress bar -->
                        <div class="mt-4 pt-4 border-t border-slate-100">
                             <div class="flex items-center justify-between text-[11px] font-semibold text-slate-500 mb-1.5">
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
                        <span class="text-[11px] font-semibold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-clock text-gov-green mr-1"></i> Workflow Processing Timeline</span>
                        <span class="text-[10px] font-normal text-slate-400">{{ $application->logs->count() }} events</span>
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
                                     <span class="text-[10px] font-semibold uppercase tracking-wider
                                        @if($loop->first) text-gov-green
                                        @elseif(str_contains($log->action, 'reject')) text-rose-600
                                        @else text-amber-600 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                     <span class="text-[10px] text-slate-400 font-normal flex-shrink-0">{{ $log->created_at->format('d M Y · h:i A') }}</span>
                                </div>
                                 <p class="text-xs text-slate-700 font-normal leading-relaxed mt-1">{{ $log->remarks }}</p>
                                @if($log->actor)
                                <div class="flex items-center space-x-1.5 mt-1.5">
                                     <span class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-semibold text-slate-600 flex-shrink-0">
                                        {{ strtoupper(substr($log->actor->name, 0, 1)) }}
                                    </span>
                                     <span class="text-[10px] text-slate-500 font-normal">by {{ $log->actor->name }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 space-y-2">
                            <i class="fa-solid fa-clock text-3xl text-slate-300 block"></i>
                             <p class="text-xs text-slate-400 font-normal">No timeline entries yet.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- TAB: VETTING -->
                @if($application->vettings->count())
                <div class="detail-panel hidden" id="panel-vetting">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                        <span class="text-[11px] font-semibold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-shield-halved text-gov-green mr-1"></i> Agency Vetting Clearances</span>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($application->vettings as $v)
                            <div class="flex items-center justify-between px-3.5 py-3 rounded-xl border transition-all
                                @if($v->status === 'cleared') border-emerald-200 bg-emerald-50/50
                                @elseif($v->status === 'flagged') border-rose-200 bg-rose-50/50
                                @else border-amber-200 bg-amber-50/50 @endif">
                                <div class="flex items-center space-x-2.5">
                                    <span class="text-lg">
                                        @if($v->status === 'cleared') <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        @elseif($v->status === 'flagged') <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                                        @else <i class="fa-solid fa-hourglass-half text-amber-600"></i> @endif
                                    </span>
                                    <div>
                                         <span class="text-xs font-semibold text-slate-800 uppercase">{{ $v->agency }}</span>
                                        @if($v->vetted_at)
                                             <span class="text-[10px] text-slate-400 font-normal block">{{ $v->vetted_at->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                 <span class="text-[10px] font-semibold uppercase px-2.5 py-1 rounded-full
                                    @if($v->status === 'cleared') bg-emerald-100 text-emerald-700
                                    @elseif($v->status === 'flagged') bg-rose-100 text-rose-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ $v->status }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- ===== RIGHT: STATUS & PAYMENT ===== -->
        <div class="space-y-4">
            <!-- Status Panel -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                     <span class="text-[11px] font-semibold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-chart-line text-gov-green mr-1"></i> Current File Status</span>
                </div>
                <div class="p-5 text-center">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider inline-block
                        @if($status === 'payment_pending') bg-amber-500/10 text-amber-600 border border-amber-500/20
                        @elseif($status === 'waiting_for_license_fee') bg-indigo-500/10 text-indigo-600 border border-indigo-500/20
                        @elseif(str_contains($status, 'approved')) bg-emerald-500/10 text-emerald-600 border border-emerald-500/20
                        @elseif(str_contains($status, 'reject')) bg-rose-500/10 text-rose-600 border border-rose-500/20
                        @else bg-amber-500/10 text-amber-600 border border-amber-500/20 @endif">
                        {{ str_replace('_', ' ', $status) }}
                    </span>
                     <p class="text-[11px] text-slate-500 mt-2 font-normal">Active Desk: {{ is_string($application->current_actor_role) ? ucwords(str_replace('_', ' ', $application->current_actor_role)) : (\App\Enums\Role::tryFrom($application->current_actor_role)?->label() ?? 'Applicant') }}</p>

                    @if($status === 'payment_pending')
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                             <p class="text-[11px] text-slate-500 font-semibold uppercase">Platform Service Charge Pending</p>
                             <a href="{{ route('payment.initiate', [Crypt::encryptString($application->id), 'type' => 'service_fee']) }}" class="w-full block py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold shadow-sm transition-colors">
                                <i class="fa-solid fa-credit-card mr-1"></i> Pay Platform Fee (PayStation)
                            </a>
                             <button onclick="checkPaymentStatus('{{ Crypt::encryptString($application->id) }}', this)" class="w-full block py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold border border-slate-300 transition-colors">
                                <i class="fa-solid fa-magnifying-glass mr-1"></i> Verify Payment Status
                            </button>
                        </div>
                    @elseif($status === 'waiting_for_license_fee')
                        <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                             <p class="text-[11px] text-slate-500 font-semibold uppercase block">Approved &bull; Waiting for License Fee</p>
                             <p class="text-base font-bold text-slate-800">৳{{ number_format($application->license_fee_amount ?? 0) }}</p>
                             <a href="{{ route('payment.initiate', [Crypt::encryptString($application->id), 'type' => 'license_fee']) }}" class="w-full block py-2 bg-gov-green hover:bg-gov-light text-white rounded-lg text-xs font-bold shadow-sm transition-colors animate-pulse">
                                <i class="fa-solid fa-credit-card mr-1"></i> Pay License Fee (PayStation)
                            </a>
                             <button onclick="checkPaymentStatus('{{ Crypt::encryptString($application->id) }}', this)" class="w-full block py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold border border-slate-300 transition-colors">
                                <i class="fa-solid fa-magnifying-glass mr-1"></i> Verify Payment Status
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Records -->
            @if($application->payment_details || $application->service_fee_paid || $application->license_fee_paid)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                     <span class="text-[11px] font-semibold uppercase text-slate-500 tracking-widest"><i class="fa-solid fa-credit-card text-gov-green mr-1"></i> Payment Records</span>
                </div>
                <div class="p-5 space-y-3 text-xs">
                    <div class="p-2.5 rounded bg-slate-50 border border-slate-200/60 space-y-1">
                         <div class="flex justify-between items-center font-semibold">
                            <span class="text-slate-700">Platform Service Charge</span>
                            @if($application->service_fee_paid)
                                 <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-semibold uppercase">Paid</span>
                            @else
                                 <span class="text-[10px] px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-semibold uppercase">Pending</span>
                            @endif
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Amount:</span>
                             <span class="font-semibold text-slate-800">৳{{ number_format($application->service_fee_amount ?? 850) }}</span>
                        </div>
                        @if(isset($application->payment_details['service_fee_trx_id']))
                            <div class="flex justify-between text-slate-500">
                                <span>Trx ID:</span>
                                 <span class="font-mono font-medium text-slate-800">{{ $application->payment_details['service_fee_trx_id'] }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-2.5 rounded bg-slate-50 border border-slate-200/60 space-y-1">
                         <div class="flex justify-between items-center font-semibold">
                            <span class="text-slate-700">Statutory License Fee</span>
                            @if($application->license_fee_paid)
                                 <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-semibold uppercase">Paid</span>
                            @elseif($application->license_fee_amount)
                                 <span class="text-[10px] px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 font-semibold uppercase">Awaiting Payment</span>
                            @else
                                 <span class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-semibold uppercase">Not Due Yet</span>
                            @endif
                        </div>
                        @if($application->license_fee_amount)
                            <div class="flex justify-between text-slate-500">
                                <span>Amount:</span>
                                 <span class="font-semibold text-slate-800">৳{{ number_format($application->license_fee_amount) }}</span>
                            </div>
                        @endif
                        @if(isset($application->payment_details['license_fee_trx_id']))
                            <div class="flex justify-between text-slate-500">
                                <span>Trx ID:</span>
                                 <span class="font-mono font-medium text-slate-800">{{ $application->payment_details['license_fee_trx_id'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Interactive Document Preview Modal -->
<div id="documentViewerModal" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200">
        <!-- Modal Header -->
        <div class="px-4 sm:px-5 py-3.5 sm:py-4 bg-gov-green text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <span class="text-xl"><i class="fa-solid fa-file-lines"></i></span>
                <div>
                     <h3 id="modalDocTitle" class="text-xs font-bold uppercase tracking-wider">Document Title</h3>
                     <p id="modalDocMeta" class="text-[11px] text-white/70 font-normal">filename.pdf &bull; 1.5 MB</p>
                </div>
            </div>
             <button type="button" onclick="closeDocumentViewer()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-sm flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Document Viewer Content Area -->
        <div class="p-4 sm:p-6 bg-slate-100 max-h-[70vh] overflow-y-auto">
            <div class="bg-white p-4 sm:p-6 rounded-xl border border-slate-300 shadow-inner space-y-4 font-sans text-xs">
                <div class="space-y-3 py-2">
                    <div class="bg-slate-50 p-3 rounded border border-slate-200 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Document Type:</span>
                             <span id="docTypeLabel" class="font-semibold text-slate-900">National Identity Document</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Applicant Reference:</span>
                             <span class="font-mono font-medium text-slate-800">{{ $application->application_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Verification Hash:</span>
                            <span class="font-mono text-[10px] text-slate-600">SHA256: 8f92a10b4c892e104f81a7b...</span>
                        </div>
                    </div>

                    <!-- Visual Rendered Document Container -->
                    <div id="previewDocumentBody" class="space-y-3">
                        <!-- Dynamic rendered document content injected via JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-4 sm:px-5 py-3 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2">
             <span class="text-[11px] text-slate-400 font-normal">NFLRMS Secure Attachment Vault</span>
            <div class="flex items-center space-x-2">
                 <button type="button" onclick="closeDocumentViewer()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs rounded-lg transition-colors">
                    Close
                </button>
                 <button type="button" onclick="triggerDocDownload()" class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-semibold text-xs rounded-lg shadow-sm transition-colors flex items-center space-x-1">
                    <span><i class="fa-solid fa-download mr-1"></i> Download PDF</span>
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
        document.querySelectorAll('.detail-panel').forEach(p => p.classList.add('hidden'));
        const panel = document.getElementById(`panel-${tabName}`);
        if (panel) panel.classList.remove('hidden');

        document.querySelectorAll('.detail-tab').forEach(btn => {
            const isActive = btn.dataset.tab === tabName;
            btn.className = isActive
                ? 'detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none bg-gov-green text-white shadow-sm'
                : 'detail-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50';
        });
    }

    // ===== DOCUMENT VIEWER =====
    let currentDocTitle = '';
    let currentDocKey = '';
    let isCurrentDocUploaded = true;
    const currentAppNo = '{{ $application->application_number }}';

    function openDocumentViewer(title, filename, size, isUploaded = true, key = '') {
        currentDocTitle = title;
        currentDocKey = key;
        isCurrentDocUploaded = isUploaded;

        document.getElementById('modalDocTitle').innerText = title;
        document.getElementById('modalDocMeta').innerText = filename + (size !== '0 KB' ? ' • ' + size : '');
        document.getElementById('docTypeLabel').innerText = title;

        const previewContainer = document.getElementById('previewDocumentBody');

        if (!isUploaded) {
            previewContainer.innerHTML = `
                <div class="bg-rose-50 border-2 border-dashed border-rose-300 rounded-xl p-8 text-center text-rose-800 space-y-3 my-2">
                    <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-3xl font-bold shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h5 class="font-bold text-slate-900 text-base font-serif">File Not Found</h5>
                    <p class="text-xs text-rose-700 max-w-md mx-auto leading-relaxed font-normal">
                        No statutory document file was uploaded by the applicant for <strong>${title}</strong>.
                    </p>
                    <div class="pt-2 flex justify-center space-x-2">
                        <span class="px-3 py-1 bg-rose-200 text-rose-900 text-[11px] font-semibold rounded uppercase">Status: Not Uploaded</span>
                    </div>
                </div>
            `;
        } else {
            const streamUrl = '{{ route("document.download") }}?key=' + encodeURIComponent(key) + '&title=' + encodeURIComponent(title) + '&app=' + encodeURIComponent(currentAppNo) + '&inline=1';
            const isImage = filename.match(/\.(jpg|jpeg|png|webp)$/i);

            let realViewerHTML = '';
            if (isImage) {
                realViewerHTML = `
                    <div class="p-3 bg-slate-100 rounded-xl border border-slate-200 text-center mb-3">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-widest block mb-2"><i class="fa-solid fa-camera mr-1"></i> Uploaded Attachment Image Preview</span>
                        <img src="${streamUrl}" alt="${title}" class="max-h-96 mx-auto rounded-lg shadow-md object-contain border border-slate-300">
                    </div>
                `;
            } else {
                realViewerHTML = `
                    <div class="mb-3 rounded-xl border border-slate-200 overflow-hidden shadow-inner bg-slate-950">
                        <div class="bg-slate-900 px-3 py-1.5 flex justify-between items-center text-white text-[11px] border-b border-slate-800">
                            <span class="font-semibold text-emerald-400"><i class="fa-solid fa-file-lines mr-1"></i> Attached File: ${filename}</span>
                            <a href="${streamUrl}" target="_blank" class="text-amber-300 hover:underline font-semibold">Open Fullscreen ↗</a>
                        </div>
                        <iframe src="${streamUrl}" class="w-full h-80 bg-white"></iframe>
                    </div>
                `;
            }
            previewContainer.innerHTML = realViewerHTML;
        }

        document.getElementById('documentViewerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDocumentViewer() {
        document.getElementById('documentViewerModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDocumentViewer();
    });

    function triggerDocDownload() {
        if (!isCurrentDocUploaded) {
            alert('File Not Found: No document uploaded for ' + currentDocTitle + ' by applicant.');
            return;
        }
        const downloadUrl = '{{ route("document.download") }}?key=' + encodeURIComponent(currentDocKey) + '&title=' + encodeURIComponent(currentDocTitle) + '&app=' + encodeURIComponent(currentAppNo);
        window.location.href = downloadUrl;
    }

    function checkPaymentStatus(appId, btnElement) {
        if (btnElement) {
            btnElement.disabled = true;
            btnElement.innerHTML = '<i class="fa-solid fa-hourglass-half mr-1"></i> Verifying with PayStation...';
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
                alert('Success: ' + data.message);
                window.location.reload();
            } else if (data.status === 'failed') {
                alert('Payment Notice: ' + data.message);
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = '<i class="fa-solid fa-magnifying-glass mr-1"></i> Verify Payment Status';
                }
            } else {
                if (btnElement) {
                    btnElement.disabled = false;
                    btnElement.innerHTML = '<i class="fa-solid fa-magnifying-glass mr-1"></i> Verify Payment Status';
                }
                alert(data.message || 'Status check complete.');
            }
        })
        .catch(err => {
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerHTML = '<i class="fa-solid fa-magnifying-glass mr-1"></i> Verify Payment Status';
            }
        });
    }

    @if(in_array($application->status, ['payment_pending', 'waiting_for_license_fee']))
    (function autoPollPayment() {
        const appId = '{{ Crypt::encryptString($application->id) }}';
        let checkCount = 0;
        const maxChecks = 24;

        const pollInterval = setInterval(() => {
            checkCount++;
            if (checkCount > maxChecks) {
                clearInterval(pollInterval);
                return;
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
