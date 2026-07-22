@extends('layouts.app')
@section('title', 'New Dealing Licence — Form K')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-black font-serif text-slate-900">New Dealing Licence (Form K)</h2>
        <p class="text-xs text-slate-500 mt-1 font-semibold">
            Arms Dealing Authorization · Appendix B, BRS §7.2 · 4-agency vetting required
        </p>
    </div>

    <!-- Fee Summary -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 flex items-center justify-between">
        <div class="text-xs font-bold text-amber-800">
            Statutory Fee: <span class="font-black">৳1,50,000</span> &bull; Platform Charge: <span class="font-black">৳2,500</span>
        </div>
        <span class="text-[10px] font-black text-amber-600 uppercase tracking-wider">Total: ৳1,52,500</span>
    </div>

    <form action="{{ route('dealer.apply.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="type" value="new_dealing_license">

        <!-- Section 1: Business Information -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-900">1. Business Information</span>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Firm / Business Name *</label>
                    <input type="text" name="firm_name" required value="{{ old('firm_name') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="e.g. Karim Arms & Ammunition">
                    @error('firm_name')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Trade License Number *</label>
                    <input type="text" name="trade_license" required value="{{ old('trade_license') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="TL-DHK-2024-XXXXX">
                    @error('trade_license')<span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">{{ $message }}</span>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Business Address *</label>
                    <input type="text" name="business_address" required value="{{ old('business_address') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="Full address of premises">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">District *</label>
                    <select name="district_id" required
                            class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                        <option value="">— Select District —</option>
                        @foreach($districts as $d)
                        <option value="{{ $d->id }}" {{ old('district_id')==$d->id?'selected':'' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Licence Class *</label>
                    <select name="license_class" required
                            class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                        <option value="">— Select Class —</option>
                        <option value="A">Class A — Retail Sale</option>
                        <option value="B">Class B — Wholesale</option>
                        <option value="C">Class C — Import / Export</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Proprietor Details -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-900">2. Proprietor / Responsible Person</span>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Full Name *</label>
                    <input type="text" name="proprietor_name" required value="{{ auth()->user()->name }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">National ID (NID) *</label>
                    <input type="text" name="nid" required value="{{ auth()->user()->nid ?? old('nid') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="17-digit NID number">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Mobile Number *</label>
                    <input type="text" name="mobile" required value="{{ auth()->user()->phone ?? old('mobile') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Annual Income (BDT) *</label>
                    <input type="number" name="annual_income" required value="{{ old('annual_income') }}"
                           class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                           placeholder="0">
                </div>
            </div>
        </div>

        <!-- Section 3: Stock Categories -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">3. Arms Categories to be Dealt</span>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach(['Pistol','Revolver','Shotgun','Rifle','Air Gun','Ammunition'] as $cat)
                    <label class="flex items-center space-x-2.5 p-3 rounded-lg border border-slate-200 cursor-pointer hover:border-gov-green hover:bg-emerald-50/50 transition-colors">
                        <input type="checkbox" name="categories[]" value="{{ $cat }}"
                               class="rounded border-slate-300 text-gov-green focus:ring-gov-green"
                               {{ in_array($cat, old('categories', [])) ? 'checked' : '' }}>
                        <span class="text-xs font-semibold text-slate-700">{{ $cat }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Section 4: Documents -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">4. Required Documents</span>
            </div>
            <div class="p-5 space-y-3">
                @foreach([
                    ['name'=>'nid_copy','label'=>'NID Copy (Front & Back)'],
                    ['name'=>'trade_license_doc','label'=>'Trade License (Current Year)'],
                    ['name'=>'premises_photo','label'=>'Premises Photograph'],
                    ['name'=>'bank_statement','label'=>'Bank Statement (Last 6 months)'],
                ] as $doc)
                <div class="flex items-center justify-between p-3 rounded-lg border border-slate-200">
                    <div>
                        <div class="text-xs font-bold text-slate-900">{{ $doc['label'] }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">PDF or JPG/PNG · Max 5MB</div>
                    </div>
                    <input type="file" name="{{ $doc['name'] }}" accept=".pdf,.jpg,.jpeg,.png"
                           class="text-[10px] text-slate-600">
                </div>
                @endforeach
            </div>
        </div>

        <!-- Declaration -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <label class="flex items-start space-x-3 cursor-pointer">
                <input type="checkbox" name="declaration" required value="1"
                       class="mt-0.5 rounded border-slate-300 text-gov-green focus:ring-gov-green">
                <span class="text-xs text-slate-600 leading-relaxed font-medium">
                    I declare that all information provided above is true and accurate to the best of my knowledge.
                    I understand that submission of false information may result in rejection of my application and legal prosecution
                    under the Arms Act 1878 and applicable provisions of the Penal Code.
                </span>
            </label>
        </div>

        <!-- Submit -->
        <div class="flex gap-3 justify-end">
            <a href="{{ route('citizen.dashboard') }}"
               class="px-5 py-2.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-gov-green hover:bg-gov-light text-white font-black text-xs shadow-md transition-colors">
                Submit Form K Application →
            </button>
        </div>
    </form>
</div>
@endsection
