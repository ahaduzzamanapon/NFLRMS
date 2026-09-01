@extends('layouts.app')
@section('title', '419 — Page Expired')

@section('content')
<div class="w-full flex flex-col items-center justify-center min-h-[60vh] text-center py-16 px-4">
    <div class="relative mb-8">
        <div class="w-36 h-36 rounded-full bg-violet-50 flex items-center justify-center mx-auto border-4 border-violet-100 shadow-inner">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-20 h-20">
                <circle cx="40" cy="40" r="38" fill="#f5f3ff" stroke="#ddd6fe" stroke-width="2"/>
                <circle cx="40" cy="40" r="20" stroke="#a78bfa" stroke-width="3"/>
                <path d="M40 24v16l10 6" stroke="#7c3aed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <span class="absolute -top-2 -right-2 text-5xl font-black text-violet-100 select-none leading-none">419</span>
    </div>
    <h1 class="text-3xl font-bold text-slate-800 mb-3">Page Expired</h1>
    <p class="text-slate-500 text-sm max-w-sm mb-8">
        Your session has expired. Please go back and try again.
    </p>
    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ url()->previous() }}"
           class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-sm rounded-xl shadow-sm transition-colors">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Go Back
        </a>
        <button onclick="window.location.reload()"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors">
            <i class="fa-solid fa-rotate-right mr-1.5"></i> Reload
        </button>
    </div>
</div>
@endsection
