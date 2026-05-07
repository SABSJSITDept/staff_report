@extends('layouts.app')
@section('title', 'Staff - Add / Edit')

@section('content')

{{-- Toast Container --}}
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-96 pointer-events-none"></div>

{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">Dashboard</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <a href="{{ route('staff.view') }}" class="hover:text-indigo-600 transition">Staff</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600" id="breadcrumb-label">New Staff</span>
</nav>
<div class="space-y-8 pb-12">
    
    {{-- Main Form Card --}}
    <div class="card-premium !p-0 overflow-hidden">
        {{-- Card Header --}}
        <div class="gradient-bg px-8 py-7 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight" id="form-title">Staff Registration Portal</h2>
                    <p class="text-indigo-100/70 text-[10px] font-bold uppercase tracking-[0.2em] mt-0.5">Personnel Onboarding & Data Entry</p>
                </div>
            </div>
            <a href="{{ route('staff.view') }}" class="btn-secondary !bg-white/10 !text-white !border-white/20 hover:!bg-white/20 px-6 py-2.5 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                View Staff Directory
            </a>
        </div>

            {{-- Form --}}
            <form id="staff-form" novalidate enctype="multipart/form-data" class="px-7 py-7">
                @csrf
                <input type="hidden" id="staff-id" value="">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" id="name" name="name"
                                placeholder="e.g. RAHUL SHARMA"
                                class="form-input-modern !pl-12 uppercase" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-name">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Father's Name --}}
                    <div>
                        <label for="f_name" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Father's Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="f_name" name="f_name" placeholder="e.g. SURESH SHARMA" class="form-input-modern !pl-12 uppercase" />
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-f_name">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Date of Birth --}}
                    <div>
                        <label for="dob" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Date of Birth <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="date" id="dob" name="dob" class="form-input-modern !pl-12" />
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-dob">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Mobile Number --}}
                    <div>
                        <label for="mobile" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Mobile Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="tel" id="mobile" name="mobile" placeholder="10 digit mobile number" maxlength="10" class="form-input-modern !pl-12" />
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-mobile">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Date of Joining --}}
                    <div>
                        <label for="doj" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Date of Joining <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="date" id="doj" name="doj" class="form-input-modern !pl-12" />
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-doj">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Department --}}
                    <div>
                        <label for="dept_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Department <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <select id="dept_id" name="dept_id" class="form-input-modern !pl-12 appearance-none">
                                <option value="">-- Select Department --</option>
                            </select>
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-dept_id">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Designation --}}
                    <div>
                        <label for="designation" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Designation <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="text" id="designation" name="designation" placeholder="e.g. SENIOR MANAGER" class="form-input-modern !pl-12 uppercase" />
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-designation">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Office --}}
                    <div>
                        <label for="office_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Office Location <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <select id="office_id" name="office_id" class="form-input-modern !pl-12 appearance-none">
                                <option value="">-- Select Office --</option>
                            </select>
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-office_id">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-status">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                {{-- Status, Role, Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="status" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Account Status <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <select id="status" name="status" class="form-input-modern !pl-12 appearance-none">
                                <option value="">-- Select Status --</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-status">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    <div>
                        <label for="role" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            User Role <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <select id="role" name="role" class="form-input-modern !pl-12 appearance-none">
                                <option value="staff">Staff</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Admin</option>
                            </select>
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="email" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Email Address
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" placeholder="e.g. rahul@example.com" class="form-input-modern !pl-12" />
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-email">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    <div>
                        <label for="photo" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Profile Photo
                        </label>
                        <div class="relative group">
                            <input type="file" id="photo" name="photo" accept="image/*"
                                class="w-full py-2.5 px-4 border border-slate-200 rounded-2xl text-sm bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition
                                       file:mr-4 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold
                                       file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer" />
                        </div>
                        <div id="photo-preview-wrap" class="mt-3 hidden">
                            <div class="relative inline-block">
                                <img id="photo-preview" src="" alt="Preview" class="w-16 h-16 rounded-2xl object-cover border-2 border-white shadow-md" />
                                <div class="absolute -top-2 -right-2 w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    {{-- Address --}}
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">
                            Residential Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute top-3 left-0 pl-4 flex items-start pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition">
                                <svg class="w-4.5 h-4.5 mt-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <textarea id="address" name="address" rows="3" placeholder="Enter full permanent address..."
                                class="form-input-modern !pl-12 uppercase resize-none min-h-[100px] py-4"></textarea>
                        </div>
                        <p class="text-rose-500 text-[10px] font-bold mt-1.5 hidden items-center gap-1 uppercase tracking-wider" id="err-address">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>
                </div>

                {{-- Footer Action Bar --}}
                <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3 text-slate-400">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest">System Ready</span>
                    </div>
                    
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <button type="button" onclick="resetForm()" class="group flex items-center justify-center gap-3 px-8 py-4 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all duration-300">
                            <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357-2H15"/>
                            </svg>
                            <span class="text-sm">Reset</span>
                        </button>
                        
                        <button type="submit" id="submit-btn" class="flex-1 sm:flex-none flex items-center justify-center gap-3 px-12 py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-[0_10px_30px_rgba(79,70,229,0.3)] hover:bg-indigo-700 hover:-translate-y-1 active:translate-y-0 transition-all duration-300">
                            <span id="submit-label" class="text-sm tracking-wide">Save Personnel Record</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Bottom: Info Section (Side-by-Side) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
            
            {{-- Quick Guidelines Card --}}
            <div class="card-premium border-none shadow-[0_20px_50px_rgba(0,0,0,0.03)] bg-white p-8">
                <h3 class="text-slate-900 font-bold text-sm mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                    Filling Guide & Information
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex gap-4 group">
                        <div class="w-10 h-10 rounded-2xl bg-slate-50 flex-shrink-0 flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 mb-1">Standard Case</p>
                            <p class="text-[11px] text-slate-400 leading-relaxed">Names & addresses are automatically converted to <span class="text-indigo-600 font-bold">UPPERCASE</span>.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 group">
                        <div class="w-10 h-10 rounded-2xl bg-slate-50 flex-shrink-0 flex items-center justify-center text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 002.25 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 mb-1">Mobile Contact</p>
                            <p class="text-[11px] text-slate-400 leading-relaxed">Exactly <span class="text-emerald-600 font-bold">10 digits</span> required for communication.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 group">
                        <div class="w-10 h-10 rounded-2xl bg-slate-50 flex-shrink-0 flex items-center justify-center text-slate-400 group-hover:bg-amber-50 group-hover:text-amber-600 transition duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 mb-1">Profile Photo</p>
                            <p class="text-[11px] text-slate-400 leading-relaxed">Recommended <span class="text-amber-600 font-bold">500x500px</span> (max 2MB).</p>
                        </div>
                    </div>
                    <div class="flex gap-4 group">
                        <div class="w-10 h-10 rounded-2xl bg-slate-50 flex-shrink-0 flex items-center justify-center text-slate-400 group-hover:bg-rose-50 group-hover:text-rose-600 transition duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700 mb-1">Account Security</p>
                            <p class="text-[11px] text-slate-400 leading-relaxed">Ensure roles match actual designation levels.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Support & Help Card --}}
            <div class="card-premium !p-8 bg-slate-900 border-none relative overflow-hidden group shadow-2xl shadow-indigo-200/50 flex flex-col justify-between">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl group-hover:bg-indigo-500/30 transition duration-500"></div>
                <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-violet-500/10 rounded-full blur-3xl group-hover:bg-violet-500/20 transition duration-500"></div>
                
                <div class="relative z-10 flex flex-col sm:flex-row items-center gap-8">
                    <div class="w-20 h-20 rounded-3xl bg-white/10 backdrop-blur-xl border border-white/10 flex items-center justify-center flex-shrink-0 shadow-2xl">
                        <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div class="text-center sm:text-left">
                        <h4 class="text-white font-bold text-lg mb-2">Need Assistance?</h4>
                        <p class="text-slate-400 text-[11px] leading-relaxed max-w-sm">If you encounter issues with registration or system access, please contact the administrator.</p>
                    </div>
                </div>

                <div class="relative z-10 mt-8 flex flex-col sm:flex-row gap-4">
                    <a href="tel:+911234567890" class="flex-1 flex items-center justify-center gap-3 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl transition shadow-xl shadow-indigo-600/20 active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        Primary Support
                    </a>
                    <a href="mailto:admin@example.com" class="flex-1 flex items-center justify-center gap-3 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl backdrop-blur-md transition border border-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Email Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

@endsection

@push('scripts')
<script>
    const API_BASE   = '/api/staff';
    const DEPT_API   = '/api/departments';
    const OFFICE_API = '/api/offices';

    const HEADERS = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest'
    };

    // ─── Toast ────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const colors = { success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-yellow-500', info: 'bg-blue-500' };
        const icons  = { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' };
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `${colors[type]} text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 transition-all duration-300 pointer-events-auto`;
        toast.innerHTML = `
            <span class="text-lg font-bold">${icons[type]}</span>
            <span class="text-sm flex-1">${message}</span>
            <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white text-lg leading-none">&times;</button>
        `;
        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 4000);
    }

    // ─── Field Error Helpers ──────────────────────────────────
    function showFieldError(field, message) {
        const el = document.getElementById('err-' + field);
        if (!el) return;
        el.querySelector('span').textContent = message;
        el.classList.remove('hidden');
        el.classList.add('flex');
    }
    function clearFieldErrors() {
        document.querySelectorAll('[id^="err-"]').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('flex');
            const span = el.querySelector('span');
            if (span) span.textContent = '';
        });
    }

    // ─── Load Departments ─────────────────────────────────────
    async function loadDepartments(selectedId = null) {
        try {
            const res  = await fetch(DEPT_API, { headers: HEADERS });
            const data = await res.json();
            const sel  = document.getElementById('dept_id');
            sel.innerHTML = '<option value="">-- Select Department --</option>';
            if (data.success) {
                data.data.filter(d => d.status === 'Active').forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = d.name;
                    if (selectedId && d.id == selectedId) opt.selected = true;
                    sel.appendChild(opt);
                });
            }
        } catch (e) {
            showToast('Departments load nahi ho sake.', 'error');
        }
    }

    // ─── Load Offices ─────────────────────────────────────────
    async function loadOffices(selectedId = null) {
        try {
            const res  = await fetch(OFFICE_API, { headers: HEADERS });
            const data = await res.json();
            const sel  = document.getElementById('office_id');
            sel.innerHTML = '<option value="">-- Select Office --</option>';
            if (data.success) {
                data.data.filter(o => o.status === 'Active').forEach(o => {
                    const opt = document.createElement('option');
                    opt.value = o.id;
                    opt.textContent = o.name;
                    if (selectedId && o.id == selectedId) opt.selected = true;
                    sel.appendChild(opt);
                });
            }
        } catch (e) {
            showToast('Offices load nahi ho sake.', 'error');
        }
    }

    // ─── Photo Preview ────────────────────────────────────────
    document.getElementById('photo').addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('photo-preview').src = e.target.result;
                document.getElementById('photo-preview-wrap').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // ─── Mobile - Numbers Only ────────────────────────────────
    document.getElementById('mobile').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    // ─── Load Edit Data ───────────────────────────────────────
    async function loadEditData(id) {
        try {
            const res  = await fetch(`${API_BASE}/${id}`, { headers: HEADERS });
            const data = await res.json();
            if (!data.success) { showToast('Staff data load nahi ho saka.', 'error'); return; }

            const s = data.data;
            document.getElementById('staff-id').value    = s.id;
            document.getElementById('name').value        = s.name;
            document.getElementById('f_name').value      = s.f_name;
            document.getElementById('dob').value         = s.dob;
            document.getElementById('mobile').value      = s.mobile;
            document.getElementById('email').value       = s.email ?? '';
            document.getElementById('doj').value         = s.doj;
            document.getElementById('designation').value = s.designation;
            document.getElementById('address').value     = s.address;
            document.getElementById('status').value      = s.status;
            document.getElementById('role').value        = s.role ?? 'staff';

            await loadDepartments(s.dept_id);
            await loadOffices(s.office_id);

            if (s.photo) {
                document.getElementById('photo-preview').src = s.photo;
                document.getElementById('photo-preview-wrap').classList.remove('hidden');
            }

            document.getElementById('form-heading').textContent  = 'Staff Edit Karein';
            document.getElementById('breadcrumb-label').textContent = 'Edit Staff';
            document.getElementById('submit-label').textContent  = 'Update Karein';
        } catch (e) {
            showToast('Server error. Data load nahi ho saka.', 'error');
        }
    }

    // ─── Reset Form ───────────────────────────────────────────
    function resetForm() {
        document.getElementById('staff-form').reset();
        document.getElementById('staff-id').value = '';
        document.getElementById('photo-preview-wrap').classList.add('hidden');
        document.getElementById('photo-preview').src = '';
        clearFieldErrors();
        document.getElementById('form-heading').textContent  = 'Naya Staff Add Karein';
        document.getElementById('breadcrumb-label').textContent  = 'New Staff';
        document.getElementById('submit-label').textContent  = 'Save Karein';
        loadDepartments();
        loadOffices();
    }

    // ─── Submit Form ──────────────────────────────────────────
    document.getElementById('staff-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearFieldErrors();

        const id     = document.getElementById('staff-id').value;
        const isEdit = !!id;

        // Client-side validation
        let hasError = false;
        const name        = document.getElementById('name').value.trim();
        const f_name      = document.getElementById('f_name').value.trim();
        const dob         = document.getElementById('dob').value;
        const mobile      = document.getElementById('mobile').value.trim();
        const doj         = document.getElementById('doj').value;
        const dept_id     = document.getElementById('dept_id').value;
        const designation = document.getElementById('designation').value.trim();
        const address     = document.getElementById('address').value.trim();
        const office_id   = document.getElementById('office_id').value;
        const status      = document.getElementById('status').value;

        if (!name)        { showFieldError('name', 'Name required hai.'); hasError = true; }
        if (!f_name)      { showFieldError('f_name', "Father's Name required hai."); hasError = true; }
        if (!dob)         { showFieldError('dob', 'Date of Birth required hai.'); hasError = true; }
        if (!mobile)      { showFieldError('mobile', 'Mobile number required hai.'); hasError = true; }
        else if (!/^\d{10}$/.test(mobile)) { showFieldError('mobile', 'Mobile number sirf 10 digits ka hona chahiye.'); hasError = true; }
        if (!doj)         { showFieldError('doj', 'Date of Joining required hai.'); hasError = true; }
        if (!dept_id)     { showFieldError('dept_id', 'Department select karo.'); hasError = true; }
        if (!designation) { showFieldError('designation', 'Designation required hai.'); hasError = true; }
        if (!address)     { showFieldError('address', 'Address required hai.'); hasError = true; }
        if (!office_id)   { showFieldError('office_id', 'Office select karo.'); hasError = true; }
        if (!status)      { showFieldError('status', 'Status select karo.'); hasError = true; }

        if (hasError) {
            showToast('Please sare required fields fill karein.', 'warning');
            return;
        }

        const formData = new FormData(document.getElementById('staff-form'));

        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        const origLabel = document.getElementById('submit-label').textContent;
        document.getElementById('submit-label').textContent = 'Saving...';

        try {
            const endpoint = isEdit ? `${API_BASE}/${id}` : API_BASE;
            const response = await fetch(endpoint, {
                method: isEdit ? 'POST' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const res = await response.json();

            if (response.ok && res.success) {
                showToast(res.message, 'success');
                if (!isEdit) {
                    resetForm();
                }
            } else if (response.status === 422 && res.errors) {
                Object.entries(res.errors).forEach(([field, messages]) => {
                    showFieldError(field, messages[0]);
                });
                showToast('Validation error! Fields check karein.', 'error');
            } else {
                showToast(res.message || 'Kuch galat ho gaya. Dobara try karein.', 'error');
            }
        } catch (err) {
            showToast('Network error. Server se connect nahi ho saka.', 'error');
        } finally {
            btn.disabled = false;
            document.getElementById('submit-label').textContent = origLabel;
        }
    });

    // ─── Init ─────────────────────────────────────────────────
    const params   = new URLSearchParams(window.location.search);
    const editId   = params.get('id');
    const offId    = params.get('office_id');
    const roleParam = params.get('role');

    if (editId) {
        loadEditData(editId);
    } else {
        loadDepartments();
        loadOffices(offId);
        if (roleParam) {
            document.getElementById('role').value = roleParam;
        }
    }
</script>
@endpush
