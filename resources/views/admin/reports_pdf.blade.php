<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Statutory Report' }} - NFLRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            body { background: white !important; color: black !important; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 p-4 sm:p-8">

    <!-- Action Bar (No Print) -->
    <div class="max-w-5xl mx-auto mb-6 flex items-center justify-between no-print bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.reports') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-arrow-left"></i><span>Back to Reports</span>
            </a>
            <span class="text-xs font-bold text-slate-900">{{ $title }}</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-4 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i><span>Print / Save PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Report Document Container -->
    <div class="max-w-5xl mx-auto bg-white border border-slate-200 shadow-lg rounded-2xl p-6 sm:p-10 space-y-6">

        <!-- Government Statutory Header -->
        <div class="border-b-2 border-emerald-800 pb-6 text-center space-y-2 relative">
            <div class="flex justify-center mb-2">
                <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Government Seal" class="w-16 h-16 object-contain">
            </div>
            <h1 class="text-base sm:text-lg font-extrabold uppercase text-slate-900 tracking-wider">Government of the People's Republic of Bangladesh</h1>
            <h2 class="text-xs sm:text-sm font-bold text-emerald-800 uppercase">Ministry of Home Affairs &bull; Firearms Section</h2>
            <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-widest">National Firearms License &amp; Record Management System (NFLRMS)</p>
            <div class="text-[10px] text-slate-400 font-mono pt-1">Statutory Regulatory Report &bull; BRS §9.2 Compliance</div>
        </div>

        @if(!empty($isAll) && $isAll)
            <!-- BULK CATALOG PDF -->
            <div class="space-y-8">
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-wrap justify-between items-center text-xs">
                    <div>
                        <span class="font-bold text-slate-900 block text-sm">Statutory Report Catalog Summary</span>
                        <span class="text-slate-500 text-[11px]">All 10 Statutory &amp; Operational System Reports</span>
                    </div>
                    <div class="text-right text-slate-500 text-[11px] font-mono">
                        Generated: {{ date('F d, Y · h:i A') }}
                    </div>
                </div>

                @foreach($allReportsData as $index => $reportData)
                    <div class="space-y-3 {{ !$loop->last ? 'pb-8 border-b border-slate-200' : '' }}">
                        <div class="flex justify-between items-end border-b border-slate-200 pb-2">
                            <div>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase rounded font-mono">{{ $reportData['meta']['id'] }}</span>
                                <h3 class="text-sm font-bold text-slate-900 inline-block ml-2">{{ $reportData['meta']['name'] }}</h3>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-500">Total Records: {{ $reportData['totalCount'] }}</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-300 text-[10px] uppercase font-bold text-slate-700">
                                        @foreach($reportData['headers'] as $h)
                                            <th class="p-2">{{ $h }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData['rows'] as $r)
                                        <tr class="border-b border-slate-100">
                                            @foreach($r as $cell)
                                                <td class="p-2 font-normal text-slate-800">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            <!-- SINGLE REPORT PDF -->
            <div class="space-y-6">
                <!-- Report Title & Meta -->
                <div class="bg-slate-50 p-4 sm:p-5 rounded-xl border border-slate-200 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-0.5 bg-emerald-800 text-white text-xs font-mono font-bold rounded">{{ $reportData['meta']['id'] }}</span>
                            <span class="px-2.5 py-0.5 bg-slate-200 text-slate-700 text-[10px] font-bold uppercase rounded tracking-wider">{{ $reportData['meta']['category'] }}</span>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 mt-1">{{ $reportData['meta']['name'] }}</h2>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $reportData['meta']['desc'] }}</p>
                    </div>
                    <div class="text-right text-xs space-y-1">
                        <span class="text-slate-500 block">Generated On: <strong class="text-slate-800 font-mono">{{ date('F d, Y · h:i A') }}</strong></span>
                        <span class="text-slate-500 block">Total Records: <strong class="text-emerald-800 font-bold font-mono">{{ $reportData['totalCount'] }}</strong></span>
                    </div>
                </div>

                <!-- Report Summary Indicators -->
                @if(!empty($reportData['summary']))
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($reportData['summary'] as $sLabel => $sValue)
                            <div class="p-3 bg-emerald-50/60 rounded-lg border border-emerald-200/80">
                                <span class="text-[10px] font-semibold uppercase text-emerald-800 block">{!! $sLabel !!}</span>
                                <span class="text-sm font-bold text-emerald-950 font-mono mt-0.5 block">{!! $sValue !!}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Report Table -->
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-300 text-[10px] uppercase font-bold text-slate-700 tracking-wider">
                                @foreach($reportData['headers'] as $h)
                                    <th class="p-3">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($reportData['rows'] as $r)
                                <tr class="hover:bg-slate-50/50">
                                    @foreach($r as $cell)
                                        <td class="p-3 font-normal text-slate-800">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($reportData['headers']) }}" class="p-6 text-center text-slate-400 font-medium">
                                        No statutory records match the report criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Footer & Official Verification Watermark -->
        <div class="pt-8 border-t border-slate-200 flex flex-wrap justify-between items-end gap-6 text-xs text-slate-500">
            <div class="space-y-1">
                <div class="font-bold text-slate-900 uppercase">National Firearms License &amp; Record Management System</div>
                <div class="text-[10px] text-slate-400">Digitally Generated Official Record &bull; Ministry of Home Affairs &bull; Bangladesh</div>
            </div>
            <div class="text-right space-y-1">
                <div class="font-mono text-[10px] text-slate-400">VERIFICATION HASH: {{ strtoupper(md5(($title ?? 'report').date('Y-m-d'))) }}</div>
                <div class="font-semibold text-emerald-800 text-[11px]"><i class="fa-solid fa-circle-check mr-1"></i> OFFICIAL GOVERNMENT REPORT</div>
            </div>
        </div>

    </div>

</body>
</html>
