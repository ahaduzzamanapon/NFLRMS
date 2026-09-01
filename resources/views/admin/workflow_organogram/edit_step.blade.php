@extends('layouts.app')
@section('title', 'ধাপ সম্পাদনা')

@section('content')
<div class="w-full max-w-lg space-y-5">

    <div>
        <div class="text-xs text-slate-400 mb-1">
            <a href="{{ route('admin.workflow_organogram.index') }}" class="hover:text-gov-green">ওয়ার্কফ্লো কনফিগ</a>
            <span class="mx-1">›</span>
            <a href="{{ route('admin.workflow_organogram.show', $encryptedWfId) }}" class="hover:text-gov-green">{{ $workflow->name }}</a>
            <span class="mx-1">›</span>
            <span class="text-slate-600">ধাপ সম্পাদনা</span>
        </div>
        <h2 class="text-xl font-bold font-serif text-slate-900">ধাপ সম্পাদনা — ধাপ {{ $step->step_order }}</h2>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.workflow_organogram.steps.update', [$encryptedWfId, $encryptedStepId]) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">ভূমিকা (Role) <span class="text-rose-500">*</span></label>
                <select name="role_key" id="edit_role_key" required
                        class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none bg-white">
                    @foreach($allRoles as $key => $label)
                    <option value="{{ $key }}" {{ old('role_key', $step->role_key) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">ভূমিকার নাম (প্রদর্শন) <span class="text-rose-500">*</span></label>
                <input type="text" name="role_name" id="edit_role_name" value="{{ old('role_name', $step->role_name) }}" required
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">ধাপের নাম <span class="text-rose-500">*</span></label>
                <input type="text" name="step_name" value="{{ old('step_name', $step->step_name) }}" required
                       class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-gov-green outline-none">
            </div>

            <div class="space-y-2">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-700">ক্ষমতা</p>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="can_approve" value="1" {{ $step->can_approve ? 'checked' : '' }} class="rounded text-gov-green">
                    <span class="text-xs font-semibold text-emerald-700">✓ অনুমোদন করতে পারবে</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="can_reject" value="1" {{ $step->can_reject ? 'checked' : '' }} class="rounded text-gov-green">
                    <span class="text-xs font-semibold text-rose-700">✗ প্রত্যাখ্যান করতে পারবে</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="can_return" value="1" {{ $step->can_return ? 'checked' : '' }} class="rounded text-gov-green">
                    <span class="text-xs font-semibold text-amber-700">↩ ফেরত পাঠাতে পারবে</span>
                </label>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $step->is_active ? 'checked' : '' }} class="rounded text-gov-green">
                <span class="text-xs font-semibold text-slate-700">সক্রিয়</span>
            </label>

            @if($errors->any())
            <div class="text-rose-600 text-xs space-y-0.5">
                @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
            </div>
            @endif

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="px-5 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors shadow-sm">
                    <i class="fa-solid fa-save mr-1"></i> আপডেট করুন
                </button>
                <a href="{{ route('admin.workflow_organogram.show', $encryptedWfId) }}"
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                    বাতিল
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const roleLabels = @json($allRoles);
document.getElementById('edit_role_key').addEventListener('change', function () {
    const nameInput = document.getElementById('edit_role_name');
    if (this.value && roleLabels[this.value]) {
        nameInput.value = roleLabels[this.value];
    }
});
</script>
@endsection
