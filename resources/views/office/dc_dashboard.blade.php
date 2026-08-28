@extends('layouts.app')
@section('title', 'DC Approval Queue')

@section('content')
<div class="max-w-full space-y-5">

    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900">DC Approval Queue</h2>
            <p class="text-xs text-slate-500 mt-1">
                {{ auth()->user()->district->name ?? 'Dhaka' }} District &bull; {{ $applications->count() }} case{{ $applications->count() !== 1 ? 's' : '' }} in your queue
            </p>
        </div>
    </div>

    <!-- Stats -->
    @php
        $inQueue        = $applications->count();
        $awaitingAgency = $applications->where('status','pending_vetting')->count();
        $pendingAction  = $applications->where('status','recommended')->count();
        $slaBreached    = $applications->filter(fn($a) => $a->created_at->diffInDays(now()) >= 10)->count();
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach([
            ['label' => 'In Queue',            'value' => $inQueue,        'color' => 'text-slate-900'],
            ['label' => 'Awaiting Agency',     'value' => $awaitingAgency, 'color' => 'text-gov-green'],
            ['label' => 'Pending Your Action', 'value' => $pendingAction,  'color' => 'text-amber-600'],
            ['label' => 'SLA Breach (10d+)',   'value' => $slaBreached,    'color' => 'text-rose-600'],
        ] as $stat)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3.5 sm:p-4">
            <div class="text-[10px] font-semibold uppercase text-slate-500 tracking-wider">{{ $stat['label'] }}</div>
            <div class="text-2xl sm:text-3xl font-bold {{ $stat['color'] }} mt-1">{{ $stat['value'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2">
        @foreach(['All','Long Gun','Handgun','Dealing'] as $i => $f)
        <button onclick="filterApps('{{ $f }}')" id="ftab-{{ $i }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors
                       {{ $i === 0 ? 'bg-gov-green text-white' : 'bg-white border border-slate-200 text-slate-600 hover:border-gov-green hover:text-gov-green' }}">
            {{ $f }}
        </button>
        @endforeach
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[560px]" id="apps-table">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-semibold uppercase text-slate-500 tracking-wider">
                    <th class="p-3 pl-5">Reference</th>
                    <th class="p-3">Applicant</th>
                    <th class="p-3">Service</th>
                    <th class="p-3">Age</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 pr-5 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-slate-100">
                @forelse($applications as $a)
                @php
                    $ageDays = (int) $a->created_at->diffInDays(now());
                    $weaponType = $a->firearm_details['weapon_type'] ?? 'N/A';
                    $category = match(true) {
                        in_array($weaponType, ['Pistol','Revolver']) => 'Handgun',
                        in_array($weaponType, ['Shotgun','Rifle']) => 'Long Gun',
                        default => 'Dealing',
                    };
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors app-row" data-category="{{ $category }}">
                    <td class="p-3 pl-5">
                        <span class="font-semibold font-mono text-gov-green text-[10px]">{{ $a->application_number }}</span>
                    </td>
                    <td class="p-3">
                        <div class="font-semibold text-slate-900">{{ strtoupper($a->user->name) }}</div>
                        <div class="text-[9px] text-slate-400">NID {{ $a->user->nid ?? 'N/A' }}</div>
                    </td>
                    <td class="p-3 font-medium text-slate-700">
                        {{ ucfirst(str_replace('_', ' ', $a->type)) }} &bull; {{ $weaponType }}
                    </td>
                    <td class="p-3 font-semibold {{ $ageDays >= 10 ? 'text-rose-600' : 'text-slate-600' }}">{{ $ageDays }}d</td>
                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold uppercase border
                            @if($a->status === 'recommended') border-purple-200 bg-purple-50 text-purple-700
                            @elseif(str_contains($a->status,'vetting')) border-amber-200 bg-amber-50 text-amber-700
                            @else border-slate-200 bg-slate-50 text-slate-600 @endif">
                            {{ ucfirst(str_replace('_', ' ', $a->status)) }}
                        </span>
                    </td>
                    <td class="p-3 pr-5 text-right">
                        <a href="{{ route('dc.show', Crypt::encryptString($a->id)) }}"
                           class="text-xs font-semibold text-gov-green hover:underline">Open <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-slate-500 font-medium">No cases pending DC approval.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
function filterApps(cat) {
    document.querySelectorAll('.app-row').forEach(row => {
        row.style.display = (cat === 'All' || row.dataset.category === cat) ? '' : 'none';
    });
    document.querySelectorAll('[id^="ftab-"]').forEach((btn,i) => {
        const tabs = ['All','Long Gun','Handgun','Dealing'];
        btn.className = `px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors ${tabs[i]===cat?'bg-gov-green text-white':'bg-white border border-slate-200 text-slate-600 hover:border-gov-green hover:text-gov-green'}`;
    });
}
</script>
@endsection
