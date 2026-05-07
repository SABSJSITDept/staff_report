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

<div class="flex flex-col lg:flex-row gap-8">

    {{-- Left: Form Card --}}
    <div class="flex-1 min-w-0">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-7 py-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-white font-semibold text-base leading-tight" id="form-heading">Add new Staff </h2>
                        <p class="text-indigo-200 text-xs mt-0.5">Star fields are required </p>
                    </div>
                </div>
                <a href="{{ route('staff.view') }}"
                    class="flex items-center gap-1.5 text-xs font-medium text-white/80 hover:text-white bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to List
                </a>
            </div>

            {{-- Form --}}
            <form id="staff-form" novalidate enctype="multipart/form-data" class="px-7 py-7">
                @csrf
                <input type="hidden" id="staff-id" value="">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" id="name" name="name"
                                placeholder="e.g. RAHUL SHARMA"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm uppercase bg-gray-50
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                       transition placeholder:normal-case" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-name">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Father's Name --}}
                    <div>
                        <label for="f_name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Father's Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="f_name" name="f_name"
                                placeholder="e.g. SURESH SHARMA"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm uppercase bg-gray-50
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                       transition placeholder:normal-case" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-f_name">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Date of Birth --}}
                    <div>
                        <label for="dob" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="date" id="dob" name="dob"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-dob">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Mobile --}}
                    <div>
                        <label for="mobile" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="tel" id="mobile" name="mobile"
                                placeholder="10 digit mobile number"
                                maxlength="10"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-mobile">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Date of Joining --}}
                    <div>
                        <label for="doj" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Date of Joining <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="date" id="doj" name="doj"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-doj">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Department --}}
                    <div>
                        <label for="dept_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <select id="dept_id" name="dept_id"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 appearance-none
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                <option value="">-- Select Department --</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-dept_id">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Designation --}}
                    <div>
                        <label for="designation" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Designation <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="text" id="designation" name="designation"
                                placeholder="e.g. SENIOR MANAGER"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm uppercase bg-gray-50
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                       transition placeholder:normal-case" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-designation">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Office --}}
                    <div>
                        <label for="office_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Office <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <select id="office_id" name="office_id"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 appearance-none
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                <option value="">-- Select Office --</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-office_id">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <select id="status" name="status"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 appearance-none
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                                <option value="">-- Select Status --</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-status">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email"
                                placeholder="e.g. rahul@example.com"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-email">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Photo --}}
                    <div>
                        <label for="photo" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Photo
                        </label>
                        <div class="relative">
                            <input type="file" id="photo" name="photo" accept="image/*"
                                class="w-full py-2 px-3 border border-gray-200 rounded-xl text-sm bg-gray-50
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition
                                       file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                       file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        </div>
                        <div id="photo-preview-wrap" class="mt-2 hidden">
                            <img id="photo-preview" src="" alt="Preview" class="w-20 h-20 rounded-xl object-cover border border-gray-200 shadow-sm" />
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-photo">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>

                    {{-- Address --}}
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute top-3 left-0 pl-3.5 flex items-start pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <textarea id="address" name="address" rows="3"
                                placeholder="Full address ..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm uppercase bg-gray-50 resize-none
                                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                       transition placeholder:normal-case"></textarea>
                        </div>
                        <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="err-address">
                            <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span></span>
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" id="submit-btn"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                               font-semibold px-6 py-2.5 rounded-xl transition text-sm shadow-sm shadow-indigo-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="submit-label">Save Karein</span>
                    </button>
                    <button type="button" onclick="resetForm()"
                        class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200
                               text-gray-600 font-medium px-5 py-2.5 rounded-xl transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Right: Info Panel --}}
    <div class="w-full lg:w-72 flex-shrink-0 flex flex-col gap-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <span class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </span>
                Filling Tips
            </h3>
            <ul class="text-xs text-gray-500 space-y-2.5">
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 mt-1.5 flex-shrink-0"></span>
                    Name, Father's Name, Designation and Address automatically convert to CAPITAL letters.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 mt-1.5 flex-shrink-0"></span>
                    Mobile should be exactly 10 digits, no characters allowed.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 mt-1.5 flex-shrink-0"></span>
                    Photo is optional, max size 2MB. Only image files are allowed.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 mt-1.5 flex-shrink-0"></span>
                    Only active records appear in the Department and Office dropdowns.
                </li>
            </ul>
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
    const params = new URLSearchParams(window.location.search);
    const editId = params.get('id');

    if (editId) {
        loadEditData(editId);
    } else {
        loadDepartments();
        loadOffices();
    }
</script>
@endpush
