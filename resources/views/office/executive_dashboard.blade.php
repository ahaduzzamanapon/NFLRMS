@extends('layouts.app')
@section('title', 'Executive Dashboard')

@section('content')
<div class="max-w-6xl space-y-5">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black font-serif text-slate-900">Executive Dashboard</h2>
            <p class="text-xs text-slate-500 mt-1">Minister / Secretary · Real-time national oversight (BRS §9.1)</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('verify') }}" class="px-3.5 py-2 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                Check any licence →
            </a>
            <button class="px-3.5 py-2 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                ⬇ Export PDF
            </button>
            <button class="px-3.5 py-2 rounded-lg text-xs font-black bg-rose-600 hover:bg-rose-700 text-white transition-colors">
                ⚡ Emergency Kill-Switch
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
        $kpis = [
            ['label' => 'Active Licenses',         'value' => number_format($stats['approved_licenses']),   'sub' => '+dynamic from DB', 'icon' => '👥', 'color' => 'text-gov-green'],
            ['label' => 'Total Arms in Dealer Stock','value' => number_format($stats['total_dealer_stock']), 'sub' => 'Across registered dealers', 'icon' => '📦', 'color' => 'text-gov-green'],
            ['label' => 'Revenue Collected (FY26)', 'value' => '৳' . number_format($stats['total_revenue']), 'sub' => 'Licence & application fees', 'icon' => '🏦', 'color' => 'text-gov-green'],
            ['label' => 'On-Time Renewal Rate',     'value' => number_format($stats['renewal_rate'], 1) . '%', 'sub' => 'Annual renewal metrics', 'icon' => '📈', 'color' => 'text-gov-green'],
        ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest">{{ $kpi['label'] }}</div>
                <span class="text-base">{{ $kpi['icon'] }}</span>
            </div>
            <div class="text-2xl font-black {{ $kpi['color'] }}">{{ $kpi['value'] }}</div>
            <div class="text-[9px] text-slate-400 mt-0.5">{{ $kpi['sub'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Recent Applications -->
        <div class="md:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-900">Recent Applications</span>
                <a href="{{ route('executive.licenses') }}" class="text-[10px] font-black text-gov-green hover:underline">All Licences →</a>
            </div>
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">
                        <th class="p-3 pl-5">Reference</th>
                        <th class="p-3">Applicant</th>
                        <th class="p-3">Service</th>
                        <th class="p-3 pr-5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($latestApplications as $a)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-3 pl-5 font-bold font-mono text-gov-green text-[10px]">{{ $a->application_number }}</td>
                        <td class="p-3 font-semibold text-slate-800">{{ $a->user->name }}</td>
                        <td class="p-3 text-slate-600">{{ $a->firearm_details['weapon_type'] ?? 'N/A' }}</td>
                        <td class="p-3 pr-5">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border border-slate-200 bg-slate-50 text-slate-600">
                                {{ ucfirst(str_replace('_',' ',$a->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-6 text-center text-slate-400 font-semibold">No applications yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Backlog & Ageing -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                <span class="text-xs font-bold text-slate-900">Backlog & Ageing</span>
            </div>
            <div class="p-5 space-y-4">
                @php
                use App\Models\Application;
                $backlogs = [
                    ['label'=>'Under DC Review',    'count'=> Application::where('current_actor_role','district_commissioner')->count(), 'color'=>'bg-gov-green'],
                    ['label'=>'Awaiting Vetting',   'count'=> Application::where('status','pending_vetting')->count(), 'color'=>'bg-amber-400'],
                    ['label'=>'MoHA Review',         'count'=> Application::whereIn('status',['referred_moha','moha_processing','pending_screening','screened'])->count(), 'color'=>'bg-blue-400'],
                    ['label'=>'SLA Breach (>10d)',   'count'=> Application::where('created_at','<',now()->subDays(10))->whereNotIn('status',['approved','rejected','license_issued'])->count(), 'color'=>'bg-rose-500'],
                ];
                $maxCount = max(array_column($backlogs,'count'), 1);
                @endphp
                @foreach($backlogs as $b)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold text-slate-700">{{ $b['label'] }}</span>
                        <span class="text-xs font-black text-slate-900">{{ $b['count'] }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100">
                        <div class="h-2 rounded-full {{ $b['color'] }}"
                             style="width: {{ $maxCount > 0 ? round(($b['count']/$maxCount)*100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Total Applications Summary -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @php
        $summary = [
            ['label'=>'Total Applications', 'value'=>$stats['total_applications'], 'color'=>'text-slate-900'],
            ['label'=>'Licenses Issued',    'value'=>$stats['approved_licenses'],  'color'=>'text-gov-green'],
            ['label'=>'Pending Vettings',   'value'=>$stats['pending_vetting'],    'color'=>'text-amber-600'],
        ];
        @endphp
        @foreach($summary as $s)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-600">{{ $s['label'] }}</span>
            <span class="text-xl font-black {{ $s['color'] }}">{{ number_format($s['value']) }}</span>
        </div>
        @endforeach
    </div>
</div>
@endsection
