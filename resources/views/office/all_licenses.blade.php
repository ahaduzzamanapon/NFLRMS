@extends('layouts.app')
@section('title', 'All Licence Records')

@section('content')
<div class="max-w-6xl space-y-5">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">All Licence Records</h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">{{ $licenses->total() }} total licenses in the system</p>
        </div>
        <button class="px-4 py-2 bg-gov-green hover:bg-gov-light text-white font-semibold text-xs rounded-lg flex items-center space-x-1.5 shadow-sm transition-colors">
            <span>⬇</span><span>Export PDF</span>
        </button>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-semibold uppercase text-slate-500 tracking-wider">
                    <th class="p-3 pl-5">Licence No.</th>
                    <th class="p-3">Holder</th>
                    <th class="p-3">Weapon</th>
                    <th class="p-3">District</th>
                    <th class="p-3">Issued</th>
                    <th class="p-3">Expires</th>
                    <th class="p-3 pr-5">Status</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($licenses as $l)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5 font-semibold font-mono text-gov-green">{{ $l->license_number }}</td>
                    <td class="p-3">
                        <div class="font-semibold text-slate-900">{{ $l->user->name ?? 'N/A' }}</div>
                        <div class="text-[9px] text-slate-400 font-normal">{{ $l->user->nid ?? '' }}</div>
                    </td>
                    <td class="p-3 font-medium text-slate-700">{{ $l->firearm_details['weapon_type'] ?? 'N/A' }}</td>
                    <td class="p-3 text-slate-600 font-normal">{{ $l->user->district->name ?? 'N/A' }}</td>
                    <td class="p-3 text-slate-500 font-normal">{{ $l->issue_date->format('d M Y') }}</td>
                    <td class="p-3 text-slate-500 font-normal">{{ $l->expiry_date->format('d M Y') }}</td>
                    <td class="p-3 pr-5">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold uppercase border
                            @if($l->status === 'active') border-emerald-200 bg-emerald-50 text-emerald-700
                            @elseif($l->status === 'suspended') border-amber-200 bg-amber-50 text-amber-700
                            @else border-rose-200 bg-rose-50 text-rose-700 @endif">
                            {{ ucfirst($l->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-slate-400 font-normal">No license records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($licenses->hasPages())
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
            {{ $licenses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
