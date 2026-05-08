@extends('layouts.app')

@section('title', 'Raise IT Ticket')

@section('content')
<div class="max-w-2xl mx-auto animate-fade-in">
    <!-- Compact Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Raise IT Ticket</h1>
                <p class="text-xs text-slate-500">Quickly report your technical issues.</p>
            </div>
        </div>
        <a href="{{ route('it-tickets.index') }}" class="text-xs font-bold text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-wider">Cancel</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('it-tickets.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Domain Selection -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Issue Category</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex items-center gap-3 p-4 border-2 rounded-2xl cursor-pointer transition-all duration-300 hover:border-indigo-200 bg-white shadow-sm
                                 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 group">
                        <input type="radio" name="category" value="Hardware" required class="sr-only">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 
                                    group-has-[:checked]:bg-indigo-600 group-has-[:checked]:text-white transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21h6l-.75-4M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-grow">
                            <span class="block text-sm font-black text-slate-700 group-has-[:checked]:text-indigo-700">Hardware</span>
                            <span class="text-[10px] text-slate-400 group-has-[:checked]:text-indigo-400 font-bold uppercase">Device Issues</span>
                        </div>
                        <div class="hidden group-has-[:checked]:block text-indigo-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-4 border-2 rounded-2xl cursor-pointer transition-all duration-300 hover:border-indigo-200 bg-white shadow-sm
                                 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50 group">
                        <input type="radio" name="category" value="Software" class="sr-only">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 
                                    group-has-[:checked]:bg-indigo-600 group-has-[:checked]:text-white transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <div class="flex-grow">
                            <span class="block text-sm font-black text-slate-700 group-has-[:checked]:text-indigo-700">Software</span>
                            <span class="text-[10px] text-slate-400 group-has-[:checked]:text-indigo-400 font-bold uppercase">App & OS Issues</span>
                        </div>
                        <div class="hidden group-has-[:checked]:block text-indigo-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                    </label>
                </div>
                @error('category') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <!-- Subject -->
            <div class="space-y-2">
                <label for="subject" class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Subject</label>
                <input type="text" name="subject" id="subject" required value="{{ old('subject') }}"
                       placeholder="E.g., Mouse not working"
                       class="w-full px-4 py-2.5 bg-slate-50 border rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition-all text-sm font-medium">
                @error('subject') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label for="issue_description" class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Explain Issue</label>
                <textarea name="issue_description" id="issue_description" rows="4" required
                          placeholder="Tell us exactly what's wrong..."
                          class="w-full px-4 py-3 bg-slate-50 border rounded-xl focus:bg-white focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 outline-none transition-all text-sm font-medium resize-none leading-relaxed">{{ old('issue_description') }}</textarea>
                @error('issue_description') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <!-- Compact Photo Upload -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider ml-1">Evidence (Max 5)</label>
                <div class="relative">
                    <input type="file" name="photos[]" id="photos" accept="image/*" multiple class="sr-only" onchange="previewImages(this)">
                    <label for="photos" class="flex items-center justify-between px-4 py-3 bg-slate-50 border border-dashed rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span class="text-xs font-bold text-slate-500" id="upload-text">Select photos...</span>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400">MAX 5MB</span>
                    </label>
                    <div id="previews-container" class="hidden grid grid-cols-5 gap-2 mt-3">
                        <!-- Previews -->
                    </div>
                </div>
                @error('photos') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-indigo-600 transition-all transform active:scale-95 shadow-lg shadow-slate-100 flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                Submit Ticket
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>
    </div>
</div>

<script>
function previewImages(input) {
    const container = document.getElementById('previews-container');
    const text = document.getElementById('upload-text');
    
    container.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        if (input.files.length > 5) {
            alert("Sirf 5 photos hi upload kar sakte hain.");
            input.value = "";
            container.classList.add('hidden');
            text.innerText = "Select photos...";
            return;
        }

        container.classList.remove('hidden');
        text.innerText = input.files.length + " photos selected";

        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'aspect-square rounded-lg overflow-hidden border border-slate-100';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                container.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    } else {
        container.classList.add('hidden');
        text.innerText = "Select photos...";
    }
}
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.4s ease-out; }
</style>
@endsection
