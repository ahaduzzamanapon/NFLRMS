@extends('layouts.app')
@section('title', 'Firearms Overview List')

@section('content')
<div class="max-w-full space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                @php
                    $backRoute = auth()->user()->hasRole('system_admin') ? route('admin.dashboard') : route('senior_secretary.dashboard');
                @endphp
                <a href="{{ $backRoute }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors text-xs">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">Firearms Overview List</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Consolidated firearm inventory across individual citizen licensees &amp; commercial dealers</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $backRoute }}" class="px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-gauge text-slate-500"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Total Firearms</span>
                <div class="text-2xl font-extrabold text-cyan-700 tracking-tight mt-0.5">{{ number_format($totalCount) }}</div>
                <span class="text-[10px] text-slate-500 font-medium">Exact sum across all records</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-gun"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Citizen Firearms</span>
                <div class="text-2xl font-extrabold text-blue-600 tracking-tight mt-0.5">{{ number_format($citizenCount) }}</div>
                <span class="text-[10px] text-slate-500 font-medium">Personal security &amp; sports</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Dealer Firearms Stock</span>
                <div class="text-2xl font-extrabold text-purple-600 tracking-tight mt-0.5">{{ number_format($dealerCount) }}</div>
                <span class="text-[10px] text-slate-500 font-medium">Commercial inventory ledger</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-store"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Search and Filter Bar -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('overview.firearms') }}" class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, ref, or item..." class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-gov-green/20 focus:border-gov-green">
                </div>
                <select name="user_type" onchange="this.form.submit()" class="w-full sm:w-40 py-1.5 px-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-gov-green/20 focus:border-gov-green">
                    <option value="">All User Types</option>
                    <option value="Citizen" {{ request('user_type') === 'Citizen' ? 'selected' : '' }}>Citizen</option>
                    <option value="Dealer" {{ request('user_type') === 'Dealer' ? 'selected' : '' }}>Dealer</option>
                </select>
                @if(request('search') || request('user_type'))
                    <a href="{{ route('overview.firearms') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold px-2 py-1">Clear</a>
                @endif
            </form>
            <div class="text-xs text-slate-500 font-medium">
                Showing <span class="font-bold text-slate-800">{{ $items->count() }}</span> records
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[750px]">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold uppercase text-slate-500 tracking-wider">
                        <th class="p-3.5 pl-5">Holder Name</th>
                        <th class="p-3.5">User Type</th>
                        <th class="p-3.5">Reference / ID</th>
                        <th class="p-3.5">Item / Model</th>
                        <th class="p-3.5">Bore / Calibre</th>
                        <th class="p-3.5 text-center">Quantity</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs divide-y divide-slate-100">
                    @forelse($items as $item)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="p-3.5 pl-5 font-semibold text-slate-900">
                            {{ $item['name'] }}
                            <div class="text-[10px] text-slate-400 font-normal">{{ $item['district'] }}, {{ $item['thana'] }}</div>
                        </td>
                        <td class="p-3.5">
                            @if($item['user_type'] === 'Citizen')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-user text-[8px]"></i> Citizen
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-store text-[8px]"></i> Dealer
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5 font-mono text-gov-green font-semibold">{{ $item['reference'] }}</td>
                        <td class="p-3.5 font-medium text-slate-800">{{ $item['item_type'] }}</td>
                        <td class="p-3.5 text-slate-600 font-mono">{{ $item['bore'] }}</td>
                        <td class="p-3.5 text-center">
                            <span class="inline-block px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-900 font-extrabold font-mono text-xs border border-slate-200">
                                {{ number_format($item['quantity']) }}
                            </span>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td class="p-3.5 pr-5 text-right">
                            <button onclick="openDetailModal({{ json_encode($item) }})" class="px-3 py-1 bg-gov-green/10 hover:bg-gov-green hover:text-white text-gov-green text-xs font-bold rounded-lg transition-colors">
                                View Details
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400 font-medium">No firearm records found matching your criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3.5 border-t border-slate-100 bg-slate-50/50 text-[11px] text-slate-500 text-center font-medium">
            Total Firearms Quantity across list: <strong class="text-cyan-700 font-bold font-mono">{{ number_format($totalCount) }}</strong> units
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="firearm-detail-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                    <i class="fa-solid fa-gun"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Firearm Record Details</h3>
                    <p class="text-[10px] text-slate-400" id="modal-ref"></p>
                </div>
            </div>
            <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 transition-colors p-1 text-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-4 text-xs">
            <div class="grid grid-cols-2 gap-3 bg-slate-50 rounded-xl p-3 border border-slate-100">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Holder Name</span>
                    <strong class="text-slate-900 text-sm" id="modal-name"></strong>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">User Category</span>
                    <span id="modal-user-type" class="inline-block mt-0.5"></span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NID / Trade Reg</span>
                    <span class="font-mono font-semibold text-slate-700" id="modal-nid"></span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 inline-block mt-0.5" id="modal-status"></span>
                </div>
            </div>

            <div class="space-y-2">
                <h4 class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Item &amp; Weapon Details</h4>
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-2.5 bg-white border border-slate-200/80 rounded-xl">
                        <span class="text-[10px] text-slate-400 block">Firearm Model</span>
                        <strong class="text-slate-800" id="modal-item"></strong>
                    </div>
                    <div class="p-2.5 bg-white border border-slate-200/80 rounded-xl">
                        <span class="text-[10px] text-slate-400 block">Bore / Calibre</span>
                        <strong class="text-slate-800 font-mono" id="modal-bore"></strong>
                    </div>
                    <div class="p-2.5 bg-white border border-slate-200/80 rounded-xl">
                        <span class="text-[10px] text-slate-400 block">Authorized Quantity</span>
                        <strong class="text-cyan-700 font-mono text-sm" id="modal-qty"></strong>
                    </div>
                    <div class="p-2.5 bg-white border border-slate-200/80 rounded-xl">
                        <span class="text-[10px] text-slate-400 block">District / Thana</span>
                        <strong class="text-slate-800" id="modal-location"></strong>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <h4 class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Licensing Authority &amp; History</h4>
                <div class="p-3 bg-white border border-slate-200/80 rounded-xl space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Address:</span>
                        <span class="font-medium text-slate-800" id="modal-address"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Issuing Body:</span>
                        <span class="font-medium text-slate-800" id="modal-issuer"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Issue / Expiry:</span>
                        <span class="font-medium text-slate-800" id="modal-dates"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Last Verified:</span>
                        <span class="font-semibold text-emerald-700" id="modal-verified"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button onclick="closeDetailModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-xs rounded-xl transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function openDetailModal(item) {
    document.getElementById('modal-ref').innerText = item.reference;
    document.getElementById('modal-name').innerText = item.name;
    document.getElementById('modal-user-type').innerHTML = item.user_type === 'Citizen'
        ? '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100"><i class="fa-solid fa-user text-[8px]"></i> Citizen</span>'
        : '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100"><i class="fa-solid fa-store text-[8px]"></i> Dealer</span>';
    document.getElementById('modal-nid').innerText = item.nid_trade;
    document.getElementById('modal-status').innerText = item.status;
    document.getElementById('modal-item').innerText = item.item_type;
    document.getElementById('modal-bore').innerText = item.bore;
    document.getElementById('modal-qty').innerText = item.quantity + ' unit(s)';
    document.getElementById('modal-location').innerText = item.district + ', ' + item.thana;
    document.getElementById('modal-address').innerText = item.address;
    document.getElementById('modal-issuer').innerText = item.issuer;
    document.getElementById('modal-dates').innerText = item.issue_date + ' to ' + item.expiry_date;
    document.getElementById('modal-verified').innerText = item.verified_date;

    document.getElementById('firearm-detail-modal').classList.remove('hidden');
}

function closeDetailModal() {
    document.getElementById('firearm-detail-modal').classList.add('hidden');
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDetailModal();
    }
});
</script>
@endsection
