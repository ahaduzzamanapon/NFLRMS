@extends('layouts.app')

@section('title', auth()->user()->hasRole(\App\Enums\Role::DealerApplicant) ? 'Dealer License Application' : 'New Firearm License Application')

@section('content')
@php $isDealer = auth()->user()->hasRole(\App\Enums\Role::DealerApplicant) || auth()->user()->hasRole('dealer_applicant'); @endphp
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Title and Subtitle -->
    <div>
        <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">
            {{ $isDealer ? 'Dealer Arms License Application' : 'New Firearm License Application' }}
        </h2>
        <p class="text-xs text-slate-500 mt-1 font-normal">
            {{ $isDealer ? 'Dealer/Stock authorization — Appendix B, BRS §7.2 · Class A / B / C' : 'Fields correspond to the official Arms License Application (Appendix A, BRS §7.1)' }}
        </p>
    </div>

    <!-- Stepper Navigation Header -->
    <div class="flex flex-wrap items-center justify-between gap-2 bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm text-[10px] font-semibold">
        <div class="flex items-center space-x-1.5 step-indicator" data-step="1">
            <span class="w-5 h-5 rounded-full bg-gov-green text-white flex items-center justify-center font-bold text-[9px] step-number">1</span>
            <span class="text-slate-900 step-label">Service</span>
        </div>
        <span class="text-slate-300 hidden sm:inline">&mdash;</span>

        <div class="flex items-center space-x-1.5 step-indicator" data-step="2">
            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number">2</span>
            <span class="text-slate-500 step-label">Applicant</span>
        </div>
        <span class="text-slate-300 hidden sm:inline">&mdash;</span>

        <div class="flex items-center space-x-1.5 step-indicator" data-step="3">
            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number">3</span>
            <span class="text-slate-500 step-label">Address & Income</span>
        </div>
        <span class="text-slate-300 hidden sm:inline">&mdash;</span>

        <div class="flex items-center space-x-1.5 step-indicator" data-step="4">
            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number">4</span>
            <span class="text-slate-500 step-label">Declarations</span>
        </div>
        <span class="text-slate-300 hidden sm:inline">&mdash;</span>

        <div class="flex items-center space-x-1.5 step-indicator" data-step="5">
            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number">5</span>
            <span class="text-slate-500 step-label">Documents</span>
        </div>
        <span class="text-slate-300 hidden sm:inline">&mdash;</span>

        <div class="flex items-center space-x-1.5 step-indicator" data-step="6">
            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number">6</span>
            <span class="text-slate-500 step-label">Review & Submit</span>
        </div>
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

    <!-- Profile Incomplete Error -->
    <div id="profile-incomplete-error" class="hidden p-3.5 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg font-normal flex items-center space-x-2">
        <span>⚠️</span>
        <span id="profile-incomplete-error-text">Complete your profile first — the highlighted field(s) above are missing.</span>
    </div>

    <!-- Multi-Step Form Wrapper -->
    <form action="{{ route('citizen.apply') }}" method="POST" id="apply-multi-form" enctype="multipart/form-data" class="space-y-6" novalidate>
        @csrf

        <!-- STEP 1: SERVICE -->
        <div class="step-panel bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6" id="panel-1">
            <div>
                <label for="service_type" class="block text-[10px] font-semibold uppercase text-slate-455 mb-1.5">Service Type</label>
                <select name="service_type" id="service_type" onchange="onServiceTypeChanged(this.value)"
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('service_type') ? 'border-rose-400' : 'border-slate-200' }} bg-white focus:ring-1 focus:ring-gov-green focus:border-transparent outline-none">
                    @if($isDealer)
                        <option value="dealer_class_a">Class A — Manufacture &amp; Dealing (MoHA Approval)</option>
                        <option value="dealer_class_b">Class B — Wholesale Dealing only (MoHA Approval)</option>
                        <option value="dealer_class_c">Class C — Retail Dealing only (DC Approval)</option>
                    @else
                        <option value="long">New License &mdash; Long Gun (Shotgun/Rifle) &bull; DC Approval</option>
                        <option value="handgun">New License &mdash; Handgun (Pistol/Revolver) &bull; MoHA Approval</option>
                    @endif
                </select>
                @error('service_type')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="1">{{ $message }}</span>@enderror
            </div>

            @if(!$isDealer)
            <div>
                <label class="block text-[10px] font-semibold uppercase text-slate-900 mb-1.5">Weapon Category</label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="selectWeapon('Pistol')" id="btn-pistol" data-service-type="handgun" class="hidden py-2.5 rounded-lg border text-center text-xs font-semibold transition-all focus:outline-none border-slate-200 hover:bg-slate-50">Pistol</button>
                    <button type="button" onclick="selectWeapon('Revolver')" id="btn-revolver" data-service-type="handgun" class="hidden py-2.5 rounded-lg border text-center text-xs font-semibold transition-all focus:outline-none border-slate-200 hover:bg-slate-50">Revolver</button>
                    <button type="button" onclick="selectWeapon('Shotgun')" id="btn-shotgun" data-service-type="long" class="py-2.5 rounded-lg border-2 text-center text-xs font-semibold transition-all focus:outline-none border-gov-green text-gov-green bg-emerald-50/10">Shotgun</button>
                    <button type="button" onclick="selectWeapon('Rifle')" id="btn-rifle" data-service-type="long" class="py-2.5 rounded-lg border text-center text-xs font-semibold transition-all focus:outline-none border-slate-200 hover:bg-slate-50">Rifle</button>
                </div>
                <input type="hidden" name="weapon_type" id="weapon_type" value="Shotgun">
            </div>

            <div>
                <label for="bore" class="block text-[10px] font-semibold uppercase text-slate-900 mb-1.5">Bore / Caliber / Size</label>
                <select name="bore" id="bore"
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('bore') ? 'border-rose-400' : 'border-slate-200' }} bg-white focus:ring-1 focus:ring-gov-green focus:border-transparent outline-none">
                    <option value="12 Bore">12 Bore (Shotgun)</option>
                    <option value=".32 Caliber">.32 Caliber (Pistol/Revolver)</option>
                    <option value=".22 Bore">.22 Bore (Rifle)</option>
                </select>
                @error('bore')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="1">{{ $message }}</span>@enderror
            </div>

            <div>
                <label for="dealer_name" class="block text-[10px] font-semibold uppercase text-slate-900 mb-1.5">Licensed Arms Dealer / Sourcing Store (যার নিকট হইতে অস্ত্র ক্রয়/সংগ্রহ করা হইবে)</label>
                <select name="dealer_name" id="dealer_name" onchange="const selectedOpt = this.options[this.selectedIndex]; document.getElementById('dealer_id_input').value = selectedOpt.getAttribute('data-id') || '';"
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('dealer_name') ? 'border-rose-400' : 'border-slate-200' }} bg-white focus:ring-1 focus:ring-gov-green focus:border-transparent outline-none font-semibold text-slate-800">
                    <option value="M/S Metropolitan Arms Store (Govt. Reg #AD-1029)" data-id="">M/S Metropolitan Arms Store (Govt. Reg #AD-1029)</option>
                    <option value="M/S Bengal Firearms & Ammunition Depot (Reg #AD-4891)" data-id="">M/S Bengal Firearms & Ammunition Depot (Reg #AD-4891)</option>
                    <option value="M/S Rangpur Central Arms Dealer Ltd. (Reg #AD-3021)" data-id="">M/S Rangpur Central Arms Dealer Ltd. (Reg #AD-3021)</option>
                    <option value="M/S National Armoury & Defense Supplies (Reg #AD-5920)" data-id="">M/S National Armoury & Defense Supplies (Reg #AD-5920)</option>
                    @if(isset($dealers) && count($dealers) > 0)
                        @foreach($dealers as $d)
                            <option value="{{ $d->name }} (Reg Dealer)" data-id="{{ $d->id }}">{{ $d->name }} (Reg Dealer #{{ $d->id }})</option>
                        @endforeach
                    @endif
                </select>
                <input type="hidden" name="dealer_id" id="dealer_id_input" value="">
                @error('dealer_name')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="1">{{ $message }}</span>@enderror
                @error('dealer_id')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="1">{{ $message }}</span>@enderror
            </div>
            @else
            <!-- Dealer-specific fields -->
            <div>
                <label class="block text-[10px] font-semibold uppercase text-slate-900 mb-1.5">Stock Category</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" onclick="selectWeapon('Handgun')"
                            id="btn-pistol"
                            class="py-2.5 rounded-lg border-2 text-center text-xs font-semibold transition-all focus:outline-none border-gov-green text-gov-green bg-emerald-50/10">Handguns</button>
                    <button type="button" onclick="selectWeapon('LongGun')"
                            id="btn-shotgun"
                            class="py-2.5 rounded-lg border text-center text-xs font-semibold transition-all focus:outline-none border-slate-200 hover:bg-slate-50">Long Guns</button>
                    <button type="button" onclick="selectWeapon('All')"
                            id="btn-rifle"
                            class="py-2.5 rounded-lg border text-center text-xs font-semibold transition-all focus:outline-none border-slate-200 hover:bg-slate-50">All Categories</button>
                </div>
                <input type="hidden" name="weapon_type" id="weapon_type" value="Handgun">
            </div>

            <div>
                <label for="bore" class="block text-[10px] font-semibold uppercase text-slate-900 mb-1.5">Licensed Stock Quantity (annual quota)</label>
                <select name="bore" id="bore"
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('bore') ? 'border-rose-400' : 'border-slate-200' }} bg-white focus:ring-1 focus:ring-gov-green focus:border-transparent outline-none">
                    <option value="Up to 50 units">Up to 50 units/year</option>
                    <option value="Up to 200 units">Up to 200 units/year</option>
                    <option value="Unlimited">Unlimited (Ministry approval required)</option>
                </select>
                @error('bore')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="1">{{ $message }}</span>@enderror
            </div>
            @endif

            <!-- Fee Preview -->
            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                <span class="text-[9px] font-semibold uppercase text-slate-900">Fee Preview</span>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-semibold">Statutory license fee</span>
                    <span class="font-semibold text-slate-800" id="fee-statutory">
                        @if($isDealer) BDT 1,00,000 @else BDT 40,000 @endif
                    </span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-semibold">Platform service charge</span>
                    <span class="font-semibold text-slate-800" id="fee-platform">BDT 850</span>
                </div>
                <div class="flex justify-between items-center text-xs border-t border-slate-200 pt-2 mt-1">
                    <span class="text-slate-800 font-semibold">Total payable on approval</span>
                    <span class="font-bold text-gov-green" id="fee-total">
                        @if($isDealer) BDT 1,00,850 @else BDT 40,850 @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- STEP 2: APPLICANT -->
        <div class="step-panel hidden bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4" id="panel-2">
            <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-xl font-normal space-y-1">
                <span class="block text-sm font-bold font-serif">ℹ️ Pulled from your Profile</span>
                <p class="font-semibold">
                    Fields below are pulled from your <span class="font-semibold">Profile</span> and can't be edited here. To change them, update your Profile first.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name_bn" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Full Name (Bengali) <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="name_bn" disabled required value="{{ strtoupper(auth()->user()->name_bn ?? '') }}"
                           placeholder="বাংলায় পূর্ণ নাম লিখুন"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('name_bn') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="name_bn" value="{{ strtoupper(auth()->user()->name_bn ?? '') }}">
                    @error('name_bn')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="name_en" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Full Name (English, Block Letters) <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="name_en" disabled required value="{{ strtoupper(auth()->user()->name) }}"
                           placeholder="FULL NAME IN BLOCK LETTERS"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('name') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="name" value="{{ strtoupper(auth()->user()->name) }}">
                    @error('name')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nid" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">National ID (NID) <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="nid" disabled required value="{{ auth()->user()->nid ?? '' }}"
                           placeholder="10 or 17 digit NID number"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('nid') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="nid" value="{{ auth()->user()->nid ?? '' }}">
                    @error('nid')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="dob" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Date of Birth <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    @php $dobFormatted = auth()->user()->dob ? \Carbon\Carbon::parse(auth()->user()->dob)->format('Y-m-d') : ''; @endphp
                    <input type="date" id="dob" disabled required value="{{ $dobFormatted }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('dob') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="dob" value="{{ $dobFormatted }}">
                    @error('dob')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="father_name" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Father's Name <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="father_name" disabled required value="{{ auth()->user()->father_name }}"
                           placeholder="Father's full name"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('father_name') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="father_name" value="{{ auth()->user()->father_name }}">
                    @error('father_name')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="mother_name" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Mother's Name <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="mother_name" disabled required value="{{ auth()->user()->mother_name }}"
                           placeholder="Mother's full name"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('mother_name') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="mother_name" value="{{ auth()->user()->mother_name }}">
                    @error('mother_name')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="marital_status" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Marital Status <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <select id="marital_status" disabled required
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('marital_status') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                        <option value="" {{ !auth()->user()->marital_status ? 'selected' : '' }}>Select</option>
                        <option value="Married" {{ auth()->user()->marital_status === 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Single" {{ auth()->user()->marital_status === 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Divorced" {{ auth()->user()->marital_status === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="Widowed" {{ auth()->user()->marital_status === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                    </select>
                    <input type="hidden" name="marital_status" value="{{ auth()->user()->marital_status }}">
                    @error('marital_status')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div>
                <div id="spouse-group">
                    <label for="spouse_name" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Spouse Name <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="spouse_name" disabled required value="{{ auth()->user()->spouse_name }}"
                           placeholder="Spouse's full name"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('spouse_name') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="spouse_name" value="{{ auth()->user()->spouse_name }}">
                    @error('spouse_name')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- <div>
                    <label for="nationality" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Nationality <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="nationality" disabled required value="{{ auth()->user()->nationality }}"
                           placeholder="e.g. Bangladeshi"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('nationality') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="nationality" value="{{ auth()->user()->nationality }}">
                    @error('nationality')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div> --}}
                <div>
                    <label for="religion" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Religion <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="religion" disabled required value="{{ auth()->user()->religion }}"
                           placeholder="e.g. Islam, Hindu, Christian"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('religion') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="religion" value="{{ auth()->user()->religion }}">
                    @error('religion')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="2">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- STEP 3: ADDRESS & INCOME -->
        <div class="step-panel hidden bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4" id="panel-3">
            <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-xl font-normal space-y-1">
                <span class="block text-sm font-bold font-serif">ℹ️ Pulled from your Profile</span>
                <p class="font-semibold">
                    Fields below are pulled from your <span class="font-semibold">Profile</span> and can't be edited here. To change them, update your Profile first.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="present_address" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Present Address <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="present_address" disabled required value="{{ auth()->user()->present_address }}"
                           placeholder="House No, Road, Area, City"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('present_address') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="present_address" value="{{ auth()->user()->present_address }}">
                    @error('present_address')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="3">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="permanent_address" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Permanent Address <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="permanent_address" disabled required value="{{ auth()->user()->permanent_address }}"
                           placeholder="Village, Thana, District"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('permanent_address') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="permanent_address" value="{{ auth()->user()->permanent_address }}">
                    @error('permanent_address')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="3">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="district_id" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">District <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <select id="district_id" disabled required
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('district_id') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                        <option value="">Select District</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ auth()->user()->district_id === $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="district_id" value="{{ auth()->user()->district_id }}">
                    @error('district_id')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="3">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="upazila_id" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Upazila / Thana <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <select id="upazila_id" disabled required
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('upazila_id') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                        <option value="">Select Upazila / Thana</option>
                    </select>
                    <input type="hidden" name="upazila_id" value="{{ auth()->user()->upazila_id }}">
                    @error('upazila_id')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="3">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edu_qualification" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Educational Qualification <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="edu_qualification" disabled required value="{{ auth()->user()->edu_qualification }}"
                           placeholder="e.g. HSC, Bachelor's, MBA"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('edu_qualification') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="edu_qualification" value="{{ auth()->user()->edu_qualification }}">
                    @error('edu_qualification')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="3">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="occupation" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Occupation <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="occupation" disabled required value="{{ auth()->user()->occupation }}"
                           placeholder="e.g. Business Owner, Government Officer"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('occupation') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="occupation" value="{{ auth()->user()->occupation }}">
                    @error('occupation')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="3">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="employer_address" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Employer / Office Address <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="employer_address" disabled required value="{{ auth()->user()->employer_address }}"
                           placeholder="Office/employer address"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('employer_address') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="employer_address" value="{{ auth()->user()->employer_address }}">
                    @error('employer_address')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="3">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="annual_income" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">Annual Income (BDT) <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="number" id="annual_income" disabled required value="{{ auth()->user()->annual_income }}"
                           placeholder="e.g. 500000"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('annual_income') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed">
                    <input type="hidden" name="annual_income" value="{{ auth()->user()->annual_income }}">
                    @error('annual_income')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="3">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tin_number" class="block text-[10px] font-semibold uppercase text-slate-400 mb-1.5">TIN Number <span class="inline-block align-middle ml-1 px-1.5 py-0.5 rounded-full border border-gov-green text-gov-green bg-emerald-50/10 text-[9px] font-bold normal-case">From Profile</span></label>
                    <input type="text" id="tin_number" disabled required value="{{ auth()->user()->tin_number }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('tin_number') ? 'border-rose-400' : 'border-slate-200' }} outline-none bg-white disabled:bg-slate-100 disabled:text-slate-900 disabled:cursor-not-allowed"
                           placeholder="12-digit TIN Code">
                    <input type="hidden" name="tin_number" value="{{ auth()->user()->tin_number }}">
                    @error('tin_number')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="3">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- STEP 4: DECLARATIONS -->
        <div class="step-panel hidden bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4" id="panel-4">
            <div>
                <label for="purpose" class="block text-[10px] font-semibold uppercase text-slate-455 mb-1.5">Justification / Purpose of License</label>
                <textarea name="purpose" id="purpose" required rows="3"
                          placeholder="Describe the reason you are applying for a firearm license..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('purpose') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white"></textarea>
                @error('purpose')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="4">{{ $message }}</span>@enderror
            </div>

            <!-- Declarations checkboxes list -->
            <div class="space-y-3 pt-2">
                <div>
                    <label class="flex items-start space-x-2.5 p-3 rounded-lg border {{ $errors->has('declare_no_duty_free_import') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-100' }} hover:bg-slate-50 cursor-pointer js-error-wrapper" data-wrapper-for="declare_no_duty_free_import">
                        <input type="checkbox" name="declare_no_duty_free_import" id="declare_no_duty_free_import" value="1" required checked
                               data-required-message="You must confirm this to continue." class="rounded text-gov-green focus:ring-0 mt-0.5">
                        <span class="text-xs text-slate-600 font-semibold leading-normal">I confirm no prior duty-free weapon import</span>
                    </label>
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="declare_no_duty_free_import"></span>
                    @error('declare_no_duty_free_import')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="4">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="flex items-start space-x-2.5 p-3 rounded-lg border {{ $errors->has('declare_no_cancellation_history') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-100' }} hover:bg-slate-50 cursor-pointer js-error-wrapper" data-wrapper-for="declare_no_cancellation_history">
                        <input type="checkbox" name="declare_no_cancellation_history" id="declare_no_cancellation_history" value="1" required checked
                               data-required-message="You must confirm this to continue." class="rounded text-gov-green focus:ring-0 mt-0.5">
                        <span class="text-xs text-slate-600 font-semibold leading-normal">I have no prior license cancellation history</span>
                    </label>
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="declare_no_cancellation_history"></span>
                    @error('declare_no_cancellation_history')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="4">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="flex items-start space-x-2.5 p-3 rounded-lg border {{ $errors->has('declare_no_other_license') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-100' }} hover:bg-slate-50 cursor-pointer js-error-wrapper" data-wrapper-for="declare_no_other_license">
                        <input type="checkbox" name="declare_no_other_license" id="declare_no_other_license" value="1" required checked
                               data-required-message="You must confirm this to continue." class="rounded text-gov-green focus:ring-0 mt-0.5">
                        <span class="text-xs text-slate-600 font-semibold leading-normal">I hold no other firearm license (notarized affidavit uploaded)</span>
                    </label>
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="declare_no_other_license"></span>
                    @error('declare_no_other_license')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="4">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="flex items-start space-x-2.5 p-3 rounded-lg border {{ $errors->has('declare_no_criminal_history') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-100' }} hover:bg-slate-50 cursor-pointer js-error-wrapper" data-wrapper-for="declare_no_criminal_history">
                        <input type="checkbox" name="declare_no_criminal_history" id="declare_no_criminal_history" value="1" required checked
                               data-required-message="You must confirm this to continue." class="rounded text-gov-green focus:ring-0 mt-0.5">
                        <span class="text-xs text-slate-600 font-semibold leading-normal">I have no criminal case history</span>
                    </label>
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="declare_no_criminal_history"></span>
                    @error('declare_no_criminal_history')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="4">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- STEP 5: DOCUMENTS -->
        <div class="step-panel hidden bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4" id="panel-5">
            <span class="text-[9px] font-semibold uppercase text-slate-400">PDF/JPG/PNG &bull; Max 5MB per file. Documents are stored encrypted.</span>

            <div class="divide-y divide-slate-100 text-xs">
                @php
                    $docs = [
                        'nid_copy' => 'National ID copy',
                        'tin_certificate' => 'TIN certificate',
                        'birth_cert' => 'Birth certificate',
                        'edu_cert' => 'Educational certificate',
                        'tax_yr1' => 'Income-tax return &bull; Year 1',
                        'tax_yr2' => 'Income-tax return &bull; Year 2',
                        'tax_yr3' => 'Income-tax return &bull; Year 3',
                        'affidavit' => 'Notarized affidavit (BDT 300 stamp, with photo)',
                        'nationality_cert' => 'Nationality certificate',
                        'photo' => 'Recent passport-size photograph'
                    ];
                    $requiredDocs = ['nid_copy', 'tin_certificate'];
                @endphp
                @foreach($docs as $key => $label)
                    <div class="py-2.5 px-2 -mx-2 rounded-lg border {{ $errors->has($key) ? '!border-rose-400 !border-b bg-rose-50/40' : 'border-transparent' }} js-error-wrapper" data-wrapper-for="{{ $key }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span>📄</span>
                                <span class="font-semibold text-slate-800">{!! $label !!}</span>
                                @if(in_array($key, $requiredDocs))
                                    <span class="text-rose-500 font-bold" title="Required">*</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-3 text-[10px]">
                                <span id="status-{{ $key }}" class="text-amber-600 font-semibold">⚠️ Not uploaded</span>
                                <input type="file" name="{{ $key }}" id="file-{{ $key }}" class="hidden" {{ in_array($key, $requiredDocs) ? 'required' : '' }}
                                       data-required-message="This document is required." onchange="handleFileSelected('{{ $key }}')">
                                <button type="button" onclick="triggerUpload('{{ $key }}')" id="btn-{{ $key }}" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold border border-slate-200/50 transition-colors {{ $errors->has($key) ? 'ring-2 ring-rose-400' : '' }}">Upload</button>
                            </div>
                        </div>
                        <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block text-right js-error" data-for="{{ $key }}"></span>
                        @error($key)<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block text-right" data-error-step="5">{{ $message }}</span>@enderror
                    </div>
                @endforeach
            </div>
        </div>

        <!-- STEP 6: REVIEW & SUBMIT -->
        <div class="step-panel hidden space-y-5" id="panel-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-semibold text-slate-900 font-serif">Review your submission</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Service card -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-[8px] font-semibold uppercase text-slate-400 block mb-1">Service</span>
                        <div class="font-semibold text-slate-900 leading-tight" id="review-service">New License &mdash; Long Gun</div>
                        <div class="text-[10px] text-slate-500 font-semibold mt-1" id="review-weapon">Shotgun</div>
                    </div>
                    <!-- Applicant card (populated dynamically from form inputs) -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-[8px] font-semibold uppercase text-slate-400 block mb-1">Applicant</span>
                        <div class="font-semibold text-slate-900 leading-tight" id="review-name">—</div>
                        <div class="text-[10px] text-slate-500 font-semibold mt-1" id="review-nid">—</div>
                    </div>
                    <!-- Address card (populated dynamically) -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-[8px] font-semibold uppercase text-slate-400 block mb-1">Address</span>
                        <div class="font-semibold text-slate-900 leading-tight" id="review-address">—</div>
                        <div class="text-[10px] text-slate-500 font-semibold mt-1" id="review-district">—</div>
                    </div>
                    <!-- Fee card -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-[8px] font-semibold uppercase text-slate-400 block mb-1">Fee</span>
                        <div class="font-bold text-gov-green text-sm mt-1" id="review-fee">BDT 40,850</div>
                    </div>
                    <!-- Sourcing Dealer card -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg sm:col-span-2">
                        <span class="text-[8px] font-semibold uppercase text-slate-900 block mb-1">Licensed Arms Dealer / Sourcing Store</span>
                        <div class="font-semibold text-emerald-800 leading-tight" id="review-dealer">M/S Metropolitan Arms Store</div>
                    </div>
                </div>

                <!-- Final Declaration Checkbox -->
                <div class="js-error-wrapper" data-wrapper-for="final_declaration">
                    <label class="flex items-start space-x-2.5 p-4 bg-slate-50 border {{ $errors->has('final_declaration') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200' }} rounded-lg cursor-pointer">
                        <input type="checkbox" name="final_declaration" id="final_declaration" value="1" required
                               data-required-message="You must accept this declaration to continue." class="rounded text-gov-green focus:ring-0 mt-0.5">
                        <div class="text-[11px] leading-relaxed text-slate-600 font-semibold">
                            I declare that the information provided is true and correct. I understand that false statements will render the license void.
                            <p class="text-[9px] text-slate-400 mt-1 font-medium">Digital consent + OTP is legally equivalent to wet-ink signature.</p>
                    </div>
                    </label>
                    <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error" data-for="final_declaration"></span>
                    @error('final_declaration')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block" data-error-step="6">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Wizard Navigation Bar -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-200">
            <button type="button" id="btn-prev" onclick="prevStep()" disabled
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200/80 text-slate-500 text-xs font-semibold rounded-lg focus:outline-none transition-colors">
                &larr; Previous
            </button>
            <button type="button" id="btn-next" onclick="nextStep()"
                    class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white text-xs font-semibold rounded-lg focus:outline-none transition-colors">
                Continue &rarr;
            </button>
            <button type="submit" id="btn-submit" class="hidden px-6 py-2.5 bg-gov-amber hover:bg-amber-500 text-slate-950 font-bold text-xs rounded-lg transition-colors shadow-md">
                Submit Application
            </button>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 6;

    function setWeaponButtonClass(btn, className) {
        const isHidden = btn.classList.contains('hidden');
        btn.className = className;
        if (isHidden) {
            btn.classList.add('hidden');
        }
    }

    function selectWeapon(weapon) {
        document.getElementById('weapon_type').value = weapon;

        // Reset styles
        ['pistol', 'revolver', 'shotgun', 'rifle'].forEach(w => {
            const btn = document.getElementById(`btn-${w}`);
            if (btn) {
                setWeaponButtonClass(btn, 'py-2.5 rounded-lg border text-center text-xs font-semibold transition-all focus:outline-none border-slate-200 hover:bg-slate-50');
            }
        });

        // Style selected
        const selectedBtn = document.getElementById(`btn-${weapon.toLowerCase()}`);
        if(selectedBtn) {
            setWeaponButtonClass(selectedBtn, 'py-2.5 rounded-lg border-2 text-center text-xs font-semibold transition-all focus:outline-none border-gov-green text-gov-green bg-emerald-50/10');
        }

        // Set default bore based on weapon category
        const boreSelect = document.getElementById('bore');
        if (boreSelect) {
            if (weapon === 'Pistol' || weapon === 'Revolver') {
                boreSelect.value = '.32 Caliber';
            } else if (weapon === 'Shotgun') {
                boreSelect.value = '12 Bore';
            } else if (weapon === 'Rifle') {
                boreSelect.value = '.22 Bore';
            }
        }

        recalculateFees();
    }

    function onServiceTypeChanged(val) {
        // Show/hide weapon category buttons based on service type (citizen only)
        ['pistol', 'revolver', 'shotgun', 'rifle'].forEach(w => {
            const btn = document.getElementById(`btn-${w}`);
            if (btn && btn.dataset.serviceType) {
                btn.classList.toggle('hidden', btn.dataset.serviceType !== val);
            }
        });

        // Automatically default selected weapon based on service tier
        // (only for citizen service types; dealer class values are untouched)
        if (val === 'long') {
            selectWeapon('Shotgun');
        } else if (val === 'handgun') {
            selectWeapon('Pistol');
        }
    }

    function recalculateFees() {
        const serviceType = document.getElementById('service_type').value;
        const weapon = document.getElementById('weapon_type').value;

        // Long gun = 40,000 BDT, Handgun = 60,000 BDT
        const statutory = (serviceType === 'handgun') ? 60000 : 40000;
        const platform = 850;
        const total = statutory + platform;

        document.getElementById('fee-statutory').innerText = `BDT ${statutory.toLocaleString()}`;
        document.getElementById('fee-total').innerText = `BDT ${total.toLocaleString()}`;

        document.getElementById('review-service').innerText = serviceType === 'handgun' ? 'New License — Handgun' : 'New License — Long Gun';
        document.getElementById('review-weapon').innerText = weapon;
        document.getElementById('review-fee').innerText = `BDT ${total.toLocaleString()}`;
        document.getElementById('review-dealer').innerText = document.getElementById('dealer_name')?.value || 'M/S Metropolitan Arms Store';
    }

    function toggleSpouse(val) {
        const showSpouse = ['Married', 'Divorced', 'Widowed'].includes(val);
        document.getElementById('spouse-group').style.display = showSpouse ? 'block' : 'none';
    }

    function updateStepIndicator() {
        document.querySelectorAll('.step-indicator').forEach(el => {
            const stepNum = parseInt(el.getAttribute('data-step'));
            const numSpan = el.querySelector('.step-number');
            const sisterLabel = el.querySelector('.step-label');

            if (!numSpan || !sisterLabel) {
                return;
            }

            if (stepNum < currentStep) {
                numSpan.className = 'w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[9px] step-number';
                numSpan.innerText = '✓';
                sisterLabel.className = 'text-slate-400 step-label';
            } else if (stepNum === currentStep) {
                numSpan.className = 'w-5 h-5 rounded-full bg-gov-green text-white flex items-center justify-center font-bold text-[9px] step-number';
                numSpan.innerText = stepNum;
                sisterLabel.className = 'text-slate-900 step-label';
            } else {
                numSpan.className = 'w-5 h-5 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[9px] step-number';
                numSpan.innerText = stepNum;
                sisterLabel.className = 'text-slate-550 step-label';
            }
        });
    }

    function showStepPanel() {
        document.querySelectorAll('.step-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById(`panel-${currentStep}`).classList.remove('hidden');

        document.getElementById('btn-prev').disabled = currentStep === 1;

        if (currentStep === totalSteps) {
            document.getElementById('btn-next').classList.add('hidden');
            document.getElementById('btn-submit').classList.remove('hidden');

            // Sync form values into review card
            document.getElementById('review-name').innerText = document.getElementById('name_en').value;
            document.getElementById('review-nid').innerText = `NID ${document.getElementById('nid').value} &bull; DOB ${document.getElementById('dob').value}`;

            const distSelect = document.getElementById('district_id');
            const selectedDistText = distSelect.options[distSelect.selectedIndex] ? distSelect.options[distSelect.selectedIndex].text : '';
            document.getElementById('review-address').innerText = document.getElementById('present_address').value;
            document.getElementById('review-district').innerText = `${selectedDistText} District`;

            recalculateFees();
        } else {
            document.getElementById('btn-next').classList.remove('hidden');
            document.getElementById('btn-submit').classList.add('hidden');
        }
    }

    // Field IDs that also exist on the Profile page. Used only to decide
    // WHICH message to show when a required field is left empty.
    const profileMatchedFieldIds = new Set([
        'name_bn', 'name_en', 'nid', 'dob', 'father_name', 'mother_name', 'marital_status', 'spouse_name',
        'nationality', 'religion', 'present_address', 'permanent_address',
        'district_id', 'upazila_id', 'edu_qualification', 'occupation',
        'employer_address', 'annual_income', 'tin_number'
    ]);

    // Checks EVERY [required] field inside the current step's panel — not just
    // profile-matching ones. If anything required is empty/unchecked, the user
    // cannot move to the next tab.
    function validateStepFields(step) {
        const panel = document.getElementById(`panel-${step}`);
        if (!panel) return true;

        const fields = panel.querySelectorAll('[required]');
        let firstInvalidEl = null;
        let isProfileField = false;

        fields.forEach(el => {
            // Skip fields that are currently hidden because a conditional
            // section is collapsed (e.g. spouse name when Single). File
            // inputs are always styled `hidden` by design (a custom Upload
            // button drives them), so they're deliberately excluded from
            // this skip — they must still be validated.
            if (el.offsetParent === null && el.type !== 'file') {
                el.classList.remove('border-rose-400');
                return;
            }

            const filled = (el.type === 'checkbox' || el.type === 'radio')
                ? el.checked
                : el.value !== null && el.value.trim() !== '';

            const uploadBtn = el.type === 'file' ? document.getElementById(`btn-${el.name}`) : null;

            // Checkboxes and file inputs render without a visible border on
            // the control itself, so highlight the surrounding card instead,
            // and surface a live message in that field's .js-error span.
            const wrapper = (el.type === 'checkbox' || el.type === 'file')
                ? document.querySelector(`.js-error-wrapper[data-wrapper-for="${el.name}"]`)
                : null;
            const wrapperLabelBorder = el.type === 'checkbox' ? wrapper?.querySelector('label') : null;
            const errorSpan = document.querySelector(`.js-error[data-for="${el.name}"]`);
            const requiredMessage = el.dataset.requiredMessage || 'This field is required.';

            if (!filled) {
                el.classList.add('border-rose-400');
                if (uploadBtn) uploadBtn.classList.add('ring-2', 'ring-rose-400');
                if (errorSpan) errorSpan.textContent = requiredMessage;
                if (wrapper) {
                    if (el.type === 'checkbox') {
                        const isFinalDeclaration = wrapper.dataset.wrapperFor === 'final_declaration';
                        const target = wrapperLabelBorder || wrapper;
                        target.classList.remove('border-slate-100', 'border-slate-200');
                        target.classList.add('border-rose-400', 'bg-rose-50/40');
                        if (isFinalDeclaration) {
                            target.classList.remove('bg-slate-50');
                        }
                    } else {
                        wrapper.classList.add('bg-rose-50/40');
                        wrapper.classList.remove('border-transparent');
                        wrapper.classList.add('!border-rose-400', '!border-b');
                    }
                }
                if (!firstInvalidEl) {
                    firstInvalidEl = el;
                    isProfileField = profileMatchedFieldIds.has(el.id);
                }
            } else {
                el.classList.remove('border-rose-400');
                if (uploadBtn) uploadBtn.classList.remove('ring-2', 'ring-rose-400');
                if (errorSpan) errorSpan.textContent = '';
                if (wrapper) {
                    if (el.type === 'checkbox') {
                        const isFinalDeclaration = wrapper.dataset.wrapperFor === 'final_declaration';
                        const target = wrapperLabelBorder || wrapper;
                        target.classList.remove('border-rose-400', 'bg-rose-50/40');
                        target.classList.add(isFinalDeclaration ? 'border-slate-200' : 'border-slate-100');
                        if (isFinalDeclaration) {
                            target.classList.add('bg-slate-50');
                        }
                    } else {
                        wrapper.classList.remove('bg-rose-50/40', 'border-rose-400', '!border-rose-400', '!border-b');
                        wrapper.classList.add('border-transparent');
                    }
                }
            }
        });

        if (firstInvalidEl) {
            showIncompleteError(isProfileField);
            (firstInvalidEl.type === 'file' ? document.getElementById(`btn-${firstInvalidEl.name}`) : firstInvalidEl)
                ?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        return true;
    }

    function showIncompleteError(isProfileField) {
        const box = document.getElementById('profile-incomplete-error');
        const text = document.getElementById('profile-incomplete-error-text');
        if (!box) return;

        if (text) {
            text.textContent = isProfileField
                ? 'Complete your profile first — the highlighted field(s) above are missing.'
                : 'Please fill in the highlighted required field(s) above before continuing.';
        }

        box.classList.remove('hidden');
    }

    function hideIncompleteError() {
        const box = document.getElementById('profile-incomplete-error');
        if (box) box.classList.add('hidden');
    }

    function nextStep() {
        hideIncompleteError();

        if (!validateStepFields(currentStep)) {
            return;
        }

        if (currentStep < totalSteps) {
            currentStep++;
            updateStepIndicator();
            showStepPanel();
        }
    }

    function prevStep() {
        hideIncompleteError();
        if (currentStep > 1) {
            currentStep--;
            updateStepIndicator();
            showStepPanel();
        }
    }

    // Dynamic Address Geodata AJAX Loader
    document.getElementById('district_id').addEventListener('change', function () {
        loadUpazilas(this.value);
    });

    function loadUpazilas(districtId, selectedUpazilaId = null) {
        const upazilaSelect = document.getElementById('upazila_id');
        upazilaSelect.innerHTML = '<option value="">Loading...</option>';
        upazilaSelect.disabled = true;

        if (!districtId) {
            upazilaSelect.innerHTML = '<option value="">Select District First</option>';
            return;
        }

        fetch(`/api/districts/${districtId}/upazilas`)
            .then(response => response.json())
            .then(data => {
                upazilaSelect.innerHTML = '<option value="">Select Upazila / Thana</option>';
                data.forEach(upazila => {
                    const selected = selectedUpazilaId && parseInt(upazila.id) === parseInt(selectedUpazilaId) ? 'selected' : '';
                    upazilaSelect.innerHTML += `<option value="${upazila.id}" ${selected}>${upazila.name}</option>`;
                });
                // upazila_id is a profile-locked field — keep it disabled even
                // after options load, since the user can't edit it here.
                upazilaSelect.disabled = true;
            })
            .catch(error => {
                console.error('Error fetching upazilas:', error);
                upazilaSelect.innerHTML = '<option value="">Error loading upazilas</option>';
            });
    }

    // Trigger on load for current user's default district
    window.addEventListener('DOMContentLoaded', () => {
        const districtSelect = document.getElementById('district_id');
        if (districtSelect && districtSelect.value) {
            loadUpazilas(districtSelect.value, "{{ auth()->user()->upazila_id }}");
        }
        recalculateFees();

        // Initialize weapon category visibility based on default service type
        // (citizen only — dealer buttons don't have data-service-type)
        const serviceTypeSelect = document.getElementById('service_type');
        if (serviceTypeSelect && document.querySelector('[data-service-type]')) {
            onServiceTypeChanged(serviceTypeSelect.value);
        }

        // Show/hide spouse name based on the pre-filled marital status
        const maritalSelect = document.getElementById('marital_status');
        if (maritalSelect) {
            toggleSpouse(maritalSelect.value);
        }

        // If the server redirected back with validation errors, jump the
        // wizard straight to the earliest step that has one, so the error
        // isn't hidden inside a step-panel the user hasn't opened yet.
        const errorMessageEls = document.querySelectorAll('[data-error-step]');
        if (errorMessageEls.length > 0) {
            const errorSteps = Array.from(errorMessageEls).map(el => parseInt(el.dataset.errorStep, 10));
            currentStep = Math.min(...errorSteps);
            updateStepIndicator();
            showStepPanel();
            errorMessageEls[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Declaration checkbox live validation (matches dealer apply page behavior)
        document.querySelectorAll('.js-error-wrapper input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', function () {
                const wrapper = document.querySelector(`.js-error-wrapper[data-wrapper-for="${this.name}"]`);
                const label = wrapper?.querySelector('label');
                const errorSpan = document.querySelector(`.js-error[data-for="${this.name}"]`);
                const requiredMessage = this.dataset.requiredMessage || 'This field is required.';
                const isFinalDeclaration = wrapper?.dataset.wrapperFor === 'final_declaration';
                const target = label || wrapper;

                if (!this.checked) {
                    this.classList.add('border-rose-400');
                    if (errorSpan) errorSpan.textContent = requiredMessage;
                    if (target) {
                        target.classList.remove('border-slate-100', 'border-slate-200');
                        target.classList.add('border-rose-400', 'bg-rose-50/40');
                        if (isFinalDeclaration) {
                            target.classList.remove('bg-slate-50');
                        }
                    }
                } else {
                    this.classList.remove('border-rose-400');
                    if (errorSpan) errorSpan.textContent = '';
                    if (target) {
                        target.classList.remove('border-rose-400', 'bg-rose-50/40');
                        target.classList.add(isFinalDeclaration ? 'border-slate-200' : 'border-slate-100');
                        if (isFinalDeclaration) {
                            target.classList.add('bg-slate-50');
                        }
                    }
                }
            });
        });

        // Form submit validation handler (prevents default browser tooltips)
        const form = document.getElementById('apply-multi-form');
        form?.addEventListener('submit', function (e) {
            if (!validateStepFields(currentStep)) {
                e.preventDefault();
            }
        });
    });

    function triggerUpload(key) {
        document.getElementById(`file-${key}`).click();
    }

    function handleFileSelected(key) {
        const fileInput = document.getElementById(`file-${key}`);
        const statusSpan = document.getElementById(`status-${key}`);
        const btn = document.getElementById(`btn-${key}`);
        const wrapper = document.querySelector(`.js-error-wrapper[data-wrapper-for="${key}"]`);
        const errorSpan = document.querySelector(`.js-error[data-for="${key}"]`);

        if (fileInput.files && fileInput.files.length > 0) {
            const fileName = fileInput.files[0].name;
            statusSpan.className = 'text-emerald-600 font-semibold';
            statusSpan.innerText = `✓ Uploaded (${fileName})`;
            btn.innerText = 'Replace';
            btn.classList.remove('ring-2', 'ring-rose-400');
            fileInput.classList.remove('border-rose-400');
            if (errorSpan) errorSpan.textContent = '';
            if (wrapper) {
                wrapper.classList.remove('border-rose-400', '!border-rose-400', '!border-b', 'bg-rose-50/40');
                wrapper.classList.add('border-transparent');
            }
        } else {
            statusSpan.className = 'text-amber-600 font-semibold';
            statusSpan.innerText = '⚠️ Not uploaded';
            btn.innerText = 'Upload';
        }
    }
</script>
@endsection
