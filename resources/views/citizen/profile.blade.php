@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="max-w-2xl space-y-5">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-900 font-serif">My Profile</h2>
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">
                Save your personal details once — they will auto-fill in every new application.
            </p>
        </div>
        @if(auth()->user()->nid)
            <span class="flex items-center space-x-1 px-2.5 py-1 rounded-full text-[9px] font-black uppercase border border-emerald-500/30 bg-emerald-50 text-emerald-700">
                <span>✓</span><span>Profile Complete</span>
            </span>
        @else
            <span class="flex items-center space-x-1 px-2.5 py-1 rounded-full text-[9px] font-black uppercase border border-amber-500/30 bg-amber-50 text-amber-700">
                <span>⚠</span><span>Incomplete</span>
            </span>
        @endif
    </div>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Section: Identity & Photo -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <span class="text-[10px] font-extrabold uppercase text-slate-900 font-black tracking-widest">Identity &amp; Profile Picture</span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 flex items-center space-x-4 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gov-green bg-slate-200 flex items-center justify-center text-slate-400 font-bold text-xl flex-shrink-0">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile Photo" class="w-full h-full object-cover">
                        @else
                            <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <label for="profile_photo" class="block text-[10px] font-extrabold uppercase text-slate-900">Upload Profile Photo (Passport Size)</label>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gov-green file:text-white hover:file:bg-gov-light cursor-pointer">
                        <span class="text-[9px] text-slate-500 block font-medium">Supported: JPG, PNG, WEBP (Max 2MB)</span>
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Full Name (English)</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white @error('name') border-rose-400 @enderror">
                    @error('name')<span class="text-[9px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                <div>
                    <label for="name_bn" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Full Name (Bengali)</label>
                    <input type="text" name="name_bn" id="name_bn" value="{{ old('name_bn', $user->name_bn) }}" required
                           placeholder="বাংলায় পূর্ণ নাম লিখুন"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white @error('name_bn') border-rose-400 @enderror">
                    @error('name_bn')<span class="text-[9px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="email" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white @error('email') border-rose-400 @enderror">
                    @error('email')<span class="text-[9px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="nid" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">National ID (NID)</label>
                    <input type="text" name="nid" id="nid" value="{{ old('nid', $user->nid) }}"
                           placeholder="10 or 17 digit NID"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white @error('nid') border-rose-400 @enderror">
                    @error('nid')<span class="text-[9px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="dob" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Date of Birth</label>
                    @php $dobFormatted = $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : ''; @endphp
                    <input type="date" name="dob" id="dob" value="{{ old('dob', $dobFormatted) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
                <div>
                    <label for="phone" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Mobile Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                           placeholder="01XXXXXXXXX"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
                <div>
                    <label for="marital_status" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Marital Status</label>
                    <select name="marital_status" id="marital_status"
                            class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                        <option value="">Select</option>
                        @foreach(['Married','Single','Divorced','Widowed'] as $ms)
                            <option value="{{ $ms }}" {{ old('marital_status', $user->marital_status) === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="spouse_name" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Spouse Name</label>
                    <input type="text" name="spouse_name" id="spouse_name" value="{{ old('spouse_name', $user->spouse_name) }}"
                           placeholder="Spouse full name (if married)"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
                <div>
                    <label for="nationality" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Nationality</label>
                    <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $user->nationality ?? 'Bangladeshi') }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
                <div>
                    <label for="religion" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Religion</label>
                    <input type="text" name="religion" id="religion" value="{{ old('religion', $user->religion) }}"
                           placeholder="e.g. Islam, Hindu, Christian"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
            </div>
        </div>

        <!-- Section: Family -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                <span class="text-[10px] font-extrabold uppercase text-slate-900 font-black tracking-widest">Family</span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="father_name" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Father's Name</label>
                    <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $user->father_name) }}"
                           placeholder="Father's full name"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
                <div>
                    <label for="mother_name" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Mother's Name</label>
                    <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name', $user->mother_name) }}"
                           placeholder="Mother's full name"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
            </div>
        </div>

        <!-- Section: Address & District -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                <span class="text-[10px] font-extrabold uppercase text-slate-900 font-black tracking-widest">Address</span>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="district_id" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">District</label>
                        <select name="district_id" id="district_id"
                                class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                            <option value="">Select District</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ (old('district_id', $user->district_id) == $d->id) ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="upazila_id" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Upazila / Thana</label>
                        <select name="upazila_id" id="upazila_id"
                                class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                            <option value="">Select Upazila</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="present_address" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Present Address</label>
                    <textarea name="present_address" id="present_address" rows="2"
                              placeholder="House No, Road, Area, City"
                              class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">{{ old('present_address', $user->present_address) }}</textarea>
                </div>
                <div>
                    <label for="permanent_address" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Permanent Address</label>
                    <textarea name="permanent_address" id="permanent_address" rows="2"
                              placeholder="Village, Thana, District"
                              class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">{{ old('permanent_address', $user->permanent_address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Section: Occupation & Income -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                <span class="text-[10px] font-extrabold uppercase text-slate-900 font-black tracking-widest">Occupation & Income</span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edu_qualification" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Educational Qualification</label>
                    <input type="text" name="edu_qualification" id="edu_qualification" value="{{ old('edu_qualification', $user->edu_qualification) }}"
                           placeholder="e.g. HSC, Bachelor's, MBA"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
                <div>
                    <label for="occupation" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Occupation</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $user->occupation) }}"
                           placeholder="e.g. Business Owner, Officer"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
                <div class="sm:col-span-2">
                    <label for="employer_address" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Employer / Office Address</label>
                    <input type="text" name="employer_address" id="employer_address" value="{{ old('employer_address', $user->employer_address) }}"
                           placeholder="Office or employer address"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
                <div>
                    <label for="annual_income" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Annual Income (BDT)</label>
                    <input type="number" name="annual_income" id="annual_income" value="{{ old('annual_income', $user->annual_income) }}"
                           placeholder="e.g. 500000"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
                <div>
                    <label for="tin_number" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">TIN Number</label>
                    <input type="text" name="tin_number" id="tin_number" value="{{ old('tin_number', $user->tin_number) }}"
                           placeholder="12-digit TIN code"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
            </div>
        </div>

        <!-- Section: Security & Password -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                <span class="text-[10px] font-extrabold uppercase text-slate-900 font-black tracking-widest">Security & Password</span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">New Password</label>
                    <input type="password" name="password" id="password" minlength="6"
                           placeholder="Leave blank to keep current"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white @error('password') border-rose-400 @enderror">
                    @error('password')<span class="text-[9px] text-rose-600 font-bold mt-1 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-[10px] font-extrabold uppercase text-slate-900 mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="6"
                           placeholder="Leave blank to keep current"
                           class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit"
                    class="px-6 py-2.5 bg-gov-green hover:bg-gov-light text-white text-xs font-black rounded-lg transition-colors shadow-sm">
                Save Profile
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Load upazilas on district change
    document.getElementById('district_id').addEventListener('change', function () {
        loadUpazilas(this.value);
    });

    function loadUpazilas(districtId, selectedUpazilaId = null) {
        const sel = document.getElementById('upazila_id');
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

    // On load: populate upazilas for user's current district
    window.addEventListener('DOMContentLoaded', () => {
        const dist = document.getElementById('district_id');
        if (dist && dist.value) {
            loadUpazilas(dist.value, "{{ $user->upazila_id }}");
        }
    });
</script>
@endsection
