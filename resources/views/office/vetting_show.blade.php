@extends('layouts.app')
@section('title', 'Vetting Report — ' . $vetting->application->application_number)

@section('content')
<div class="w-full space-y-5">

    <div>
        <a href="{{ route('vetting.dashboard') }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 shadow-sm text-[10px] font-semibold text-slate-500 hover:text-gov-green hover:border-gov-green/40 transition-all mb-3">
            <span><i class="fa-solid fa-arrow-left"></i></span><span>Back to vetting queue</span>
        </a>
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold font-serif text-slate-900 leading-tight">Vetting Report</h2>
                <p class="text-xs text-slate-500 mt-0.5 font-medium">
                    Case {{ $vetting->application->application_number }} &bull;
                    {{ strtoupper($vetting->agency) }} Agency
                </p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[9px] font-semibold uppercase border
                @if($vetting->status === 'cleared') border-emerald-500/30 bg-emerald-50 text-emerald-700
                @elseif($vetting->status === 'flagged') border-rose-500/30 bg-rose-50 text-rose-700
                @else border-amber-500/30 bg-amber-50 text-amber-700 @endif">
                {{ ucfirst($vetting->status) }}
            </span>
        </div>
    </div>

    <!-- Applicant Info -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">Applicant</span>
        </div>
        <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 text-xs">
            <div>
                <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-wider block">Name</span>
                <span class="font-semibold text-slate-900">{{ $vetting->application->user->name }}</span>
            </div>
            <div>
                <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-wider block">NID</span>
                <span class="font-semibold text-slate-900">{{ $vetting->application->user->nid ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-wider block">District</span>
                <span class="font-semibold text-slate-900">{{ $vetting->application->user->district->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-[9px] font-semibold uppercase text-slate-400 tracking-wider block">Weapon</span>
                <span class="font-semibold text-slate-900">{{ $vetting->application->firearm_details['weapon_type'] ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    @if($vetting->status === 'pending')
    <!-- Submit Report Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <span class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">Submit Clearance Report</span>
        </div>
        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl font-normal space-y-1">
                <span class="block text-sm font-bold font-serif"><i class="fa-solid fa-triangle-exclamation"></i> Please resolve the following errors:</span>
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('vetting.submit', Crypt::encryptString($vetting->id)) }}" method="POST" class="p-4 sm:p-5 space-y-4">
            @csrf
            <div>
                <label class="text-[9px] font-semibold uppercase text-slate-700 tracking-wider block mb-2">Clearance Decision</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-center space-x-2 p-3 rounded-lg border border-slate-200 cursor-pointer hover:border-emerald-300">
                        <input type="radio" name="status" value="cleared" class="text-gov-green">
                        <span class="text-xs font-semibold text-emerald-700"><i class="fa-solid fa-check mr-1"></i> Cleared</span>
                    </label>
                    <label class="flex items-center space-x-2 p-3 rounded-lg border border-slate-200 cursor-pointer hover:border-rose-300">
                        <input type="radio" name="status" value="flagged" class="text-rose-600">
                        <span class="text-xs font-semibold text-rose-700"><i class="fa-solid fa-xmark mr-1"></i> Flagged</span>
                    </label>
                </div>
            </div>
            @if($customComments->isNotEmpty())
            <div>
                <label for="custom_comment_select" class="text-[9px] font-semibold uppercase text-slate-700 tracking-wider block mb-1.5"><i class="fa-solid fa-comments text-gov-green mr-1"></i> Quick Fill from Custom Comments</label>
                <select id="custom_comment_select" onchange="fillRemarksFromCustomComment(this)"
                        class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <option value="">— Select a saved comment —</option>
                    @foreach($customComments as $cc)
                        <option value="{{ $cc->comment }}">{{ $cc->title }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="text-[9px] font-semibold uppercase text-slate-700 tracking-wider block mb-1.5">Remarks (mandatory)</label>
                <textarea name="remarks" id="remarks" rows="4" placeholder="Provide details of your vetting findings..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white resize-none"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors shadow-sm">
                Submit Vetting Report
            </button>
        </form>
    </div>
    @else
    <!-- Completed Report -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3">
        <div class="text-[10px] font-semibold uppercase text-slate-400 tracking-wider">Submitted Report</div>
        <p class="text-xs text-slate-700 font-normal leading-relaxed">{{ $vetting->remarks ?? 'No remarks.' }}</p>
        @if($vetting->vetted_at)
        <p class="text-[9px] text-slate-400 font-normal">Submitted {{ $vetting->vetted_at->format('d M Y, H:i') }}</p>
        @endif
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Fill the remarks textarea with the selected custom comment
    function fillRemarksFromCustomComment(selectEl) {
        const remarks = document.getElementById('remarks');
        if (remarks && selectEl.value) {
            remarks.value = selectEl.value;
        }
    }
</script>
@endsection
