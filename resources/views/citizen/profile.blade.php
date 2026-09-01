@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="w-full space-y-5">

    <!-- Page Header -->
    <div>
        <h2 class="text-lg font-bold text-slate-900 font-serif">My Profile</h2>
        <p class="text-[11px] text-slate-400 font-normal mt-0.5">
            Save your personal details once — they will auto-fill in every new application.
        </p>
    </div>

    <!-- Common Profile Incomplete Error Banner -->
    <div id="profile-incomplete-error" class="{{ $errors->any() ? '' : 'hidden' }} p-3.5 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg font-normal flex items-center space-x-2">
        <span><i class="fa-solid fa-triangle-exclamation"></i></span>
        <span id="profile-incomplete-error-text">Complete your profile first — the highlighted field(s) above are missing.</span>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" id="profile-form" enctype="multipart/form-data" class="space-y-4" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="active_tab" id="active_tab" value="{{ old('active_tab', session('active_tab', 'personal')) }}">

        <!-- Tab Navigation -->
        <div class="flex flex-wrap items-center justify-between gap-1.5 bg-white p-2 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex flex-wrap items-center gap-1.5">
                <button type="button" data-tab="personal" onclick="switchTab('personal')"
                        class="profile-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none bg-gov-green text-white shadow-sm">
                    <span><i class="fa-solid fa-user"></i></span><span>Personal Info</span>
                </button>
                <button type="button" data-tab="address" onclick="switchTab('address')"
                        class="profile-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
                    <span><i class="fa-solid fa-location-dot"></i></span><span>Address</span>
                </button>
                <button type="button" data-tab="education" onclick="switchTab('education')"
                        class="profile-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
                    <span><i class="fa-solid fa-graduation-cap"></i></span><span>Education & Income</span>
                </button>
                <button type="button" data-tab="security" onclick="switchTab('security')"
                        class="profile-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50">
                    <span><i class="fa-solid fa-lock"></i></span><span>Security</span>
                </button>
            </div>

            @php $missingCount = count($user->profileMissingFields()); @endphp
            @if($user->isProfileComplete())
                <span class="flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase border border-emerald-500/30 bg-emerald-50 text-emerald-700">
                    <span><i class="fa-solid fa-check"></i></span><span>Profile Complete</span>
                </span>
            @else
                <span class="flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase border border-amber-500/30 bg-amber-50 text-amber-700"
                      title="{{ $missingCount }} field(s) missing for license application">
                    <span><i class="fa-solid fa-triangle-exclamation"></i></span><span>Incomplete</span>
                </span>
            @endif
        </div>

        <!-- TAB 1: PERSONAL INFO -->
        <div class="profile-panel bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" id="panel-personal">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 bg-slate-50 flex flex-wrap justify-between items-center gap-2">
                <span class="text-[11px] font-semibold uppercase text-slate-900 tracking-widest">Personal Information</span>
                <button type="submit" data-submit-tab="personal"
                        class="px-4 sm:px-5 py-1.5 bg-gov-green hover:bg-gov-light text-white text-[11px] font-bold rounded-lg transition-colors shadow-sm">
                    Save Profile
                </button>
            </div>
            <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="sm:col-span-2 lg:col-span-3 flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                    <div id="profile-photo-preview-container" class="w-16 h-16 rounded-full overflow-hidden border-2 border-gov-green bg-slate-200 flex items-center justify-center text-slate-400 font-bold text-xl flex-shrink-0">
                        <img id="profile-photo-preview" src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : '' }}" alt="Profile Photo" class="w-full h-full object-cover {{ $user->profile_photo_path ? '' : 'hidden' }}">
                        <span id="profile-photo-initials" class="{{ $user->profile_photo_path ? 'hidden' : '' }}">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="space-y-1 min-w-0 flex-1">
                        <label for="profile_photo" class="block text-[11px] font-semibold uppercase text-slate-900">Upload Profile Photo (Passport Size)</label>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gov-green file:text-white hover:file:bg-gov-light cursor-pointer">
                        <span class="text-[10px] text-slate-500 block font-medium">Supported: JPG, PNG, WEBP (Max 2MB)</span>
                        <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="profile_photo">@error('profile_photo'){{ $message }}@enderror</span>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Full Name (English)</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('name') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="name">@error('name'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="name_bn" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Full Name (Bengali)</label>
                    <input type="text" name="name_bn" id="name_bn" value="{{ old('name_bn', $user->name_bn) }}" required
                           placeholder="বাংলায় পূর্ণ নাম লিখুন"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('name_bn') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="name_bn">@error('name_bn'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="father_name" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Father's Name</label>
                    <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $user->father_name) }}"
                           placeholder="Father's full name"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('father_name') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="father_name">@error('father_name'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="mother_name" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Mother's Name</label>
                    <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name', $user->mother_name) }}"
                           placeholder="Mother's full name"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('mother_name') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="mother_name">@error('mother_name'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="email" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('email') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="email">@error('email'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="nid" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">National ID (NID)</label>
                    <input type="text" name="nid" id="nid" value="{{ old('nid', $user->nid) }}"
                           placeholder="10 or 17 digit NID"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('nid') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="nid">@error('nid'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="dob" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Date of Birth</label>
                    @php $dobFormatted = $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : ''; @endphp
                    <input type="date" name="dob" id="dob" value="{{ old('dob', $dobFormatted) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('dob') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="dob">@error('dob'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="phone" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Mobile Number (BD 11 Digits)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" maxlength="11"
                           placeholder="01712345678"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('phone') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="phone">@error('phone'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="marital_status" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Marital Status</label>
                    <select name="marital_status" id="marital_status"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('marital_status') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                        <option value="">Select</option>
                        @foreach(['Married','Single','Divorced','Widowed'] as $ms)
                            <option value="{{ $ms }}" {{ old('marital_status', $user->marital_status) === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                        @endforeach
                    </select>
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="marital_status">@error('marital_status'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="spouse_name" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Spouse Name</label>
                    <input type="text" name="spouse_name" id="spouse_name" value="{{ old('spouse_name', $user->spouse_name) }}"
                           placeholder="Spouse full name (if married)"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('spouse_name') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="spouse_name">@error('spouse_name'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="religion" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Religion</label>
                    <input type="text" name="religion" id="religion" value="{{ old('religion', $user->religion) }}"
                           placeholder="e.g. Islam, Hindu, Christian"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('religion') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="religion">@error('religion'){{ $message }}@enderror</span>
                </div>
            </div>
        </div>

        <!-- TAB 2: ADDRESS -->
        <div class="profile-panel hidden bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" id="panel-address">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 bg-slate-50 flex flex-wrap justify-between items-center gap-2">
                <span class="text-[11px] font-semibold uppercase text-slate-900 tracking-widest">Address Information</span>
                <button type="submit" data-submit-tab="address"
                        class="px-4 sm:px-5 py-1.5 bg-gov-green hover:bg-gov-light text-white text-[11px] font-bold rounded-lg transition-colors shadow-sm">
                    Save Profile
                </button>
            </div>
            <div class="p-4 sm:p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="district_id" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">District</label>
                        <select name="district_id" id="district_id"
                                class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('district_id') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                            <option value="">Select District</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ (old('district_id', $user->district_id) == $d->id) ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="district_id">@error('district_id'){{ $message }}@enderror</span>
                    </div>
                    <div>
                        <label for="upazila_id" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Upazila / Thana</label>
                        <select name="upazila_id" id="upazila_id"
                                class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('upazila_id') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                            <option value="">Select Upazila</option>
                        </select>
                        <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="upazila_id">@error('upazila_id'){{ $message }}@enderror</span>
                    </div>
                </div>
                <div>
                    <label for="present_address" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Present Address</label>
                    <textarea name="present_address" id="present_address" rows="2"
                              placeholder="House No, Road, Area, City"
                              class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('present_address') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">{{ old('present_address', $user->present_address) }}</textarea>
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="present_address">@error('present_address'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="permanent_address" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Permanent Address</label>
                    <textarea name="permanent_address" id="permanent_address" rows="2"
                              placeholder="Village, Thana, District"
                              class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('permanent_address') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">{{ old('permanent_address', $user->permanent_address) }}</textarea>
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="permanent_address">@error('permanent_address'){{ $message }}@enderror</span>
                </div>
            </div>
        </div>

        <!-- TAB 3: EDUCATION & INCOME -->
        <div class="profile-panel hidden bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" id="panel-education">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 bg-slate-50 flex flex-wrap justify-between items-center gap-2">
                <span class="text-[11px] font-semibold uppercase text-slate-900 tracking-widest">Education, Occupation & Income</span>
                <button type="submit" data-submit-tab="education"
                        class="px-4 sm:px-5 py-1.5 bg-gov-green hover:bg-gov-light text-white text-[11px] font-bold rounded-lg transition-colors shadow-sm">
                    Save Profile
                </button>
            </div>
            <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="edu_qualification" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Educational Qualification</label>
                    <input type="text" name="edu_qualification" id="edu_qualification" value="{{ old('edu_qualification', $user->edu_qualification) }}"
                           placeholder="e.g. HSC, Bachelor's, MBA"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('edu_qualification') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="edu_qualification">@error('edu_qualification'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="occupation" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Occupation</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $user->occupation) }}"
                           placeholder="e.g. Business Owner, Officer"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('occupation') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="occupation">@error('occupation'){{ $message }}@enderror</span>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="employer_address" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Employer / Office Address</label>
                    <input type="text" name="employer_address" id="employer_address" value="{{ old('employer_address', $user->employer_address) }}"
                           placeholder="Office or employer address"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('employer_address') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="employer_address">@error('employer_address'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="annual_income" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Annual Income (BDT)</label>
                    <input type="number" name="annual_income" id="annual_income" value="{{ old('annual_income', $user->annual_income) }}"
                           placeholder="e.g. 500000"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('annual_income') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="annual_income">@error('annual_income'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="tin_number" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">TIN Number</label>
                    <input type="text" name="tin_number" id="tin_number" value="{{ old('tin_number', $user->tin_number) }}"
                           placeholder="12-digit TIN code"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('tin_number') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="tin_number">@error('tin_number'){{ $message }}@enderror</span>
                </div>
            </div>
        </div>

        <!-- TAB 4: SECURITY -->
        <div class="profile-panel hidden bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" id="panel-security">
            <div class="px-4 sm:px-5 py-3 border-b border-slate-100 bg-slate-50 flex flex-wrap justify-between items-center gap-2">
                <span class="text-[11px] font-semibold uppercase text-slate-900 tracking-widest">Security & Password</span>
                <button type="submit" data-submit-tab="security"
                        class="px-4 sm:px-5 py-1.5 bg-gov-green hover:bg-gov-light text-white text-[11px] font-bold rounded-lg transition-colors shadow-sm">
                    Save Profile
                </button>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="current_password" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Current Password</label>
                    <input type="password" name="current_password" id="current_password"
                           placeholder="Required to change password"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('current_password') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="current_password">@error('current_password'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="password" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">New Password</label>
                    <input type="password" name="password" id="password" minlength="6"
                           placeholder="Leave blank to keep current"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('password') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="password">@error('password'){{ $message }}@enderror</span>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-[11px] font-semibold uppercase text-slate-900 mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="6"
                           placeholder="Leave blank to keep current"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('password_confirmation') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <span class="text-[11px] text-rose-500 font-semibold mt-0.5 block js-error" data-for="password_confirmation">@error('password_confirmation'){{ $message }}@enderror</span>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    function getCurrentActiveTab() {
        const activeInput = document.getElementById('active_tab');
        return (activeInput && activeInput.value) ? activeInput.value : 'personal';
    }

    // Tab switching
    function switchTab(tabName) {
        // Update hidden active_tab input
        const activeInput = document.getElementById('active_tab');
        if (activeInput) {
            activeInput.value = tabName;
        }

        // Hide all panels
        document.querySelectorAll('.profile-panel').forEach(p => p.classList.add('hidden'));

        // Show selected panel
        const panel = document.getElementById(`panel-${tabName}`);
        if (panel) panel.classList.remove('hidden');

        // Update tab button styles
        document.querySelectorAll('.profile-tab').forEach(btn => {
            const isActive = btn.dataset.tab === tabName;
            btn.className = isActive
                ? 'profile-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none bg-gov-green text-white shadow-sm'
                : 'profile-tab flex items-center space-x-1.5 px-3.5 py-2 rounded-lg text-[11px] font-semibold uppercase transition-all focus:outline-none text-slate-500 hover:bg-slate-50';
        });

        // Check and toggle error banner visibility for current tab only
        checkAndHideIncompleteError(tabName);
    }

    // Load upazilas on district change
    document.getElementById('district_id')?.addEventListener('change', function () {
        loadUpazilas(this.value);
    });

    function loadUpazilas(districtId, selectedUpazilaId = null) {
        const sel = document.getElementById('upazila_id');
        if (!sel) return;
        sel.innerHTML = '<option value="">Loading...</option>';
        sel.disabled = true;
        if (!districtId) { sel.innerHTML = '<option value="">Select District First</option>'; return; }

        fetch(`/api/districts/${districtId}/upazilas`)
            .then(r => r.json())
            .then(data => {
                sel.innerHTML = '<option value="">Select Upazila / Thana</option>';
                data.forEach(u => {
                    const sel2 = selectedUpazilaId && parseInt(u.id) === parseInt(selectedUpazilaId) ? 'selected' : '';
                    sel.innerHTML += `<option value="${u.id}" ${sel2}>${u.name}</option>`;
                });
                sel.disabled = false;
            });
    }

    function showIncompleteError() {
        const box = document.getElementById('profile-incomplete-error');
        if (box) box.classList.remove('hidden');
    }

    function hideIncompleteError() {
        const box = document.getElementById('profile-incomplete-error');
        if (box) box.classList.add('hidden');
    }

    function checkAndHideIncompleteError(targetTab = null) {
        const tabName = targetTab || getCurrentActiveTab();
        const panel = document.getElementById(`panel-${tabName}`);
        if (!panel) return;

        const invalidInputs = panel.querySelectorAll('.border-rose-400');
        const activeErrorSpans = Array.from(panel.querySelectorAll('.js-error')).filter(s => s.textContent.trim() !== '');

        if (invalidInputs.length > 0 || activeErrorSpans.length > 0) {
            showIncompleteError();
        } else {
            hideIncompleteError();
        }
    }

    function validateTab(tabName) {
        let isValid = true;
        let firstInvalidEl = null;

        const panel = document.getElementById(`panel-${tabName}`);
        if (!panel) return true;

        // 1. Clear previous error styling & messages for this tab
        panel.querySelectorAll('input, select, textarea').forEach(el => {
            el.classList.remove('border-rose-400');
            const errorSpan = panel.querySelector(`.js-error[data-for="${el.name}"]`);
            if (errorSpan) errorSpan.textContent = '';
        });

        // 2. Tab-specific validations
        if (tabName === 'personal') {
            const requiredFields = panel.querySelectorAll('[required]');
            requiredFields.forEach(el => {
                const filled = el.value !== null && el.value.trim() !== '';
                const errorSpan = panel.querySelector(`.js-error[data-for="${el.name}"]`);

                if (!filled) {
                    isValid = false;
                    el.classList.add('border-rose-400');
                    if (errorSpan) errorSpan.textContent = el.dataset.requiredMessage || 'This field is required.';
                    if (!firstInvalidEl) firstInvalidEl = el;
                }
            });

            const emailInput = document.getElementById('email');
            if (emailInput && emailInput.value.trim() !== '') {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailInput.value.trim())) {
                    isValid = false;
                    emailInput.classList.add('border-rose-400');
                    const errorSpan = panel.querySelector('.js-error[data-for="email"]');
                    if (errorSpan) errorSpan.textContent = 'Please enter a valid email address.';
                    if (!firstInvalidEl) firstInvalidEl = emailInput;
                }
            }

            const phoneInput = document.getElementById('phone');
            if (phoneInput && phoneInput.value.trim() !== '') {
                const phonePattern = /^01[3-9]\d{8}$/;
                if (!phonePattern.test(phoneInput.value.trim())) {
                    isValid = false;
                    phoneInput.classList.add('border-rose-400');
                    const errorSpan = panel.querySelector('.js-error[data-for="phone"]');
                    if (errorSpan) errorSpan.textContent = 'The mobile number must be a valid 11-digit Bangladeshi phone number (e.g. 01712345678).';
                    if (!firstInvalidEl) firstInvalidEl = phoneInput;
                }
            }

            const nidInput = document.getElementById('nid');
            if (nidInput && nidInput.value.trim() !== '') {
                const nidPattern = /^(\d{10}|\d{17})$/;
                if (!nidPattern.test(nidInput.value.trim())) {
                    isValid = false;
                    nidInput.classList.add('border-rose-400');
                    const errorSpan = panel.querySelector('.js-error[data-for="nid"]');
                    if (errorSpan) errorSpan.textContent = 'National ID (NID) must be exactly 10 or 17 digits.';
                    if (!firstInvalidEl) firstInvalidEl = nidInput;
                }
            }
        } else if (tabName === 'address' || tabName === 'education') {
            const requiredFields = panel.querySelectorAll('[required]');
            requiredFields.forEach(el => {
                const filled = el.value !== null && el.value.trim() !== '';
                const errorSpan = panel.querySelector(`.js-error[data-for="${el.name}"]`);

                if (!filled) {
                    isValid = false;
                    el.classList.add('border-rose-400');
                    if (errorSpan) errorSpan.textContent = el.dataset.requiredMessage || 'This field is required.';
                    if (!firstInvalidEl) firstInvalidEl = el;
                }
            });
        } else if (tabName === 'security') {
            const currentPass = document.getElementById('current_password');
            const newPass = document.getElementById('password');
            const confirmPass = document.getElementById('password_confirmation');

            const curVal = currentPass ? currentPass.value : '';
            const newVal = newPass ? newPass.value : '';
            const confVal = confirmPass ? confirmPass.value : '';

            if (curVal === '') {
                isValid = false;
                if (currentPass) currentPass.classList.add('border-rose-400');
                const errorSpan = panel.querySelector('.js-error[data-for="current_password"]');
                if (errorSpan) errorSpan.textContent = 'Current password is required to change your password.';
                if (!firstInvalidEl) firstInvalidEl = currentPass;
            }

            if (newVal === '') {
                isValid = false;
                if (newPass) newPass.classList.add('border-rose-400');
                const errorSpan = panel.querySelector('.js-error[data-for="password"]');
                if (errorSpan) errorSpan.textContent = 'New password is required.';
                if (!firstInvalidEl) firstInvalidEl = newPass;
            } else if (newVal.length < 6) {
                isValid = false;
                if (newPass) newPass.classList.add('border-rose-400');
                const errorSpan = panel.querySelector('.js-error[data-for="password"]');
                if (errorSpan) errorSpan.textContent = 'The password must be at least 6 characters.';
                if (!firstInvalidEl) firstInvalidEl = newPass;
            }

            if (newVal !== '' && confVal !== newVal) {
                isValid = false;
                if (confirmPass) confirmPass.classList.add('border-rose-400');
                const errorSpan = panel.querySelector('.js-error[data-for="password_confirmation"]');
                if (errorSpan) errorSpan.textContent = 'Confirm new password does not match.';
                if (!firstInvalidEl) firstInvalidEl = confirmPass || newPass;
            }
        }

        if (!isValid) {
            showIncompleteError();
            if (firstInvalidEl) {
                firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            hideIncompleteError();
        }

        return isValid;
    }

    // On DOM Content Loaded
    window.addEventListener('DOMContentLoaded', () => {
        const dist = document.getElementById('district_id');
        if (dist && dist.value) {
            loadUpazilas(dist.value, "{{ $user->upazila_id }}");
        }

        let initialTab = "{{ old('active_tab', session('active_tab', '')) }}";
        if (!initialTab) {
            @if($errors->hasAny(['current_password', 'password', 'password_confirmation']))
                initialTab = 'security';
            @elseif($errors->hasAny(['district_id', 'upazila_id', 'present_address', 'permanent_address']))
                initialTab = 'address';
            @elseif($errors->hasAny(['edu_qualification', 'occupation', 'employer_address', 'annual_income', 'tin_number']))
                initialTab = 'education';
            @else
                initialTab = 'personal';
            @endif
        }

        switchTab(initialTab);

        // Profile photo live image preview
        document.getElementById('profile_photo')?.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.getElementById('profile-photo-preview');
                    const initials = document.getElementById('profile-photo-initials');
                    if (img) {
                        img.src = e.target.result;
                        img.classList.remove('hidden');
                    }
                    if (initials) {
                        initials.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Clear error styling on user input for element
        document.querySelectorAll('#profile-form input, #profile-form select, #profile-form textarea').forEach(el => {
            const clearError = function () {
                if (el.classList.contains('border-rose-400')) {
                    el.classList.remove('border-rose-400');
                }
                const errorSpan = document.querySelector(`.js-error[data-for="${el.name}"]`);
                if (errorSpan) errorSpan.textContent = '';
                checkAndHideIncompleteError();
            };
            el.addEventListener('input', clearError);
            el.addEventListener('change', clearError);
        });

        // Bind Save Profile submit buttons to update active_tab
        document.querySelectorAll('#profile-form button[type="submit"]').forEach(btn => {
            btn.addEventListener('click', function () {
                const submitTab = this.dataset.submitTab || getCurrentActiveTab();
                const activeInput = document.getElementById('active_tab');
                if (activeInput) activeInput.value = submitTab;
            });
        });

        // Form submit handler
        const form = document.getElementById('profile-form');
        form?.addEventListener('submit', function (e) {
            const currentTab = getCurrentActiveTab();
            if (!validateTab(currentTab)) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
