@extends('layouts.app')
@section('title', '500 — Server Error')

@section('content')
<div class="w-full flex flex-col items-center justify-center min-h-[65vh] text-center px-4">

    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-50 border border-amber-100 text-xs font-bold text-amber-600 uppercase tracking-widest mb-8">
        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
        Error 500
    </span>

    <div class="relative select-none mb-4">
        <span class="text-[9rem] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-b from-amber-100 to-amber-50">500</span>
        <div class="absolute inset-0 flex items-center justify-center">
            <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 opacity-75">
                <rect x="8" y="14" width="48" height="14" rx="3" fill="#fbbf24"/>
                <rect x="8" y="32" width="48" height="14" rx="3" fill="#fbbf24"/>
                <circle cx="48" cy="21" r="3" fill="#fff"/>
                <circle cx="48" cy="39" r="3" fill="#f87171"/>
                <!-- Bolt -->
                <path d="M36 6l-8 14h6l-5 14 12-16h-7l5-12z" fill="#f59e0b"/>
            </svg>
        </div>
    </div>

    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Internal Server Error</p><h1 class="text-4xl font-black text-slate-800 mb-2">Coming Soon</h1>
    <p class="text-slate-500 text-sm max-w-xs mb-1">
        Something went wrong on our end. We're already working on a fix.
    </p>
    <p class="text-[11px] font-mono text-slate-400 bg-slate-50 border border-slate-100 px-3 py-1 rounded-lg mb-8">
        Please try again in a few minutes.
    </p>

    <div class="flex flex-wrap gap-3 justify-center">
        <button onclick="window.location.reload()"
                class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-sm rounded-xl shadow-sm transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-rotate-right mr-1.5"></i> Retry
        </button>
        <a href="{{ url('/') }}"
           class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-house mr-1.5"></i> Home
        </a>
    </div>
</div>
@endsection
