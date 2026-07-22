@extends('layouts.app')
@section('title', 'Stock Ledger')

@section('content')
<div class="max-w-6xl space-y-5">

    <!-- Header -->
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-black font-serif text-slate-900">
                {{ auth()->user()->name }} — Stock Ledger
            </h2>
            <p class="text-xs text-slate-500 mt-1">
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
            <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">Total Firearms in Stock</div>
            <div class="text-3xl font-black text-slate-900 mt-1">{{ number_format($totalFirearms) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">Total Ammunition Rounds</div>
            <div class="text-3xl font-black text-slate-900 mt-1">{{ number_format($totalAmmo) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">Anomaly Alerts</div>
            <div class="text-3xl font-black {{ $anomalyAlerts > 0 ? 'text-rose-600' : 'text-gov-green' }} mt-1">{{ $anomalyAlerts }}</div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-widest">Stock Items</span>
            <span class="text-[10px] text-slate-400 font-semibold">{{ $stocks->count() }} items</span>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 text-[10px] font-extrabold uppercase text-slate-400 tracking-wider bg-slate-50">
                    <th class="p-3 pl-5">Item</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Quantity</th>
                    <th class="p-3">Source</th>
                    <th class="p-3">Updated</th>
                    <th class="p-3 pr-5 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($stocks as $s)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5 font-bold text-slate-900">{{ $s->item }}</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border
                            @if($s->category === 'Firearm') border-gov-green/30 bg-emerald-50 text-gov-green
                            @elseif($s->category === 'Ammunition') border-amber-200 bg-amber-50 text-amber-700
                            @else border-slate-200 bg-slate-50 text-slate-600 @endif">
                            {{ $s->category }}
                        </span>
                    </td>
                    <td class="p-3 font-black text-slate-900">{{ number_format($s->quantity) }}</td>
                    <td class="p-3 text-slate-500">{{ $s->source ?? '—' }}</td>
                    <td class="p-3 text-slate-400">{{ $s->updated_at->format('d M Y') }}</td>
                    <td class="p-3 pr-5 text-right">
                        <form action="{{ route('dealer.stock_ledger.delete', $s->id) }}" method="POST"
                              onsubmit="return confirm('Remove this item?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-[10px] font-black text-rose-500 hover:text-rose-700 transition-colors">
                                Remove
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-slate-400 font-bold">
                        No stock items recorded yet. Click "Add Item" to begin.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Stock Modal -->
<div id="add-stock-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-md mx-4 p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900">Add Stock Item</h3>
            <button onclick="document.getElementById('add-stock-modal').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
        </div>

        <form action="{{ route('dealer.stock_ledger.save') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Item Name</label>
                <input type="text" name="item" required
                       class="w-full px-3 py-2.5 text-xs rounded-lg border border-slate-200 outline-none focus:ring-1 focus:ring-gov-green"
                       placeholder="e.g. 12-bore Shotgun, .22 Rifle, 9mm Pistol">
            </div>
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-900 mb-1">Category</label>
                <select name="category" required
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
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('add-stock-modal').classList.remove('hidden');
    });
</script>
@endif
@endsection
