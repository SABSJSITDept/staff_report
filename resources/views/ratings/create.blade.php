@extends('layouts.app')

@section('title', 'Rate Staff - ' . $staffMember->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Rate Staff</h2>
            <p class="text-indigo-600 font-medium text-lg mt-1 flex items-center gap-2">
                <span class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm text-sm">
                    {{ strtoupper(substr($staffMember->name, 0, 1)) }}
                </span>
                {{ $staffMember->name }}
            </p>
        </div>
        <div>
            <a href="{{ route('ratings.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to List
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .star-group {
            display: inline-flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .star-group input:checked ~ label {
            color: #facc15; /* yellow-400 */
        }
        .star-group label:hover,
        .star-group label:hover ~ label {
            color: #fbbf24; /* yellow-500 */
        }
        .star-group input:checked ~ label .star-number {
            color: #854d0e; /* dark yellow for contrast */
        }
    </style>

    <form action="{{ route('ratings.store', $staffMember->id) }}" method="POST" id="multiStepForm" class="space-y-8 relative">
        @csrf
        
        @php
            $totalSteps = $categories->count() + 1; // Categories + Overall Assessment
        @endphp

        <!-- Progress Indicator -->
        <div class="mb-8 hidden sm:block">
            <div class="flex justify-between items-center relative px-4">
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-slate-200 z-0 rounded-full"></div>
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-indigo-500 z-0 rounded-full transition-all duration-300" id="progressBar" style="width: 0%;"></div>
                
                @forelse($categories as $index => $category)
                    <div class="relative z-10 flex flex-col items-center step-indicator" data-step="{{ $loop->iteration }}">
                        <div class="w-10 h-10 rounded-full border-4 border-slate-200 bg-white flex items-center justify-center font-bold text-slate-500 transition-colors duration-300 step-circle shadow-sm">
                            {{ $loop->iteration }}
                        </div>
                    </div>
                @empty
                @endforelse
                @if($categories->count() > 0)
                <div class="relative z-10 flex flex-col items-center step-indicator" data-step="{{ $totalSteps }}">
                    <div class="w-10 h-10 rounded-full border-4 border-slate-200 bg-white flex items-center justify-center font-bold text-slate-500 transition-colors duration-300 step-circle shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="sm:hidden mb-4 text-center font-medium text-slate-600">
            Step <span id="mobileStepText">1</span> of {{ $totalSteps }}
        </div>

        @forelse($categories as $category)
            <div class="step-content bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all hidden" data-step="{{ $loop->iteration }}">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h5 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        {{ $category->name }}
                    </h5>
                </div>
                <div class="p-6 space-y-8">
                    @forelse($category->questions as $question)
                        <div class="pb-6 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                            <p class="text-slate-800 font-medium text-base mb-4 flex gap-2">
                                <span class="text-indigo-500 font-bold">{{ $loop->iteration }}.</span> 
                                {{ $question->question }}
                            </p>
                            
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pl-6">
                                <div class="lg:col-span-5">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        Rating (1 to 5) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="star-group gap-1">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star_{{ $question->id }}_{{ $i }}" name="ratings[{{ $question->id }}][rating]" value="{{ $i }}" class="hidden rating-input" required>
                                            <label for="star_{{ $question->id }}_{{ $i }}" class="cursor-pointer text-slate-200 transition-colors relative" title="{{ $i }} Star">
                                                <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                <span class="absolute inset-0 flex items-center justify-center text-[13px] font-bold text-slate-500 mt-1 pointer-events-none star-number">{{ $i }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="lg:col-span-7">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Remark (Optional)</label>
                                    <input type="text" name="ratings[{{ $question->id }}][remark]" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50 py-2.5 px-3 transition-colors placeholder-slate-400" placeholder="Any specific remark for this question">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-500 italic">No questions found in this category.</div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">No rating categories and questions found. Please add them to the database first.</p>
                    </div>
                </div>
            </div>
        @endforelse

        @if($categories->count() > 0)
        <div class="step-content bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all hidden" data-step="{{ $totalSteps }}">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h5 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Overall Assessment
                </h5>
            </div>
            <div class="p-6">
                <div>
                    <label for="overall_remark" class="block text-sm font-medium text-slate-700 mb-2">Overall Remark (Optional)</label>
                    <textarea name="overall_remark" id="overall_remark" rows="4" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50 py-3 px-4 transition-colors placeholder-slate-400 resize-none" placeholder="Enter overall remarks about the staff member's performance..."></textarea>
                </div>
            </div>
        </div>
        
        <div class="flex justify-between items-center pt-6 pb-12">
            <button type="button" id="prevBtn" class="hidden inline-flex items-center px-6 py-3 border border-slate-300 rounded-xl shadow-sm text-base font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Previous
            </button>
            <div class="ml-auto flex gap-3">
                <button type="button" id="nextBtn" class="inline-flex items-center px-8 py-3.5 border border-transparent rounded-xl shadow-md text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    Next
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button type="submit" id="submitBtn" class="hidden inline-flex items-center px-8 py-3.5 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Submit Rating
                </button>
            </div>
        </div>
        @endif
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalSteps = {{ $totalSteps ?? 0 }};
        if (totalSteps === 0) return;

        let currentStep = 1;
        const form = document.getElementById('multiStepForm');
        const steps = document.querySelectorAll('.step-content');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const progressBar = document.getElementById('progressBar');
        const indicators = document.querySelectorAll('.step-indicator');
        const mobileStepText = document.getElementById('mobileStepText');

        function updateUI() {
            // Update Visibility of Steps
            steps.forEach(step => {
                if (parseInt(step.getAttribute('data-step')) === currentStep) {
                    step.classList.remove('hidden');
                    // Add simple fade in animation
                    step.style.opacity = '0';
                    setTimeout(() => step.style.opacity = '1', 50);
                } else {
                    step.classList.add('hidden');
                }
            });

            // Update Progress Bar
            const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
            progressBar.style.width = `${progressPercentage}%`;

            // Update Indicators
            indicators.forEach(indicator => {
                const stepNum = parseInt(indicator.getAttribute('data-step'));
                const circle = indicator.querySelector('.step-circle');
                
                if (stepNum < currentStep) {
                    circle.classList.add('bg-indigo-600', 'border-indigo-600', 'text-white');
                    circle.classList.remove('bg-white', 'text-slate-500', 'border-indigo-600', 'border-slate-200');
                } else if (stepNum === currentStep) {
                    circle.classList.add('border-indigo-600', 'text-indigo-600', 'bg-white');
                    circle.classList.remove('bg-indigo-600', 'text-white', 'border-slate-200', 'text-slate-500');
                } else {
                    circle.classList.add('bg-white', 'border-slate-200', 'text-slate-500');
                    circle.classList.remove('bg-indigo-600', 'border-indigo-600', 'text-white', 'text-indigo-600');
                }
            });

            // Update Mobile Text
            if(mobileStepText) mobileStepText.innerText = currentStep;

            // Buttons visibility
            if (currentStep === 1) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'inline-flex';
            }

            if (currentStep === totalSteps) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-flex';
            } else {
                nextBtn.style.display = 'inline-flex';
                submitBtn.style.display = 'none';
            }
            
            // Scroll to top of page to avoid sticky nav overlap
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateCurrentStep() {
            const currentStepEl = document.querySelector(`.step-content[data-step="${currentStep}"]`);
            const inputs = currentStepEl.querySelectorAll('.rating-input[required]');
            let isValid = true;
            
            // Check if any radio group is missing a selection
            const names = new Set();
            inputs.forEach(input => names.add(input.name));
            
            names.forEach(name => {
                const checked = currentStepEl.querySelector(`input[name="${name}"]:checked`);
                if (!checked) {
                    isValid = false;
                }
            });

            if (!isValid) {
                // If invalid, trigger browser validation UI
                form.reportValidity();
            }

            return isValid;
        }

        nextBtn.addEventListener('click', () => {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateUI();
                }
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateUI();
            }
        });

        // Initialize UI
        updateUI();
    });
</script>
@endpush
