<x-auth-full-layout title="Create Account" :chrome="true">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/css/intlTelInput.css">
    <style>
        .iti {
            width: 100%;
        }

        .iti__country-list {
            z-index: 50;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            border: 1px solid #e2e8f0;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: none;
        }

        .profile-for-option input:checked + span {
            border-color: #E91E63;
            color: #E91E63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.15);
        }

        .register-step-dot {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        @media (max-width: 640px) {
            .register-shell {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }
        }

        @media (min-width: 1024px) {
            .register-shell .register-body {
                max-width: 1150px;
            }
        }
    </style>

    <div class="register-shell min-h-screen bg-slate-50 py-4 sm:py-8 px-3 sm:px-6 lg:px-8" x-data="{ 
        step: {{ $socialData || request('social') ? 2 : 1 }},
        email: '{{ old('email', $socialData['email'] ?? '') }}',
        password: '',
        isSocial: {{ $socialData || request('social') ? 'true' : 'false' }},
        declarationAgreement: '{{ old('declaration_agreement', '') }}'
    }">
        <div class="register-body max-w-5xl mx-auto bg-white rounded-xl sm:rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
            <div class="h-1 bg-gradient-to-r from-brand-500 to-pink-500"></div>

            <div class="p-4 sm:p-6 md:p-8">
                <div class="text-center mb-6">
                    <a href="/" class="inline-flex items-center gap-2 mb-3">
                        <div class="h-10 w-10 bg-brand-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </div>
                        <span class="font-display font-bold text-xl text-slate-800">IDEAL MARRIAGE CENTER</span>
                    </a>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900"
                        x-text="step === 1 ? 'Get Started' : 'Complete Your Profile'"></h1>

                    <div class="mt-4 flex items-center justify-center gap-5 text-xs sm:text-sm font-semibold text-slate-500">
                        <div class="flex items-center gap-2">
                            <span class="register-step-dot"
                                :class="step === 1 ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-500 border border-slate-300'">1</span>
                            <span :class="step === 1 ? 'text-brand-700' : 'text-slate-500'">Account</span>
                        </div>
                        <div class="h-px w-8 sm:w-12 bg-slate-200"></div>
                        <div class="flex items-center gap-2">
                            <span class="register-step-dot"
                                :class="step === 2 ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-500 border border-slate-300'">2</span>
                            <span :class="step === 2 ? 'text-brand-700' : 'text-slate-500'">Profile Details</span>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Initial Credentials -->
                <div x-show="step === 1" class="space-y-6">
                    <p class="text-center text-slate-500 text-sm">Enter your email and password to start, or use your
                        social account.</p>

                    <div class="grid grid-cols-1 gap-4">
                        <a href="{{ route('auth.google') }}"
                            class="flex items-center justify-center gap-3 w-full py-3 px-4 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                    fill="#4285F4" />
                                <path
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                    fill="#34A853" />
                                <path
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                                    fill="#FBBC05" />
                                <path
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                    fill="#EA4335" />
                            </svg>
                            <span class="text-slate-700 font-medium">Continue with Google</span>
                        </a>

                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200"></div>
                            </div>
                            <div class="relative flex justify-center text-xs uppercase"><span
                                    class="bg-white px-2 text-slate-500">Or use email</span></div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                            <input type="email" x-model="email"
                                class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500"
                                placeholder="your@email.com">
                        </div>

                        <div x-data="{
                            hasMinLength: false,
                            hasUppercase: false,
                            hasLowercase: false,
                            hasNumber: false,
                            hasSymbol: false,
                            checkPassword() {
                                const pwd = this.$refs.passwordInput.value;
                                this.hasMinLength = pwd.length >= 6;
                                this.hasUppercase = /[A-Z]/.test(pwd);
                                this.hasLowercase = /[a-z]/.test(pwd);
                                this.hasNumber = /[0-9]/.test(pwd);
                                this.hasSymbol = /[^A-Za-z0-9]/.test(pwd);
                            }
                        }">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                            <input type="password" x-model="password" x-ref="passwordInput" @input="checkPassword"
                                class="w-full rounded-lg border-slate-300 py-2 text-sm focus:border-brand-500 focus:ring-brand-500"
                                placeholder="••••••••">
                            
                            <div class="mt-2 space-y-1 text-xs">
                                <div class="flex items-center gap-2" :class="hasMinLength ? 'text-green-600' : 'text-slate-400'">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path x-show="hasMinLength" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        <circle x-show="!hasMinLength" cx="10" cy="10" r="8" stroke="currentColor" fill="none" stroke-width="2"></circle>
                                    </svg>
                                    <span>6+ characters</span>
                                </div>
                                <div class="flex items-center gap-2" :class="hasUppercase ? 'text-green-600' : 'text-slate-400'">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path x-show="hasUppercase" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        <circle x-show="!hasUppercase" cx="10" cy="10" r="8" stroke="currentColor" fill="none" stroke-width="2"></circle>
                                    </svg>
                                    <span>Uppercase letter</span>
                                </div>
                                <div class="flex items-center gap-2" :class="hasLowercase ? 'text-green-600' : 'text-slate-400'">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path x-show="hasLowercase" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        <circle x-show="!hasLowercase" cx="10" cy="10" r="8" stroke="currentColor" fill="none" stroke-width="2"></circle>
                                    </svg>
                                    <span>Lowercase letter</span>
                                </div>
                                <div class="flex items-center gap-2" :class="hasNumber ? 'text-green-600' : 'text-slate-400'">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path x-show="hasNumber" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        <circle x-show="!hasNumber" cx="10" cy="10" r="8" stroke="currentColor" fill="none" stroke-width="2"></circle>
                                    </svg>
                                    <span>Number</span>
                                </div>
                                <div class="flex items-center gap-2" :class="hasSymbol ? 'text-green-600' : 'text-slate-400'">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path x-show="hasSymbol" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        <circle x-show="!hasSymbol" cx="10" cy="10" r="8" stroke="currentColor" fill="none" stroke-width="2"></circle>
                                    </svg>
                                    <span>Symbol (!@#$%...)</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="if(email && password) step = 2"
                            class="w-full py-3 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-lg transition-colors">
                            Continue Registration
                        </button>
                    </div>
                    <p class="text-center text-sm text-slate-500 mt-3">
                        Already have an account? <a href="{{ route('login') }}"
                            class="text-brand-600 font-medium hover:underline">Sign In</a>
                    </p>
                </div>

                <!-- Step 2: Full Profile Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-5" x-show="step === 2" x-cloak>
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-lg bg-red-50 p-3 border border-red-100 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Account Security (Locked) -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                            <div class="h-8 w-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="flex-grow">
                                <h2 class="text-sm font-semibold text-slate-800">Account Details</h2>
                            </div>
                            <button type="button" @click="if(!isSocial) step = 1" x-show="!isSocial"
                                class="text-xs text-brand-600 font-medium hover:underline">Change</button>
                        </div>
                        <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email</label>
                                <input type="email" name="email" :value="email" readonly
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 text-sm text-slate-600 cursor-not-allowed">
                            </div>
                            <div x-show="!isSocial" x-data="{ show: false }">
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Password</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="password" :value="password" readonly
                                        class="w-full rounded-xl border-slate-200 bg-slate-50 py-2.5 text-sm text-slate-600 cursor-not-allowed pr-10">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                    </button>
                                </div>
                                <input type="hidden" name="password_confirmation" :value="password">
                            </div>
                            <div x-show="isSocial" class="flex items-center">
                                <p class="text-xs text-brand-600 flex items-center gap-1.5 bg-brand-50 px-3 py-1.5 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Linked with Google
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                            <div class="h-8 w-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-slate-800">Personal Information</h2>
                        </div>
                        <div class="p-4 sm:p-6 space-y-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">This Profile is for</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2" id="profileForGroup">
                                    @php
                                        $profileForOptions = ['Myself', 'My Son', 'My Daughter', 'My Brother', 'My Sister', 'My Friend', 'My Relative'];
                                        $selectedProfileFor = old('profile_for');
                                    @endphp
                                    @foreach($profileForOptions as $option)
                                        <label class="profile-for-option flex items-center cursor-pointer">
                                            <input type="radio" name="profile_for" value="{{ $option }}" class="sr-only"
                                                {{ $selectedProfileFor === $option ? 'checked' : '' }} required>
                                            <span class="w-full text-center border border-slate-200 rounded-full py-2 text-sm font-medium text-slate-600 hover:border-brand-400 hover:text-brand-600 transition">
                                                {{ $option }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" id="genderLabel">I am a</label>
                                    <select name="gender" id="genderSelect"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select...</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male (Groom)</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female (Bride)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">First Name</label>
                                    <input type="text" name="first_name"
                                        value="{{ old('first_name', $socialData['name'] ?? '') }}"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Last Name</label>
                                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date of Birth</label>
                                    <input type="date" name="dob" value="{{ old('dob') }}"
                                        max="{{ now()->subYears(18)->format('Y-m-d') }}"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Marital Status</label>
                                    <select name="marital_status"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="Single">Single</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Height</label>
                                    <select name="height_cm"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        @for ($cm = 140; $cm <= 200; $cm++)
                                            @php $feet = floor($cm / 30.48);
                                            $inches = round(($cm / 2.54) - ($feet * 12)); @endphp
                                            <option value="{{ $cm }}" {{ old('height_cm') == $cm ? 'selected' : '' }}>
                                                    {{ $feet }}'{{ $inches }}" ({{ $cm }}cm)</option>
                                    @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Weight (KG)</label>
                                    <input type="number" name="weight_kg" value="{{ old('weight_kg') }}"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="e.g. 70">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Looking to Marry</label>
                                    <select name="looking_to_marry"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500">
                                        <option value="">Select...</option>
                                        @foreach(\App\Helpers\FormOptions::lookingToMarryOptions() as $opt)
                                            <option value="{{ $opt }}" {{ old('looking_to_marry') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Disability <span class="text-slate-400 font-normal normal-case">(Optional)</span></label>
                                <input type="text" name="disability" value="{{ old('disability') }}"
                                       class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                       placeholder="None">
                            </div>
                        </div>
                    </div>

                    <!-- Religion & Identity -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                            <div class="h-8 w-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-slate-800">Religion & Identity</h2>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Religion</label>
                                    <select name="religion" id="religionSelect"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        @foreach(\App\Helpers\FormOptions::religions() as $religion)
                                            <option value="{{ $religion }}" {{ old('religion', 'Islam') == $religion ? 'selected' : '' }}>
                                                {{ $religion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Sect</label>
                                    <select name="sect" id="sectSelect"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select Sect...</option>
                                    </select>
                                    <input type="text" id="sectOtherInput" placeholder="Specify (Optional)"
                                        class="hidden w-full mt-2 rounded-xl border-slate-200 py-2.5 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Are you a Revert?</label>
                                <select name="is_revert"
                                    class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500">
                                    <option value="0" {{ old('is_revert') == '0' ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('is_revert') == '1' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                 <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Caste</label>
                                    <select name="caste" id="casteSelect"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select Caste...</option>
                                        @foreach(\App\Helpers\FormOptions::castes() as $caste)
                                            <option value="{{ $caste }}" {{ old('caste') == $caste ? 'selected' : '' }}>
                                                {{ $caste }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" id="casteOtherInput" placeholder="Specify Caste (Optional)"
                                        class="hidden w-full mt-2 rounded-xl border-slate-200 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Mother Tongue</label>
                                    <select name="mother_tongue" id="motherTongueSelect"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select...</option>
                                        @foreach(\App\Helpers\FormOptions::motherTongues() as $tongue)
                                            <option value="{{ $tongue }}" {{ old('mother_tongue') == $tongue ? 'selected' : '' }}>
                                                {{ $tongue }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" id="motherTongueOtherInput" placeholder="Specify Tongue"
                                        class="hidden w-full mt-2 rounded-xl border-slate-200 py-2.5 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Details -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                            <div class="h-8 w-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-slate-800">Family Details</h2>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Father's Name</label>
                                    <input type="text" name="father_name" value="{{ old('father_name') }}"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="Enter name">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Father's Occupation</label>
                                    <input type="text" name="father_occupation" value="{{ old('father_occupation') }}"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="Occupation">
                                </div>
                             </div>
                             <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Mother's Occupation</label>
                                 <input type="text" name="mother_occupation" value="{{ old('mother_occupation', 'Housewife') }}"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="Occupation">
                             </div>
                             <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 text-center">Brothers</label>
                                    <input type="number" name="brothers_count" value="{{ old('brothers_count', 0) }}" min="0"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm text-center focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 text-center">Married</label>
                                    <input type="number" name="married_brothers_count" value="{{ old('married_brothers_count', old('married_siblings_count', 0)) }}" min="0"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm text-center focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 text-center">Sisters</label>
                                    <input type="number" name="sisters_count" value="{{ old('sisters_count', 0) }}" min="0"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm text-center focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 text-center">Married</label>
                                    <input type="number" name="married_sisters_count" value="{{ old('married_sisters_count', 0) }}" min="0"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm text-center focus:border-brand-500 focus:ring-brand-500">
                                </div>
                             </div>
                             <input type="hidden" name="married_siblings_count" value="{{ old('married_siblings_count', 0) }}">
                        </div>
                    </div>

                    <!-- Education & Career -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                            <div class="h-8 w-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-slate-800">Education & Career</h2>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Qualification</label>
                                    <select name="education" id="educationSelect"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select Qualification...</option>
                                    @foreach(\App\Helpers\FormOptions::educationLevels() as $level)
                                        <option value="{{ $level }}" {{ old('education') == $level ? 'selected' : '' }}>
                                                {{ $level }}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Major</label>
                                    <select name="major_course" id="majorSelect"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select Major...</option>
                                    </select>
                                    <input type="text" id="majorOtherInput" placeholder="Specify Major (Optional)"
                                        class="hidden w-full mt-2 rounded-xl border-slate-200 py-2.5 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">University / College</label>
                                <input type="text" name="university" value="{{ old('university') }}"
                                    class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                    placeholder="Enter University or College Name">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Profession</label>
                                    <select name="job_title" id="jobSelect"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select Profession...</option>
                                    @foreach(\App\Helpers\FormOptions::professions() as $category => $jobs)
                                        <optgroup label="{{ $category }}">
                                        @foreach($jobs as $job)
                                            <option value=
                                            "{{ $job }}" {{ old('job_title') == $job ? 'selected' : '' }}>
                                                    {{ $job }}</option>
                                        @endforeach
                                            </optgroup>
                                    @endforeach
                                    </select>
                                    <input type="text" id="jobOtherInput" placeholder="Specify Profession (Optional)"
                                        class="hidden w-full mt-2 rounded-xl border-slate-200 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Job Post / Designation</label>
                                    <input type="text" name="job_type" value="{{ old('job_type') }}"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        placeholder="e.g. Senior Manager">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Monthly Income</label>
                                    <select name="income"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select Income...</option>
                                        @foreach(\App\Helpers\FormOptions::incomeOptions() as $opt)
                                            <option value="{{ $opt }}" {{ old('income') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Location (Reorganized) -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                            <div class="h-8 w-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h2 class="text-sm font-semibold text-slate-800">Contact & Location</h2>
                        </div>
                        <div class="p-4 sm:p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Mobile</label>
                                <input type="tel" id="mobile_no_input"
                                    class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                    required>
                                <input type="hidden" name="mobile_no" id="mobile_no_full">
                                <div id="phone_error" class="error-message">Please enter a valid mobile number</div>
                            </div>

                            <div class="h-px bg-slate-100 my-2"></div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Country</label>
                                    <select id="countrySelect" name="country"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Loading countries...</option>
                                    </select>
                                    <input type="text" id="countryOtherInput" placeholder="Specify Country"
                                        class="hidden w-full mt-2 rounded-xl border-slate-200 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">State/Province</label>
                                    <select id="stateSelect" name="state"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select State...</option>
                                    </select>
                                    <input type="text" id="stateOtherInput" placeholder="Specify State"
                                        class="hidden w-full mt-2 rounded-xl border-slate-200 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">City</label>
                                    <select id="citySelect" name="city"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="">Select City...</option>
                                    </select>
                                    <input type="text" id="cityOtherInput" placeholder="Specify City"
                                        class="hidden w-full mt-2 rounded-xl border-slate-200 py-2.5 text-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">House Status</label>
                                    <select name="home_status"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="Own">Own</option>
                                        <option value="Rent">Rent</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">House Size</label>
                                    <select name="home_size"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500"
                                        required>
                                        <option value="5 Marla">5 Marla</option>
                                        <option value="10 Marla">10 Marla</option>
                                        <option value="1 Kanal">1 Kanal</option>
                                        <option value="2 Kanal+">2 Kanal+</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Willing to Relocate</label>
                                    <select name="willing_to_relocate"
                                        class="w-full rounded-xl border-slate-200 py-2.5 text-sm focus:border-brand-500 focus:ring-brand-500">
                                        <option value="">Select...</option>
                                        @foreach(\App\Helpers\FormOptions::relocationOptions() as $opt)
                                            <option value="{{ $opt }}" {{ old('willing_to_relocate') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Fields -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 text-center bg-slate-50/50">
                            <h2 class="text-sm font-semibold text-slate-800">Declaration & Agreement</h2>
                        </div>
                        <div class="p-4 sm:p-6 text-center space-y-4">
                            <p class="text-slate-700 leading-7">
                                I take oath that the information I have provided is true, correct, and complete to the best of my knowledge.
                                I understand that giving false information may lead to disqualification.
                            </p>

                            <div class="flex flex-wrap items-center justify-center gap-3">
                                <button
                                    type="button"
                                    @click="declarationAgreement = 'agree'"
                                    :class="declarationAgreement === 'agree'
                                        ? 'bg-brand-600 text-white border-brand-600'
                                        : 'bg-white text-brand-700 border-brand-300 hover:bg-brand-50'"
                                    class="px-6 py-2.5 rounded-lg text-sm font-bold border transition"
                                >
                                    I Agree
                                </button>

                                <button
                                    type="button"
                                    @click="declarationAgreement = 'disagree'"
                                    :class="declarationAgreement === 'disagree'
                                        ? 'bg-red-600 text-white border-red-600'
                                        : 'bg-white text-red-700 border-red-300 hover:bg-red-50'"
                                    class="px-6 py-2.5 rounded-lg text-sm font-bold border transition"
                                >
                                    I Do Not Agree
                                </button>
                            </div>

                            <input type="hidden" name="declaration_agreement" x-model="declarationAgreement">

                            <p
                                x-show="declarationAgreement === 'disagree'"
                                x-cloak
                                class="text-sm text-red-600 font-semibold"
                            >
                                You must agree to this declaration to access and use this website.
                            </p>

                            @error('declaration_agreement')
                                <p class="text-sm text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <input type="hidden" name="home_location" value="Not Provided">
                    <input type="hidden" name="referral_code" value="">
                    <input type="hidden" name="profile_visibility" value="public">

                    <!-- Submit -->
                    <div class="pt-4">
                        <button type="submit"
                            :disabled="declarationAgreement !== 'agree'"
                            :class="declarationAgreement !== 'agree'
                                ? 'bg-slate-300 text-slate-500 cursor-not-allowed'
                                : 'bg-brand-500 hover:bg-brand-600 text-white'"
                            class="w-full py-3 font-semibold rounded-lg transition-colors">
                            Complete Account Creation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            // Profile-for to gender mapping
            const profileForGroup = document.getElementById('profileForGroup');
            const genderSelect = document.getElementById('genderSelect');
            const profileForRules = {
                'My Son': 'male',
                'My Brother': 'male',
                'My Daughter': 'female',
                'My Sister': 'female'
            };
            const profileForLabels = {
                'Myself': 'I am a',
                'My Son': 'My son is',
                'My Daughter': 'My daughter is',
                'My Brother': 'My brother is',
                'My Sister': 'My sister is',
                'My Friend': 'My friend is',
                'My Relative': 'My relative is'
            };

            function applyProfileForGender(profileFor) {
                if (!genderSelect) return;
                const label = document.getElementById('genderLabel');
                if (label) {
                    label.textContent = profileForLabels[profileFor] || 'I am a';
                }
                const forced = profileForRules[profileFor] || '';
                if (forced) {
                    genderSelect.value = forced;
                    Array.from(genderSelect.options).forEach(opt => {
                        if (opt.value && opt.value !== forced) opt.disabled = true;
                        if (opt.value === forced) opt.disabled = false;
                    });
                } else {
                    Array.from(genderSelect.options).forEach(opt => opt.disabled = false);
                }
            }

            if (profileForGroup && genderSelect) {
                profileForGroup.addEventListener('change', () => {
                    const selected = document.querySelector('input[name="profile_for"]:checked');
                    applyProfileForGender(selected ? selected.value : '');
                });
                const preselected = document.querySelector('input[name="profile_for"]:checked');
                if (preselected) applyProfileForGender(preselected.value);
            }
            // Phone Input
            const phoneInput = document.querySelector("#mobile_no_input");
            const phoneHidden = document.querySelector("#mobile_no_full");
            const phoneError = document.querySelector("#phone_error");
            const form = document.querySelector("form");
            const marriedBrothersInput = form.querySelector('input[name="married_brothers_count"]');
            const marriedSistersInput = form.querySelector('input[name="married_sisters_count"]');
            const marriedSiblingsTotalInput = form.querySelector('input[name="married_siblings_count"]');

            const syncMarriedSiblingsTotal = () => {
                if (!marriedSiblingsTotalInput) return;
                const marriedBrothers = parseInt(marriedBrothersInput?.value ?? '0', 10) || 0;
                const marriedSisters = parseInt(marriedSistersInput?.value ?? '0', 10) || 0;
                marriedSiblingsTotalInput.value = marriedBrothers + marriedSisters;
            };

            [marriedBrothersInput, marriedSistersInput].forEach((field) => {
                if (!field) return;
                field.addEventListener('input', syncMarriedSiblingsTotal);
                field.addEventListener('change', syncMarriedSiblingsTotal);
            });

            syncMarriedSiblingsTotal();

            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "pk",
                separateDialCode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
                preferredCountries: ["pk", "ae", "sa", "gb", "us"],
            });

            phoneInput.addEventListener('blur', () => {
                phoneError.style.display = "none";
                if (phoneInput.value.trim() && !iti.isValidNumber()) {
                    phoneError.style.display = "block";
                }
            });

            form.addEventListener('submit', (e) => {
                syncMarriedSiblingsTotal();
                if (phoneInput.value.trim() && !iti.isValidNumber()) {
                    e.preventDefault();
                    phoneError.style.display = "block";
                    phoneInput.focus();
                    return;
                }
                phoneHidden.value = iti.getNumber();
            });

            // Location Dropdowns
            const countrySelect = document.getElementById('countrySelect');
            const stateSelect = document.getElementById('stateSelect');
            const citySelect = document.getElementById('citySelect');
            let initialCountry = @json((string) old('country', 'Pakistan'));
            let initialState = @json((string) old('state', ''));
            let initialCity = @json((string) old('city', ''));

            function ensureOption(select, value) {
                if (!select || !value) return;
                const exists = Array.from(select.options).some(option => option.value === value);
                if (!exists) {
                    select.add(new Option(value, value));
                }
            }

            async function fetchCountries() {
                countrySelect.innerHTML = '<option value="">Loading countries...</option>';
                try {
                    const response = await fetch('/locations/countries');
                    const data = await response.json();

                    countrySelect.innerHTML = '<option value="">Select Country...</option>';
                    (data || []).forEach(country => {
                        countrySelect.add(new Option(country.name, country.name));
                    });
                    countrySelect.add(new Option('Other', 'Other'));

                    const selectedCountry = initialCountry || countrySelect.value || 'Pakistan';
                    if (selectedCountry) {
                        ensureOption(countrySelect, selectedCountry);
                        countrySelect.value = selectedCountry;
                        if (selectedCountry !== 'Other') {
                            await fetchStates(selectedCountry);
                        }
                    }
                    initialCountry = '';
                } catch (error) {
                    countrySelect.innerHTML = '<option value="">Select Country...</option><option value="Pakistan">Pakistan</option><option value="Other">Other</option>';
                    const selectedCountry = initialCountry || 'Pakistan';
                    countrySelect.value = selectedCountry;
                    if (selectedCountry !== 'Other') {
                        await fetchStates(selectedCountry);
                    }
                    initialCountry = '';
                }
            }

            async function fetchStates(countryName) {
                if (!countryName || countryName === 'Other') {
                    stateSelect.innerHTML = '<option value="">Select State/Province...</option><option value="Other">Other</option>';
                    citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
                    return;
                }

                stateSelect.innerHTML = '<option value="">Loading states...</option>';
                citySelect.innerHTML = '<option value="">Select City...</option>';
                try {
                    const response = await fetch(`/locations/states/${encodeURIComponent(countryName)}`);
                    const data = await response.json();
                    stateSelect.innerHTML = '<option value="">Select State/Province...</option>';
                    (data || []).forEach(state => {
                        const option = new Option(state.name, state.name);
                        option.dataset.id = String(state.id);
                        stateSelect.add(option);
                    });
                    stateSelect.add(new Option('Other', 'Other'));

                    const selectedState = initialState || stateSelect.value;
                    if (selectedState) {
                        ensureOption(stateSelect, selectedState);
                        stateSelect.value = selectedState;
                        const matched = Array.from(stateSelect.options).find(option => option.value === selectedState);
                        if (matched?.dataset?.id) {
                            await fetchCities(matched.dataset.id);
                        }
                    }
                    initialState = '';
                } catch (error) {
                    stateSelect.innerHTML = '<option value="">Select State/Province...</option><option value="Other">Other</option>';
                    if (initialState) {
                        ensureOption(stateSelect, initialState);
                        stateSelect.value = initialState;
                    }
                }
            }

            async function fetchCities(stateId) {
                if (!stateId) {
                    citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
                    return;
                }

                citySelect.innerHTML = '<option value="">Loading cities...</option>';
                try {
                    const response = await fetch(`/locations/cities/${stateId}`);
                    const data = await response.json();
                    citySelect.innerHTML = '<option value="">Select City...</option>';
                    (data || []).forEach(city => citySelect.add(new Option(city.name, city.name)));
                    citySelect.add(new Option('Other', 'Other'));

                    const selectedCity = initialCity || citySelect.value;
                    if (selectedCity) {
                        ensureOption(citySelect, selectedCity);
                        citySelect.value = selectedCity;
                    }
                    initialCity = '';
                } catch (error) {
                    citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
                    if (initialCity) {
                        ensureOption(citySelect, initialCity);
                        citySelect.value = initialCity;
                    }
                }
            }

            countrySelect.addEventListener('change', async function () {
                document.getElementById('stateOtherInput')?.classList.add('hidden');
                document.getElementById('cityOtherInput')?.classList.add('hidden');
                if (this.value === 'Other') {
                    stateSelect.innerHTML = '<option value="">Select State/Province...</option><option value="Other">Other</option>';
                    citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
                    return;
                }
                await fetchStates(this.value);
            });

            stateSelect.addEventListener('change', async function () {
                document.getElementById('cityOtherInput')?.classList.add('hidden');
                if (this.value === 'Other') {
                    citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
                    return;
                }

                const selectedOption = this.options[this.selectedIndex];
                const stateId = selectedOption?.dataset?.id;
                if (stateId) {
                    await fetchCities(stateId);
                } else {
                    citySelect.innerHTML = '<option value="">Select City...</option><option value="Other">Other</option>';
                }
            });

            // Education to Major
            const qualificationMajors = @json(\App\Helpers\FormOptions::majorsByQualification());
            const educationSelect = document.getElementById('educationSelect');
            const majorSelect = document.getElementById('majorSelect');

            function updateMajors(qualification, currentMajor = '') {
                if (!majorSelect) return;
                const majors = qualificationMajors[qualification] || ['Other'];
                const previousValue = currentMajor || majorSelect.value;
                const oldMajor = "{{ old('major_course') }}";

                majorSelect.innerHTML = '<option value="">Select Major...</option>';
                majors.forEach(major => {
                    const option = new Option(major, major);
                    if (major === previousValue || (oldMajor && major === oldMajor)) option.selected = true;
                    majorSelect.add(option);
                });
                // Clear major if it doesn't belong to the new qualification (and it's not during restoration)
                if (!currentMajor && !oldMajor && !majors.includes(previousValue)) {
                    majorSelect.value = '';
                }
            }

            if (educationSelect) {
                educationSelect.addEventListener('change', () => updateMajors(educationSelect.value));
                if (educationSelect.value) updateMajors(educationSelect.value);
            }

            // Religion to Sect
            const religionSects = {
                'Islam': ['Sunni', 'Shia', 'Wahabi', 'Deobandi', 'Barelvi', 'Salafi', 'Ahle Hadees', 'Sufi', 'Other'],
                'Christianity': ['Catholic', 'Protestant', 'Orthodox', 'Other'],
                'Hinduism': ['Vaishnavism', 'Shaivism', 'Other'],
                'Buddhism': ['Theravada', 'Mahayana', 'Other'],
                'Sikhism': ['Khalsa', 'Other'],
                'Judaism': ['Orthodox', 'Conservative', 'Reform', 'Other'],
                'Bahai': ['Other'],
                'Other': ['Other']
            };

            const religionSelect = document.getElementById('religionSelect');
            const sectSelect = document.getElementById('sectSelect');

            function updateSects(religion) {
                const sects = religionSects[religion] || ['Other'];
                sectSelect.innerHTML = '<option value="">Select Sect...</option>';
                sects.forEach(sect => sectSelect.add(new Option(sect, sect)));
            }

            religionSelect.addEventListener('change', () => updateSects(religionSelect.value));
            updateSects(religionSelect.value);

            // Other Toggle
            function setupOtherToggle(selectId, inputId) {
                const select = document.getElementById(selectId);
                const input = document.getElementById(inputId);
                if (!select || !input) return;
                select.addEventListener('change', function () {
                    input.classList.toggle('hidden', this.value !== 'Other');
                    if (this.value === 'Other') input.focus();
                });
            }

            setupOtherToggle('sectSelect', 'sectOtherInput');
            setupOtherToggle('casteSelect', 'casteOtherInput');
            setupOtherToggle('majorSelect', 'majorOtherInput');
            setupOtherToggle('jobSelect', 'jobOtherInput');
            setupOtherToggle('countrySelect', 'countryOtherInput');
            setupOtherToggle('stateSelect', 'stateOtherInput');
            setupOtherToggle('citySelect', 'cityOtherInput');
            setupOtherToggle('motherTongueSelect', 'motherTongueOtherInput');

            // Handle Other values on submit
            form.addEventListener('submit', function () {
                const pairs = [
                    { select: 'sectSelect', input: 'sectOtherInput' },
                    { select: 'casteSelect', input: 'casteOtherInput' },
                    { select: 'majorSelect', input: 'majorOtherInput' },
                    { select: 'jobSelect', input: 'jobOtherInput' },
                    { select: 'countrySelect', input: 'countryOtherInput' },
                    { select: 'stateSelect', input: 'stateOtherInput' },
                    { select: 'citySelect', input: 'cityOtherInput' },
                    { select: 'motherTongueSelect', input: 'motherTongueOtherInput' }
                ];
                pairs.forEach(pair => {
                    const s = document.getElementById(pair.select);
                    const i = document.getElementById(pair.input);
                    if (s && i && s.value === 'Other' && i.value.trim() !== '') {
                        s.options[s.selectedIndex].value = i.value.trim();
                    }
                });
            });

            // --- Persistence Logic ---
            const STORAGE_KEY = 'idlmatch_registration_draft';
            const formFields = form.querySelectorAll('input:not([type="password"]):not([type="hidden"]), select');

            // Save draft to local storage
            function saveDraft() {
                const draft = {};
                formFields.forEach(field => {
                    if (field.name) draft[field.name] = field.value;
                });
                // Special case for phone input (as it might not have name if it's the ITI display input)
                draft['_mobile_display'] = phoneInput.value;
                localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
            }

            // Restore draft from local storage
            async function restoreDraft() {
                const savedData = localStorage.getItem(STORAGE_KEY);
                if (!savedData) return;

                const draft = JSON.parse(savedData);

                // Helper to set value only if currently empty (prioritize Laravel's old())
                const setValueIfEmpty = (name, value) => {
                    const field = form.querySelector(`[name="${name}"]`);
                    if (field && !field.value && value) {
                        field.value = value;
                        // Trigger change event for any dependent logic
                        field.dispatchEvent(new Event('change'));
                    }
                };

                // Restore simple inputs first
                Object.keys(draft).forEach(name => {
                    if (!['_mobile_display', 'country', 'state', 'city', 'sect', 'caste', 'major_course', 'job_title'].includes(name)) {
                        setValueIfEmpty(name, draft[name]);
                    }
                });

                // Restore Phone Display
                if (!phoneInput.value && draft['_mobile_display']) {
                    phoneInput.value = draft['_mobile_display'];
                }

                // Restore Religion/Identity (order matters)
                if (draft['religion']) {
                    setValueIfEmpty('religion', draft['religion']);
                    updateSects(draft['religion']);
                    if (draft['sect']) setValueIfEmpty('sect', draft['sect']);
                }
                if (draft['caste']) setValueIfEmpty('caste', draft['caste']);

                // Restore Education/Career
                if (draft['education']) {
                    setValueIfEmpty('education', draft['education']);
                    updateMajors(draft['education'], draft['major_course']);
                }
                if (draft['job_title']) setValueIfEmpty('job_title', draft['job_title']);

                // Restore Locations (wait for fetches)
                if (draft['country']) {
                    // Country is usually pre-selected or fetched first
                    setTimeout(() => {
                        setValueIfEmpty('country', draft['country']);
                        fetchStates(draft['country']).then(() => {
                            if (draft['state']) {
                                setValueIfEmpty('state', draft['state']);
                                const stateOption = Array.from(stateSelect.options).find(o => o.value === draft['state']);
                                if (stateOption?.dataset.id) {
                                    fetchCities(stateOption.dataset.id).then(() => {
                                        if (draft['city']) setValueIfEmpty('city', draft['city']);
                                    });
                                }
                            }
                        });
                    }, 500);
                }
            }

            // Attach listeners for saving
            formFields.forEach(field => {
                field.addEventListener('input', saveDraft);
                field.addEventListener('change', saveDraft);
            });

            // Clear draft on successful submit (or check dashboard)
            // We added a check in Dashboard or search for idlmatch_registration_draft to clear it.

            // Initialize
            await fetchCountries();
            await restoreDraft();
        });
    </script>
</x-auth-full-layout>


