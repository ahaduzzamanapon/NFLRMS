@extends('layouts.app')
@section('title', 'Immutable Audit Log')

@section('content')
<div class="max-w-5xl space-y-5">

    <div>
        <h2 class="text-2xl font-black font-serif text-slate-900">Immutable Audit Log</h2>
        <p class="text-xs text-slate-500 mt-1">Every status change, approval & override — attributed & timestamped (FR-ADM-03)</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">
                    <th class="p-3 pl-5">Timestamp</th>
                    <th class="p-3">Actor</th>
                    <th class="p-3">Case</th>
                    <th class="p-3 pr-5">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="p-3 pl-5 text-slate-500 font-semibold whitespace-nowrap">
                        {{ $log->created_at->format('d M Y, H:i:s') }}
                    </td>
                    <td class="p-3">
                        @if($log->actor)
                        <span class="font-bold text-slate-900">{{ $log->actor->name }}</span>
                        <div class="text-[9px] text-slate-400">{{ $log->actor->role->label() }}</div>
                        @else
                        <span class="text-slate-400 font-semibold italic">System</span>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($log->application)
                        <span class="font-bold font-mono text-gov-green text-[10px]">{{ $log->application->application_number }}</span>
                        @else
                        <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="p-3 pr-5 text-slate-700 font-semibold">{{ $log->remarks }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-400 font-bold">No audit log entries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
