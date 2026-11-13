<x-guest-layout title="Verify Your Identity">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-brand-500">Step 1 of 2</span>
            <span id="step-indicator" class="text-xs font-bold text-slate-400">Step 1 of 3</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
            <div id="progress-bar" class="h-full bg-brand-500 transition-all duration-500" style="width: 33%"></div>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.verify.store') }}" enctype="multipart/form-data" id="verifyForm">
        @csrf
        
        <!-- Hidden file inputs -->
        <input type="file" name="selfie" id="selfie-input" class="hidden" accept="image/*">
        <input type="file" name="cnic_front" id="cnic_front_file" accept="image/*" class="hidden" onchange="handleCnicUpload('front', this)">
        <input type="file" name="cnic_back" id="cnic_back_file" accept="image/*" class="hidden" onchange="handleCnicUpload('back', this)">
        <canvas id="capture-canvas" class="hidden"></canvas>
        <canvas id="cnic-camera-canvas" class="hidden"></canvas>

        <!-- STEP 1: Selfie -->
        <div id="step-selfie" class="space-y-4">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Take a Selfie</h2>
                <p class="text-sm text-slate-500">Look at the camera and capture your photo</p>
            </div>
            
            @php
                $hasSelfie = isset($profile) && $profile && $profile->selfie;
                $selfieUrl = $hasSelfie ? \Illuminate\Support\Facades\Storage::url($profile->selfie) : '';
            @endphp
            <script>
                // Initialize currentSelfieUrl if it exists
                window.currentSelfieUrl = null;
                @if($hasSelfie)
                    window.currentSelfieUrl = "{{ $selfieUrl }}";
                @endif
            </script>
            
            <div id="camera-container" class="{{ $hasSelfie ? 'hidden' : '' }} border-2 border-dashed border-slate-300 rounded-xl overflow-hidden bg-slate-900 relative">
                <video id="camera-video" autoplay playsinline class="w-full aspect-[4/3] object-cover" style="transform: scaleX(-1);"></video>
                <div id="camera-error" class="hidden absolute inset-0 bg-slate-100 flex items-center justify-center p-6 text-center">
                    <div class="text-slate-600">
                        <svg class="h-12 w-12 text-slate-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <h3 class="font-semibold text-lg mb-1">Camera Access Blocked</h3>
                        <p class="text-sm">Please allow camera access.</p>
                        <p class="text-xs mt-2 text-slate-500">Note: Browsers only allow camera access on a secure
                            (https) connection. Allow it in your browser's site settings, or use the QR code below to
                            upload from your phone instead.</p>
                        <button onclick="startCamera()" class="mt-4 px-4 py-2 bg-brand-500 text-white rounded-lg text-sm">Retry</button>
                    </div>
                </div>
            </div>
            
            <div id="selfie-preview" class="{{ $hasSelfie ? '' : 'hidden' }} border-2 border-green-500 rounded-xl overflow-hidden">
                <img id="captured-preview" src="{{ $selfieUrl }}" class="w-full aspect-[4/3] object-cover" style="transform: scaleX(-1);">
            </div>
            
            <div class="flex gap-3">
                <button type="button" id="capture-btn" onclick="capturePhoto()" class="{{ $hasSelfie ? 'hidden' : '' }} flex-1 py-3 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Capture Selfie
                </button>
                <div id="post-capture-actions" class="{{ $hasSelfie ? 'flex' : 'hidden' }} flex-1 gap-3">
                     <button type="button" id="retake-btn" onclick="retakeSelfie()" class="flex-1 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl transition-colors">
                        Retake
                    </button>
                    <button type="button" id="next-to-cnic-front" onclick="goToStep('cnic-front')" class="flex-1 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-colors">
                        Next →
                    </button>
                </div>
            </div>
            
            <div id="mobile-upload-btn-container" class="text-center mt-2 {{ $hasSelfie ? 'hidden' : '' }}">
                <button type="button" onclick="toggleSelfieQRCode()" class="text-brand-600 font-medium hover:underline text-sm flex items-center justify-center gap-1 mx-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Or upload from mobile
                </button>
            </div>
            @error('selfie')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- STEP 2: CNIC Front -->
        <div id="step-cnic-front" class="hidden space-y-4">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900">CNIC Front Side</h2>
                <p class="text-sm text-slate-500">Upload or capture the front of your CNIC</p>
            </div>
            
            <div id="cnic-front-preview" class="hidden border-2 border-green-500 rounded-xl overflow-hidden">
                <img id="cnic-front-img" class="w-full aspect-video object-cover">
            </div>
            
            <div id="cnic-front-buttons" class="flex gap-3">
                <button type="button" onclick="document.getElementById('cnic_front_file').click()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 border-2 border-dashed border-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Upload
                </button>
                <button type="button" onclick="openCnicCameraModal('front')" class="flex-1 py-3 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Camera
                </button>
            </div>
            
            <div id="cnic-front-actions" class="hidden flex gap-3">
                <button type="button" onclick="clearCnicFront()" class="flex-1 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl transition-colors">
                    Retake
                </button>
                <button type="button" onclick="goToStep('cnic-back')" class="flex-1 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-colors">
                    Next →
                </button>
            </div>
            
            <button type="button" onclick="skipToSubmit()" class="w-full py-2 text-slate-500 hover:text-slate-700 text-sm font-medium">
                Skip CNIC (Optional)
            </button>
            @error('cnic_front')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- STEP 3: CNIC Back -->
        <div id="step-cnic-back" class="hidden space-y-4">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900">CNIC Back Side</h2>
                <p class="text-sm text-slate-500">Upload or capture the back of your CNIC</p>
            </div>
            
            <div id="cnic-back-preview" class="hidden border-2 border-green-500 rounded-xl overflow-hidden">
                <img id="cnic-back-img" class="w-full aspect-video object-cover">
            </div>
            
            <div id="cnic-back-buttons" class="flex gap-3">
                <button type="button" onclick="document.getElementById('cnic_back_file').click()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 border-2 border-dashed border-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Upload
                </button>
                <button type="button" onclick="openCnicCameraModal('back')" class="flex-1 py-3 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Camera
                </button>
            </div>
            
            <div id="cnic-back-actions" class="hidden">
                <button type="button" onclick="clearCnicBack()" class="w-full py-3 mb-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl transition-colors">
                    Retake
                </button>
            </div>
            @error('cnic_back')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <!-- Submit Section -->
        <div id="step-submit" class="hidden space-y-4">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Ready to Submit!</h2>
                <p class="text-sm text-slate-500">Review and submit your verification</p>
            </div>
            
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-600">
                <p class="font-medium text-slate-800 mb-1">🔒 Privacy Guaranteed</p>
                <p>Your documents are only used for verification and are never shared publicly.</p>
            </div>

            <button type="submit" class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-colors">
                Submit for Verification
            </button>
        </div>
    </form>
    
    <!-- QR Code Modal (Selfie) -->
    <div id="selfie-qr-modal" class="fixed inset-0 bg-slate-900/50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl transform transition-all">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-brand-100 mb-4">
                    <svg class="h-6 w-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900">Scan to Upload Selfie</h3>
                <p class="text-sm text-slate-500 mt-2">Point your phone's camera at the QR code below.</p>
                
                <div class="mt-6 flex justify-center bg-white p-4 rounded-xl border-2 border-brand-100">
                     <div id="selfie-qrcode"></div>
                </div>

                <p class="text-xs text-slate-400 mt-4">This page will update automatically once you upload.</p>
                
                <button type="button" onclick="toggleSelfieQRCode()" class="mt-6 w-full inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- CNIC Camera Modal -->
    <div id="cnic-camera-modal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden">
            <div class="p-4 bg-slate-100 flex items-center justify-between">
                <span id="cnic-camera-title" class="font-semibold text-slate-800">Capture CNIC</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="switchCamera()" class="p-2 text-brand-600 hover:bg-brand-50 rounded-full transition-colors" title="Switch Camera">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                    <button type="button" onclick="closeCnicCameraModal()" class="text-slate-500 hover:text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div class="bg-slate-900">
                <video id="cnic-camera-video" autoplay playsinline class="w-full aspect-video object-cover"></video>
            </div>
            <div class="p-4">
                <button type="button" onclick="captureCnicPhoto()" class="w-full py-3 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Capture Photo
                </button>
            </div>
        </div>
    </div>

    <script src="{{ asset('static/qrcode.min.js') }}"></script>
    <script>
    // QR Code for Selfie
    const mobileUploadUrl = "{{ route('profile.mobile-upload', ['user' => auth()->id(), 'token' => $token, 'type' => 'selfie']) }}";
    const checkStatusUrl = "{{ route('profile.photo-check', ['type' => 'selfie'], false) }}";
    let selfiePollingInterval = null;

    // Wait for DOM to be ready before generating QR code
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById("selfie-qrcode")) {
            new QRCode(document.getElementById("selfie-qrcode"), {
                text: mobileUploadUrl,
                width: 180,
                height: 180,
                colorDark : "#E91E63",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }
        
        // Check for selfie query param to resume state
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('step') === 'cnic-front') {
            goToStep('cnic-front');
        }
    });

    function toggleSelfieQRCode() {
        const modal = document.getElementById('selfie-qr-modal');
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
        
        if (!modal.classList.contains('hidden')) {
            startSelfiePolling();
        } else {
            stopSelfiePolling();
        }
    }

    function startSelfiePolling() {
        if (selfiePollingInterval) return;
        selfiePollingInterval = setInterval(checkSelfieStatus, 3000);
    }

    function stopSelfiePolling() {
        if (selfiePollingInterval) {
            clearInterval(selfiePollingInterval);
            selfiePollingInterval = null;
        }
    }

    // Removed local let currentSelfieUrl = null; in favor of window.currentSelfieUrl

    async function checkSelfieStatus() {
        try {
            // Add timestamp to prevent caching
            const response = await fetch(checkStatusUrl + (checkStatusUrl.includes('?') ? '&' : '?') + '_t=' + new Date().getTime());
            const data = await response.json();
            
            if (data.uploaded) {
                // If we are in "retake" mode (currentSelfieUrl is set), only accept if the URL has changed
                if (window.currentSelfieUrl && data.photo_url === window.currentSelfieUrl) {
                    return; // Still the old photo, keep polling
                }

                stopSelfiePolling();
                window.currentSelfieUrl = data.photo_url; // Update current URL
                document.getElementById('captured-preview').src = data.photo_url;
                
                // Update UI state
                document.getElementById('camera-container').classList.add('hidden');
                document.getElementById('selfie-preview').classList.remove('hidden'); 
                document.getElementById('capture-btn').classList.add('hidden'); 
                document.getElementById('mobile-upload-btn-container').classList.add('hidden'); 
                
                // Show actions content
                document.getElementById('post-capture-actions').classList.remove('hidden');
                document.getElementById('post-capture-actions').classList.add('flex');
                
                // Hide modal
                const modal = document.getElementById('selfie-qr-modal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            }
        } catch (error) {
            console.error('Error checking selfie status:', error);
        }
    }

    // Modal outside click
    document.getElementById('selfie-qr-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            toggleSelfieQRCode();
        }
    });

    let stream = null;
    let cnicCameraStream = null;
    let currentCnicSide = 'front';
    let currentFacingMode = 'environment';

    // Start camera on page load
    async function startCamera() {
        // Don't start if selfie already exists or hidden
        if (document.getElementById('camera-container').classList.contains('hidden')) return;

        // Reset UI
        document.getElementById('camera-error').classList.add('hidden');
        document.getElementById('camera-video').classList.remove('hidden');

        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false 
            });
            document.getElementById('camera-video').srcObject = stream;
        } catch (err) {
            console.error('Camera error:', err);
            // Show error message
            document.getElementById('camera-video').classList.add('hidden');
            document.getElementById('camera-error').classList.remove('hidden');
        }
    }

    function capturePhoto() {
        const video = document.getElementById('camera-video');
        const canvas = document.getElementById('capture-canvas');
        const preview = document.getElementById('captured-preview');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0);
        
        canvas.toBlob(blob => {
            preview.src = URL.createObjectURL(blob);
            
            const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('selfie-input').files = dataTransfer.files;
            
            // Show preview, hide camera
            document.getElementById('camera-container').classList.add('hidden');
            document.getElementById('selfie-preview').classList.remove('hidden');
            document.getElementById('capture-btn').classList.add('hidden');
            document.getElementById('mobile-upload-btn-container').classList.add('hidden');
            
            // Show actions
            document.getElementById('post-capture-actions').classList.remove('hidden');
            document.getElementById('post-capture-actions').classList.add('flex');
            
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        }, 'image/jpeg', 0.9);
    }

    async function retakeSelfie() {
        if (!confirm('Are you sure you want to retake your selfie? This will delete the current one.')) {
            return;
        }

        try {
            const response = await fetch("{{ route('profile.photo-delete') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ type: 'selfie' })
            });

            const data = await response.json();
            
            if (data.success) {
                // Clear local state
                window.currentSelfieUrl = null;
                
                document.getElementById('camera-container').classList.remove('hidden');
                document.getElementById('selfie-preview').classList.add('hidden');
                document.getElementById('capture-btn').classList.remove('hidden');
                document.getElementById('mobile-upload-btn-container').classList.remove('hidden');
                
                document.getElementById('post-capture-actions').classList.add('hidden');
                document.getElementById('post-capture-actions').classList.remove('flex');
        
                startCamera();
                
                // Reset file input
                document.getElementById('selfie-input').value = '';
            } else {
                alert('Failed to reset photo. Please try again.');
            }
        } catch (error) {
            console.error('Error deleting photo:', error);
            alert('An error occurred. Please try again.');
        }
    }

    function goToStep(step) {
        // Hide all steps
        document.getElementById('step-selfie').classList.add('hidden');
        document.getElementById('step-cnic-front').classList.add('hidden');
        document.getElementById('step-cnic-back').classList.add('hidden');
        document.getElementById('step-submit').classList.add('hidden');
        
        // Show target step and update progress
        if (step === 'cnic-front') {
            document.getElementById('step-cnic-front').classList.remove('hidden');
            document.getElementById('step-indicator').textContent = 'Step 2 of 3';
            document.getElementById('progress-bar').style.width = '66%';
        } else if (step === 'cnic-back') {
            document.getElementById('step-cnic-back').classList.remove('hidden');
            document.getElementById('step-indicator').textContent = 'Step 3 of 3';
            document.getElementById('progress-bar').style.width = '100%';
        } else if (step === 'submit') {
            document.getElementById('step-submit').classList.remove('hidden');
            document.getElementById('step-indicator').textContent = 'Complete!';
            document.getElementById('progress-bar').style.width = '100%';
        }
    }

    function skipToSubmit() {
        goToStep('submit');
    }

    // CNIC Upload handling
    function handleCnicUpload(side, input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(`cnic-${side}-img`).src = e.target.result;
                document.getElementById(`cnic-${side}-preview`).classList.remove('hidden');
                document.getElementById(`cnic-${side}-buttons`).classList.add('hidden');
                document.getElementById(`cnic-${side}-actions`).classList.remove('hidden');
                
                if (side === 'back') {
                    setTimeout(() => goToStep('submit'), 500);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearCnicFront() {
        document.getElementById('cnic_front_file').value = '';
        document.getElementById('cnic-front-preview').classList.add('hidden');
        document.getElementById('cnic-front-buttons').classList.remove('hidden');
        document.getElementById('cnic-front-actions').classList.add('hidden');
    }

    function clearCnicBack() {
        document.getElementById('cnic_back_file').value = '';
        document.getElementById('cnic-back-preview').classList.add('hidden');
        document.getElementById('cnic-back-buttons').classList.remove('hidden');
        document.getElementById('cnic-back-actions').classList.add('hidden');
    }

    // CNIC Camera Modal
    async function openCnicCameraModal(side) {
        currentCnicSide = side;
        document.getElementById('cnic-camera-title').textContent = `Capture CNIC ${side === 'front' ? 'Front' : 'Back'}`;
        document.getElementById('cnic-camera-modal').classList.remove('hidden');
        await startCnicCameraStream();
    }

    async function startCnicCameraStream() {
        if (cnicCameraStream) {
            cnicCameraStream.getTracks().forEach(track => track.stop());
        }
        
        try {
            cnicCameraStream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: currentFacingMode, width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false 
            });
            document.getElementById('cnic-camera-video').srcObject = cnicCameraStream;
        } catch (err) {
            console.error('Camera error:', err);
            // alert('Unable to access camera. Please allow camera permissions.');
            // Don't close modal on error, maybe they can try another camera
        }
    }

    async function switchCamera() {
        currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
        await startCnicCameraStream();
    }

    function closeCnicCameraModal() {
        document.getElementById('cnic-camera-modal').classList.add('hidden');
        if (cnicCameraStream) {
            cnicCameraStream.getTracks().forEach(track => track.stop());
            cnicCameraStream = null;
        }
    }

    function captureCnicPhoto() {
        const video = document.getElementById('cnic-camera-video');
        const canvas = document.getElementById('cnic-camera-canvas');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        
        canvas.toBlob(blob => {
            const file = new File([blob], `cnic_${currentCnicSide}.jpg`, { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById(`cnic_${currentCnicSide}_file`).files = dataTransfer.files;
            
            document.getElementById(`cnic-${currentCnicSide}-img`).src = URL.createObjectURL(blob);
            document.getElementById(`cnic-${currentCnicSide}-preview`).classList.remove('hidden');
            document.getElementById(`cnic-${currentCnicSide}-buttons`).classList.add('hidden');
            document.getElementById(`cnic-${currentCnicSide}-actions`).classList.remove('hidden');
            
            closeCnicCameraModal();
            
            if (currentCnicSide === 'back') {
                setTimeout(() => goToStep('submit'), 500);
            }
        }, 'image/jpeg', 0.9);
    }

    // Start camera when page loads
    document.addEventListener('DOMContentLoaded', startCamera);
    </script>
</x-guest-layout>
