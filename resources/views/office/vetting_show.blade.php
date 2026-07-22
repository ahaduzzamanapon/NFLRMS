@extends('layouts.app')
@section('title', 'Vetting Report — ' . $vetting->application->application_number)

@section('content')
<div class="max-w-3xl space-y-5">

    <div>
        <a href="{{ route('vetting.dashboard') }}" class="text-[10px] font-extrabold text-slate-400 hover:text-gov-green flex items-center space-x-1 mb-3">
            <span>←</span><span>Back to vetting queue</span>
        </a>
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-black font-serif text-slate-900">Vetting Report</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Case {{ $vetting->application->application_number }} &bull;
                    {{ strtoupper($vetting->agency) }} Agency
                </p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase border
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
            <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Applicant</span>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Name</span>
                <span class="font-bold text-slate-900">{{ $vetting->application->user->name }}</span>
            </div>
            <div>
                <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">NID</span>
                <span class="font-bold text-slate-900">{{ $vetting->application->user->nid ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">District</span>
                <span class="font-bold text-slate-900">{{ $vetting->application->user->district->name ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">Weapon</span>
                <span class="font-bold text-slate-900">{{ $vetting->application->firearm_details['weapon_type'] ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    @if($vetting->status === 'pending')
    <!-- Submit Report Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
            <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Submit Clearance Report</span>
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
        <form action="{{ route('vetting.submit', $vetting->id) }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-2">Clearance Decision</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center space-x-2 p-3 rounded-lg border border-slate-200 cursor-pointer hover:border-emerald-300">
                        <input type="radio" name="status" value="cleared" class="text-gov-green">
                        <span class="text-xs font-bold text-emerald-700">✓ Cleared</span>
                    </label>
                    <label class="flex items-center space-x-2 p-3 rounded-lg border border-slate-200 cursor-pointer hover:border-rose-300">
                        <input type="radio" name="status" value="flagged" class="text-rose-600">
                        <span class="text-xs font-bold text-rose-700">✗ Flagged</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="text-[9px] font-extrabold uppercase text-slate-900 tracking-widest block mb-1.5">Remarks (mandatory)</label>
                <textarea name="remarks" rows="4" placeholder="Provide details of your vetting findings..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white resize-none"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-gov-green hover:bg-gov-light text-white font-black text-xs rounded-lg transition-colors">
                Submit Vetting Report
            </button>
        </form>
    </div>
    @else
    <!-- Completed Report -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3">
        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-widest">Submitted Report</div>
        <p class="text-xs text-slate-700 font-semibold">{{ $vetting->remarks ?? 'No remarks.' }}</p>
        @if($vetting->vetted_at)
        <p class="text-[9px] text-slate-400">Submitted {{ $vetting->vetted_at->format('d M Y, H:i') }}</p>
        @endif
    </div>
    @endif
</div>
@endsection
