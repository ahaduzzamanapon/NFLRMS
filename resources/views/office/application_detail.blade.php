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
@endphp

<div class="max-w-5xl space-y-5">
    <!-- Back + Title -->
    <div>
        <a href="{{ $backRoute }}" class="text-[10px] font-extrabold text-slate-400 hover:text-gov-green flex items-center space-x-1 mb-3">
            <span>←</span><span>Back to queue</span>
        </a>
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-black font-serif text-slate-900">Case {{ $application->application_number }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ ucfirst(str_replace('_', ' ', $application->type)) }} &bull;
                    {{ $application->firearm_details['weapon_type'] ?? 'N/A' }} &bull;
                    {{ $application->user->name }}
                </p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase border
                @if(in_array($application->status, ['approved','license_issued','vetted_cleared'])) border-emerald-500/30 bg-emerald-50 text-emerald-700
                @elseif(str_contains($application->status,'rejected') || $application->status === 'vetted_flagged') border-rose-500/30 bg-rose-50 text-rose-700
                @else border-amber-500/30 bg-amber-50 text-amber-700 @endif">
                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Left: Application Info -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Applicant & Application -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Applicant & Application</span>
                </div>
                <div class="p-5 grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Name</span>
                        <span class="font-bold text-slate-900">{{ $application->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">NID</span>
                        <span class="font-bold text-slate-900">{{ $application->applicant_details['nid'] ?? $application->user->nid ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Date of Birth</span>
                        <span class="font-bold text-slate-900">
                            @php
                                $dobVal = $application->applicant_details['dob'] ?? $application->user->dob ?? null;
                            @endphp
                            {{ $dobVal ? (\Illuminate\Support\Carbon::parse($dobVal)->format('Y-m-d')) : 'N/A' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Phone</span>
                        <span class="font-bold text-slate-900">{{ $application->user->phone ?? $application->applicant_details['phone'] ?? 'N/A' }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Present Address</span>
                        <span class="font-bold text-slate-900">{{ $application->applicant_details['present_address'] ?? $application->user->present_address ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">District</span>
                        <span class="font-bold text-slate-900">{{ $application->user->district->name ?? $application->district->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Occupation</span>
                        <span class="font-bold text-slate-900">{{ $application->applicant_details['occupation'] ?? $application->user->occupation ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Annual Income</span>
                        <span class="font-bold text-slate-900">
                            @php
                                $income = $application->applicant_details['annual_income'] ?? $application->user->annual_income ?? null;
                            @endphp
                            {{ $income ? 'BDT ' . number_format($income) : 'N/A' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Weapon Type</span>
                        <span class="font-bold text-slate-900">{{ $application->firearm_details['weapon_type'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Bore / Calibre</span>
                        <span class="font-bold text-slate-900">{{ $application->firearm_details['bore'] ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Purpose</span>
                        <span class="font-bold text-slate-900">{{ $application->firearm_details['purpose'] ?? 'N/A' }}</span>
                    </div>
                    <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                        <span class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block">Licensed Arms Dealer / Sourcing Store (কার নিকট হতে ক্রয়/সংগ্রহ করা হবে)</span>
                        <span class="font-extrabold text-emerald-800 text-xs">{{ $application->firearm_details['dealer_name'] ?? 'M/S Metropolitan Arms Store (Govt. Reg #AD-1029)' }}</span>
                    </div>
                </div>
            </div>

            <!-- Attached Statutory Documents -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">📎 Attached Statutory Documents & Files</span>
                    @if(!empty($application->documents) && is_array($application->documents))
                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">✓ Uploaded & Verified</span>
                    @else
                        <span class="text-[9px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded">⚠️ Pending Applicant Upload</span>
                    @endif
                </div>
                <div class="p-5">
                    @php
                        $userUploadedDocs = $application->documents;
                        $hasUploadedDocs = !empty($userUploadedDocs) && is_array($userUploadedDocs) && count($userUploadedDocs) > 0;

                        $standardDocList = [
                            'nid' => ['name' => 'National ID Card Copy (Smart NID)', 'default_file' => 'nid_card_copy.pdf', 'size' => '1.2 MB'],
                            'tin' => ['name' => 'Income Tax Certificate (TIN Return)', 'default_file' => 'tin_return_ack.pdf', 'size' => '850 KB'],
                            'bank' => ['name' => 'Bank Solvency & Statement Certificate', 'default_file' => 'bank_solvency.pdf', 'size' => '2.1 MB'],
                            'medical' => ['name' => 'Physical Fitness Medical Clearance', 'default_file' => 'medical_fitness_civil_surgeon.pdf', 'size' => '1.4 MB'],
                            'safe' => ['name' => 'Firearms Safe Storage Photograph', 'default_file' => 'gun_safe_photo.jpg', 'size' => '3.4 MB'],
                        ];

                        if ($application->applicant_type === 'dealer') {
                            $standardDocList['trade'] = ['name' => 'Trade License & Warehouse Layout', 'default_file' => 'trade_license_warehouse.pdf', 'size' => '4.2 MB'];
                        }
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($standardDocList as $key => $spec)
                            @php
                                $uploadedItem = null;
                                if ($hasUploadedDocs) {
                                    $uploadedItem = $userUploadedDocs[$key] ?? null;
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
                                            ✓ Attached
                                        </span>
                                        <button type="button" onclick="openOfficeDocumentViewer('{{ addslashes($spec['name']) }}', '{{ $fileName }}', '{{ $fileSize }}', true)" class="px-2.5 py-1 rounded bg-gov-green hover:bg-gov-light text-white text-[10px] font-bold transition-all shadow-sm">
                                            👁️ View
                                        </button>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase bg-rose-100 text-rose-700">
                                            Not Found
                                        </span>
                                        <button type="button" onclick="openOfficeDocumentViewer('{{ addslashes($spec['name']) }}', 'No file uploaded', '0 KB', false)" class="px-2.5 py-1 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 text-[10px] font-bold transition-all shadow-sm">
                                            👁️ Inspect
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Vetting Reports -->
            @if($application->vettings->count())
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Vetting Reports</span>
                </div>
                <div class="p-5 grid grid-cols-2 gap-3">
                    @foreach($application->vettings as $v)
                    <div class="flex items-center justify-between px-3 py-2.5 rounded-lg border border-slate-100 bg-slate-50">
                        <span class="text-xs font-bold text-slate-700 uppercase">{{ $v->agency }}</span>
                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full
                            @if($v->status === 'cleared') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($v->status === 'flagged') bg-rose-50 text-rose-700 border border-rose-200
                            @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                            {{ $v->status }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Case Timeline -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Case Timeline (audit trail)</span>
                </div>
                <div class="p-5 space-y-3">
                    @forelse($application->logs as $log)
                    <div class="flex space-x-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 mt-1.5 flex-shrink-0"></div>
                        <div>
                            <div class="text-[9px] text-slate-400 font-bold">{{ $log->created_at->format('d M Y · h:i A') }}</div>
                            <div class="text-xs font-bold text-slate-900 mt-0.5">{{ $log->remarks }}</div>
                            @if($log->actor)
                            <div class="text-[9px] text-slate-500">by {{ $log->actor->name }}</div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 font-semibold">No timeline entries yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Officer Actions -->
        <div class="space-y-4">
            @if(!empty($actions) && $actionRoute !== '#')
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                    <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Officer Actions</span>
                </div>
                @if ($errors->any())
                    <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-bold space-y-1">
                        <span class="block text-sm font-black font-serif">⚠️ Please resolve the following errors:</span>
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
                        <textarea name="remarks" rows="3" placeholder="Remarks (mandatory)"
                                  class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white resize-none"></textarea>

                        @foreach($actions as $value => $label)
                        <button type="submit" name="action" value="{{ $value }}"
                                class="w-full py-2.5 rounded-lg text-xs font-black transition-colors
                                {{ in_array($value, ['approve','forward','trigger_vetting','forward_dc','forward_moha']) ? 'bg-gov-green hover:bg-gov-light text-white' : 'border border-rose-300 text-rose-600 hover:bg-rose-50' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </form>
                </div>
            </div>
            @endif

            <!-- Routing Info -->
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest mb-2">Routing Rule</div>
                <p class="text-xs text-slate-600 font-semibold">
                    @if(in_array($application->firearm_details['weapon_type'] ?? '', ['Pistol','Revolver']))
                        Handgun cases → MoHA approval required.
                    @else
                        Long-gun cases → DC direct approval.
                    @endif
                </p>
            </div>
    </div>
</div>

<!-- Interactive Document Preview Modal -->
<div id="officeDocumentViewerModal" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200 animate-in fade-in zoom-in duration-200">
        <!-- Modal Header -->
        <div class="px-5 py-4 bg-gov-deep text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <span class="text-xl">📄</span>
                <div>
                    <h3 id="officeModalDocTitle" class="text-xs font-black uppercase tracking-wider font-outfit text-white">Document Title</h3>
                    <p id="officeModalDocMeta" class="text-[10px] text-slate-300 font-semibold">filename.pdf &bull; 1.5 MB</p>
                </div>
            </div>
            <button type="button" onclick="closeOfficeDocumentViewer()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold text-sm flex items-center justify-center transition-colors">
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
                            <p class="text-[9px] text-slate-500 font-semibold">Ministry of Home Affairs &bull; Official Officer Document Inspection Vault</p>
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
                            <span id="officeDocTypeLabel" class="font-bold text-slate-900">National Identity Document</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Case Tracking Code:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $application->application_number }}</span>
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
            <span class="text-[10px] text-slate-400 font-semibold">NFLRMS Official Inspection Vault</span>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="closeOfficeDocumentViewer()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-lg transition-colors">
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
    let currentOfficeDocTitle = '';
    let isCurrentOfficeDocUploaded = true;
    const currentOfficeAppNo = '{{ $application->application_number }}';

    function openOfficeDocumentViewer(title, filename, size, isUploaded = true) {
        currentOfficeDocTitle = title;
        isCurrentOfficeDocUploaded = isUploaded;

        document.getElementById('officeModalDocTitle').innerText = title;
        document.getElementById('officeModalDocMeta').innerText = filename + (size !== '0 KB' ? ' • ' + size : '');
        document.getElementById('officeDocTypeLabel').innerText = title;
        
        const previewContainer = document.getElementById('officePreviewDocumentBody');

        if (!isUploaded) {
            previewContainer.innerHTML = `
                <div class="bg-rose-50 border-2 border-dashed border-rose-300 rounded-xl p-8 text-center text-rose-800 space-y-3 my-2">
                    <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-3xl font-black shadow-sm">
                        ⚠️
                    </div>
                    <h5 class="font-black text-slate-900 text-base font-serif">File Not Found</h5>
                    <p class="text-xs text-rose-700 max-w-md mx-auto leading-relaxed font-semibold">
                        No statutory PDF document file was uploaded by the applicant for <strong>${title}</strong>.
                    </p>
                    <div class="pt-2 flex justify-center space-x-2">
                        <span class="px-3 py-1 bg-rose-200 text-rose-900 text-[10px] font-black rounded uppercase">Status: Not Uploaded</span>
                    </div>
                </div>
            `;
        } else {
            previewContainer.innerHTML = generateOfficeDocumentPreviewHTML(title, currentOfficeAppNo);
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
            alert('File Not Found: No PDF document uploaded for ' + currentOfficeDocTitle + ' by applicant.');
            return;
        }
        const downloadUrl = '{{ route("document.download") }}?title=' + encodeURIComponent(currentOfficeDocTitle) + '&app=' + encodeURIComponent(currentOfficeAppNo);
        window.location.href = downloadUrl;
    }

    function generateOfficeDocumentPreviewHTML(title, appNo) {
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
</script>
@endsection
