<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff Daily Report')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="/images/icon-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }
        .truncate-2-lines {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }
        /* Toast Notification Styling */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 360px;
            width: calc(100% - 48px);
        }
        .toast-notification {
            background: rgba(255, 255, 255, 0.98);
            border-left: 4px solid #4f46e5;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            padding: 16px;
            display: flex;
            align-items: start;
            gap: 12px;
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease;
            opacity: 0;
            cursor: pointer;
        }
        .toast-notification.show {
            transform: translateX(0);
            opacity: 1;
        }
        .toast-notification.hide {
            transform: translateX(120%);
            opacity: 0;
        }
    </style>
</head>
@php
    $isHod = Auth::check() && Auth::user()->role === 'staff' && Auth::user()->staff && Auth::user()->staff->isHod();
    $ratingSetting = \App\Models\RatingSetting::first();
    $isRatingActive = $ratingSetting ? $ratingSetting->is_active : false;
    $canSeeSubmitRating = Auth::check() && ((in_array(Auth::user()->role, ['admin', 'sanyojak', 'karyalay_sanyojak']) || $isHod) && $isRatingActive);
    $canSeeReports = Auth::check() && Auth::user()->role === 'pst';
    $canSeeConfig = Auth::check() && Auth::user()->role === 'admin';
    $canSeeRatingMenu = $canSeeSubmitRating || $canSeeReports || $canSeeConfig;
@endphp
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col">
    <nav class="glass-nav text-white flex-none">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-20">
                <!-- Left side: Brand/Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : (Auth::user()->role === 'manager' ? route('manager.dashboard') : (in_array(Auth::user()->role, ['sanyojak', 'karyalay_sanyojak']) ? route('daily-report.index') : (Auth::user()->role === 'pst' ? route('ratings.report') : route('staff.dashboard')))) }}"
                       class="font-bold text-xl hover:text-indigo-200 transition">Dashboard</a>
                </div>

                <!-- Desktop Menu (Links) -->
                <div class="hidden md:flex items-center gap-4 lg:gap-6">
                    {{-- Administration Dropdown --}}
                    @if(Auth::user()->role === 'admin')
                    <div class="relative" id="admin-menu-wrapper">
                        <button onclick="toggleAdminMenu()"
                            class="flex items-center gap-1 text-sm font-medium hover:text-indigo-200 transition focus:outline-none">
                            Administration
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="admin-dropdown"
                            class="hidden absolute left-0 mt-2 w-48 bg-white text-gray-700 rounded-xl shadow-lg py-1 z-50 border border-gray-100">
                            
                            <div class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Structure</div>
                            <a href="{{ route('office.view') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">Offices</a>
                            <a href="{{ route('department.view') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">Departments</a>
                            <a href="{{ route('admin.employee-of-month.index') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">Emp of the Month</a>
                            
                            <div class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1 border-t border-gray-50 pt-2">Roles</div>
                            <a href="{{ route('sanyojak.view') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">Sanyojaks</a>
                            <a href="{{ route('pst.view') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">PST Users</a>
                        </div>
                    </div>
                    @endif

                    {{-- Staff Dropdown --}}
                    @if(in_array(Auth::user()->role, ['admin', 'manager']))
                    <div class="relative" id="staff-menu-wrapper">
                        <button onclick="toggleStaffMenu()"
                            class="flex items-center gap-1 text-sm font-medium hover:text-indigo-200 transition focus:outline-none">
                            Staff
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="staff-dropdown"
                            class="hidden absolute left-0 mt-2 w-48 bg-white text-gray-700 rounded-xl shadow-lg py-1 z-50 border border-gray-100">
                            <a href="{{ route('staff.create') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">Add Staff</a>
                            <a href="{{ route('staff.view') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">View Directory</a>
                        </div>
                    </div>
                    @endif

                    {{-- Ratings Dropdown --}}
                    @if($canSeeRatingMenu)
                    <div class="relative" id="ratings-menu-wrapper">
                        <button onclick="toggleRatingsMenu()"
                            class="flex items-center gap-1 text-sm font-medium hover:text-indigo-200 transition focus:outline-none">
                            Ratings
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="ratings-dropdown"
                            class="hidden absolute left-0 mt-2 w-48 bg-white text-gray-700 rounded-xl shadow-lg py-1 z-50 border border-gray-100">
                            @if($canSeeSubmitRating)
                            <a href="{{ route('ratings.index') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">Submit Rating</a>
                            @endif
                            @if($canSeeReports)
                            <a href="{{ route('ratings.report') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">Rating Reports</a>
                            @endif
                            @if($canSeeConfig)
                            <a href="{{ route('admin.rating-config.index') }}" class="block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition border-t border-gray-50 mt-1 pt-2">Rating Questions</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- IT Management Dropdown --}}
                    @if(Auth::user()->canAccessIT())
                    <div class="relative" id="it-menu-wrapper">
                        <button onclick="toggleITMenu()"
                            class="flex items-center gap-1 text-sm font-medium hover:text-indigo-200 transition focus:outline-none">
                            IT Management
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="it-dropdown"
                            class="hidden absolute left-0 mt-2 w-52 bg-white text-gray-700 rounded-xl shadow-lg py-1 z-50 border border-gray-100">
                            <a href="{{ route('it-tickets.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition font-bold text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                IT Tickets Dashboard
                            </a>
                            <a href="{{ route('it-management.backup-locations.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Backup Locations
                            </a>
                            <a href="{{ route('it-management.backup-reports.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Generate Reports
                            </a>
                            <a href="{{ route('it-management.recharges.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition border-t border-slate-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                                Recharges (Bills)
                            </a>
                            <a href="{{ route('it-management.backup-defaulters.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-red-50 hover:text-red-700 transition border-t border-slate-100">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Defaulters List
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Stock Management Dropdown --}}
                    @if(Auth::user()->canAccessIT())
                    <div class="relative" id="stock-menu-wrapper">
                        <button onclick="toggleStockMenu()"
                            class="flex items-center gap-1 text-sm font-medium hover:text-indigo-200 transition focus:outline-none">
                            Stock Management
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="stock-dropdown"
                            class="hidden absolute left-0 mt-2 w-52 bg-white text-gray-700 rounded-xl shadow-lg py-1 z-50 border border-gray-100">
                            <a href="{{ route('stock-management.categories.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                Stock Categories
                            </a>
                            <a href="{{ route('stock-management.items.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                Manage Stock Items
                            </a>
                            <a href="{{ route('stock-management.purchases.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                Stock Purchase
                            </a>
                            <a href="{{ route('stock-management.allotments.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Allot Stock
                            </a>
                            <a href="{{ route('stock-management.allotments.history') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                Allotment History
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Profile Requests (Admin & Manager) --}}
                    @if(in_array(Auth::user()->role, ['admin', 'manager']))
                    <a href="{{ route('profile.requests.index') }}"
                       class="text-sm font-medium hover:text-indigo-200 transition focus:outline-none flex items-center gap-1">
                        Profile Requests
                        @php
                            $pendingReqs = \App\Models\Staff\ProfileUpdateRequest::where('status', 'pending');
                            if(Auth::user()->role === 'manager' && Auth::user()->staff) {
                                $officeId = Auth::user()->staff->office_id;
                                $pendingReqs->whereHas('staff', function($q) use ($officeId) {
                                    $q->where('office_id', $officeId);
                                });
                            }
                            $pendingCount = $pendingReqs->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    @endif

                    {{-- Daily Report Dropdown --}}
                    <div class="relative" id="dailyreport-menu-wrapper">
                        <button onclick="toggleDailyReportMenu()"
                            class="flex items-center gap-1 text-sm font-medium hover:text-indigo-200 transition focus:outline-none">
                            Daily Report
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="dailyreport-dropdown"
                            class="hidden absolute left-0 mt-2 w-48 bg-white text-gray-700 rounded-xl shadow-lg py-1 z-50 border border-gray-100">
                            @if(in_array(Auth::user()->role, ['admin', 'manager', 'pst', 'karyalay_sanyojak']))
                            <a href="{{ route('daily-report.live-tasks') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-green-50 text-green-600 font-bold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                Live Tasks
                            </a>
                            @endif

                            @if(Auth::user()->role === 'staff')
                            <a href="{{ route('staff.track-task') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-green-50 text-green-600 font-bold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Track My Task
                            </a>
                            @endif

                            @if(!in_array(Auth::user()->role, ['sanyojak', 'karyalay_sanyojak', 'pst']))
                            <a href="{{ route('staff.daily-backup.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                                My Daily Backup
                            </a>
                            @endif

                            <a href="{{ route('daily-report.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                {{ $isHod ? 'Department Reports' : 'View Reports' }}
                            </a>
                        </div>
                    </div>

                    {{-- IT Support Top Level --}}
                    @if(!in_array(Auth::user()->role, ['sanyojak', 'pst']))
                    <a href="{{ route('it-tickets.index') }}"
                       class="text-sm font-medium hover:text-indigo-200 transition focus:outline-none flex items-center gap-1.5 lg:ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        IT Support
                    </a>
                    @endif
                </div>

                <!-- Right side: User Info & Logout (Desktop) -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Notifications Dropdown -->
                    <div class="relative" id="notifications-menu-wrapper">
                        <button onclick="toggleNotificationsMenu(); requestNotificationPermission()" class="relative p-2 text-white hover:text-indigo-200 transition focus:outline-none flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span id="notification-badge" class="hidden absolute top-1 right-1 bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[15px] text-center border-2 border-slate-900 leading-none">0</span>
                        </button>
                        <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white text-gray-700 rounded-xl shadow-2xl py-2 z-50 border border-gray-100 divide-y divide-gray-100 transition-all duration-200 origin-top-right transform">
                            <div class="px-4 py-2 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
                                <span class="font-bold text-sm text-gray-800">Notifications</span>
                                <button onclick="markAllNotificationsAsRead()" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition focus:outline-none">Mark all as read</button>
                            </div>
                            <div id="notification-items" class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                                <div class="px-4 py-6 text-center text-xs text-gray-400">
                                    No new notifications.
                                </div>
                            </div>
                            @if(!in_array(Auth::user()->role, ['sanyojak', 'pst']))
                            <div class="px-4 py-2 text-center bg-gray-50 rounded-b-xl">
                                <a href="{{ route('it-tickets.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">View IT Support Dashboard</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="relative" id="profile-menu-wrapper">
                        <button onclick="toggleProfileMenu()" onmouseenter="openProfileMenu()" class="flex items-center gap-1 text-sm font-medium focus:outline-none hover:text-indigo-200 transition">
                            <span class="whitespace-nowrap">{{ Auth::user()->name }}</span>
                            <span class="capitalize bg-indigo-500 px-2 py-0.5 rounded text-xs ml-1">{{ Auth::user()->role }}</span>
                        </button>
                        <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-1 hidden border border-gray-100 z-50">
                            <button onclick="window.location.reload(true)" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                    Refresh App
                                </span>
                            </button>
                            <button onclick="document.getElementById('global-change-password-modal').classList.remove('hidden')" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                                    Change Password
                                </span>
                            </button>
                            <form method="POST" action="{{ route('logout') }}" class="block w-full">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button & Mobile Notifications -->
                <div class="md:hidden flex items-center gap-2">
                    <!-- Mobile Notifications Bell -->
                    <div class="relative" id="mobile-notifications-menu-wrapper">
                        <button onclick="toggleMobileNotificationsMenu(); requestNotificationPermission()" class="relative p-2 text-white hover:text-indigo-200 transition focus:outline-none flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span id="mobile-notification-badge" class="hidden absolute top-1 right-1 bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full min-w-[15px] text-center border border-slate-900 leading-none">0</span>
                        </button>
                        <div id="mobile-notifications-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white text-gray-700 rounded-xl shadow-2xl py-2 z-50 border border-gray-100 divide-y divide-gray-100">
                            <div class="px-4 py-2 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
                                <span class="font-bold text-xs text-gray-800">Notifications</span>
                                <button onclick="markAllNotificationsAsRead()" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-semibold transition focus:outline-none">Mark all as read</button>
                            </div>
                            <div id="mobile-notification-items" class="max-h-60 overflow-y-auto divide-y divide-gray-50">
                                <div class="px-4 py-4 text-center text-[10px] text-gray-400">
                                    No new notifications.
                                </div>
                            </div>
                            @if(!in_array(Auth::user()->role, ['sanyojak', 'pst']))
                            <div class="px-4 py-2 text-center bg-gray-50 rounded-b-xl">
                                <a href="{{ route('it-tickets.index') }}" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-medium transition">View IT Support Dashboard</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <button onclick="toggleMobileMenu()" class="text-white hover:text-indigo-200 focus:outline-none p-2 rounded-md bg-indigo-600">
                        <svg id="mobile-menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                        <svg id="mobile-menu-close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div id="mobile-menu" class="hidden md:hidden bg-indigo-800 border-t border-indigo-600 pb-4">
            <div class="px-4 py-3 border-b border-indigo-700 mb-2">
                <div class="text-base font-medium">{{ Auth::user()->name }}</div>
                <div class="text-sm text-indigo-300 capitalize">{{ Auth::user()->role }}</div>
                <div class="mt-2 flex flex-wrap gap-2">
                    <button onclick="window.location.reload(true)" class="bg-indigo-700 hover:bg-indigo-600 text-white text-xs px-3 py-1.5 rounded transition">
                        Refresh App
                    </button>
                    <button onclick="document.getElementById('global-change-password-modal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs px-3 py-1.5 rounded transition">
                        Change Password
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="px-2 space-y-1">
                @if(Auth::user()->role === 'admin')
                    <div class="text-xs font-semibold text-indigo-300 uppercase tracking-wider px-3 py-2">Management</div>
                    <a href="{{ route('office.view') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Office Management</a>
                    <a href="{{ route('department.view') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Department Management</a>
                @endif
                @if(in_array(Auth::user()->role, ['admin', 'manager']))
                    <a href="{{ route('staff.view') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Staff Management</a>
                @endif
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('sanyojak.view') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Sanyojak Management</a>
                    <a href="{{ route('pst.view') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">PST Management</a>
                @endif
                
                @if($canSeeSubmitRating)
                    <a href="{{ route('ratings.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-yellow-300 hover:bg-indigo-600 transition">Staff Ratings</a>
                @endif
                @if($canSeeReports)
                    <a href="{{ route('ratings.report') }}" class="block px-3 py-2 rounded-md text-base font-medium text-yellow-300 hover:bg-indigo-600 transition">Rating Reports</a>
                @endif
                @if($canSeeConfig)
                    <a href="{{ route('admin.rating-config.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-yellow-300 hover:bg-indigo-600 transition">Rating Config</a>
                @endif


                @if(Auth::user()->canAccessIT())
                    <div class="text-xs font-semibold text-indigo-300 uppercase tracking-wider px-3 py-2">IT & Stock</div>
                    <a href="{{ route('it-management.backup-locations.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Backup Locations</a>
                    <a href="{{ route('it-management.backup-reports.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Generate Reports</a>
                    <a href="{{ route('it-management.backup-defaulters.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Defaulters List</a>
                    <a href="{{ route('stock-management.items.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Manage Stock Items</a>
                    <a href="{{ route('stock-management.allotments.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Allot Stock</a>
                    <a href="{{ route('stock-management.allotments.history') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">Allotment History</a>
                @endif

                <div class="text-xs font-semibold text-indigo-300 uppercase tracking-wider px-3 py-2">Daily Work</div>
                @if(in_array(Auth::user()->role, ['admin', 'manager', 'pst']))
                <a href="{{ route('daily-report.live-tasks') }}" class="block px-3 py-2 rounded-md text-base font-medium text-green-300 hover:bg-indigo-600 transition">Live Tasks</a>
                @endif

                @if(Auth::user()->role === 'staff')
                <a href="{{ route('staff.track-task') }}" class="block px-3 py-2 rounded-md text-base font-medium text-green-300 hover:bg-indigo-600 transition">Track My Task</a>
                @endif

                @if(!in_array(Auth::user()->role, ['sanyojak', 'pst']))
                <a href="{{ route('staff.daily-backup.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">My Daily Backup</a>
                @endif
                <a href="{{ route('daily-report.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">{{ $isHod ? 'Department Reports' : 'View Reports' }}</a>
                
                @if(!in_array(Auth::user()->role, ['sanyojak', 'pst']))
                <div class="text-xs font-semibold text-indigo-300 uppercase tracking-wider px-3 py-2 mt-2">Support</div>
                <a href="{{ route('it-tickets.index') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-600 transition">IT Support & Tickets</a>
                @endif
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto flex-none shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)]">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
            <div class="text-[10px] sm:text-xs text-gray-500 font-medium">
                DEVELOPED BY <span class="font-bold text-gray-700">IT DEPARTMENT SABSJS (CENTRAL OFFICE)</span>
            </div>
            <div class="relative group">
                <button class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center focus:outline-none hover:bg-indigo-200 transition shadow-sm">
                    <span class="text-xs font-bold italic font-serif">i</span>
                </button>
                <div class="absolute bottom-full right-0 mb-3 w-52 bg-gray-800 text-white text-xs rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 p-4 pointer-events-none z-50 border border-gray-700">
                    <div class="font-bold mb-2 text-indigo-300 border-b border-gray-600 pb-1">Developer Contact</div>
                    <div class="mb-1"><span class="text-gray-400">Name:</span> ADITYA</div>
                    <div><span class="text-gray-400">Phone:</span> 9636501008</div>
                    <div class="absolute -bottom-1.5 right-2 w-3 h-3 bg-gray-800 border-b border-r border-gray-700 rotate-45"></div>
                </div>
            </div>
        </div>
    </footer>

    {{-- Global Change Password Modal --}}
    <div id="global-change-password-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Change Password</h3>
                <button onclick="document.getElementById('global-change-password-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="global-change-password-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="new_password" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-2 border">
                </div>
                <div id="global-password-alert" class="hidden rounded-md p-3 text-sm"></div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('global-change-password-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">Change Password</button>
                </div>
            </form>
        </div>
    </div>

    @stack('scripts')

    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered successfully!', reg.scope))
                    .catch(err => console.error('Service Worker registration failed:', err));
            });
        }

        // Global Fetch Interceptor to handle Session Expiration (419 CSRF Token Mismatch & 401 Unauthorized)
        const originalFetch = window.fetch;
        window.fetch = async function() {
            try {
                const response = await originalFetch.apply(this, arguments);
                if (response.status === 419 || response.status === 401) {
                    if (typeof showToast === 'function') {
                        showToast('Session expired. Refreshing page to secure your session...', 'error');
                    }
                    setTimeout(() => window.location.reload(), 1500);
                    return Promise.reject('Session Expired');
                }
                return response;
            } catch (error) {
                throw error;
            }
        };

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('mobile-menu-icon');
            const closeIcon = document.getElementById('mobile-menu-close-icon');
            
            menu.classList.toggle('hidden');
            icon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }

        // Desktop Menu Toggles
        function toggleAdminMenu() {
            document.getElementById('admin-dropdown').classList.toggle('hidden');
        }
        function toggleRatingsMenu() {
            document.getElementById('ratings-dropdown').classList.toggle('hidden');
        }
        function toggleStaffMenu() {
            document.getElementById('staff-dropdown').classList.toggle('hidden');
        }
        function toggleITMenu() {
            document.getElementById('it-dropdown').classList.toggle('hidden');
        }
        function toggleStockMenu() {
            document.getElementById('stock-dropdown').classList.toggle('hidden');
        }
        function toggleDailyReportMenu() {
            document.getElementById('dailyreport-dropdown').classList.toggle('hidden');
        }
        function openProfileMenu() {
            document.getElementById('profile-dropdown').classList.remove('hidden');
        }
        function toggleProfileMenu() {
            document.getElementById('profile-dropdown').classList.toggle('hidden');
        }
        function toggleNotificationsMenu() {
            document.getElementById('notifications-dropdown').classList.toggle('hidden');
        }
        function toggleMobileNotificationsMenu() {
            document.getElementById('mobile-notifications-dropdown').classList.toggle('hidden');
        }

        // Close desktop dropdowns when clicking outside
        window.addEventListener('click', function(e) {
            const adminMenu = document.getElementById('admin-menu-wrapper');
            const ratingsMenu = document.getElementById('ratings-menu-wrapper');
            const staffMenu = document.getElementById('staff-menu-wrapper');
            const itMenu = document.getElementById('it-menu-wrapper');
            const stockMenu = document.getElementById('stock-menu-wrapper');
            const dailyreportMenu = document.getElementById('dailyreport-menu-wrapper');

            if (adminMenu && !adminMenu.contains(e.target)) {
                document.getElementById('admin-dropdown')?.classList.add('hidden');
            }
            if (ratingsMenu && !ratingsMenu.contains(e.target)) {
                document.getElementById('ratings-dropdown')?.classList.add('hidden');
            }
            if (staffMenu && !staffMenu.contains(e.target)) {
                document.getElementById('staff-dropdown')?.classList.add('hidden');
            }
            if (itMenu && !itMenu.contains(e.target)) {
                document.getElementById('it-dropdown')?.classList.add('hidden');
            }
            if (stockMenu && !stockMenu.contains(e.target)) {
                document.getElementById('stock-dropdown')?.classList.add('hidden');
            }
            if (dailyreportMenu && !dailyreportMenu.contains(e.target)) {
                document.getElementById('dailyreport-dropdown')?.classList.add('hidden');
            }
        });

        // Close other dropdowns when clicking outside
        document.addEventListener('click', function (e) {
            const dropdowns = [
                { wrapper: 'office-menu-wrapper', dropdown: 'office-dropdown' },
                { wrapper: 'department-menu-wrapper', dropdown: 'department-dropdown' },
                { wrapper: 'staff-menu-wrapper', dropdown: 'staff-dropdown' },
                { wrapper: 'sanyojak-menu-wrapper', dropdown: 'sanyojak-dropdown' },
                { wrapper: 'it-menu-wrapper', dropdown: 'it-dropdown' },
                { wrapper: 'stock-menu-wrapper', dropdown: 'stock-dropdown' },
                { wrapper: 'dailyreport-menu-wrapper', dropdown: 'dailyreport-dropdown' },
                { wrapper: 'profile-menu-wrapper', dropdown: 'profile-dropdown' },
                { wrapper: 'notifications-menu-wrapper', dropdown: 'notifications-dropdown' },
                { wrapper: 'mobile-notifications-menu-wrapper', dropdown: 'mobile-notifications-dropdown' }
            ];

            dropdowns.forEach(item => {
                const wrapper = document.getElementById(item.wrapper);
                const dropdown = document.getElementById(item.dropdown);
                if (wrapper && dropdown && !wrapper.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }); 

        document.getElementById('global-change-password-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const alertBox = document.getElementById('global-password-alert');
            
            fetch('{{ route('password.update') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async res => {
                const data = await res.json();
                alertBox.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'bg-green-50', 'text-green-700');
                if (data.success) {
                    alertBox.classList.add('bg-green-50', 'text-green-700');
                    alertBox.innerText = data.message;
                    setTimeout(() => {
                        document.getElementById('global-change-password-modal').classList.add('hidden');
                        alertBox.classList.add('hidden');
                        document.getElementById('global-change-password-form').reset();
                    }, 2000);
                } else {
                    alertBox.classList.add('bg-red-50', 'text-red-700');
                    alertBox.innerText = data.message || 'Error updating password.';
                }
            })
            .catch(err => {
                alertBox.classList.remove('hidden');
                alertBox.classList.add('bg-red-50', 'text-red-700');
                alertBox.innerText = 'Something went wrong. Please check your inputs.';
            });
        });

        // Heartbeat to keep session alive
        setInterval(() => {
            fetch('/keep-alive').then(response => {
                if (response.status === 401 || response.status === 419) {
                    window.location.reload();
                }
            }).catch(e => console.log('Session keep-alive failed'));
        }, 5 * 60 * 1000); // Every 5 minutes

        // Notifications System Logic
        let lastNotificationCount = null;

        function requestNotificationPermission() {
            if ("Notification" in window) {
                if (Notification.permission === "default") {
                    Notification.requestPermission();
                }
            }
        }

        function playNotificationSound() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                // Chime note 1
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                gain1.gain.setValueAtTime(0.08, audioCtx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.005, audioCtx.currentTime + 0.12);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start();
                osc1.stop(audioCtx.currentTime + 0.12);

                // Chime note 2
                setTimeout(() => {
                    const osc2 = audioCtx.createOscillator();
                    const gain2 = audioCtx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                    gain2.gain.setValueAtTime(0.08, audioCtx.currentTime);
                    gain2.gain.exponentialRampToValueAtTime(0.005, audioCtx.currentTime + 0.3);
                    osc2.connect(gain2);
                    gain2.connect(audioCtx.destination);
                    osc2.start();
                    osc2.stop(audioCtx.currentTime + 0.3);
                }, 100);
            } catch (e) {
                console.warn("Web Audio chime failed", e);
            }
        }

        function showInAppToast(title, message, url, type) {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            
            let borderColor = '#4f46e5'; // Indigo default
            if (type === 'it_ticket_created') borderColor = '#10b981'; // Emerald
            if (type === 'it_ticket_status') borderColor = '#f59e0b'; // Amber
            if (type === 'it_ticket_time') borderColor = '#3b82f6'; // Blue
            toast.style.borderLeftColor = borderColor;

            toast.innerHTML = `
                <div class="flex-shrink-0 mt-0.5">
                    ${getNotificationIcon(type)}
                </div>
                <div class="flex-1 min-w-0" onclick="window.location.href='${url}'">
                    <h4 class="text-xs font-bold text-gray-900">${title}</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-2">${message}</p>
                </div>
                <button class="text-gray-400 hover:text-gray-600 transition flex-shrink-0 focus:outline-none" onclick="event.stopPropagation(); this.closest('.toast-notification').classList.replace('show', 'hide'); setTimeout(() => this.closest('.toast-notification').remove(), 400)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;

            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 50);

            // Auto-remove after 6 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.classList.replace('show', 'hide');
                    setTimeout(() => toast.remove(), 400);
                }
            }, 6000);
        }

        function fetchNotifications() {
            fetch('{{ route('notifications.index') }}')
                .then(response => response.json())
                .then(data => {
                    updateNotificationUI(data.notifications, data.unread_count);
                    
                    if (lastNotificationCount !== null && data.unread_count > lastNotificationCount) {
                        playNotificationSound();
                        
                        if (data.notifications.length > 0) {
                            const latest = data.notifications[0];
                            if (latest && !latest.is_read) {
                                // 1. Trigger desktop system notification (WhatsApp style, works even when tab is background)
                                showDesktopNotification(latest.title, latest.message, latest.id);
                                // 2. Trigger beautifully styled in-app Toast notification (bottom right corner)
                                showInAppToast(latest.title, latest.message, `/notifications/${latest.id}/read`, latest.type);
                            }
                        }
                    }
                    lastNotificationCount = data.unread_count;
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        function showDesktopNotification(title, message, id) {
            if ("Notification" in window && Notification.permission === "granted") {
                const notification = new Notification(title, {
                    body: message,
                    tag: 'ticket-' + id,
                    requireInteraction: false
                });
                
                notification.onclick = function() {
                    window.focus();
                    window.location.href = `/notifications/${id}/read`;
                };
            }
        }

        function updateNotificationUI(notifications, unreadCount) {
            const badges = [
                document.getElementById('notification-badge'),
                document.getElementById('mobile-notification-badge')
            ];
            
            badges.forEach(badge => {
                if (badge) {
                    if (unreadCount > 0) {
                        badge.innerText = unreadCount;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            });

            const desktopContainer = document.getElementById('notification-items');
            const mobileContainer = document.getElementById('mobile-notification-items');
            
            const htmlContent = notifications.length > 0 
                ? notifications.map(n => `
                    <a href="/notifications/${n.id}/read" class="block px-4 py-3 hover:bg-slate-50 transition ${!n.is_read ? 'bg-indigo-50/40' : ''}">
                        <div class="flex items-start gap-2.5">
                            <div class="mt-0.5 flex-shrink-0">
                                ${getNotificationIcon(n.type)}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold ${!n.is_read ? 'text-indigo-950 font-bold' : 'text-gray-700'} truncate-2-lines">${n.title}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-2">${n.message}</p>
                                <span class="text-[9px] text-gray-400 block mt-1">${formatTimeAgo(n.created_at)}</span>
                            </div>
                            ${!n.is_read ? `<span class="w-1.5 h-1.5 rounded-full bg-indigo-600 mt-2 flex-shrink-0"></span>` : ''}
                        </div>
                    </a>
                `).join('')
                : `<div class="px-4 py-8 text-center text-xs text-gray-400">No new notifications</div>`;

            if (desktopContainer) desktopContainer.innerHTML = htmlContent;
            if (mobileContainer) mobileContainer.innerHTML = htmlContent;
        }

        function getNotificationIcon(type) {
            switch (type) {
                case 'it_ticket_created':
                    return `<span class="flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg></span>`;
                case 'it_ticket_reply':
                    return `<span class="flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></span>`;
                case 'it_ticket_status':
                    return `<span class="flex items-center justify-center w-7 h-7 rounded-lg bg-amber-50 text-amber-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></span>`;
                case 'it_ticket_time':
                    return `<span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 text-blue-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>`;
                default:
                    return `<span class="flex items-center justify-center w-7 h-7 rounded-lg bg-gray-50 text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></span>`;
            }
        }

        function formatTimeAgo(dateString) {
            try {
                const date = new Date(dateString);
                const seconds = Math.floor((new Date() - date) / 1000);
                
                let interval = Math.floor(seconds / 31536000);
                if (interval >= 1) return interval + " years ago";
                interval = Math.floor(seconds / 2592000);
                if (interval >= 1) return interval + " months ago";
                interval = Math.floor(seconds / 86400);
                if (interval >= 1) return interval + " days ago";
                interval = Math.floor(seconds / 3600);
                if (interval >= 1) return interval + " hours ago";
                interval = Math.floor(seconds / 60);
                if (interval >= 1) return interval + " minutes ago";
                return seconds < 10 ? "Just now" : Math.floor(seconds) + " seconds ago";
            } catch(e) {
                return "some time ago";
            }
        }

        function markAllNotificationsAsRead() {
            fetch('{{ route('notifications.read-all') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchNotifications();
                }
            })
            .catch(error => console.error('Error marking all as read:', error));
        }

        // Initialize and poll
        requestNotificationPermission();
        fetchNotifications();
        setInterval(fetchNotifications, 10000); // Check every 10 seconds
    </script>
</body>
</html>
