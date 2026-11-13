<x-app-layout>
    <style>
        .payment-tab { transition: all 0.3s ease; }
        .payment-tab.active { background: linear-gradient(135deg, #E91E63, #C2185B); color: white; }
        .payment-tab:not(.active) { background: #f1f5f9; color: #475569; }
        .payment-content { display: none; }
        .payment-content.active { display: block; }
        .copy-btn:hover { background: #e2e8f0; }
        .copy-btn.copied svg { color: #22c55e; }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-pink-50 to-slate-50 py-8">
        <div class="max-w-4xl mx-auto px-4">
            
            {{-- Top Section: Plan + Amount --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="px-8 py-6 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-500 text-sm font-medium mb-1">Selected Package</p>
                            <h1 class="text-3xl font-bold text-slate-900">{{ $plan->name }}</h1>
                            <p class="text-slate-500 mt-1">{{ $plan->duration_days }} Days Premium Access</p>
                        </div>
                        <div class="text-right">
                            <p class="text-slate-500 text-sm">Total Amount</p>
                            <p class="text-5xl font-bold text-slate-900">{{ number_format($plan->price) }}</p>
                            <p class="text-slate-500">PKR</p>
                        </div>
                    </div>
                </div>
                
                {{-- Features Row --}}
                <div class="grid grid-cols-3 divide-x divide-slate-100 bg-slate-50">
                    <div class="p-4 text-center">
                        <div class="w-10 h-10 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <p class="font-bold text-slate-900">{{ $plan->limits_json['profile_views'] ?? 'Unlimited' }}</p>
                        <p class="text-xs text-slate-500">Profile Views</p>
                    </div>
                    <div class="p-4 text-center">
                        <div class="w-10 h-10 {{ ($plan->limits_json['contact_access'] ?? false) ? 'bg-green-100' : 'bg-slate-100' }} rounded-full flex items-center justify-center mx-auto mb-2">
                            @if($plan->limits_json['contact_access'] ?? false)
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @else
                                <svg class="w-5 h-5 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            @endif
                        </div>
                        <p class="font-bold text-slate-900">{{ ($plan->limits_json['contact_access'] ?? false) ? 'Yes' : 'No' }}</p>
                        <p class="text-xs text-slate-500">Contact Access</p>
                    </div>
                    <div class="p-4 text-center">
                        <div class="w-10 h-10 {{ ($plan->limits_json['verified_badge'] ?? false) ? 'bg-green-100' : 'bg-slate-100' }} rounded-full flex items-center justify-center mx-auto mb-2">
                            @if($plan->limits_json['verified_badge'] ?? false)
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            @else
                                <svg class="w-5 h-5 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            @endif
                        </div>
                        <p class="font-bold text-slate-900">{{ ($plan->limits_json['verified_badge'] ?? false) ? 'Yes' : 'No' }}</p>
                        <p class="text-xs text-slate-500">Verified Badge</p>
                    </div>
                </div>
            </div>

            {{-- Payment Methods --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-bold text-slate-900">Choose Payment Method</h2>
                    <p class="text-slate-500 text-sm mt-1">Select your preferred payment option</p>
                </div>

                {{-- Payment Tabs --}}
                <div class="grid grid-cols-3 gap-0 border-b border-slate-100">
                    <button onclick="switchTab('bank')" class="payment-tab active flex flex-col items-center gap-2 py-4 font-semibold text-center border-r border-slate-100" id="tab-bank">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">MB</span>
                        </div>
                        <span class="text-xs">Meezan Bank</span>
                    </button>
                    <button onclick="switchTab('jazzcash')" class="payment-tab flex flex-col items-center gap-2 py-4 font-semibold text-center border-r border-slate-100" id="tab-jazzcash">
                        <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">JC</span>
                        </div>
                        <span class="text-xs">JazzCash</span>
                    </button>
                    <button onclick="switchTab('easypaisa')" class="payment-tab flex flex-col items-center gap-2 py-4 font-semibold text-center" id="tab-easypaisa">
                        <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm">EP</span>
                        </div>
                        <span class="text-xs">EasyPaisa</span>
                    </button>
                </div>

                {{-- Bank Transfer Content --}}
                <div id="content-bank" class="payment-content active p-6">
                    <div class="flex items-center gap-4 mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
                        <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-600/30">
                            <span class="text-white font-bold text-xl">MB</span>
                        </div>
                        <div>
                            <p class="font-bold text-lg text-green-800">Meezan Bank Limited</p>
                            <p class="text-sm text-green-600">Pakistan's Leading Islamic Bank</p>
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-xs text-slate-500 uppercase">Account Title</p>
                                <p class="font-bold text-slate-900">MEHBOOB AHMAD</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-xs text-slate-500 uppercase">Account Number</p>
                                <p class="font-mono font-bold text-slate-900 text-lg">22010102897989</p>
                            </div>
                            <button onclick="copyText('22010102897989', this)" class="copy-btn p-3 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                            <div>
                                <p class="text-xs text-slate-500 uppercase">IBAN</p>
                                <p class="font-mono font-bold text-slate-900">PK40MEZN0022010102897989</p>
                            </div>
                            <button onclick="copyText('PK40MEZN0022010102897989', this)" class="copy-btn p-3 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- JazzCash Content --}}
                <div id="content-jazzcash" class="payment-content p-6">
                    <div class="flex items-center gap-4 mb-6 p-4 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl border border-red-200">
                        <div class="w-16 h-16 bg-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-600/30">
                            <span class="text-white font-bold text-xl">JC</span>
                        </div>
                        <div>
                            <p class="font-bold text-lg text-red-800">JazzCash</p>
                            <p class="text-sm text-red-600">Pakistan's #1 Mobile Wallet</p>
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl border border-red-100">
                            <div>
                                <p class="text-xs text-red-600 uppercase font-semibold">Account Title</p>
                                <p class="font-bold text-slate-900 text-lg">MEHBOOB AHMAD</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl border border-red-100">
                            <div>
                                <p class="text-xs text-red-600 uppercase font-semibold">Mobile Number</p>
                                <p class="font-mono font-bold text-slate-900 text-2xl">0300 2294445</p>
                            </div>
                            <button onclick="copyText('03002294445', this)" class="copy-btn p-3 rounded-lg transition-colors bg-white hover:bg-red-100 border border-red-200">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- EasyPaisa Content --}}
                <div id="content-easypaisa" class="payment-content p-6">
                    <div class="flex items-center gap-4 mb-6 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl border border-emerald-200">
                        <div class="w-16 h-16 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                            <span class="text-white font-bold text-xl">EP</span>
                        </div>
                        <div>
                            <p class="font-bold text-lg text-emerald-800">EasyPaisa</p>
                            <p class="text-sm text-emerald-600">Powered by Telenor</p>
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div>
                                <p class="text-xs text-emerald-600 uppercase font-semibold">Account Name</p>
                                <p class="font-bold text-slate-900 text-lg">Mahboob Ahmed</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                            <div>
                                <p class="text-xs text-emerald-600 uppercase font-semibold">Mobile Number</p>
                                <p class="font-mono font-bold text-slate-900 text-2xl">0345 2450024</p>
                            </div>
                            <button onclick="copyText('03452450024', this)" class="copy-btn p-3 rounded-lg transition-colors bg-white hover:bg-emerald-100 border border-emerald-200">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upload Section --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-xl font-bold text-slate-900">Upload Payment Proof</h2>
                    <p class="text-slate-500 text-sm mt-1">Take a screenshot of your transaction and upload it</p>
                </div>
                <form action="{{ route('plans.subscribe.store', $plan) }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <input type="hidden" name="payment_method" value="bank_transfer">
                    
                    <div class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center hover:border-brand-500 transition-colors cursor-pointer bg-slate-50"
                         onclick="document.getElementById('payment_proof').click()">
                        <input type="file" id="payment_proof" name="payment_proof" accept="image/*,application/pdf" required class="hidden"
                               onchange="showFileName(this)">
                        <div id="upload-icon">
                            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-slate-700 font-semibold">Click to upload screenshot</p>
                            <p class="text-sm text-slate-500 mt-1">JPG, PNG or PDF (Max 4MB)</p>
                        </div>
                        <div id="file-selected" class="hidden">
                            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-green-600 font-semibold" id="file-name"></p>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 py-4 rounded-xl font-bold text-lg text-white transition-all hover:opacity-90" style="background: linear-gradient(135deg, #E91E63, #C2185B);">
                        Submit Payment Proof
                    </button>
                </form>
            </div>

            {{-- Steps Timeline --}}
            <div class="bg-slate-900 rounded-2xl p-6 text-white">
                <h3 class="font-bold text-lg mb-6">What happens next?</h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center flex-shrink-0 font-bold">1</div>
                        <div>
                            <p class="font-semibold">Transfer {{ number_format($plan->price) }} PKR</p>
                            <p class="text-sm text-slate-400">Send payment to any account above</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center flex-shrink-0 font-bold">2</div>
                        <div>
                            <p class="font-semibold">Upload Screenshot</p>
                            <p class="text-sm text-slate-400">Take a screenshot and upload above</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center flex-shrink-0 font-bold">3</div>
                        <div>
                            <p class="font-semibold">Verification (24 hours)</p>
                            <p class="text-sm text-slate-400">We'll verify and activate your plan</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-green-400">Email Notification</p>
                            <p class="text-sm text-slate-400">You'll receive confirmation email</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Back Link --}}
            <div class="mt-8 text-center">
                <a href="{{ route('packages.index') }}" class="text-slate-600 hover:text-slate-900 font-medium">← Back to Plans</a>
            </div>
        </div>
    </div>

    <script>
    function switchTab(tab) {
        // Remove active from all tabs and contents
        document.querySelectorAll('.payment-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.payment-content').forEach(c => c.classList.remove('active'));
        
        // Add active to selected
        document.getElementById('tab-' + tab).classList.add('active');
        document.getElementById('content-' + tab).classList.add('active');
    }

    function copyText(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            btn.classList.add('copied');
            btn.innerHTML = '<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            setTimeout(() => {
                btn.classList.remove('copied');
                btn.innerHTML = '<svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>';
            }, 2000);
        });
    }

    function showFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('upload-icon').classList.add('hidden');
            document.getElementById('file-selected').classList.remove('hidden');
            document.getElementById('file-name').textContent = input.files[0].name;
        }
    }
    </script>
</x-app-layout>
