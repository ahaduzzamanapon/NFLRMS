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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
                        sans:  ['"Inter"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        serif: ['"Inter"', 'sans-serif'],
                        bn:    ['"Nikosh"', '"Noto Sans Bengali"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        @font-face {
            font-family: 'Nikosh';
            src: url('{{ asset('fonts/Nikosh.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        body { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; font-size: 15.5px; line-height: 1.55; -webkit-font-smoothing: antialiased; }
        h1, h2, h3, h4, h5, h6, .font-serif { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        h1, .text-3xl, .text-2xl { letter-spacing: -0.02em; }
        h2, h3, .text-xl, .text-lg { letter-spacing: -0.01em; }
        .font-bn, [lang="bn"] { font-family: 'Nikosh', 'Noto Sans Bengali', sans-serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

        /* Global Typography Base Standards (Reference + 1-2px Larger) */
        input, select, textarea, button {
            font-family: inherit;
            font-size: 0.9375rem; /* ~15px (+1px over reference 14px) */
            line-height: 1.45;
        }
        label, .form-label {
            font-size: 0.84375rem; /* ~13.5px (+1.5px over reference 12px) */
            font-weight: 500;
            letter-spacing: 0.01em;
        }
        th {
            font-size: 0.8125rem; /* ~13px (+1px over reference 12px) */
            font-weight: 600;
            letter-spacing: 0.035em;
        }
        td {
            font-size: 0.90625rem; /* ~14.5px (+1px over reference 13.5px) */
            line-height: 1.5;
        }
        .text-\[9px\]  { font-size: 11.5px !important; }
        .text-\[10px\] { font-size: 12px !important; }
        .text-\[11px\] { font-size: 13px !important; }
        .text-xs       { font-size: 13.5px !important; line-height: 1.4 !important; }
        .text-sm       { font-size: 15px !important; line-height: 1.5 !important; }
        .text-base     { font-size: 16px !important; line-height: 1.55 !important; }
        .text-lg       { font-size: 19px !important; line-height: 1.4 !important; }
        .text-xl       { font-size: 21px !important; line-height: 1.35 !important; }
        .text-2xl      { font-size: 26px !important; line-height: 1.3 !important; }
        .text-3xl      { font-size: 32px !important; line-height: 1.25 !important; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9.5px 12px;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 500;
            letter-spacing: 0.01em;
            color: rgba(255, 255, 255, 0.70) !important;
            transition: all 0.15s;
            text-decoration: none;
            white-space: nowrap;
        }
        .nav-link:hover { background: rgba(255,255,255,0.07); color: #ffffff !important; }
        .nav-link.active {
            background: #d99432 !important;
            color: #0b1e17 !important;
            font-weight: 700;
        }
        .nav-section {
            font-size: 12.5px;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.40);
            padding: 0 12px;
            margin-top: 20px;
            margin-bottom: 4px;
        }
        .nav-icon { font-size: 15px; width: 22px; text-align: center; flex-shrink: 0; }

        @media (max-width: 1023px) {
            .mobile-sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                bottom: 0;
                width: 256px !important;
                max-width: 85vw !important;
                z-index: 50;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .mobile-sidebar.open {
                transform: translateX(0) !important;
            }
            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(4px);
                z-index: 45;
            }
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="h-full font-sans antialiased bg-[#faf8f5] text-slate-800 flex overflow-hidden">

    <!-- Sidebar Backdrop for mobile -->
    <div id="sidebar-backdrop" class="sidebar-backdrop hidden" onclick="toggleSidebar(false)"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside style="width:256px; background:#0b2519; border-right:1px solid rgba(255,255,255,0.06);" class="flex-shrink-0 flex flex-col overflow-y-auto mobile-sidebar lg:translate-x-0">
        <!-- Brand -->
        <div style="padding:20px 16px 14px; border-bottom:1px solid rgba(255,255,255,0.06);" class="relative">
            <!-- Close Button (visible only on mobile) -->
            <button type="button" onclick="toggleSidebar(false)" aria-label="Close navigation sidebar" class="lg:hidden absolute top-4 right-3 text-white/60 hover:text-white p-1.5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="flex items-center gap-3 mb-3">
                <img src="{{ asset('assets/brand/govt-logo.png') }}" alt="Government of Bangladesh" class="w-12 h-12 object-contain shrink-0">
                <div>
                    <div style="color:#fff;font-weight:800;font-size: 20px;letter-spacing:-0.01em;line-height:1.1;">NFLRMS</div>
                    <div style="color:#e8b84b;font-size:13px;font-weight:600;margin-top:2px;line-height:1.1;">Home Affairs</div>
                </div>
            </div>
            <p style="font-size:10.5px;color:rgba(255,255,255,0.45);font-weight:500;line-height:1.4;margin:0;">National Firearms Licensing &amp; Renewal<br>Management System</p>
        </div>

        <!-- User Pill -->
        @auth
        <div style="padding:12px 16px;border-bottom:1px solid rgba(255,255,255,0.06);">
        <div style="font-size:11px;color:rgba(255, 255, 255, 0.40);font-weight:600;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:8px;">Signed in as</div>
            <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;justify-content:between;gap:8px;padding:12px 14px;border-radius:12px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);text-decoration:none;transition:background 0.15s;width:100%;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                <div style="display:flex;align-items:center;gap:8px;min-width:0;flex:1;">
                    <span style="color:#10b981;font-size:13px;flex-shrink:0;"><i class="fa-solid fa-arrow-right text-emerald-400 text-xs"></i></span>
                    <span style="color:#fff;font-weight:600;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</span>
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
                    ['route'=>'admin.dashboard', 'icon'=>'fa-solid fa-house','label'=>'Home', 'active_check'=>'exact'],
                    ['route'=>'admin.users',     'icon'=>'fa-solid fa-user','label'=>'User Management', 'active_check'=>'prefix'],
                    ['route'=>'admin.fee_config', 'icon'=>'fa-solid fa-money-bill-wave','label'=>'Fee & Fine Config', 'active_check'=>'exact'],
                    ['route'=>'admin.acl',         'icon'=>'fa-solid fa-key','label'=>'ACL / Permissions', 'active_check'=>'exact'],
                    ['route'=>'admin.audit_log',   'icon'=>'fa-solid fa-file-lines','label'=>'Audit Log', 'active_check'=>'exact'],
                    ['route'=>'admin.reports',     'icon'=>'fa-solid fa-chart-pie','label'=>'Reports & Analytics', 'active_check'=>'exact'],
                    ['route'=>'admin.api_config',  'icon'=>'fa-solid fa-plug','label'=>'API Configuration', 'active_check'=>'exact'],
                ]; @endphp
                @foreach($adminLinks as $lnk)
                @php
                    $isActive = false;
                    if (($lnk['active_check'] ?? 'exact') === 'prefix') {
                        $isActive = str_starts_with(Route::currentRouteName() ?? '', $lnk['route']);
                    } else {
                        $isActive = Route::currentRouteName() === $lnk['route'];
                    }
                @endphp
                <a href="{{ route($lnk['route']) }}" class="nav-link {{ $isActive ? 'active' : '' }}">
                    <span class="nav-icon"><i class="{{ $lnk['icon'] }}"></i></span><span>{{ $lnk['label'] }}</span>
                </a>
                @endforeach

            {{-- CITIZEN --}}
            @elseif($roleVal === 'citizen_applicant')
                <div class="nav-section">My Licences</div>
                <a href="{{ route('citizen.dashboard') }}" class="nav-link {{ Route::currentRouteName()==='citizen.dashboard'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-file-lines"></i></span><span>My Applications</span>
                </a>
                <a href="{{ route('citizen.apply') }}" class="nav-link {{ Route::currentRouteName()==='citizen.apply'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-plus"></i></span><span>New License</span>
                </a>
                <a href="{{ route('citizen.renew_general') }}" class="nav-link {{ str_starts_with(Route::currentRouteName() ?? '', 'citizen.renew') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-arrows-rotate"></i></span><span>Renew License</span>
                </a>
                <a href="{{ route('applicant.tracking') }}" class="nav-link {{ Route::currentRouteName()==='applicant.tracking'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-location-dot"></i></span><span>Application Tracking</span>
                </a>
                <a href="{{ route('dashboard.verify') }}" class="nav-link {{ Route::currentRouteName()==='dashboard.verify'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-magnifying-glass"></i></span><span>Verify Certificate</span>
                </a>
                <div class="nav-section">Account</div>
                <a href="{{ route('profile.edit') }}" class="nav-link {{ Route::currentRouteName()==='profile.edit'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-user"></i></span><span>My Profile</span>
                </a>

            {{-- DEALER --}}
            @elseif($roleVal === 'dealer_applicant')
                <div class="nav-section">Dealer Portal</div>
                <a href="{{ route('dealer.dashboard') }}" class="nav-link {{ Route::currentRouteName()==='dealer.dashboard'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-store"></i></span><span>Dealer Home</span>
                </a>
                <a href="{{ route('dealer.apply') }}" class="nav-link {{ Route::currentRouteName()==='dealer.apply'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span><span>Apply for New Licence</span>
                </a>
                <a href="{{ route('dealer.renew') }}" class="nav-link {{ str_starts_with(Route::currentRouteName() ?? '', 'dealer.renew') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fa-solid fa-arrows-rotate"></i></span><span>Renew Dealing Licence</span>
                </a>
                <a href="{{ route('applicant.tracking') }}" class="nav-link {{ Route::currentRouteName()==='applicant.tracking'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-location-dot"></i></span><span>Application Tracking</span>
                </a>
                <a href="{{ route('dealer.stock_ledger') }}" class="nav-link {{ Route::currentRouteName()==='dealer.stock_ledger'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-boxes-stacked"></i></span><span>Stock Ledger</span>
                </a>
                <a href="{{ route('dashboard.verify') }}" class="nav-link {{ Route::currentRouteName()==='dashboard.verify'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-magnifying-glass"></i></span><span>Verify Certificate</span>
                </a>
                <div class="nav-section">Account</div>
                <a href="{{ route('profile.edit') }}" class="nav-link {{ Route::currentRouteName()==='profile.edit'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-user"></i></span><span>My Profile</span>
                </a>

            {{-- DC FRONT DESK --}}
            @elseif($role===\App\Enums\Role::DcFrontDesk)
                <div class="nav-section">DC Office</div>
                <a href="{{ route('front_desk.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','front_desk')?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-inbox"></i></span><span>Front Desk Intake</span>
                </a>

            {{-- DC JM BRANCH --}}
            @elseif($role===\App\Enums\Role::DcJmBranch)
                <div class="nav-section">DC Office</div>
                <a href="{{ route('jm_branch.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','jm_branch')?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span><span>JM Branch Queue</span>
                </a>

            {{-- DISTRICT COMMISSIONER --}}
            @elseif($role===\App\Enums\Role::DistrictCommissioner)
                <div class="nav-section">DC Office</div>
                <a href="{{ route('dc.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','dc.')?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-building-columns"></i></span><span>DC Approval Queue</span>
                </a>

            {{-- VETTING --}}
            @elseif(in_array($roleVal,['police_officer','special_branch','nsi_officer','dgfi_officer']))
                <div class="nav-section">Security Vetting</div>
                <a href="{{ route('vetting.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','vetting')?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-shield-halved"></i></span>
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
                @if($role === \App\Enums\Role::SeniorSecretary)
                    <a href="{{ route('senior_secretary.dashboard') }}" class="nav-link {{ Route::currentRouteName() === 'senior_secretary.dashboard' ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-house"></i></span><span>Home</span>
                    </a>
                    <a href="{{ route('moha.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName() ?? '', 'moha') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-building-columns"></i></span><span>Approval Queue</span>
                    </a>
                    <a href="{{ route('admin.reports') }}" class="nav-link {{ str_starts_with(Route::currentRouteName() ?? '', 'admin.reports') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span><span>Reports &amp; Analytics</span>
                    </a>
                @else
                    <a href="{{ route('moha.dashboard') }}" class="nav-link {{ str_starts_with(Route::currentRouteName() ?? '', 'moha') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-building"></i></span>
                        <span>
                            @if($role === \App\Enums\Role::MohaDesk) Political-4 / Sasan-4 Desk
                            @elseif($role === \App\Enums\Role::JointSecretary) Joint / Additional Secretary
                            @elseif($role === \App\Enums\Role::NationalScreeningCommittee) Nat. Screening Committee
                            @else Senior Secretary / Minister
                            @endif
                        </span>
                    </a>
                @endif

            {{-- EXECUTIVE --}}
            @elseif($role===\App\Enums\Role::Executive)
                <div class="nav-section">Executive</div>
                <a href="{{ route('executive.dashboard') }}" class="nav-link {{ Route::currentRouteName()==='executive.dashboard'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span><span>Executive Dashboard</span>
                </a>
                <a href="{{ route('executive.licenses') }}" class="nav-link {{ Route::currentRouteName()==='executive.licenses'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-scroll"></i></span><span>All Licences</span>
                </a>
                <a href="{{ route('executive.dealers') }}" class="nav-link {{ Route::currentRouteName()==='executive.dealers'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-store"></i></span><span>Dealers &amp; Stock</span>
                </a>
                <a href="{{ route('executive.dealing_central') }}" class="nav-link {{ Route::currentRouteName()==='executive.dealing_central'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-folder-tree"></i></span><span>Dealing License Central</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="nav-link {{ Route::currentRouteName()==='admin.reports'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span><span>Reports &amp; Analytics</span>
                </a>
            @endif

            {{-- Custom Comment (ACL permission-based) --}}
            @php
                $aclMatrix = json_decode(\App\Models\Setting::get('acl_matrix', '{}'), true) ?: [];
                $customCommentPerm = $aclMatrix['Custom Comment'][$roleVal] ?? 'none';
            @endphp
            @if($customCommentPerm !== 'none')
                <div class="nav-section">Tools</div>
                <a href="{{ route('custom_comment.index') }}" class="nav-link {{ str_starts_with(Route::currentRouteName()??'','custom_comment')?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-comments"></i></span><span>Custom Comment</span>
                </a>
            @endif

            {{-- Officer Profile --}}
            @if(!in_array($roleVal,['citizen_applicant','dealer_applicant','system_admin']))
                <div class="nav-section">Account</div>
                <a href="{{ route('profile.edit') }}" class="nav-link {{ Route::currentRouteName()==='profile.edit'?'active':'' }}">
                    <span class="nav-icon"><i class="fa-solid fa-user"></i></span><span>My Profile</span>
                </a>
            @endif
        </nav>
        @endauth

        <!-- Bottom -->
        <div style="padding:12px 16px 20px;border-top:1px solid rgba(255,255,255,0.06);">
            <div class="flex items-center justify-between">
                <div style="font-size:11px;color:rgba(255, 255, 255, 0.6);font-weight:500;display:inline-flex;align-items:center;gap:6px;">
                    <span>Designed & Developed By</span>
                    <img src="https://mysoftheaven.com/fwedget/img/mysoft-logo.png" alt="MysoftheavenBD Ltd." style="height:25px;width:auto;object-fit:contain;border-radius:3px;background:#ffffff;padding:2px 4px;">
                </div>
            </div>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <div class="flex-grow flex flex-col overflow-hidden">

        <!-- Header Bar -->
        <header class="h-14 bg-transparent border-b border-slate-200/60 flex items-center justify-between px-3 sm:px-5 lg:px-7 flex-shrink-0 gap-2">
            <div class="flex items-center gap-2 text-sm text-slate-500 font-medium min-w-0">
                <!-- Hamburger Button (visible on mobile/tablet) -->
                <button type="button" onclick="toggleSidebar(true)" aria-label="Toggle navigation menu" class="lg:hidden mr-1 sm:mr-2 p-1.5 text-slate-500 hover:text-slate-900 border border-slate-200 rounded-lg bg-white flex items-center justify-center transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <i class="fa-solid fa-building hidden sm:inline flex-shrink-0 text-slate-400"></i>
                <span class="font-medium text-slate-700 truncate text-[11px] sm:text-sm">Ministry of Home Affairs</span>
                <span class="text-slate-300 hidden md:inline">·</span>
                <span class="hidden md:inline truncate text-slate-500 text-[11px] sm:text-sm">Government of the People's Republic of Bangladesh</span>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
                {{-- <div class="hidden sm:flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">System Live</span>
                </div>
                <div class="hidden md:block px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 uppercase tracking-wider truncate max-w-[170px]">
                    @yield('title','Dashboard')
                </div> --}}
                @auth
                @php
                    $authUser = auth()->user();
                    $userPhoto = $authUser->photo_url ?? $authUser->avatar_url ?? $authUser->avatar ?? null;
                    $ws = explode(' ', trim($authUser->name));
                    $initials = count($ws) >= 2 ? strtoupper(substr($ws[0],0,1).substr($ws[1],0,1)) : strtoupper(substr($authUser->name,0,2));
                @endphp
                <div class="relative" id="user-menu-wrapper">
                    <button type="button" id="user-menu-button" onclick="toggleUserMenu()"
                        class="flex items-center gap-1.5 sm:gap-2.5 pl-1 sm:pl-1.5 pr-2 sm:pr-2.5 py-1 sm:py-1.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 transition-colors"
                        aria-haspopup="true" aria-expanded="false">
                        @if($userPhoto)
                            <img src="{{ $userPhoto }}" alt="{{ $authUser->name }}"
                                class="w-7 h-7 rounded-full object-cover flex-shrink-0 ring-1 ring-slate-200">
                        @else
                            <span style="width:28px;height:28px;background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;"
                                class="rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ $initials }}
                            </span>
                        @endif
                        <span class="hidden sm:inline text-xs sm:text-sm font-semibold text-slate-700 max-w-[130px] truncate">{{ $authUser->name }}</span>
                        <svg id="user-menu-chevron" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="user-menu-dropdown"
                        class="hidden absolute right-0 mt-2 w-56 max-w-[calc(100vw-1.5rem)] bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden z-50"
                        style="animation: none;">
                        <div class="px-3.5 py-3 border-b border-slate-100">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $authUser->name }}</p>
                            @if($authUser->email)
                                <p class="text-xs text-slate-400 truncate mt-0.5">{{ $authUser->email }}</p>
                            @endif
                        </div>
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>My Profile</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm font-medium text-slate-600 hover:bg-rose-50 hover:text-rose-600 transition-colors border-t border-slate-100">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <!-- Content -->
        <main class="flex-grow overflow-y-auto p-3.5 sm:p-5 md:p-6 lg:p-7 min-w-0 max-w-full">

            @if(session('success'))
            <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">
                <span class="text-emerald-500 flex-shrink-0"><i class="fa-solid fa-circle-check"></i></span>
                <span>{{ session('success') }}</span>
            </div>
            @endif
            @if(session('warning'))
            <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-semibold">
                <span class="flex-shrink-0"><i class="fa-solid fa-triangle-exclamation"></i></span>
                <span>{{ session('warning') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold">
                <span class="flex-shrink-0"><i class="fa-solid fa-circle-xmark"></i></span>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function toggleUserMenu(forceState) {
            const dropdown = document.getElementById('user-menu-dropdown');
            const chevron = document.getElementById('user-menu-chevron');
            const button = document.getElementById('user-menu-button');
            if (!dropdown) return;
            const shouldOpen = typeof forceState === 'boolean' ? forceState : dropdown.classList.contains('hidden');
            if (shouldOpen) {
                dropdown.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
                button.setAttribute('aria-expanded', 'true');
            } else {
                dropdown.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
                button.setAttribute('aria-expanded', 'false');
            }
        }

        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('user-menu-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                toggleUserMenu(false);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') toggleUserMenu(false);
        });

        function toggleSidebar(isOpen) {
            const sidebar = document.querySelector('.mobile-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (sidebar && backdrop) {
                if (isOpen) {
                    sidebar.classList.add('open');
                    backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.remove('open');
                    backdrop.classList.add('hidden');
                }
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
