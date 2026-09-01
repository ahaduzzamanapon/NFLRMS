@extends('layouts.app')
@section('title', '500 — Server Error')

@section('content')
<div class="w-full flex flex-col items-center justify-center min-h-[60vh] text-center py-16 px-4">

    <!-- Illustration -->
    <div class="relative mb-8">
        <div class="w-36 h-36 rounded-full bg-amber-50 flex items-center justify-center mx-auto border-4 border-amber-100 shadow-inner">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-20 h-20">
                <circle cx="40" cy="40" r="38" fill="#fffbeb" stroke="#fde68a" stroke-width="2"/>
                <!-- Server box -->
                <rect x="18" y="22" width="44" height="12" rx="3" fill="#fbbf24"/>
                <rect x="18" y="38" width="44" height="12" rx="3" fill="#fbbf24"/>
                <circle cx="56" cy="28" r="2.5" fill="#fff"/>
                <circle cx="56" cy="44" r="2.5" fill="#f87171"/>
                <!-- Bolt -->
                <path d="M43 16l-6 12h5l-4 12 10-14h-6l4-10z" fill="#f59e0b" stroke="#d97706" stroke-width="0.5"/>
            </svg>
        </div>
        <span class="absolute -top-2 -right-2 text-5xl font-black text-amber-100 select-none leading-none">500</span>
    </div>

    <!-- Text -->
    <h1 class="text-3xl font-bold text-slate-800 mb-3">Internal Server Error</h1>
    <p class="text-slate-500 text-sm max-w-sm mb-2">
        Something went wrong on our end. Our team has been notified and we're working to fix it.
    </p>
    <p class="text-xs text-slate-400 mb-8 font-mono bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg inline-block">
        Error 500 &bull; Please try again in a few minutes
    </p>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ url('/') }}"
           class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-sm rounded-xl shadow-sm transition-colors">
            <i class="fa-solid fa-house mr-1.5"></i> Go to Dashboard
        </a>
        <button onclick="window.location.reload()"
                class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors">
            <i class="fa-solid fa-rotate-right mr-1.5"></i> Retry
        </button>
    </div>
</div>
@endsection
