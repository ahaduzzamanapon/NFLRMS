@extends('layouts.app')
@section('title', 'ওয়ার্কফ্লো কনফিগ')

@section('content')
<div class="w-full space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900">ওয়ার্কফ্লো কনফিগ</h2>
            <p class="text-xs text-slate-500 mt-1">প্রতিটি আবেদন মডিউলে একটি করে ওয়ার্কফ্লো — প্রতিটিতে ক্রমানুসারে অনুমোদনের ধাপ সেট করুন।</p>
        </div>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-700">
        <i class="fa-solid fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif

    <!-- 4 Workflow Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($workflows as $wf)
        @php
            $icons = [
                'citizen_new'   => ['icon' => 'fa-id-card', 'bg' => 'bg-blue-50', 'iconColor' => 'text-blue-600', 'border' => 'border-blue-200'],
                'citizen_renew' => ['icon' => 'fa-rotate',  'bg' => 'bg-teal-50',  'iconColor' => 'text-teal-600',  'border' => 'border-teal-200'],
                'dealer_new'    => ['icon' => 'fa-store',   'bg' => 'bg-amber-50', 'iconColor' => 'text-amber-600', 'border' => 'border-amber-200'],
                'dealer_renew'  => ['icon' => 'fa-arrows-rotate', 'bg' => 'bg-rose-50', 'iconColor' => 'text-rose-600', 'border' => 'border-rose-200'],
            ];
            $cfg = $icons[$wf->key] ?? ['icon' => 'fa-diagram-project', 'bg' => 'bg-slate-50', 'iconColor' => 'text-slate-600', 'border' => 'border-slate-200'];
            $encId = \Illuminate\Support\Facades\Crypt::encryptString($wf->id);
        @endphp
        <div class="bg-white rounded-xl border {{ $cfg['border'] }} shadow-sm p-5 flex flex-col gap-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl {{ $cfg['bg'] }} flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid {{ $cfg['icon'] }} {{ $cfg['iconColor'] }} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $wf->name }}</h3>
                        @if($wf->name_bn)
                        <p class="text-xs text-slate-500 font-bn mt-0.5">{{ $wf->name_bn }}</p>
                        @endif
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border
                    {{ $wf->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                    {{ $wf->is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                </span>
            </div>

            <p class="text-xs text-slate-500">{{ $wf->description ?: 'কোনো বিবরণ যোগ হয়নি।' }}</p>

            <!-- Step count + actions -->
            <div class="flex items-center justify-between border-t border-slate-100 pt-3 mt-auto">
                <span class="text-xs font-semibold text-slate-600">
                    <i class="fa-solid fa-list-check mr-1 text-gov-green"></i>
                    {{ $wf->steps_count }} টি ধাপ
                </span>
                <div class="flex gap-2">
                    <a href="{{ route('admin.workflow_organogram.edit', $encId) }}"
                       class="px-3 py-1.5 text-[11px] font-bold rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition-colors">
                        <i class="fa-solid fa-pen-to-square mr-1"></i> সম্পাদনা
                    </a>
                    <a href="{{ route('admin.workflow_organogram.show', $encId) }}"
                       class="px-3 py-1.5 text-[11px] font-bold rounded-lg bg-gov-green hover:bg-gov-light text-white transition-colors shadow-sm">
                        <i class="fa-solid fa-diagram-project mr-1"></i> ধাপ ব্যবস্থাপনা
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
