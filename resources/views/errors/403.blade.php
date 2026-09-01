@extends('layouts.app')
@section('title', '403 — Access Forbidden')

@section('content')
<div class="w-full flex flex-col items-center justify-center min-h-[65vh] text-center px-4">

    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-rose-50 border border-rose-100 text-xs font-bold text-rose-500 uppercase tracking-widest mb-8">
        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
        Error 403
    </span>

    <div class="relative select-none mb-4">
        <span class="text-[9rem] font-black leading-none tracking-tighter text-transparent bg-clip-text bg-gradient-to-b from-rose-100 to-rose-50">403</span>
        <div class="absolute inset-0 flex items-center justify-center">
            <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 opacity-70">
                <rect x="10" y="26" width="40" height="28" rx="4" fill="#fecdd3"/>
                <path d="M18 26v-8a12 12 0 0124 0v8" stroke="#f43f5e" stroke-width="3" stroke-linecap="round"/>
                <circle cx="30" cy="40" r="4" fill="#fff1f2"/>
                <rect x="28.5" y="39" width="3" height="6" rx="1.5" fill="#f43f5e"/>
            </svg>
        </div>
    </div>

    <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Access Forbidden</p><h1 class="text-4xl font-black text-slate-800 mb-2">Coming Soon</h1>
    <p class="text-slate-500 text-sm max-w-xs mb-1">
        You don't have permission to view this page.
    </p>
    @if(isset($exception) && $exception->getMessage())
    <p class="text-[11px] font-mono text-rose-400 bg-rose-50 border border-rose-100 px-3 py-1 rounded-lg mb-8 max-w-xs">
        {{ $exception->getMessage() }}
    </p>
    @else
    <p class="text-[11px] font-mono text-slate-400 bg-slate-50 border border-slate-100 px-3 py-1 rounded-lg mb-8">
        Contact your administrator to request access.
    </p>
    @endif

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
