@extends('layouts.app')
@section('title', $workflow->name . ' — ধাপ ব্যবস্থাপনা')

@section('content')
@php
    $encWfId = \Illuminate\Support\Facades\Crypt::encryptString($workflow->id);
@endphp
<div class="w-full space-y-5">

    <!-- Breadcrumb + Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.workflow_organogram.index') }}" class="hover:text-gov-green">ওয়ার্কফ্লো কনফিগ</a>
                <span class="mx-1">›</span>
                <span class="text-slate-600 font-semibold">{{ $workflow->name }}</span>
            </div>
            <h2 class="text-xl font-bold font-serif text-slate-900">{{ $workflow->name }}</h2>
            @if($workflow->name_bn)
            <p class="text-xs text-slate-500 font-bn mt-0.5">{{ $workflow->name_bn }}</p>
            @endif
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.workflow_organogram.edit', $encWfId) }}"
               class="px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition-colors">
                <i class="fa-solid fa-pen-to-square mr-1"></i> তথ্য সম্পাদনা
            </a>
            <a href="{{ route('admin.workflow_organogram.index') }}"
               class="px-3 py-2 text-xs font-bold rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i> ফিরে যান
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold text-emerald-700">
        <i class="fa-solid fa-check mr-1"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        <!-- LEFT: Organogram Step Chain -->
        <div class="lg:col-span-3 space-y-3">
            <div class="flex items-center justify-between mb-1">
                <h3 class="text-sm font-bold text-slate-800">
                    <i class="fa-solid fa-diagram-project text-gov-green mr-1.5"></i>
                    অনুমোদন শৃঙ্খল ({{ $workflow->steps->count() }} ধাপ)
                </h3>
            </div>

            @if($workflow->steps->isEmpty())
            <div class="bg-white rounded-xl border-2 border-dashed border-slate-200 p-10 text-center text-slate-400">
                <i class="fa-solid fa-circle-nodes text-3xl mb-3 block"></i>
                <p class="font-semibold text-sm">এখনো কোনো ধাপ যোগ হয়নি।</p>
                <p class="text-xs mt-1">নিচের ফর্ম থেকে প্রথম ধাপ যোগ করুন।</p>
            </div>
            @else

            <!-- Vertical chain -->
            <div class="space-y-0">
                @foreach($workflow->steps as $idx => $step)
                @php
                    $encStepId = \Illuminate\Support\Facades\Crypt::encryptString($step->id);
                    $isFirst = $idx === 0;
                    $isLast  = $idx === $workflow->steps->count() - 1;
                @endphp

                <!-- Connector line above (except first) -->
                @if(!$isFirst)
                <div class="flex justify-center">
                    <div class="w-0.5 h-5 bg-slate-300"></div>
                </div>
                @endif

                <!-- Step Card -->
                <div class="bg-white rounded-xl border {{ $step->is_active ? 'border-slate-200' : 'border-slate-100 opacity-60' }} shadow-sm p-4 flex items-start gap-4">
                    <!-- Order bubble -->
                    <div class="w-9 h-9 rounded-full {{ $step->is_active ? 'bg-gov-green' : 'bg-slate-300' }} flex items-center justify-center text-white font-black text-sm flex-shrink-0 shadow-sm">
                        {{ $step->step_order }}
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-slate-900 text-sm">{{ $step->step_name }}</span>
                            @if(!$step->is_active)
                            <span class="px-1.5 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded uppercase">নিষ্ক্রিয়</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            <span class="font-semibold text-gov-green">{{ $step->role_name }}</span>
                            <span class="mx-1 text-slate-300">·</span>
                            <span class="font-mono text-[10px] text-slate-400">{{ $step->role_key }}</span>
                        </div>
                        <!-- Capabilities -->
                        <div class="flex gap-1.5 mt-2 flex-wrap">
                            @if($step->can_approve)
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold rounded">✓ অনুমোদন</span>
                            @endif
                            @if($step->can_reject)
                            <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold rounded">✗ প্রত্যাখ্যান</span>
                            @endif
                            @if($step->can_return)
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold rounded">↩ ফেরত</span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-1.5 flex-shrink-0">
                        <!-- Move up/down -->
                        <div class="flex gap-1">
                            @if(!$isFirst)
                            <form action="{{ route('admin.workflow_organogram.steps.move_up', [$encWfId, $encStepId]) }}" method="POST">
                                @csrf
                                <button type="submit" title="উপরে" class="w-7 h-7 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 text-[11px] flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-chevron-up"></i>
                                </button>
                            </form>
                            @else
                            <div class="w-7 h-7"></div>
                            @endif

                            @if(!$isLast)
                            <form action="{{ route('admin.workflow_organogram.steps.move_down', [$encWfId, $encStepId]) }}" method="POST">
                                @csrf
                                <button type="submit" title="নিচে" class="w-7 h-7 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 text-[11px] flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </form>
                            @else
                            <div class="w-7 h-7"></div>
                            @endif
                        </div>

                        <!-- Edit -->
                        <a href="{{ route('admin.workflow_organogram.steps.edit', [$encWfId, $encStepId]) }}"
                           class="w-full text-center px-2 py-1 text-[10px] font-bold bg-slate-50 border border-slate-200 rounded text-slate-700 hover:bg-slate-100 transition-colors">
                            <i class="fa-solid fa-pen"></i> সম্পাদনা
                        </a>

                        <!-- Delete -->
                        <form action="{{ route('admin.workflow_organogram.steps.destroy', [$encWfId, $encStepId]) }}" method="POST"
                              onsubmit="return confirm('এই ধাপটি মুছে ফেলবেন?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-full px-2 py-1 text-[10px] font-bold bg-rose-50 border border-rose-200 rounded text-rose-600 hover:bg-rose-100 transition-colors">
                                <i class="fa-solid fa-trash"></i> মুছুন
                            </button>
                        </form>
                    </div>
                </div>

                @endforeach

                <!-- End node -->
                <div class="flex justify-center"><div class="w-0.5 h-5 bg-slate-300"></div></div>
                <div class="flex justify-center">
                    <div class="px-4 py-1.5 bg-slate-800 text-white text-[11px] font-bold rounded-full shadow-sm">
                        <i class="fa-solid fa-flag-checkered mr-1"></i> চূড়ান্ত সিদ্ধান্ত / লাইসেন্স ইস্যু
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- RIGHT: Add New Step Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sticky top-4">
                <h4 class="text-sm font-bold text-slate-900 mb-4">
                    <i class="fa-solid fa-plus-circle text-gov-green mr-1.5"></i>
                    নতুন ধাপ যোগ করুন
                </h4>

                <form action="{{ route('admin.workflow_organogram.steps.store', $encWfId) }}" method="POST" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">ভূমিকা (Role) নির্বাচন করুন</label>
                        <select name="role_key" id="role_key_select" required
                                class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none bg-white">
                            <option value="">— ভূমিকা বেছে নিন —</option>
                            @foreach($allRoles as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">ভূমিকার নাম (প্রদর্শন)</label>
                        <input type="text" name="role_name" id="role_name_input" required
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none"
                               placeholder="যেমন: DC Front Desk">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">ধাপের নাম / বিবরণ</label>
                        <input type="text" name="step_name" required
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none"
                               placeholder="যেমন: DC Front Desk যাচাই">
                    </div>

                    <div class="space-y-2 pt-1">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-700">এই ধাপে কী করতে পারবে?</p>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="can_approve" value="1" checked class="rounded text-gov-green focus:ring-gov-green">
                            <span class="text-xs font-semibold text-emerald-700">✓ অনুমোদন করতে পারবে</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="can_reject" value="1" checked class="rounded text-gov-green focus:ring-gov-green">
                            <span class="text-xs font-semibold text-rose-700">✗ প্রত্যাখ্যান করতে পারবে</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="can_return" value="1" class="rounded text-gov-green focus:ring-gov-green">
                            <span class="text-xs font-semibold text-amber-700">↩ ফেরত পাঠাতে পারবে</span>
                        </label>
                    </div>

                    @if($errors->any())
                    <div class="text-rose-600 text-xs font-semibold space-y-0.5">
                        @foreach($errors->all() as $e)
                        <p>• {{ $e }}</p>
                        @endforeach
                    </div>
                    @endif

                    <button type="submit"
                            class="w-full py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors shadow-sm">
                        <i class="fa-solid fa-plus mr-1"></i> ধাপ যোগ করুন
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
// Auto-fill role_name when selecting a role
const roleLabels = @json($allRoles);
document.getElementById('role_key_select').addEventListener('change', function () {
    const nameInput = document.getElementById('role_name_input');
    if (this.value && roleLabels[this.value]) {
        nameInput.value = roleLabels[this.value];
    }
});
</script>
@endsection
