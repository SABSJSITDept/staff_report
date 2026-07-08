@extends('layouts.app')

@section('title', 'Rating Configuration')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Rating Configuration</h2>
            <p class="text-slate-500 mt-1">Manage rating categories and their associated questions.</p>
        </div>
        <div>
            <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Category
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <ul class="text-sm text-red-700 list-disc pl-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Settings Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">Rating Session Settings</h3>
            <p class="text-sm text-slate-500 mt-1">Control whether the rating link is open for staff and set the financial year for the current session.</p>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.rating-config.settings.update') }}" method="POST" class="space-y-4">
                @csrf
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Rating Link Status</label>
                        <p class="text-xs text-slate-500">Enable this to allow staff to submit ratings.</p>
                    </div>
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $setting->is_active ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-700">{{ $setting->is_active ? 'Open (Active)' : 'Closed (Inactive)' }}</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="financial_year" class="block text-sm font-medium text-slate-700">Financial Year / Session</label>
                    <input type="text" name="financial_year" id="financial_year" value="{{ old('financial_year', $setting->financial_year) }}" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3" placeholder="e.g., 2026-2027">
                    <p class="text-xs text-slate-500 mt-1">This session identifier will be attached to all ratings submitted while the link is open.</p>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        @forelse($categories as $category)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800">{{ $category->name }}</h3>
                    <form action="{{ route('admin.rating-config.category.delete', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category and ALL its questions?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors p-2" title="Delete Category">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                <div class="p-6">
                    @if($category->questions->count() > 0)
                        <ul class="divide-y divide-slate-100">
                            @foreach($category->questions as $question)
                                <li class="py-3 flex justify-between items-center group hover:bg-slate-50 px-2 rounded-lg transition-colors">
                                    <span class="text-slate-700 font-medium text-sm flex-1">{{ $loop->iteration }}. {{ $question->question }}</span>
                                    <form action="{{ route('admin.rating-config.question.delete', $question->id) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all px-3 py-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-slate-500 text-sm italic mb-4">No questions added to this category yet.</p>
                    @endif
                    
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <button onclick="openQuestionModal({{ $category->id }}, '{{ addslashes($category->name) }}')" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Add Question
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-2xl shadow-sm border border-slate-200">
                <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <h3 class="text-lg font-medium text-slate-900">No categories found</h3>
                <p class="mt-1 text-slate-500">Get started by creating a new rating category.</p>
                <div class="mt-6">
                    <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Category
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('addCategoryModal').classList.add('hidden')"></div>
    
    <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg">
        <form action="{{ route('admin.rating-config.category.store') }}" method="POST">
            @csrf
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">Add New Category</h3>
                    <div class="mt-4">
                        <label for="category_name" class="block text-sm font-medium text-slate-700 mb-1">Category Name</label>
                        <input type="text" name="name" id="category_name" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3" placeholder="e.g., Discipline, Punctuality..." required>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Save Category</button>
                <button type="button" onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Question Modal -->
<div id="addQuestionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('addQuestionModal').classList.add('hidden')"></div>
    
    <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all w-full max-w-lg">
        <form action="{{ route('admin.rating-config.question.store') }}" method="POST">
            @csrf
            <input type="hidden" name="category_id" id="modal_category_id">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">Add Question to <span id="modal_category_name" class="text-indigo-600"></span></h3>
                    <div class="mt-4">
                        <label for="question_text" class="block text-sm font-medium text-slate-700 mb-1">Question</label>
                        <textarea name="question" id="question_text" rows="3" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 resize-none" placeholder="Enter the rating question here..." required></textarea>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Save Question</button>
                <button type="button" onclick="document.getElementById('addQuestionModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openQuestionModal(categoryId, categoryName) {
        document.getElementById('modal_category_id').value = categoryId;
        document.getElementById('modal_category_name').textContent = categoryName;
        document.getElementById('addQuestionModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
