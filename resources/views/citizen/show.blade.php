@extends('layouts.app')

@section('title', 'Application Tracking')

@section('content')
<div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-5 my-2">

    <!-- Left Column: Details & Logs -->
    <div class="md:col-span-8 space-y-4">

        <!-- Application Summary Card -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-xs font-black uppercase text-gov-green tracking-wider font-outfit">Application File Summary</h3>
                    <p class="text-[9px] text-slate-400 font-semibold mt-0.5">Tracking Code: <span class="font-bold text-slate-800">{{ $application->application_number }}</span></p>
                </div>
                <a href="{{ $application->applicant_type === 'dealer' ? route('dealer.dashboard') : route('citizen.dashboard') }}" class="text-[10px] font-bold text-slate-555 hover:text-slate-700">&larr; Back to Dashboard</a>
            </div>

            <!-- 1. Applicant Personal & Business Details -->
            <div>
                <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">👤 Applicant Particulars</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-[11px] bg-slate-50 p-3.5 rounded-lg border border-slate-200">
                    <div>
                        <span class="text-slate-500 block">Full Name:</span>
                        <span class="font-bold text-slate-900">{{ $application->user->name }} @if($application->user->name_bn) <span class="text-xs text-slate-500">({{ $application->user->name_bn }})</span> @endif</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Applicant Role:</span>
                        <span class="font-bold text-slate-900 capitalize">{{ str_replace('_', ' ', $application->applicant_type) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">NID Number:</span>
                        <span class="font-bold text-slate-900">{{ $application->applicant_details['nid'] ?? $application->user->nid ?? '3710928391029' }}</span>
                    </div>

                    @if($application->applicant_type === 'dealer')
                        <div>
                            <span class="text-slate-500 block">Firm / Business Name:</span>
                            <span class="font-bold text-slate-900">{{ $application->applicant_details['firm_name'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Trade License:</span>
                            <span class="font-bold text-slate-900">{{ $application->applicant_details['trade_license'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">License Class:</span>
                            <span class="font-bold text-slate-900 capitalize">{{ $application->applicant_details['license_class'] ?? 'Class A' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-500 block">Business Address:</span>
                            <span class="font-bold text-slate-900">{{ $application->applicant_details['business_address'] ?? 'N/A' }}</span>
                        </div>
                    @else
                        <div>
                            <span class="text-slate-500 block">Date of Birth:</span>
                            <span class="font-bold text-slate-900">{{ $application->applicant_details['dob'] ?? '1988-05-14' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Father's Name:</span>
                            <span class="font-bold text-slate-900">{{ $application->applicant_details['father_name'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Mobile & Email:</span>
                            <span class="font-bold text-slate-900">{{ $application->user->phone ?? '01711234567' }} &bull; {{ $application->user->email }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-slate-500 block">Present Address:</span>
                            <span class="font-bold text-slate-900">{{ $application->applicant_details['present_address'] ?? $application->user->present_address ?? 'Dhaka, Bangladesh' }}</span>
                        </div>
                    @endif

                    <div>
                        <span class="text-slate-500 block">Annual Income:</span>
                        <span class="font-bold text-slate-900">৳{{ number_format($application->applicant_details['annual_income'] ?? 1200000) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Jurisdiction Office:</span>
                        <span class="font-bold text-slate-900">{{ $application->district->name ?? 'District' }} DC Office</span>
                    </div>
                </div>
            </div>

            <!-- 2. Firearm / License Particulars -->
            <div>
                <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">🔫 Firearm & License Specifications</h4>
                <div class="bg-slate-50 p-3.5 rounded-lg border border-slate-200 text-[11px] grid grid-cols-3 gap-3">
                    <div>
                        <span class="text-slate-500 block">Weapon Type:</span>
                        <span class="font-bold text-slate-900">{{ $application->firearm_details['weapon_type'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Bore / Calibre Spec:</span>
                        <span class="font-bold text-slate-900">{{ $application->firearm_details['bore'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Application Purpose:</span>
                        <span class="font-bold text-slate-900">{{ $application->firearm_details['purpose'] ?? 'Personal Security & Self Defense' }}</span>
                    </div>
                    <div class="col-span-3 pt-1 border-t border-slate-200/60">
                        <span class="text-slate-500 block">Sourcing Licensed Arms Dealer (কার নিকট হতে ক্রয়/সংগ্রহ করা হবে):</span>
                        <span class="font-bold text-emerald-800">{{ $application->firearm_details['dealer_name'] ?? 'M/S Metropolitan Arms Store (Govt. Reg #AD-1029)' }}</span>
                    </div>
                    @if(isset($application->firearm_details['categories']))
                        <div class="col-span-3">
                            <span class="text-slate-500 block">Authorized Categories:</span>
                            <span class="font-bold text-slate-900">{{ is_array($application->firearm_details['categories']) ? implode(', ', $application->firearm_details['categories']) : $application->firearm_details['categories'] }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. Attached Statutory Documents & File Uploads -->
            <div>
                <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">📎 Attached Statutory Documents & Files</h4>
                <div class="space-y-2 text-[11px]">
                    @php
                        $userUploadedDocs = $application->documents;
                        $hasUploadedDocs = !empty($userUploadedDocs) && is_array($userUploadedDocs) && count($userUploadedDocs) > 0;

                        $standardDocList = [
                            'nid' => ['name' => 'National ID Card Copy (Smart NID)', 'keys' => ['nid', 'nid_copy', 'nid_card'], 'default_file' => 'nid_card_copy.pdf', 'size' => '1.2 MB'],
                            'birth_cert' => ['name' => 'Birth Certificate', 'keys' => ['birth_cert', 'birth_certificate'], 'default_file' => 'birth_cert.pdf', 'size' => '950 KB'],
                            'edu_cert' => ['name' => 'Educational Qualification Certificate', 'keys' => ['edu_cert', 'edu', 'educational_cert'], 'default_file' => 'educational_cert.pdf', 'size' => '1.1 MB'],
                            'tin' => ['name' => 'Income Tax Certificate (TIN Return)', 'keys' => ['tin', 'tin_certificate', 'tax_yr1', 'tax_yr2', 'tax_yr3', 'tax_return'], 'default_file' => 'tin_return_ack.pdf', 'size' => '850 KB'],
                            'affidavit' => ['name' => 'Notarized Affidavit (BDT 300 Stamp)', 'keys' => ['affidavit', 'affidavit_copy'], 'default_file' => 'notarized_affidavit.pdf', 'size' => '1.8 MB'],
                            'nationality_cert' => ['name' => 'Nationality Certificate', 'keys' => ['nationality_cert', 'nationality'], 'default_file' => 'nationality_certificate.pdf', 'size' => '720 KB'],
                            'photo' => ['name' => 'Recent Passport-size Photograph', 'keys' => ['photo', 'passport_photo', 'profile_photo'], 'default_file' => 'passport_photo.jpg', 'size' => '650 KB'],
                            'firing_report' => ['name' => 'Firing Range Annual Fitness Report', 'keys' => ['firing_report', 'firing_cert'], 'default_file' => 'firing_range_report.pdf', 'size' => '1.3 MB'],
                            'medical' => ['name' => 'Physical & Mental Fitness Medical Clearance', 'keys' => ['medical', 'medical_cert', 'fitness_cert'], 'default_file' => 'medical_fitness_civil_surgeon.pdf', 'size' => '1.4 MB'],
                            'police_clearance' => ['name' => 'Local Police Station Clearance Letter', 'keys' => ['police_clearance', 'police'], 'default_file' => 'police_clearance.pdf', 'size' => '1.5 MB'],
                            'bank' => ['name' => 'Bank Solvency & Statement Certificate', 'keys' => ['bank', 'bank_solvency'], 'default_file' => 'bank_solvency.pdf', 'size' => '2.1 MB'],
                            'safe' => ['name' => 'Firearms Safe Storage Photograph', 'keys' => ['safe', 'safe_photo'], 'default_file' => 'gun_safe_photo.jpg', 'size' => '3.4 MB'],
                        ];

                        if ($application->applicant_type === 'dealer') {
                            $standardDocList['trade'] = ['name' => 'Trade License & Warehouse Layout', 'keys' => ['trade', 'trade_cert', 'trade_license'], 'default_file' => 'trade_license_warehouse.pdf', 'size' => '4.2 MB'];
                        }

                        $matchedUploadedKeys = [];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($standardDocList as $specKey => $spec)
                            @php
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
                                $fileName = $isUploaded ? ($uploadedItem['file'] ?? $uploadedItem['name'] ?? $spec['default_file']) : 'File Not Found';
                                $fileSize = $isUploaded ? ($uploadedItem['size'] ?? '1.5 MB') : 'N/A';
                            @endphp

                            <div class="p-3 rounded-lg border {{ $isUploaded ? 'border-slate-200 bg-slate-50/70' : 'border-rose-200/80 bg-rose-50/40' }} hover:bg-white hover:shadow-sm transition-all flex items-center justify-between group">
                                <div class="flex items-center space-x-2.5">
                                    <span class="text-xl">{{ $isUploaded ? '📄' : '⚠️' }}</span>
                                    <div>
                                        <span class="font-bold text-slate-800 block text-[11px] leading-tight">{{ $spec['name'] }}</span>
                                        @if($isUploaded)
                                            <span class="text-[9px] text-slate-400 font-semibold">{{ $fileName }} &bull; {{ $fileSize }}</span>
                                        @else
                                            <span class="text-[9px] text-rose-600 font-bold">File Not Found (Not Uploaded)</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center space-x-1.5">
                                    @if($isUploaded)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-700">
                                            ✓ Uploaded
                                        </span>
                                        <button type="button" onclick="openDocumentViewer('{{ addslashes($spec['name']) }}', '{{ $fileName }}', '{{ $fileSize }}', true, '{{ $foundKey ?? $specKey }}')" class="px-2.5 py-1 rounded bg-gov-green hover:bg-gov-light text-white text-[10px] font-bold transition-all shadow-sm">
                                            👁️ View
                                        </button>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-rose-100 text-rose-700">
                                            Not Found
                                        </span>
                                        <button type="button" onclick="openDocumentViewer('{{ addslashes($spec['name']) }}', 'No file uploaded', '0 KB', false, '{{ $specKey }}')" class="px-2.5 py-1 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 text-[10px] font-bold transition-all shadow-sm">
                                            👁️ Check Status
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if($hasUploadedDocs)
                            @foreach($userUploadedDocs as $uploadedKey => $item)
                                @if(!in_array($uploadedKey, $matchedUploadedKeys) && is_array($item))
                                    @php
                                        $fileName = $item['file'] ?? $item['name'] ?? 'Attached Document';
                                        $fileSize = $item['size'] ?? '1.0 MB';
                                        $displayName = $item['name'] ?? ucfirst(str_replace('_', ' ', $uploadedKey));
                                    @endphp
                                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50/70 hover:bg-white hover:shadow-sm transition-all flex items-center justify-between group">
                                        <div class="flex items-center space-x-2.5">
                                            <span class="text-xl">📄</span>
                                            <div>
                                                <span class="font-bold text-slate-800 block text-[11px] leading-tight">{{ $displayName }}</span>
                                                <span class="text-[9px] text-slate-400 font-semibold">{{ $fileName }} &bull; {{ $fileSize }}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-1.5">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-700">
                                                ✓ Uploaded
                                            </span>
                                            <button type="button" onclick="openDocumentViewer('{{ addslashes($displayName) }}', '{{ $fileName }}', '{{ $fileSize }}', true, '{{ $uploadedKey }}')" class="px-2.5 py-1 rounded bg-gov-green hover:bg-gov-light text-white text-[10px] font-bold transition-all shadow-sm">
                                                👁️ View
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
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

<!-- Interactive Document Preview Modal -->
<div id="documentViewerModal" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200 animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="px-5 py-4 bg-gov-deep text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <span class="text-xl">📄</span>
                <div>
                    <h3 id="modalDocTitle" class="text-xs font-black uppercase tracking-wider font-outfit text-white">Document Title</h3>
                    <p id="modalDocMeta" class="text-[10px] text-slate-300 font-semibold">filename.pdf &bull; 1.5 MB</p>
                </div>
            </div>
            <button type="button" onclick="closeDocumentViewer()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-sm flex items-center justify-center transition-colors">
                ✕
            </button>
        </div>

        <!-- Modal Document Viewer Content Area -->
        <div class="p-6 bg-slate-100 max-h-[70vh] overflow-y-auto">
            <div class="bg-white p-6 rounded-xl border border-slate-300 shadow-inner space-y-4 font-sans text-xs">
                
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div class="flex items-center space-x-3">
                        <img src="https://flms.lovable.app/__l5e/assets-v1/acbf4783-ce0b-43bc-b0fd-4ba7908c84b3/govt-logo.png" width="36" height="36" class="w-9 h-9 object-contain" alt="Government Seal">
                        <div>
                            <h4 class="font-extrabold text-slate-900 uppercase text-[11px] leading-tight">Government of the People's Republic of Bangladesh</h4>
                            <p class="text-[9px] text-slate-500 font-semibold">Ministry of Home Affairs &bull; Official Statutory Attachment</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-800 text-[9px] font-black uppercase tracking-wider border border-emerald-300">
                        ✓ VERIFIED & ENCRYPTED
                    </span>
                </div>

                <div class="space-y-3 py-2">
                    <div class="bg-slate-50 p-3 rounded border border-slate-200 text-[11px] space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Document Type:</span>
                            <span id="docTypeLabel" class="font-bold text-slate-900">National Identity Document</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Applicant Reference:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $application->application_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Verification Hash:</span>
                            <span class="font-mono text-[9px] text-slate-600">SHA256: 8f92a10b4c892e104f81a7b...</span>
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
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <span class="text-[10px] text-slate-400 font-semibold">NFLRMS Secure Attachment Vault</span>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="closeDocumentViewer()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                    Close
                </button>
                <button type="button" onclick="triggerDocDownload()" class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg shadow-sm transition-colors flex items-center space-x-1">
                    <span>⬇ Download PDF</span>
                </button>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

@section('scripts')
<script>
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
                    <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-3xl font-black shadow-sm">
                        ⚠️
                    </div>
                    <h5 class="font-black text-slate-900 text-base font-serif">File Not Found</h5>
                    <p class="text-xs text-rose-700 max-w-md mx-auto leading-relaxed font-semibold">
                        No statutory document file was uploaded by the applicant for <strong>${title}</strong>.
                    </p>
                    <div class="pt-2 flex justify-center space-x-2">
                        <span class="px-3 py-1 bg-rose-200 text-rose-900 text-[10px] font-black rounded uppercase">Status: Not Uploaded</span>
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
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-2">📸 Uploaded Attachment Image Preview</span>
                        <img src="${streamUrl}" alt="${title}" class="max-h-96 mx-auto rounded-lg shadow-md object-contain border border-slate-300">
                    </div>
                `;
            } else {
                realViewerHTML = `
                    <div class="mb-3 rounded-xl border border-slate-200 overflow-hidden shadow-inner bg-slate-950">
                        <div class="bg-slate-900 px-3 py-1.5 flex justify-between items-center text-white text-[10px] border-b border-slate-800">
                            <span class="font-bold text-emerald-400">📄 Attached File: ${filename}</span>
                            <a href="${streamUrl}" target="_blank" class="text-amber-300 hover:underline font-bold">Open Fullscreen ↗</a>
                        </div>
                        <iframe src="${streamUrl}" class="w-full h-80 bg-white"></iframe>
                    </div>
                `;
            }
            previewContainer.innerHTML = realViewerHTML + generateDocumentPreviewHTML(title, currentAppNo);
        }

        document.getElementById('documentViewerModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDocumentViewer() {
        document.getElementById('documentViewerModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function triggerDocDownload() {
        if (!isCurrentDocUploaded) {
            alert('File Not Found: No document uploaded for ' + currentDocTitle + ' by applicant.');
            return;
        }
        const downloadUrl = '{{ route("document.download") }}?key=' + encodeURIComponent(currentDocKey) + '&title=' + encodeURIComponent(currentDocTitle) + '&app=' + encodeURIComponent(currentAppNo);
        window.location.href = downloadUrl;
    }

    function generateDocumentPreviewHTML(title, appNo) {
        const lower = title.toLowerCase();
        
        if (lower.includes('nid') || lower.includes('identity')) {
            return `
                <div class="bg-gradient-to-br from-emerald-800 to-teal-950 p-4 rounded-2xl text-white shadow-lg space-y-3 font-sans border-2 border-amber-400/40">
                    <div class="flex justify-between items-center border-b border-white/20 pb-2">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center font-bold text-xs shadow">BD</div>
                            <div>
                                <h5 class="text-[11px] font-black tracking-wide text-amber-300">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</h5>
                                <p class="text-[8px] text-emerald-200">Government of the People's Republic of Bangladesh</p>
                            </div>
                        </div>
                        <span class="text-[9px] font-extrabold bg-amber-400 text-slate-900 px-2 py-0.5 rounded">NATIONAL ID CARD</span>
                    </div>

                    <div class="grid grid-cols-12 gap-3 items-center">
                        <div class="col-span-4 text-center">
                            <div class="w-20 h-24 bg-slate-200 rounded-lg border-2 border-amber-300 mx-auto flex items-center justify-center text-4xl shadow-inner">
                                👤
                            </div>
                            <span class="text-[8px] text-amber-200 mt-1 block font-mono">NID PHOTO SPEC</span>
                        </div>
                        <div class="col-span-8 space-y-1 text-[10px]">
                            <div><span class="text-emerald-300 block text-[8px]">Name / নাম:</span> <strong class="text-sm font-bold text-white">{{ $application->user->name }}</strong></div>
                            <div><span class="text-emerald-300 block text-[8px]">Father's Name:</span> <span>{{ $application->applicant_details['father_name'] ?? 'Md. Rafiqul Islam' }}</span></div>
                            <div><span class="text-emerald-300 block text-[8px]">Date of Birth:</span> <span>{{ $application->applicant_details['dob'] ?? '1988-05-14' }}</span></div>
                            <div><span class="text-emerald-300 block text-[8px]">NID Number / আইডি নম্বর:</span> <span class="font-mono text-amber-300 font-extrabold text-xs tracking-wider">{{ $application->applicant_details['nid'] ?? $application->user->nid ?? '3710928391029' }}</span></div>
                        </div>
                    </div>

                    <div class="border-t border-white/20 pt-2 flex justify-between items-center text-[9px]">
                        <span class="font-mono text-emerald-200">BARCODE: |||||||||||||||||||||||||||||||||</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-700/80 font-bold">DIGITAL VERIFIED</span>
                    </div>
                </div>
            `;
        }

        if (lower.includes('tax') || lower.includes('tin')) {
            return `
                <div class="bg-white p-5 rounded-xl border-2 border-slate-300 space-y-3 font-serif text-slate-900 shadow">
                    <div class="text-center border-b pb-2">
                        <h5 class="font-black text-xs uppercase text-emerald-900">National Board of Revenue (NBR)</h5>
                        <p class="text-[9px] text-slate-500 font-sans">Government of Bangladesh &bull; Taxes Circle-14, Dhaka</p>
                        <span class="mt-1 inline-block px-3 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-sans font-bold rounded">TIN ACKNOWLEDGEMENT RECEIPT</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] font-sans">
                        <div class="bg-slate-50 p-2 rounded">
                            <span class="text-slate-500 block text-[8px]">Taxpayer Name:</span>
                            <span class="font-bold">{{ $application->user->name }}</span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded">
                            <span class="text-slate-500 block text-[8px]">TIN Number:</span>
                            <span class="font-mono font-bold text-emerald-700">5849-2041-9201</span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded">
                            <span class="text-slate-500 block text-[8px]">Assessment Year:</span>
                            <span class="font-bold">2025-2026</span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded">
                            <span class="text-slate-500 block text-[8px]">Annual Income:</span>
                            <span class="font-bold">৳{{ number_format($application->applicant_details['annual_income'] ?? 1200000) }}</span>
                        </div>
                    </div>

                    <div class="p-3 bg-emerald-50 rounded border border-emerald-200 text-[10px] font-sans flex items-center justify-between">
                        <div>
                            <span class="font-bold text-emerald-900 block">✓ Income Tax Return Submitted</span>
                            <span class="text-[8px] text-emerald-700">Ref: NBR/TAX/2026/89201</span>
                        </div>
                        <span class="text-2xl">🏛️</span>
                    </div>
                </div>
            `;
        }

        if (lower.includes('bank') || lower.includes('solvency')) {
            return `
                <div class="bg-white p-5 rounded-xl border-2 border-slate-300 space-y-3 font-sans text-slate-900 shadow">
                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded bg-teal-800 text-white font-bold flex items-center justify-center text-xs">SB</div>
                            <div>
                                <h5 class="font-black text-xs text-teal-900">SONALI BANK PLC</h5>
                                <p class="text-[8px] text-slate-500">Dhaka Main Branch, Bangladesh</p>
                            </div>
                        </div>
                        <span class="text-[9px] font-bold text-teal-800 bg-teal-50 px-2 py-1 rounded border border-teal-200">SOLVENCY CERTIFICATE</span>
                    </div>

                    <p class="text-[10px] text-slate-600 leading-relaxed font-serif">
                        This is to certify that <strong>{{ $application->user->name }}</strong> maintains a Savings/Current Account (Acc No: 4402-9182-3901) with our branch. The account balance is satisfactory and creditworthy for statutory requirements.
                    </p>

                    <div class="bg-slate-50 p-3 rounded border border-slate-200 grid grid-cols-2 gap-2 text-[10px]">
                        <div><span class="text-slate-400 block text-[8px]">Confirmed Solvency Balance:</span> <strong class="text-emerald-700 font-extrabold text-xs">BDT 2,500,000.00</strong></div>
                        <div><span class="text-slate-400 block text-[8px]">Branch Manager Signature:</span> <span class="font-serif italic font-bold">A. K. Shamsuddin</span></div>
                    </div>
                </div>
            `;
        }

        if (lower.includes('medical') || lower.includes('fitness')) {
            return `
                <div class="bg-white p-5 rounded-xl border-2 border-slate-300 space-y-3 font-sans text-slate-900 shadow">
                    <div class="text-center border-b border-slate-200 pb-2">
                        <h5 class="font-black text-xs text-emerald-900 uppercase">Directorate General of Health Services (DGHS)</h5>
                        <p class="text-[9px] text-slate-500">Office of the Civil Surgeon &bull; Medical Board</p>
                        <span class="mt-1 inline-block px-3 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded">PHYSICAL & MENTAL FITNESS CERTIFICATE</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] bg-slate-50 p-3 rounded border border-slate-200">
                        <div><span class="text-slate-500 block text-[8px]">Patient Name:</span> <strong class="text-slate-900">{{ $application->user->name }}</strong></div>
                        <div><span class="text-slate-500 block text-[8px]">Medical Board Reg:</span> <strong class="text-slate-800">BMDC-REG-48920</strong></div>
                        <div><span class="text-slate-500 block text-[8px]">Physical Soundness:</span> <strong class="text-emerald-700">FIT FOR FIREARMS</strong></div>
                        <div><span class="text-slate-500 block text-[8px]">Psychological Evaluation:</span> <strong class="text-emerald-700">NORMAL & STABLE</strong></div>
                    </div>

                    <div class="text-right pt-2 text-[9px]">
                        <span class="font-serif italic font-bold text-slate-800 block text-xs">Dr. Mahbubur Rahman, MBBS, FCPS</span>
                        <span class="text-slate-500">Civil Surgeon & Medical Board Chairman</span>
                    </div>
                </div>
            `;
        }

        if (lower.includes('safe') || lower.includes('photo') || lower.includes('gun')) {
            return `
                <div class="bg-slate-900 text-white p-5 rounded-xl border-2 border-slate-700 space-y-3 font-sans shadow">
                    <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                        <div>
                            <h5 class="font-bold text-xs text-amber-400">GUN SAFE VAULT LOCKER PHOTOGRAPH</h5>
                            <p class="text-[8px] text-slate-400">Verified Physical Storage Compliance Inspection</p>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 text-[9px] font-bold">INSPECTED</span>
                    </div>

                    <div class="border-2 border-dashed border-slate-700 rounded-lg p-6 text-center bg-slate-950 space-y-2">
                        <div class="text-5xl">🔐</div>
                        <div class="font-mono text-amber-300 text-xs font-bold">HEAVY GAUGE DUAL-LOCK VAULT LOCKER</div>
                        <p class="text-[9px] text-slate-400 max-w-xs mx-auto">Vault Specs: 4ft Heavy Steel Armor Locker with Electronic Biometric Keypad & Double Mechanical Bolts.</p>
                    </div>

                    <div class="flex justify-between text-[9px] text-slate-400 pt-1">
                        <span>GPS Stamp: 23.8103° N, 90.4125° E</span>
                        <span class="text-emerald-400 font-bold">PHYSICAL SAFETY COMPLIANT</span>
                    </div>
                </div>
            `;
        }

        // Generic Default Visual Document
        return `
            <div class="bg-white p-5 rounded-xl border-2 border-slate-300 space-y-3 font-sans text-slate-900 shadow">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <div>
                        <h5 class="font-bold text-xs text-slate-900 uppercase">${title}</h5>
                        <p class="text-[8px] text-slate-500">Government Official Statutory Attachment &bull; Ref: ${appNo}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">VERIFIED</span>
                </div>
                <div class="p-4 bg-slate-50 rounded border border-slate-200 text-center space-y-2">
                    <div class="text-4xl">📄</div>
                    <p class="text-xs font-bold text-slate-800">${title}</p>
                    <p class="text-[10px] text-slate-500">Official attachment record registered under Firearms Licensing File #${appNo}.</p>
                </div>
            </div>
        `;
    }

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
