@extends('layouts.app')
@section('title', '403 — Access Forbidden')

@section('content')
<div class="w-full flex flex-col items-center justify-center min-h-[60vh] text-center py-16 px-4">

    <!-- Illustration -->
    <div class="relative mb-8">
        <div class="w-36 h-36 rounded-full bg-rose-50 flex items-center justify-center mx-auto border-4 border-rose-100 shadow-inner">
            <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-20 h-20">
                <circle cx="40" cy="40" r="38" fill="#fff1f2" stroke="#fecdd3" stroke-width="2"/>
                <rect x="22" y="34" width="36" height="26" rx="4" fill="#fda4af"/>
                <path d="M28 34v-8a12 12 0 0124 0v8" stroke="#f43f5e" stroke-width="3" stroke-linecap="round"/>
                <circle cx="40" cy="47" r="4" fill="#fff1f2"/>
                <rect x="38.5" y="46" width="3" height="7" rx="1.5" fill="#f43f5e"/>
            </svg>
        </div>
        <span class="absolute -top-2 -right-2 text-5xl font-black text-rose-100 select-none leading-none">403</span>
    </div>

    <!-- Text -->
    <h1 class="text-3xl font-bold text-slate-800 mb-3">Access Forbidden</h1>
    <p class="text-slate-500 text-sm max-w-sm mb-2">
        You don't have permission to access this resource. Please contact your system administrator.
    </p>
    @if(isset($exception) && $exception->getMessage())
    <p class="text-xs text-rose-500 mb-4 font-mono bg-rose-50 border border-rose-100 px-3 py-1.5 rounded-lg inline-block">
        {{ $exception->getMessage() }}
    </p>
    @else
    <p class="text-xs text-slate-400 mb-8 font-mono bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-lg inline-block">
        Error 403 &bull; {{ request()->url() }}
    </p>
    @endif

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ url()->previous('#') !== '#' ? url()->previous() : '/' }}"
           class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-sm rounded-xl shadow-sm transition-colors">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Go Back
        </a>
        <a href="{{ url('/') }}"
           class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors">
            <i class="fa-solid fa-house mr-1.5"></i> Dashboard
        </a>
    </div>
</div>
@endsection
