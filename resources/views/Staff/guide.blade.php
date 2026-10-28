@extends('layouts.app')
@section('title', 'Staff User Guide & Operations Manual')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-16">

    {{-- Modern Hero Banner --}}
    <div class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 p-6 md:p-10 rounded-3xl text-white shadow-xl relative overflow-hidden border border-indigo-700/40">
        {{-- Subtle decorative glow --}}
        <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-0 right-1/4 w-48 h-48 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 space-y-4 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 text-xs font-semibold text-indigo-200 backdrop-blur-md border border-white/10">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Staff Operations & User Guide
            </div>
            
            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-white">
                Complete Staff User Guide
            </h1>
            
            <p class="text-indigo-200/90 text-sm md:text-base leading-relaxed">
                Portal ke sabhi features aur daily operations ko use karne ka aasan step-by-step tarika. Daily live work tracking, personal task planner, office team collaboration, aur IT support ko effectively use karein.
            </p>

            {{-- Quick Jump Navigation Pills --}}
            <div class="flex flex-wrap items-center gap-2 pt-2">
                <span class="text-xs font-bold text-indigo-300 uppercase tracking-wider mr-1">Quick Jump:</span>
                <a href="#section-tracking" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition backdrop-blur-sm">
                    ⏱️ Daily Work Tracking
                </a>
                <a href="#section-personal-tasks" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition backdrop-blur-sm">
                    📝 Task Planner (To-Do)
                </a>
                <a href="#section-projects" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition backdrop-blur-sm">
                    👥 Office Projects (WhatsApp UI)
                </a>
                <a href="#section-support" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition backdrop-blur-sm">
                    🛠️ IT Helpdesk
                </a>
                <a href="#section-profile" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition backdrop-blur-sm">
                    👤 Profile & Security
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- SECTION 1: DAILY LIVE WORK TRACKING & REPORTING                --}}
    {{-- ============================================================== --}}
    <div id="section-tracking" class="space-y-4 pt-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-lg border border-emerald-100">
                1
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">Daily Live Work Tracking & Reporting</h2>
                <p class="text-xs text-slate-500">Live timer ke sath apna rozana ka kaam track karein aur daily report submit karein.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            {{-- Card 1.1: Start, Pause & Resume --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs hover:shadow-md transition space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
                    ⏱️
                </div>
                <h3 class="text-base font-bold text-slate-900">Live Task Timer (Start, Pause, Resume)</h3>
                <ul class="text-xs text-slate-600 space-y-2.5">
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Top menu se <strong>"Track My Task"</strong> par jayein.</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Department/Task chunein, task ka naam likhein aur <strong>"Start New Task"</strong> click karein. Timer shuru ho jayega.</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Lunch ya break ke waqt <strong>"Pause"</strong> karein, wapis aakar <strong>"Resume"</strong> karein.</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Kaam pura hone par <strong>"End Task"</strong> dabayein aur final remarks save karein.</span>
                    </li>
                </ul>
            </div>

            {{-- Card 1.2: Log Update Feature --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs hover:shadow-md transition space-y-3">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-lg">
                    📝
                </div>
                <h3 class="text-base font-bold text-slate-900">Real-time Progress Notes (Log Update)</h3>
                <ul class="text-xs text-slate-600 space-y-2.5">
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-sky-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Live task chalte waqt aap bina timer roke progress note add kar sakte hain.</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-sky-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Active task ke <strong>"Add New Update"</strong> box mein details likhein.</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-sky-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span><strong>"Log Update"</strong> dabayein. Har note exact time aur date ke sath supervisor ke report mein display hota hai.</span>
                    </li>
                </ul>
            </div>

            {{-- Card 1.3: Quick Add Other Tasks --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs hover:shadow-md transition space-y-3">
                <div class="w-10 h-10 rounded-xl bg-fuchsia-100 text-fuchsia-700 flex items-center justify-center font-bold text-lg">
                    ⚡
                </div>
                <h3 class="text-base font-bold text-slate-900">Quick Add: Other Tasks (Offline Work)</h3>
                <ul class="text-xs text-slate-600 space-y-2.5">
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-fuchsia-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Agar kisi ad-hoc kaam ka live timer chalana bhool gaye ya offline meeting/call thi.</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-fuchsia-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Dashboard ya Tracker se <strong>"Quick Add: Other Task"</strong> button dabayein.</span>
                    </li>
                    <li class="flex gap-2 items-start">
                        <svg class="w-4 h-4 text-fuchsia-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Kaam ka description likhein. Yeh bina timer chalaye direct aaj ki date ke report mein add ho jata hai.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- SECTION 2: PERSONAL TASK PLANNER & TO-DO LIST                  --}}
    {{-- ============================================================== --}}
    <div id="section-personal-tasks" class="space-y-4 pt-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-lg border border-indigo-100">
                2
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">Personal Task Planner & To-Do List</h2>
                <p class="text-xs text-slate-500">Apne rozana ke routine kaam aur future dates ke tasks ko plan aur manage karein.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 md:p-8 shadow-2xs space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Type 1: Daily Routine Tasks --}}
                <div class="bg-indigo-50/50 rounded-2xl p-5 border border-indigo-100 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">🔄</span>
                        <h3 class="text-base font-black text-indigo-950">Daily Routine Tasks ("Roz ke Kaam")</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Aise kaam jo aapko <strong>har roz</strong> karne hote hain (Jaise: Morning Email Check, Daily Bank Statement Verification, Evening Work Summary).
                    </p>
                    <div class="bg-white p-3.5 rounded-xl border border-indigo-100 text-xs text-slate-700 space-y-1.5">
                        <div class="font-bold text-indigo-900">⚡ Auto-Reset Feature:</div>
                        <p class="text-slate-600">
                            Aap aaj checkbox par click karke complete mark karte hain. Raat 12 baje ke baad yeh automatically agle din ke liye reset ho jata hai taaki aapko dobara task type na karna pade!
                        </p>
                    </div>
                </div>

                {{-- Type 2: Scheduled Future Date Tasks --}}
                <div class="bg-purple-50/50 rounded-2xl p-5 border border-purple-100 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-purple-600 text-white flex items-center justify-center font-bold text-sm">📅</span>
                        <h3 class="text-base font-black text-purple-950">Scheduled Tasks ("Future Date ke Kaam")</h3>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Aise kaam jo kisi <strong>specific target date</strong> ko karne hain (Jaise: 20th ko GST Return file karna, Agle Monday client meeting).
                    </p>
                    <div class="bg-white p-3.5 rounded-xl border border-purple-100 text-xs text-slate-700 space-y-1.5">
                        <div class="font-bold text-purple-900">⏰ Deadline Alerts:</div>
                        <p class="text-slate-600">
                            System automatic badges dikhata hai: <span class="text-rose-600 font-bold">⚠️ Overdue</span> (agar date nikal gayi), <span class="text-amber-800 font-bold">⏰ Due Today</span> (aaj karna hai), ya scheduled target date.
                        </p>
                    </div>
                </div>
            </div>

            {{-- How to use Task Planner --}}
            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-3">Task Planner Step-by-Step Guide:</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-slate-700">
                    <div class="flex gap-2.5 items-start">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0">1</span>
                        <span>Top Navigation se <strong>"Task List"</strong> par click karein.</span>
                    </div>
                    <div class="flex gap-2.5 items-start">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0">2</span>
                        <span><strong>"+ Add New Task"</strong> dabayein aur chunein: <em>Daily Routine</em> ya <em>Scheduled Task</em>.</span>
                    </div>
                    <div class="flex gap-2.5 items-start">
                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0">3</span>
                        <span>Kaam pura hone par card par diye gaye circular checkbox par 1-click karke complete mark karein.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- SECTION 3: OFFICE PROJECTS & WHATSAPP GROUP CHAT COLLABORATION --}}
    {{-- ============================================================== --}}
    <div id="section-projects" class="space-y-4 pt-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-[#008069]/10 text-[#008069] flex items-center justify-center font-black text-lg border border-[#008069]/20">
                3
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">Office Projects & Team Collaboration (WhatsApp Group UI)</h2>
                <p class="text-xs text-slate-500">Same office ke staff ke sath project banayein, task delegate karein aur WhatsApp jaisi chat me collaborate karein.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 md:p-8 shadow-2xs space-y-6">
            {{-- WhatsApp UI Feature Highlights --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Point 1: Create & Share with Office Colleagues --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg">
                        📁
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Project Create & Share</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Top menu se <strong>"Projects"</strong> par jayein aur <strong>"+ Create Project"</strong> dabayein. Apne hi office ke colleagues ko chunein. Har member ko instant alert notification milta hai.
                    </p>
                </div>

                {{-- Point 2: Task Assignment & Strict Permissions --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center text-lg">
                        🔒
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Task Delegation & Role Policy</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Project ke andar aap task khud ko ya kisi bhi colleague ko assign kar sakte hain. <strong>Strict Rule:</strong> Jis staff ko task assign hai, sirf wahi status update ya complete kar sakta hai. Dusro ko lock indicator dikhta hai.
                    </p>
                </div>

                {{-- Point 3: WhatsApp Group Chat Feed --}}
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/80 space-y-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#008069] text-white flex items-center justify-center text-lg">
                        💬
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">WhatsApp Chat & Interactive Cards</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Project open karte hi WhatsApp Group jaisa UI khulta hai. Sabhi messages, status updates aur interactive task cards feed me dikhte hain with timestamp aur double checkmarks (✓✓).
                    </p>
                </div>
            </div>

            {{-- 1-Click Status Advance in WhatsApp --}}
            <div class="bg-[#d9fdd3]/50 p-5 rounded-2xl border border-emerald-200 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-800 font-bold text-sm">⚡ 1-Click Status Action:</span>
                    <span class="text-xs text-emerald-900 font-medium">Assigned staff bina kisi lambe form ke seedha card se task update kar sakta hai:</span>
                </div>
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs font-bold">
                    <span class="px-3 py-1 bg-white rounded-lg border border-emerald-200 text-slate-700">⏳ Pending</span>
                    <span class="text-emerald-600 font-black">➔</span>
                    <span class="px-3 py-1 bg-[#008069] text-white rounded-lg shadow-2xs">⚡ Start Task (In Progress)</span>
                    <span class="text-emerald-600 font-black">➔</span>
                    <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg shadow-2xs">✅ Mark as Completed</span>
                </div>
            </div>

            {{-- Sidebar vs Chat --}}
            <div class="text-xs text-slate-600 space-y-2 border-t border-slate-100 pt-4">
                <strong class="text-slate-800 block">Left Task Tracker Sidebar:</strong>
                <p>
                    Left side par sabhi tasks ki list hoti hai with instant search aur filter chips (<em>All</em>, <em>⭐ My Tasks</em>, <em>Pending</em>, <em>Done</em>). Mobile me upar diye gaye <strong>"💬 Chat | 📋 Tasks"</strong> button se ek click me switch kar sakte hain.
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- SECTION 4: IT SUPPORT & HELPDESK TICKETS                       --}}
    {{-- ============================================================== --}}
    <div id="section-support" class="space-y-4 pt-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-black text-lg border border-rose-100">
                4
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">IT Support & Helpdesk Tickets</h2>
                <p class="text-xs text-slate-500">System, Hardware, Software ya Network issue aane par official support request create karein.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 md:p-8 shadow-2xs space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs text-slate-700">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1.5">
                    <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center mb-1">1</div>
                    <strong class="text-slate-900 block">Open Helpdesk</strong>
                    <span>Top menu se <strong>"IT Support"</strong> section open karein.</span>
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1.5">
                    <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center mb-1">2</div>
                    <strong class="text-slate-900 block">Create Ticket</strong>
                    <span><strong>"Create New Ticket"</strong> click karke category chunein (Hardware, Software, Internet, Printer, etc.).</span>
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1.5">
                    <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center mb-1">3</div>
                    <strong class="text-slate-900 block">Upload Evidence</strong>
                    <span>Error message ka screenshot ya photo attach karke submit karein taaki IT engineer turant diagnose kar sake.</span>
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1.5">
                    <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-700 font-bold flex items-center justify-center mb-1">4</div>
                    <strong class="text-slate-900 block">Live Resolution Track</strong>
                    <span>Ticket status dekhein (<em>Open</em> ➔ <em>In Progress</em> ➔ <em>Resolved</em>) aur IT team se chat karein.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- SECTION 5: PROFILE, KYC & SECURITY                             --}}
    {{-- ============================================================== --}}
    <div id="section-profile" class="space-y-4 pt-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center font-black text-lg border border-amber-100">
                5
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-black text-slate-800 tracking-tight">Account, Profile Update & Security</h2>
                <p class="text-xs text-slate-500">Apni personal details, profile picture aur password security maintain karein.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Profile Update Request --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs space-y-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center text-lg">
                    👤
                </div>
                <h3 class="text-base font-bold text-slate-900">Profile Details & Photo Update</h3>
                <ul class="text-xs text-slate-600 space-y-2">
                    <li class="flex gap-2 items-start"><svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> <span>Dashboard par <strong>"Update Profile"</strong> button par click karein.</span></li>
                    <li class="flex gap-2 items-start"><svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> <span>Naya mobile number, address ya profile photo upload karein.</span></li>
                    <li class="flex gap-2 items-start"><svg class="w-4 h-4 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> <span>HR/Manager approval ke baad aapka record automatically update ho jata hai.</span></li>
                </ul>
            </div>

            {{-- Change Password --}}
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs space-y-3">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg">
                    🔑
                </div>
                <h3 class="text-base font-bold text-slate-900">Password Change & Account Safety</h3>
                <ul class="text-xs text-slate-600 space-y-2">
                    <li class="flex gap-2 items-start"><svg class="w-4 h-4 text-purple-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> <span>Upar top-right me apne naam par click karke <strong>"Change Password"</strong> chunein.</span></li>
                    <li class="flex gap-2 items-start"><svg class="w-4 h-4 text-purple-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> <span>Pehli baar login karne ke turant baad apna default password badal lein.</span></li>
                    <li class="flex gap-2 items-start"><svg class="w-4 h-4 text-purple-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> <span>Apna password kisi ke sath share na karein taaki aapka work log safe rahe.</span></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- SECTION 6: GOLDEN RULES & DAILY REMINDERS                      --}}
    {{-- ============================================================== --}}
    <div class="bg-gradient-to-r from-emerald-600 via-teal-700 to-indigo-800 p-8 md:p-10 rounded-3xl text-white shadow-xl space-y-6 relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-2xl font-black mb-2 flex items-center gap-2">
                <span>🌟</span>
                <span>Golden Rules for Every Staff Member</span>
            </h3>
            <p class="text-emerald-100 text-xs md:text-sm max-w-2xl font-medium">
                Rozana in 4 simple aadat ko follow karein taaki aapka performance rating aur attendance hamesha accurate rahe:
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 space-y-1">
                    <div class="text-lg">⏰ 01</div>
                    <div class="font-bold text-sm">Start on Time</div>
                    <p class="text-[11px] text-emerald-100">Subah aate hi apna pehla task timer start karein.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 space-y-1">
                    <div class="text-lg">⏸️ 02</div>
                    <div class="font-bold text-sm">Pause During Breaks</div>
                    <p class="text-[11px] text-emerald-100">Lunch ya seat chhodte waqt timer pause karna na bhoolein.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 space-y-1">
                    <div class="text-lg">🔄 03</div>
                    <div class="font-bold text-sm">Check Daily Tasks</div>
                    <p class="text-[11px] text-emerald-100">Task planner me apne roz ke routine tasks tick karein.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 space-y-1">
                    <div class="text-lg">👥 04</div>
                    <div class="font-bold text-sm">Update Project Tasks</div>
                    <p class="text-[11px] text-emerald-100">Project me kaam poora hote hi turant status Done karein.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-6 border-t border-white/15">
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white text-emerald-800 font-black text-xs rounded-xl shadow-md hover:bg-slate-50 transition transform active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Go to Dashboard
                </a>
                <a href="{{ route('personal-tasks.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-800/60 hover:bg-emerald-800/80 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                    Open Task List
                </a>
                <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-900/60 hover:bg-indigo-900/80 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                    Open Office Projects
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
