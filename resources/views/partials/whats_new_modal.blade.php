@auth
@php
    $whatsNewVersion = '2026_october_release';
    $userHasSeen = Auth::user()->seen_whats_new_version === $whatsNewVersion;
@endphp

{{-- What's New Announcement Modal (Hindi Unicode) --}}
<div id="whatsNewModal" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-slate-950/75 backdrop-blur-sm p-4 sm:p-6 transition-all duration-300">
    <div class="min-h-full flex items-center justify-center py-6">
        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 overflow-hidden relative transform transition-all animate-in fade-in zoom-in-95 duration-200">
            
            {{-- Header Gradient Banner --}}
            <div class="bg-gradient-to-r from-indigo-800 via-indigo-600 to-emerald-600 p-6 md:p-7 text-white relative overflow-hidden">
                {{-- Decorative background shapes --}}
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute top-0 right-1/3 w-28 h-28 bg-emerald-400/20 rounded-full blur-xl pointer-events-none"></div>

                <div class="flex items-start justify-between gap-4 relative z-10">
                    <div class="space-y-1.5">
                        <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-bold tracking-wide backdrop-blur-md border border-white/20">
                            <span>🎉</span>
                            <span>पोर्टल में क्या नया है? • नया अपडेट</span>
                        </div>
                        <h2 class="text-xl md:text-2xl font-black text-white tracking-tight">
                            नए प्रोजेक्ट एवं टास्क फीचर्स शुरू किए गए हैं!
                        </h2>
                        <p class="text-indigo-100 text-xs md:text-sm font-medium leading-relaxed">
                            आपके दैनिक कार्यों को सरल, तीव्र और व्यवस्थित बनाने के लिए नए टूल्स जोड़े गए हैं।
                        </p>
                    </div>

                    <button type="button" onclick="dismissWhatsNewModal()" class="text-white/80 hover:text-white p-1.5 rounded-xl hover:bg-white/10 transition shrink-0" title="बंद करें">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>

            {{-- Body: New Feature Highlights --}}
            <div class="p-5 md:p-7 space-y-4 max-h-[60vh] overflow-y-auto divide-y divide-slate-100">

                {{-- Feature 1: Task Planner & To-Do List --}}
                <div class="pt-1 first:pt-0 space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg border border-indigo-100 shrink-0">
                                📝
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800">१. व्यक्तिगत टास्क प्लानर (Task Planner & To-Do List)</h3>
                                <p class="text-[11px] text-slate-400 font-semibold">ऊपर मेनू में &rarr; "Task List"</p>
                            </div>
                        </div>
                        <a href="{{ route('personal-tasks.index') }}" onclick="dismissWhatsNewModal()"
                            class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg text-xs transition border border-indigo-100 shrink-0">
                            अभी देखें &rarr;
                        </a>
                    </div>
                    
                    <div class="bg-indigo-50/40 rounded-2xl p-3.5 border border-indigo-100/70 text-xs text-slate-700 space-y-2 ml-11">
                        <p class="font-bold text-indigo-900">यह कैसे काम करता है? अपने कार्यों को दो श्रेणियों में प्लान करें:</p>
                        <ul class="list-disc list-inside space-y-1.5 text-xs text-slate-600 leading-relaxed">
                            <li><strong class="text-slate-800">दैनिक कार्य (Daily Routine):</strong> ऐसे कार्य जो रोज़ाना करने होते हैं। आज पूरा होने पर टिक करें; मध्यरात्रि (रात १२ बजे) के बाद यह स्वतः ही अगले दिन के लिए रीसेट हो जाएंगे!</li>
                            <li><strong class="text-slate-800">नियत तारीख के कार्य (Scheduled Tasks):</strong> भविष्य की कोई भी तय तारीख चुनें। तारीख निकलने पर ओवरड्यू (⚠️) और आज की तारीख पर ड्यू टुडे (⏰) के अलर्ट्स मिलेंगे।</li>
                        </ul>
                    </div>
                </div>

                {{-- Feature 2: Office Projects & WhatsApp Collaboration --}}
                <div class="pt-4 space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-[#008069]/10 text-[#008069] flex items-center justify-center font-bold text-lg border border-[#008069]/20 shrink-0">
                                👥
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800">२. कार्यालय प्रोजेक्ट्स (WhatsApp ग्रुप चैट अनुसार)</h3>
                                <p class="text-[11px] text-slate-400 font-semibold">ऊपर मेनू में &rarr; "Projects"</p>
                            </div>
                        </div>
                        <a href="{{ route('projects.index') }}" onclick="dismissWhatsNewModal()"
                            class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold rounded-lg text-xs transition border border-emerald-100 shrink-0">
                            अभी देखें &rarr;
                        </a>
                    </div>
                    
                    <div class="bg-emerald-50/40 rounded-2xl p-3.5 border border-emerald-100/70 text-xs text-slate-700 space-y-2 ml-11">
                        <p class="font-bold text-emerald-950">यह कैसे काम करता है? अपने कार्यालय के साथियों के साथ मिलकर प्रोजेक्ट बनाएं:</p>
                        <ul class="list-disc list-inside space-y-1.5 text-xs text-slate-600 leading-relaxed">
                            <li><strong class="text-slate-800">व्हाट्सएप ग्रुप जैसा अनुभव:</strong> प्रोजेक्ट के अंदर सीधा संदेश भेजें, कार्य प्रगति नोट लिखें और भेजे गए संदेशों पर डबल टिक (✓✓) देखें।</li>
                            <li><strong class="text-slate-800">सख्त अनुमति नियम (Strict Role Policy):</strong> जिस कर्मचारी को जो टास्क सौंपा गया है, केवल वही उसे १-क्लिक में शुरू या पूरा कर सकता है। अन्य सदस्यों को स्पष्ट रूप से लॉक (🔒) दिखाई देगा।</li>
                        </ul>
                    </div>
                </div>

                {{-- Feature 3: Updated Staff User Guide --}}
                <div class="pt-4 space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-lg border border-purple-100 shrink-0">
                                📘
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800">३. सम्पूर्ण स्टाफ यूज़र गाइड (Staff User Guide)</h3>
                                <p class="text-[11px] text-slate-400 font-semibold">डैशबोर्ड / प्रोफाइल मेनू में &rarr; "User Guide"</p>
                            </div>
                        </div>
                        <a href="{{ route('staff.guide') }}" onclick="dismissWhatsNewModal()"
                            class="px-3 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold rounded-lg text-xs transition border border-purple-100 shrink-0">
                            गाइड पढ़ें &rarr;
                        </a>
                    </div>
                    
                    <div class="bg-purple-50/40 rounded-2xl p-3.5 border border-purple-100/70 text-xs text-slate-700 space-y-1 ml-11">
                        <p class="text-slate-600 leading-relaxed">
                            लाइव टाइमर ट्रैकर, कार्य नोट्स जोड़ना, ऑफलाइन कार्य (Quick Add), आईटी सपोर्ट टिकट्स और प्रोफाइल अपडेट की सम्पूर्ण चरणबद्ध जानकारी।
                        </p>
                    </div>
                </div>

            </div>

            {{-- Footer Controls --}}
            <div class="p-5 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer self-start sm:self-auto">
                    <input type="checkbox" id="dontShowAgainCheck" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <span>यह जानकारी दोबारा न दिखाएं</span>
                </label>

                <button type="button" onclick="dismissWhatsNewModal()"
                    class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-emerald-600 hover:from-indigo-700 hover:to-emerald-700 text-white text-xs font-black rounded-xl shadow-md transition transform active:scale-95 text-center">
                    समझ गया, शुरू करें! 🚀
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    const WHATS_NEW_VERSION = '{{ $whatsNewVersion }}';
    const USER_ID = '{{ Auth::id() }}';
    const USER_HAS_SEEN_DB = {{ $userHasSeen ? 'true' : 'false' }};
    const STORAGE_KEY = 'spf_whats_new_seen_' + USER_ID + '_' + WHATS_NEW_VERSION;

    function openWhatsNewModal(force = false) {
        const modal = document.getElementById('whatsNewModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function dismissWhatsNewModal() {
        const modal = document.getElementById('whatsNewModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        const dontShow = document.getElementById('dontShowAgainCheck') ? document.getElementById('dontShowAgainCheck').checked : true;

        if (dontShow) {
            // Save in localStorage
            try {
                localStorage.setItem(STORAGE_KEY, 'true');
            } catch (e) {}

            // Send async POST to update database permanently
            fetch('{{ route("staff.dismiss-whats-new") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ version: WHATS_NEW_VERSION })
            }).catch(() => {});
        }
    }

    // Auto-check on page load: show only once per user
    document.addEventListener('DOMContentLoaded', function() {
        const seenInStorage = localStorage.getItem(STORAGE_KEY) === 'true';

        // Only show if user has NOT seen it in DB and NOT in localStorage
        if (!USER_HAS_SEEN_DB && !seenInStorage) {
            setTimeout(function() {
                openWhatsNewModal();
            }, 600);
        }
    });

    // Close on backdrop click
    document.getElementById('whatsNewModal')?.addEventListener('click', function(e) {
        if (e.target === this || e.target === this.firstElementChild) {
            dismissWhatsNewModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('whatsNewModal');
            if (modal && !modal.classList.contains('hidden')) {
                dismissWhatsNewModal();
            }
        }
    });
</script>
@endauth
