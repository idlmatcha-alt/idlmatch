<x-guest-layout title="Upload Photo">
    @php
        $labelMap = [
            'photo' => 'Take Profile Photo',
            'gallery' => 'Add Gallery Photo',
            'selfie' => 'Take Selfie',
            'cnic_front' => 'Upload CNIC Front',
            'cnic_back' => 'Upload CNIC Back',
        ];
        $actionLabel = $labelMap[$type] ?? 'Upload Photo';
    @endphp
    <div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900">
                Upload {{ ucfirst(str_replace('_', ' ', $type)) }}
            </h2>
            <p class="mt-2 text-center text-sm text-slate-600">
                For {{ $user->name }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                @if (session('status'))
                    <div class="mb-4 rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">
                                    {{ session('status') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                @endif

                <form id="mobile-upload-form" method="POST" action="{{ route('profile.mobile-upload.store', ['user' => $user->id, 'token' => $token]) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    
                    <div id="upload-ui" class="text-center space-y-4">
                        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-brand-100 mb-4">
                            <svg class="h-12 w-12 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>

                        <!-- Hidden File Input -->
                        <input id="photo" name="photo" type="file" accept="image/*" capture="user" required class="hidden" onchange="handleUpload(this)">
                        
                        <!-- Big Action Button -->
                        <button type="button" onclick="document.getElementById('photo').click()" class="w-full flex items-center justify-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-lg font-bold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500 focus:ring-opacity-50 transition-all transform active:scale-95">
                            {{ $actionLabel }}
                        </button>
                        
                        <p class="text-sm text-slate-500">
                            Tap the button above to open your camera.
                        </p>
                    </div>

                    <!-- Progress Bar UI (Hidden initially) -->
                    <div id="progress-ui" class="hidden text-center space-y-4">
                        <p class="text-lg font-semibold text-slate-700">Uploading...</p>
                        <div class="w-full bg-slate-200 rounded-full h-4 overflow-hidden">
                            <div id="progress-bar" class="bg-brand-600 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="progress-text" class="text-sm text-slate-500">0%</p>
                    </div>

                    <!-- Success UI (Hidden initially) -->
                    <div id="success-ui" class="hidden text-center space-y-4">
                         <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-4">
                            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Upload Complete!</h3>
                        <p class="text-slate-600">Your {{ str_replace('_', ' ', $type) }} has been uploaded securely.</p>
                        <p class="text-sm text-slate-500">Check your computer screen to continue.</p>
                    </div>
                </form>

                <script>
                    function handleUpload(input) {
                        if (!input.files || !input.files[0]) return;
                        
                        // Switch UI
                        document.getElementById('upload-ui').classList.add('hidden');
                        document.getElementById('progress-ui').classList.remove('hidden');
                        
                        const formData = new FormData(document.getElementById('mobile-upload-form'));
                        
                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', document.getElementById('mobile-upload-form').action, true);
                        
                        // Progress
                        xhr.upload.onprogress = function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                document.getElementById('progress-bar').style.width = percent + '%';
                                document.getElementById('progress-text').innerText = percent + '%';
                            }
                        };
                        
                        // Complete
                        xhr.onload = function() {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                document.getElementById('progress-ui').classList.add('hidden');
                                document.getElementById('success-ui').classList.remove('hidden');
                            } else {
                                alert('Upload failed. Please try again.');
                                location.reload();
                            }
                        };
                        
                        xhr.onerror = function() {
                            alert('Upload failed (Network Error). Please try again.');
                            location.reload();
                        };
                        
                        xhr.send(formData);
                    }
                </script>
            </div>
        </div>
    </div>
</x-guest-layout>
