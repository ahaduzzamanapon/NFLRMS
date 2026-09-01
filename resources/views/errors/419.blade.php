@extends('layouts.app')
@section('title', '419 — Page Expired')

@section('content')
<div class="w-full flex flex-col items-center justify-center min-h-[65vh] text-center px-4">

    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-violet-50 border border-violet-100 text-xs font-bold text-violet-500 uppercase tracking-widest mb-8">
        <span class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-pulse"></span>
        Error 419
    </span>

    <div class="relative select-none mb-4">
        <span class="text-[9rem] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-b from-violet-100 to-violet-50">419</span>
        <div class="absolute inset-0 flex items-center justify-center">
            <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 opacity-70">
                <circle cx="30" cy="30" r="22" stroke="#a78bfa" stroke-width="3"/>
                <path d="M30 14v16l9 5" stroke="#7c3aed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>

    <h1 class="text-2xl font-bold text-slate-800 mb-2">Page Expired</h1>
    <p class="text-slate-500 text-sm max-w-xs mb-1">
        Your session has expired. Please reload and try again.
    </p>
    <p class="text-[11px] font-mono text-slate-400 bg-slate-50 border border-slate-100 px-3 py-1 rounded-lg mb-8">
        Sessions expire after a period of inactivity.
    </p>

    <div class="flex flex-wrap gap-3 justify-center">
        <button onclick="window.location.reload()"
                class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-sm rounded-xl shadow-sm transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-rotate-right mr-1.5"></i> Reload Page
        </button>
        <a href="{{ url('/') }}"
           class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-house mr-1.5"></i> Home
        </a>
    </div>
</div>
@endsection
