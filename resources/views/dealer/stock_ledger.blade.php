@extends('layouts.app')
@section('title', 'Stock Ledger')

@section('content')
<div class="max-w-6xl space-y-5">

    <!-- Header -->
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900">
                {{ auth()->user()->name }} — Stock Ledger
            </h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                Declared inventory · auditable by MoHA · Issued licences automatically deduct from stock.
            </p>
        </div>
        <button onclick="document.getElementById('add-stock-modal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg shadow-sm transition-colors flex items-center space-x-1.5">
            <span>💾</span><span>Add Item</span>
        </button>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-[9px] font-bold uppercase text-slate-400 tracking-widest">Total Firearms in Stock</div>
            <div class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($totalFirearms) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-[9px] font-bold uppercase text-slate-400 tracking-widest">Total Ammunition Rounds</div>
            <div class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($totalAmmo) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-[9px] font-bold uppercase text-slate-400 tracking-widest">Anomaly Alerts</div>
            <div class="text-3xl font-bold {{ $anomalyAlerts > 0 ? 'text-rose-600' : 'text-gov-green' }} mt-1">{{ $anomalyAlerts }}</div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <span class="text-[10px] font-bold uppercase text-slate-500 tracking-widest">Stock Items</span>
            <span class="text-[10px] text-slate-400 font-semibold">{{ $stocks->count() }} items</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 text-[10px] font-bold uppercase text-slate-400 tracking-wider bg-slate-50">
                    <th class="p-3 pl-5">Item</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Quantity</th>
                    <th class="p-3">Source</th>
                    <th class="p-3">Updated</th>
                    <th class="p-3 pr-5 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs font-normal divide-y divide-slate-100">
                @forelse($stocks as $s)
                <tr class="hover:bg-slate-50/50 transition-colors">
                     <td class="p-3 pl-5 font-semibold text-slate-900">{{ $s->item }}</td>
                    <td class="p-3">
                             <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border
                            @if($s->category === 'Firearm') border-gov-green/30 bg-emerald-50 text-gov-green
                            @elseif($s->category === 'Ammunition') border-amber-200 bg-amber-50 text-amber-700
                            @else border-slate-200 bg-slate-50 text-slate-600 @endif">
                            {{ $s->category }}
                        </span>
                    </td>
                     <td class="p-3 font-bold text-slate-900">{{ number_format($s->quantity) }}</td>
                     <td class="p-3 text-slate-500 font-medium">{{ $s->source ?? '—' }}</td>
                     <td class="p-3 text-slate-400 font-normal">{{ $s->updated_at->format('d M Y') }}</td>
                    <td class="p-3 pr-5 text-right">
                        <form action="{{ route('dealer.stock_ledger.delete', $s->id) }}" method="POST"
                              onsubmit="return confirm('Remove this item?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                     class="text-[10px] font-bold text-rose-500 hover:text-rose-700 transition-colors">
                                Remove
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                     <td colspan="6" class="p-10 text-center text-slate-400 font-medium">
                        No stock items recorded yet. Click "Add Item" to begin.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Stock Modal -->
{{-- <div id="add-stock-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md mx-4 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900">Add Stock Item</h3>
            <button onclick="document.getElementById('add-stock-modal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
        </div>

        <form action="{{ route('dealer.stock_ledger.save') }}" method="POST" class="space-y-4" id="addStockForm">
            @csrf
            <!-- Validation Summary Alert -->
            <div id="addStockValidationAlert" class="hidden bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 9v4m0 4h.01M10.29 3.86l-8.18 14.18A2 2 0 0 0 3.82 21h16.36a2 2 0 0 0 1.71-2.96L13.71 3.86a2 2 0 0 0-3.42 0z"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-xs font-bold text-rose-700 leading-relaxed">
                    Please fill in the highlighted required field(s) above before continuing.
                </span>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Item Name</label>
                <input type="text" name="item" id="stock_item" required
                       class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                       placeholder="e.g. 12-bore Shotgun, .22 Rifle, 9mm Pistol">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Category</label>
                <select name="category" id="stock_category" required
                        class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green bg-white">
                    <option value="Firearm">Firearm</option>
                    <option value="Ammunition">Ammunition</option>
                    <option value="Accessory">Accessory</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Quantity</label>
                <input type="number" name="quantity" min="0" required
                       class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                       placeholder="0">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Source (optional)</label>
                <input type="text" name="source"
                       class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                       placeholder="e.g. Import — Turkey, Local, Import — USA">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-stock-modal').classList.add('hidden')"
                        class="flex-1 py-2.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-lg bg-gov-green hover:bg-gov-light text-white font-bold text-xs shadow transition-colors">
                    + Add Item
                </button>
            </div>
        </form>
    </div>
</div> --}}

<!-- Add Stock Modal -->
<div id="add-stock-modal"
    class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-black/50 flex items-start md:items-center justify-center z-50 p-4 overflow-y-auto">

    <div
        class="bg-white rounded-2xl shadow-2xl w-full max-w-md my-8 md:my-0 max-h-[90vh] flex flex-col overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
            <h3 class="text-sm font-bold text-slate-900">
                Add Stock Item
            </h3>

            <button onclick="document.getElementById('add-stock-modal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-700 font-bold text-lg">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('dealer.stock_ledger.save') }}"
            method="POST"
            class="p-6 space-y-4 overflow-y-auto"
            id="addStockForm"
            novalidate>

            @csrf

            <!-- Validation Summary -->
            <div id="addStockValidationAlert"
                class="hidden bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 flex items-start gap-3">

                <svg class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5"
                    viewBox="0 0 24 24"
                    fill="none">

                    <path
                        d="M12 9v4m0 4h.01M10.29 3.86l-8.18 14.18A2 2 0 0 0 3.82 21h16.36a2 2 0 0 0 1.71-2.96L13.71 3.86a2 2 0 0 0-3.42 0z"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>

                <span class="text-xs font-semibold text-rose-700 leading-relaxed">
                    Please fill in the highlighted required field(s) above before continuing.
                </span>

            </div>

            <!-- Item -->

            <div>

                <label
                    class="text-[9px] font-bold uppercase tracking-widest text-slate-900 block mb-1.5">
                    Item Name
                </label>

                <input
                    type="text"
                    name="item"
                    id="stock_item"
                    required
                    minlength="2"
                    value="{{ old('item') }}"
                    placeholder="e.g. 12-bore Shotgun, .22 Rifle, 9mm Pistol"
                    class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('item') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green">

                <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error"
                    data-for="item"></span>

                @error('item')
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">
                        {{ $message }}
                    </span>
                @enderror

            </div>

            <!-- Category -->

            <div>

                <label
                    class="text-[9px] font-bold uppercase tracking-widest text-slate-900 block mb-1.5">
                    Category
                </label>

                <select
                    name="category"
                    id="stock_category"
                    required
                    class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('category') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green bg-white">

                    <option value="">
                        — Select Category —
                    </option>

                    <option value="Firearm"
                        {{ old('category') == 'Firearm' ? 'selected' : '' }}>
                        Firearm
                    </option>

                    <option value="Ammunition"
                        {{ old('category') == 'Ammunition' ? 'selected' : '' }}>
                        Ammunition
                    </option>

                    <option value="Accessory"
                        {{ old('category') == 'Accessory' ? 'selected' : '' }}>
                        Accessory
                    </option>

                </select>

                <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error"
                    data-for="category"></span>

                @error('category')
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">
                        {{ $message }}
                    </span>
                @enderror

            </div>

            <!-- Quantity -->

            <div>

                <label
                    class="text-[9px] font-bold uppercase tracking-widest text-slate-900 block mb-1.5">
                    Quantity
                </label>

                <input
                    type="number"
                    name="quantity"
                    id="stock_quantity"
                    min="0"
                    required
                    value="{{ old('quantity') }}"
                    placeholder="0"
                    class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('quantity') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green">

                <span class="text-[10px] text-rose-500 font-semibold mt-1 block js-error"
                    data-for="quantity"></span>

                @error('quantity')
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">
                        {{ $message }}
                    </span>
                @enderror

            </div>

            <!-- Source -->

            <div>

                <label
                    class="text-[9px] font-bold uppercase tracking-widest text-slate-900 block mb-1.5">
                    Source (Optional)
                </label>

                <input
                    type="text"
                    name="source"
                    id="stock_source"
                    value="{{ old('source') }}"
                    placeholder="e.g. Import — Turkey, Local, Import — USA"
                    class="w-full px-3.5 py-2.5 text-xs rounded-lg border {{ $errors->has('source') ? 'border-rose-400' : 'border-slate-200' }} outline-none focus:ring-1 focus:ring-gov-green">

                @error('source')
                    <span class="text-[10px] text-rose-500 font-semibold mt-0.5 block">
                        {{ $message }}
                    </span>
                @enderror

            </div>

            <!-- Buttons -->

            <div class="flex gap-3 pt-2">

                <button
                    type="button"
                    onclick="document.getElementById('add-stock-modal').classList.add('hidden')"
                     class="flex-1 py-2.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">

                    Cancel

                </button>

                <button
                    type="submit"
                     class="flex-1 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors">

                    + Add Item

                </button>

            </div>

        </form>

    </div>

</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('add-stock-modal').classList.remove('hidden');
    });
</script>
@endif

<script>
(function () {

    const form = document.getElementById('addStockForm');

    if (!form) return;

    function showError(fieldName, message) {

        const span = form.querySelector(`.js-error[data-for="${fieldName}"]`);

        if (span) {
            span.textContent = message || '';
        }

        const input = form.querySelector(`[name="${fieldName}"]`);

        if (input) {
            input.classList.toggle('border-rose-400', !!message);
        }
    }

    function clearAllErrors() {

        form.querySelectorAll('.js-error').forEach(function (span) {
            span.textContent = '';
        });

        form.querySelectorAll('input, select').forEach(function (element) {
            element.classList.remove('border-rose-400');
        });

    }

    function maybeHideAlert() {

        const alertBox = document.getElementById('addStockValidationAlert');

        const hasError = Array.from(form.querySelectorAll('.js-error'))
            .some(function (span) {
                return span.textContent.trim() !== '';
            });

        if (!hasError) {
            alertBox.classList.add('hidden');
        }

    }

    function validateField(input, message) {

        if (!input.checkValidity()) {

            showError(input.name, message);

            return false;

        }

        showError(input.name, '');

        return true;

    }

    function validateForm() {

        clearAllErrors();

        let valid = true;

        valid = validateField(
            document.getElementById('stock_item'),
            'Item name must be at least 2 characters.'
        ) && valid;

        valid = validateField(
            document.getElementById('stock_category'),
            'Please select a category.'
        ) && valid;

        valid = validateField(
            document.getElementById('stock_quantity'),
            'Quantity is required.'
        ) && valid;

        return valid;

    }

    /*
    |--------------------------------------------------------------------------
    | Live Validation
    |--------------------------------------------------------------------------
    */

    ['stock_item', 'stock_quantity'].forEach(function (id) {

        const element = document.getElementById(id);

        if (!element) return;

        element.addEventListener('blur', function () {

            const messages = {

                stock_item: 'Item name must be at least 2 characters.',

                stock_quantity: 'Quantity is required.'

            };

            validateField(element, messages[id]);

            maybeHideAlert();

        });

        element.addEventListener('input', function () {

            validateField(element, '');

            maybeHideAlert();

        });

    });

    document.getElementById('stock_category')
        ?.addEventListener('change', function () {

            showError('category', '');

            maybeHideAlert();

        });

    /*
    |--------------------------------------------------------------------------
    | Form Submit
    |--------------------------------------------------------------------------
    */

    form.addEventListener('submit', function (e) {

        const alertBox = document.getElementById('addStockValidationAlert');

        if (!validateForm()) {

            e.preventDefault();

            alertBox.classList.remove('hidden');

            const firstErrorField = form.querySelector('.border-rose-400');

            if (firstErrorField) {

                firstErrorField.focus();

                firstErrorField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

            }

            return false;

        }

        alertBox.classList.add('hidden');

    });

})();
</script>
@endsection
