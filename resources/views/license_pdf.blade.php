<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Certificate - {{ $license->license_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; margin: 0 !important; }
            .print-card { shadow: none !important; border: 1px solid #cbd5e1 !important; box-shadow: none !important; }
            .page-container { min-height: auto !important; padding: 1.5rem !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-center page-container">

    @php
        $userRole = $license->user->role->value ?? $license->user->role ?? '';
        $isDealerLicense = Str::contains(strtolower($license->type ?? ''), 'dealer') || Str::contains(strtolower($userRole), 'dealer');
        $verifyUrl = route('verify', ['license_number' => $license->license_number]);
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($verifyUrl);
    @endphp

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="w-full max-w-xl mb-4 flex items-center justify-between no-print bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-2">
            <button onclick="window.history.back()" class="text-xs font-semibold text-slate-500 hover:text-slate-700 flex items-center space-x-1">
                <i class="fa-solid fa-arrow-left"></i><span>Back</span>
            </button>
            <span class="text-slate-300">&bull;</span>
            <span class="text-xs font-bold text-slate-800"><i class="fa-solid fa-shield-halved text-gov-green mr-1.5"></i> Digital License Certificate</span>
        </div>
        <button onclick="window.print()" class="px-4 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-lg shadow-sm transition-colors flex items-center space-x-1.5">
            <i class="fa-solid fa-print"></i><span>Print / Save PDF</span>
        </button>
    </div>

    <!-- Official License Card (Exact Dashboard Match) -->
    <div class="w-full max-w-xl p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xl print-card flex flex-col sm:flex-row justify-between gap-6">
        <div class="flex-grow space-y-4">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2.5">
                    <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Government of Bangladesh" class="w-8 h-8 object-contain shrink-0">
                    <div>
                        <h4 class="text-[10px] font-bold uppercase text-slate-500 leading-none">
                            Government of Bangladesh &bull; MoHA
                        </h4>
                        <h3 class="text-xs font-bold text-slate-900 mt-1 leading-none">
                            {{ $isDealerLicense ? 'Dealer Dealing Licence' : 'Firearm Licence' }}
                        </h3>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                    {{ $license->status === 'active' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/25' : 'bg-rose-500/10 text-rose-600 border border-rose-500/25' }}">
                    {{ ucfirst($license->status) }}
                </span>
            </div>

            <!-- Fields Grid -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4 text-[11px]">
                <div>
                    <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">
                        {{ $isDealerLicense ? 'Firm Name' : 'Holder' }}
                    </span>
                    <span class="font-bold text-slate-900">{{ $license->user->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">
                        {{ $isDealerLicense ? 'Licence Class' : 'Weapon' }}
                    </span>
                    <span class="font-bold text-slate-900">
                        {{ $isDealerLicense ? 'Class A Dealer' : ($license->firearm_details['weapon_type'] ?? 'N/A') }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">Licence No.</span>
                    <span class="font-bold text-slate-900 uppercase font-mono break-all">{{ $license->license_number }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">District</span>
                    <span class="font-bold text-slate-900">{{ $license->application->district->name ?? $license->user->district->name ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">Issued</span>
                    <span class="font-bold text-slate-900">{{ optional($license->issue_date)->format('d M Y') ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold uppercase tracking-wider text-[9px]">Expires</span>
                    <span class="font-bold text-slate-900 {{ optional($license->expiry_date)->isPast() ? 'text-rose-600' : '' }}">
                        {{ optional($license->expiry_date)->format('d M Y') ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- QR Code Side -->
        <div class="flex-shrink-0 flex flex-col items-center justify-between sm:border-l border-slate-100 sm:pl-6 text-center">
            <div class="w-24 h-24 bg-white border border-slate-200 rounded-xl p-1.5 flex items-center justify-center shadow-sm">
                <img src="{{ $qrUrl }}" alt="License Verification QR Code" class="w-full h-full object-contain">
            </div>
            <span class="text-[8px] text-slate-400 font-medium uppercase mt-2 leading-tight">Scan to verify<br>on NFLRMS portal</span>
        </div>
    </div>

</body>
</html>
