<x-guest-layout title="Upload Your Photo">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-brand-500">Step 2 of 2</span>
            <span class="text-xs font-bold text-slate-400">Almost there!</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-brand-500 transition-all duration-500" style="width: 50%"></div>
        </div>
    </div>

    <div class="text-center mb-6">
        <div class="w-20 h-20 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Upload Profile Picture</h1>
        <p class="text-slate-500 mt-1">A clear photo increases your matches by 10x!</p>
    </div>

    <form method="POST" action="{{ route('profile.photos.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Photo Upload Area -->
        <div>
            @php
                $profile = auth()->user()->profile;
                $currentPhotoUrl = $profile?->primary_photo_url;
            @endphp

            <div id="upload-area" class="relative border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center hover:border-brand-500 hover:bg-brand-50/50 transition-all cursor-pointer">
                <input type="file" name="photo" id="photo-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @if(!$currentPhotoUrl) required @endif>
                
                <div id="preview-container" class="{{ $currentPhotoUrl ? '' : 'hidden' }}">
                    <img id="photo-preview" src="{{ $currentPhotoUrl }}" class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-brand-500 shadow-lg">
                    <p class="text-sm text-brand-600 font-medium mt-3">Looking great! ✓</p>
                </div>
                
                <div id="upload-prompt" class="{{ $currentPhotoUrl ? 'hidden' : '' }}">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <p class="mt-2 text-sm text-slate-600">
                        <span class="font-semibold text-brand-600">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-slate-500 mt-1">PNG, JPG up to 10MB</p>
                </div>
            </div>
            
            @error('photo')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-amber-800">
                    <p class="font-medium">Photo Guidelines</p>
                    <ul class="mt-1 text-amber-700 text-xs space-y-1">
                        <li>• Use a recent, clear photo of yourself</li>
                        <li>• Face should be clearly visible</li>
                        <li>• No group photos or filters</li>
                    </ul>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-xl transition-colors">
            Save & Continue
        </button>
    </form>

    <script>
        const input = document.getElementById('photo-input');
        const preview = document.getElementById('photo-preview');
        const previewContainer = document.getElementById('preview-container');
        const uploadPrompt = document.getElementById('upload-prompt');

        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    uploadPrompt.classList.add('hidden');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</x-guest-layout>
