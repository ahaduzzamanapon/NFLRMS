@extends('layouts.app')
@section('title', 'Application Tracking')

@section('content')
<div class="w-full space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-serif text-slate-900 leading-tight">
                Application Tracking
            </h2>
            <p class="text-xs text-slate-500 mt-1 font-normal">
                Track real-time processing status and workflow progression of your firearm &amp; dealer license applications.
            </p>
        </div>
        <div class="flex-shrink-0">
            <a href="{{ auth()->user()->hasRole(\App\Enums\Role::DealerApplicant) ? route('dealer.apply') : route('citizen.apply') }}"
               class="px-4 py-2.5 bg-gov-green hover:bg-gov-light text-white font-bold text-xs rounded-lg transition-colors flex items-center space-x-1.5 shadow-sm">
                <span>+</span>
                <span>New Application</span>
            </a>
        </div>
    </div>

    <!-- Summary Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Total Submitted</span>
            <p class="text-2xl font-bold font-serif text-slate-800 mt-1">{{ $applications->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">In Processing</span>
            <p class="text-2xl font-bold font-serif text-amber-600 mt-1">{{ $applications->whereNotIn('status', ['approved', 'license_issued', 'rejected'])->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Approved / Issued</span>
            <p class="text-2xl font-bold font-serif text-emerald-600 mt-1">{{ $applications->whereIn('status', ['approved', 'license_issued'])->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] sm:text-[11px] font-semibold uppercase text-slate-400 tracking-wider">Requires Action</span>
            <p class="text-2xl font-bold font-serif text-rose-600 mt-1">{{ $applications->whereIn('status', ['payment_pending', 'rejected'])->count() }}</p>
        </div>
    </div>

    <!-- Applications Tracking Table / Cards -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden space-y-0">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-lg">📍</span>
                <h3 class="text-sm font-bold text-slate-900">Your Tracked Applications</h3>
            </div>
            <span class="text-xs text-slate-400 font-medium">{{ $applications->count() }} record(s)</span>
        </div>

        @if($applications->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto">
                    📄
                </div>
                <h4 class="text-sm font-bold text-slate-700">No Applications Found</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    You have not submitted any firearm or dealer license applications yet.
                </p>
                <div class="pt-2">
                    <a href="{{ auth()->user()->hasRole(\App\Enums\Role::DealerApplicant) ? route('dealer.apply') : route('citizen.apply') }}"
                       class="inline-flex items-center space-x-1.5 px-4 py-2 bg-gov-green text-white text-xs font-bold rounded-lg shadow hover:bg-gov-light transition-colors">
                        <span>Start New Application</span>
                    </a>
                </div>
            </div>
        @else
            <!-- Desktop Table View -->
            <div class="table-responsive hidden sm:block">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[10px] font-semibold uppercase text-slate-500 tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Application Reference</th>
                            <th class="py-3 px-4">Type / Purpose</th>
                            <th class="py-3 px-4">Submitted Date</th>
                            <th class="py-3 px-4">Current Status</th>
                            <th class="py-3 px-4">Current Stage</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($applications as $app)
                            @php
                                $status = $app->status;
                                $isRejected = str_contains($status, 'rejected');
                                $encryptedId = \Illuminate\Support\Facades\Crypt::encryptString($app->id);

                                $stageLabelMap = [
                                    'submitted' => 'DC Office Intake',
                                    'received' => 'DC JM Branch',
                                    'pending_vetting' => 'Security Vetting (Police/SB/NSI/DGFI)',
                                    'vetted_cleared' => 'Vetting Cleared',
                                    'vetted_flagged' => 'Vetting Flagged',
                                    'recommended' => 'DC Approval',
                                    'referred_moha' => 'MoHA Desk Review',
                                    'moha_processing' => 'MoHA Political Desk',
                                    'pending_screening' => 'National Screening Committee',
                                    'screened' => 'MoHA Executive Desk',
                                    'waiting_for_license_fee' => 'Payment Required (License Fee)',
                                    'approved' => 'Approved (Pending Printing)',
                                    'license_issued' => 'License Issued',
                                    'payment_pending' => 'Payment Required (Service Fee)',
                                ];
                                $stageName = $stageLabelMap[$status] ?? ucfirst(str_replace('_', ' ', $status));
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-mono font-bold text-slate-900">{{ $app->application_number }}</div>
                                    <span class="text-[10px] text-slate-400 block font-sans font-normal">{{ $app->district->name ?? 'District DC Office' }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-800">
                                    {{ ucfirst(str_replace('_', ' ', $app->type)) }}
                                    <span class="text-[10px] text-slate-400 block font-normal">
                                        {{ $app->firearm_details['weapon_type'] ?? 'Firearms Licensing' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-medium">
                                    {{ $app->created_at->format('d M, Y') }}
                                    <span class="text-[10px] text-slate-400 block font-normal">{{ $app->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border
                                        @if(in_array($status, ['approved','license_issued','vetted_cleared'])) border-emerald-500/30 bg-emerald-50 text-emerald-700
                                        @elseif($isRejected || $status === 'vetted_flagged') border-rose-500/30 bg-rose-50 text-rose-700
                                        @else border-amber-500/30 bg-amber-50 text-amber-700 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">
                                    {{ $stageName }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ auth()->user()->hasRole(\App\Enums\Role::DealerApplicant) ? route('dealer.show', $encryptedId) : route('citizen.show', $encryptedId) }}"
                                       class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-gov-green hover:bg-gov-light text-white text-[11px] font-semibold transition-colors shadow-sm">
                                        <span>📍 Track Progress</span>
                                        <span>→</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-slate-100">
                @foreach($applications as $app)
                    @php
                        $status = $app->status;
                        $isRejected = str_contains($status, 'rejected');
                        $encryptedId = \Illuminate\Support\Facades\Crypt::encryptString($app->id);

                        $stageLabelMap = [
                            'submitted' => 'DC Office Intake',
                            'received' => 'DC JM Branch',
                            'pending_vetting' => 'Security Vetting',
                            'vetted_cleared' => 'Vetting Cleared',
                            'vetted_flagged' => 'Vetting Flagged',
                            'recommended' => 'DC Approval',
                            'referred_moha' => 'MoHA Desk Review',
                            'moha_processing' => 'MoHA Political Desk',
                            'pending_screening' => 'National Screening Committee',
                            'screened' => 'MoHA Executive Desk',
                            'waiting_for_license_fee' => 'Payment Required (License Fee)',
                            'approved' => 'Approved',
                            'license_issued' => 'License Issued',
                            'payment_pending' => 'Payment Required (Service Fee)',
                        ];
                        $stageName = $stageLabelMap[$status] ?? ucfirst(str_replace('_', ' ', $status));
                    @endphp
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-xs text-slate-900">{{ $app->application_number }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase border
                                @if(in_array($status, ['approved','license_issued','vetted_cleared'])) border-emerald-500/30 bg-emerald-50 text-emerald-700
                                @elseif($isRejected || $status === 'vetted_flagged') border-rose-500/30 bg-rose-50 text-rose-700
                                @else border-amber-500/30 bg-amber-50 text-amber-700 @endif">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-600 space-y-1">
                            <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $app->type)) }} ({{ $app->firearm_details['weapon_type'] ?? 'Licensing' }})</p>
                            <p><strong>Submitted:</strong> {{ $app->created_at->format('d M, Y') }}</p>
                            <p><strong>Stage:</strong> {{ $stageName }}</p>
                        </div>
                        <div class="pt-1">
                            <a href="{{ auth()->user()->hasRole(\App\Enums\Role::DealerApplicant) ? route('dealer.show', $encryptedId) : route('citizen.show', $encryptedId) }}"
                               class="w-full inline-flex items-center justify-center space-x-1 px-3 py-2 rounded-lg bg-gov-green text-white text-xs font-semibold shadow-sm">
                                <span>📍 Track Application Progress →</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
