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

    <form action="{{ route('ratings.store', $staffMember->id) }}" method="POST" class="space-y-8">
        @csrf
        
        @forelse($categories as $category)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all hover:shadow-md">
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
                            
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pl-6">
                                <div class="md:col-span-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">
                                        Rating (1 to 5) <span class="text-red-500">*</span>
                                    </label>
                                    <select name="ratings[{{ $question->id }}][rating]" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50 py-2.5 px-3 transition-colors" required>
                                        <option value="">Select Rating...</option>
                                        <option value="1">1 - Poor</option>
                                        <option value="2">2 - Fair</option>
                                        <option value="3">3 - Good</option>
                                        <option value="4">4 - Very Good</option>
                                        <option value="5">5 - Excellent</option>
                                    </select>
                                </div>
                                <div class="md:col-span-8">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Remark (Optional)</label>
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

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all hover:shadow-md">
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

        <div class="flex justify-end pt-4 pb-12">
            <button type="submit" class="inline-flex items-center px-8 py-3.5 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform transition hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Submit Rating
            </button>
        </div>
    </form>
</div>
@endsection
