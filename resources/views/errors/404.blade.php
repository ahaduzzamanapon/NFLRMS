@extends('layouts.app')
@section('title', '404 — Page Not Found')

@section('content')
<div class="w-full flex flex-col items-center justify-center min-h-[60vh] text-center py-16 px-4">

    <!-- Illustration -->
    <div class="relative mb-8">
        <div class="w-36 h-36 rounded-full bg-slate-100 flex items-center justify-center mx-auto border-4 border-slate-200/60 shadow-inner">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-20 h-20">
                <circle cx="40" cy="40" r="38" fill="#f8fafc" stroke="#e2e8f0" stroke-width="2"/>
                <path d="M24 30h6v20h-6zM50 30h6v20h-6z" rx="2" fill="#cbd5e1"/>
                <path d="M28 54c0-6.627 5.373-12 12-12s12 5.373 12 12" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="27" cy="40" r="3" fill="#94a3b8"/>
                <circle cx="53" cy="40" r="3" fill="#94a3b8"/>
            </svg>
        </div>
        <span class="absolute -top-2 -right-2 text-5xl font-black text-slate-200 select-none leading-none">404</span>
    </div>

    <!-- Text -->
    <h1 class="text-3xl font-bold text-slate-800 mb-3">Page Not Found</h1>
    <p class="text-slate-500 text-sm max-w-sm mb-2">
        The page you are looking for doesn't exist or has been moved.
    </p>
    <p class="text-xs text-slate-400 mb-8 font-mono bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg inline-block">
        Error 404 &bull; {{ request()->url() }}
    </p>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ url()->previous('#') !== '#' ? url()->previous() : '/' }}"
           class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-sm rounded-xl shadow-sm transition-colors">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Go Back
        </a>
        <a href="{{ route('dashboard') }}"
           class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors">
            <i class="fa-solid fa-house mr-1.5"></i> Dashboard
        </a>
    </div>
</div>
@endsection
