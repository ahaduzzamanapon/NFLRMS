@extends('layouts.app')
@section('title', '404 — Page Not Found')

@section('content')
<div class="w-full flex flex-col items-center justify-center min-h-[65vh] text-center px-4">

    <!-- Glowing badge -->
    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-widest mb-8">
        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
        Error 404
    </span>

    <!-- Big number -->
    <div class="relative select-none mb-4">
        <span class="text-[9rem] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-b from-slate-200 to-slate-100">404</span>
        <div class="absolute inset-0 flex items-center justify-center">
            <svg viewBox="0 0 80 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 opacity-60">
                <circle cx="40" cy="26" r="14" stroke="#94a3b8" stroke-width="2.5"/>
                <path d="M30 38c0-5.523 4.477-10 10-10s10 4.477 10 10" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="35" cy="24" r="2" fill="#94a3b8"/>
                <circle cx="45" cy="24" r="2" fill="#94a3b8"/>
                <path d="M35 31 Q40 28 45 31" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    <h1 class="text-2xl font-bold text-slate-800 mb-2">Page Not Found</h1>
    <p class="text-slate-500 text-sm max-w-xs mb-1">
        The page you're looking for doesn't exist or may have been moved.
    </p>
    <p class="text-[11px] font-mono text-slate-400 bg-slate-50 border border-slate-100 px-3 py-1 rounded-lg mb-8 truncate max-w-xs">
        {{ request()->url() }}
    </p>

    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}"
           class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-sm rounded-xl shadow-sm transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Go Back
        </a>
        <a href="{{ url('/') }}"
           class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-house mr-1.5"></i> Home
        </a>
    </div>
</div>
@endsection
