<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NFLRMS') — NFLRMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gov: {
                            sidebar: '#0b1e17',
                            green:   '#1a7a52',
                            light:   '#22a86e',
                            gold:    '#e8b84b',
                        }
                    },
                    fontFamily: {
                        sans:  ['"Inter"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.65) !important;
            transition: all 0.15s;
            text-decoration: none;
            white-space: nowrap;
        }
        .nav-link:hover { background: rgba(255,255,255,0.07); color: #ffffff !important; }
        .nav-link.active {
            background: #d99432 !important;
            color: #0b1e17 !important;
            font-weight: 800;
        }
        .nav-section {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.35);
            padding: 0 12px;
            margin-top: 20px;
            margin-bottom: 4px;
        }
        .nav-icon { font-size: 15px; width: 20px; text-align: center; flex-shrink: 0; }
    </style>
</head>

<body class="h-full font-sans antialiased bg-[#faf8f5] text-slate-800 flex overflow-hidden">

    <!-- ===== SIDEBAR ===== -->
    <aside style="width:256px; background:#0b2519; border-right:1px solid rgba(255,255,255,0.06);" class="flex-shrink-0 flex flex-col overflow-y-auto">        <!-- Brand -->
        <div style="padding:24px 20px 16px; border-bottom:1px solid rgba(255,255,255,0.06);">
            <div class="flex items-center gap-3 mb-3">
                <div style="width:36px;height:36px;background:#e8b84b;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;color:#0b1e17;flex-shrink:0;font-weight:900;">🏛</div>
                <div>
                    <div style="color:#fff;font-weight:900;font-size:14px;letter-spacing:-0.01em;line-height:1.1;">NFLRMS</div>
                    <div style="color:#e8b84b;font-size:12px;font-weight:700;margin-top:2px;line-height:1.1;">Home Affairs</div>
                </div>
            </div>
            <p style="font-size:9px;color:rgba(255,255,255,0.4);font-weight:500;line-height:1.4;margin:0;">National Firearms Licensing &amp; Renewal<br>Management System</p>
        </div>

        <!-- User Pill -->
        @auth
        <div style="padding:12px 16px;border-bottom:1px solid rgba(255,255,255,0.06);">
            <div style="font-size:9px;color:rgba(255, 255, 255, 0.35);font-weight:800;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:8px;">Signed in as</div>
            <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;justify-content:between;gap:8px;padding:12px 14px;border-radius:12px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);text-decoration:none;transition:background 0.15s;width:100%;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                <div style="display:flex;align-items:center;gap:8px;min-width:0;flex:1;">
                    <span style="color:#10b981;font-size:12px;flex-shrink:0;">➔</span>
                    <span style="color:#fff;font-weight:700;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</span>
                </div>
                <svg style="width:12px;height:12px;color:rgba(255, 255, 255, 0.5);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
            </a>
        </div>

        <!-- Nav -->
        <nav style="flex:1;padding:8px 12px;" class="space-y-0.5">
            @php 
                $role = auth()->user()->role; 
                $roleVal = $role instanceof \App\Enums\Role ? $role->value : $role;
            @endphp

            {{-- SYSTEM ADMIN --}}
            @if($role === \App\Enums\Role::SystemAdmin)
                <div class="nav-section">System Administration</div>
                @php $adminLinks = [
                    ['route'=>'admin.dashboard', 'icon'=>'👤','label'=>'User Management'],
                    ['route'=>'admin.fee_config', 'icon'=>'💵','label'=>'Fee & Fine Config'],
                    ['route'=>'admin.acl',         'icon'=>'🔑','label'=>'ACL / Permissions'],
                    ['route'=>'admin.api_config',  'icon'=>'🔌','label'=>'API Configuration'],
                    ['route'=>'admin.audit_log',   'icon'=>'📝','label'=>'Audit Log'],
                    ['route'=>'admin.reports',     'icon'=>'📊','label'=>'Reports & Analytics'],
                ]; @endphp
                @foreach($adminLinks as $lnk)
                <a href="{{ route($lnk['route']) }}" class="nav-link {{ Route::currentRouteName()===$lnk['route']?'active':'' }}">
                    <span class="nav-icon">{{ $lnk['icon'] }}</span><span>{{ $lnk['label'] }}</span>
                </a>
                @endforeach

            {{-- CITIZEN --}}
            @elseif($roleVal === 'citizen_applicant')
                <div class="nav-section">My Licences</div>
                <a href="{{ route('citizen.dashboard') }}" class="nav-link {{ Route::currentRouteName()==='citizen.dashboard'?'active':'' }}">
                    <span class="nav-icon">📄</span><span>My Applications</span>
                </a>
                <a href="{{ route('citizen.apply') }}" class="nav-link {{ Route::currentRouteName()==='citizen.apply'?'active':'' }}">
                    <span class="nav-icon">➕</span><span>New License</span>
                </a>
                <a href="{{ route('citizen.renew_general') }}" class="nav-link {{ Route::currentRouteName()==='citizen.renew_general'?'active':'' }}">
                    <span class="nav-icon">🔄</span><span>Renew License</span>
                </a>
                <a href="{{ route('verify') }}" class="nav-link {{ Route::currentRouteName()==='verify'?'active':'' }}">
                    <span class="nav-icon">🔍</span><span>Verify Certificate</span>
                </a>
                <div class="nav-section">Account</div>
                <a href="{{ route('profile.edit') }}" class="nav-link {{ Route::currentRouteName()==='profile.edit'?'active':'' }}">
                    <span class="nav-icon">👤</span><span>My Profile</span>
                </a>

            {{-- DEALER --}}
            @elseif($roleVal === 'dealer_applicant')
                <div class="nav-section">Dealer Portal</div>
                <a href="{{ route('dealer.dashboard') }}" class="nav-link {{ Route::currentRouteName()==='dealer.dashboard'?'active':'' }}">
                    <span class="nav-icon">🏪</span><span>Dealer Home</span>
                </a>
                <a href="{{ route('dealer.apply') }}" class="nav-link {{ Route::currentRouteName()==='dealer.apply'?'active':'' }}">
                    <span class="nav-icon">📋</span><span>New Dealing Licence (Form K)</span>
                </a>
                <a href="{{ route('dealer.renew') }}" class="nav-link {{ Route::currentRouteName()==='dealer.renew'?'active':'' }}">
                    <span class="nav-icon">🔄</span><span>Renew Dealing Licence</span>
                </a>
                <a href="{{ route('dealer.stock_ledger') }}" class="nav-link {{ Route::currentRouteName()==='dealer.stock_ledger'?'active':'' }}">
                    <span class="nav-icon">📦</span><span>Stock Ledger</span>
                </a>

            {{-- DC FRONT DESK --}}
            @elseif($role===\App\Enums\Role::DcFrontDesk)
                <div class="nav-section">DC Office</div>
                <a href="{{ route('front_desk.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','front_desk')?'active':'' }}">
                    <span class="nav-icon">📥</span><span>Front Desk Intake</span>
                </a>

            {{-- DC JM BRANCH --}}
            @elseif($role===\App\Enums\Role::DcJmBranch)
                <div class="nav-section">DC Office</div>
                <a href="{{ route('jm_branch.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','jm_branch')?'active':'' }}">
                    <span class="nav-icon">📋</span><span>JM Branch Queue</span>
                </a>

            {{-- DISTRICT COMMISSIONER --}}
            @elseif($role===\App\Enums\Role::DistrictCommissioner)
                <div class="nav-section">DC Office</div>
                <a href="{{ route('dc.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','dc.')?'active':'' }}">
                    <span class="nav-icon">🏛️</span><span>DC Approval Queue</span>
                </a>

            {{-- VETTING --}}
            @elseif(in_array($roleVal,['police_officer','special_branch','nsi_officer','dgfi_officer']))
                <div class="nav-section">Security Vetting</div>
                <a href="{{ route('vetting.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','vetting')?'active':'' }}">
                    <span class="nav-icon">🛡️</span>
                    <span>
                        @if($role === \App\Enums\Role::PoliceOfficer) Police Vetting Queue
                        @elseif($role === \App\Enums\Role::SpecialBranch) SB Vetting Queue
                        @elseif($role === \App\Enums\Role::NsiOfficer) NSI Vetting Queue
                        @else DGFI Vetting Queue
                        @endif
                    </span>
                </a>

            {{-- MoHA --}}
            @elseif(in_array($roleVal,['moha_desk','joint_secretary','senior_secretary','national_screening_committee']))
                <div class="nav-section">MoHA</div>
                <a href="{{ route('moha.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','moha')?'active':'' }}">
                    <span class="nav-icon">🏢</span>
                    <span>
                        @if($role === \App\Enums\Role::MohaDesk) Political-4 / Sasan-4 Desk
                        @elseif($role === \App\Enums\Role::JointSecretary) Joint / Additional Secretary
                        @elseif($role === \App\Enums\Role::NationalScreeningCommittee) Nat. Screening Committee
                        @else Senior Secretary / Minister
                        @endif
                    </span>
                </a>

            {{-- EXECUTIVE --}}
            @elseif($role===\App\Enums\Role::Executive)
                <div class="nav-section">Executive</div>
                <a href="{{ route('executive.dashboard') }}" class="nav-link {{ Route::currentRouteName()==='executive.dashboard'?'active':'' }}">
                    <span class="nav-icon">📊</span><span>Executive Dashboard</span>
                </a>
                <a href="{{ route('executive.licenses') }}" class="nav-link {{ Route::currentRouteName()==='executive.licenses'?'active':'' }}">
                    <span class="nav-icon">📑</span><span>All Licences</span>
                </a>
                <a href="{{ route('executive.dealers') }}" class="nav-link {{ Route::currentRouteName()==='executive.dealers'?'active':'' }}">
                    <span class="nav-icon">🏪</span><span>Dealers &amp; Stock</span>
                </a>
                <a href="{{ route('executive.dealing_central') }}" class="nav-link {{ Route::currentRouteName()==='executive.dealing_central'?'active':'' }}">
                    <span class="nav-icon">🗂️</span><span>Dealing License Central</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="nav-link {{ Route::currentRouteName()==='admin.reports'?'active':'' }}">
                    <span class="nav-icon">📈</span><span>Reports &amp; Analytics</span>
                </a>
            @endif

            {{-- Officer Profile --}}
            @if(!in_array($roleVal,['citizen_applicant','dealer_applicant','system_admin']))
                <div class="nav-section">Account</div>
                <a href="{{ route('profile.edit') }}" class="nav-link {{ Route::currentRouteName()==='profile.edit'?'active':'' }}">
                    <span class="nav-icon">👤</span><span>My Profile</span>
                </a>
            @endif
        </nav>
        @endauth

        <!-- Bottom -->
        <div style="padding:12px 16px 20px;border-top:1px solid rgba(255,255,255,0.06);">



            <div class="flex items-center justify-between">
                @auth
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="font-size:11px;color:rgba(255, 255, 255, 0.5);font-weight:700;background:none;border:none;cursor:pointer;transition:color 0.15s;" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='rgba(255, 255, 255, 0.5)'">
                        → Sign out
                    </button>
                </form>
                @endauth
                <span style="font-size:8px;color:rgba(255, 255, 255, 0.25);font-weight:700;">v1.0 · PROD</span>
            </div>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <div class="flex-grow flex flex-col overflow-hidden">

        <!-- Header Bar -->
        <header class="h-14 bg-transparent border-b border-slate-200/60 flex items-center justify-between px-7 flex-shrink-0">
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                <span>🏢</span>
                <span class="font-semibold text-slate-700">Ministry of Home Affairs</span>
                <span class="text-slate-300">·</span>
                <span>Government of the People's Republic of Bangladesh</span>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System Live</span>
                </div>
                <div class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-black text-slate-600 uppercase tracking-wider">
                    @yield('title','Dashboard')
                </div>
                @auth
                <a href="{{ route('profile.edit') }}"
                   style="width:34px;height:34px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#16a34a;font-size:11px;font-weight:900;text-decoration:none;"
                   onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                    @php
                        $ws = explode(' ',auth()->user()->name);
                        echo count($ws)>=2?strtoupper(substr($ws[0],0,1).substr($ws[1],0,1)):strtoupper(substr(auth()->user()->name,0,2));
                    @endphp
                </a>
                @endauth
            </div>
        </header>

        <!-- Content -->
        <main class="flex-grow overflow-y-auto p-7">

            @if(session('success'))
            <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                <span class="text-emerald-500 flex-shrink-0">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('warning'))
            <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold">
                <span class="flex-shrink-0">⚠️</span>
                <span>{{ session('warning') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                <span class="flex-shrink-0">✕</span>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>

