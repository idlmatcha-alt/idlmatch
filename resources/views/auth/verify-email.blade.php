<x-guest-layout title="Verify Your Email">
    <div class="mb-8 text-center" x-data="{ editingEmail: false }">
        <h1 class="text-2xl font-bold text-slate-900">Verify Your Email</h1>
        <p class="text-slate-500 mt-2">Enter the verification code to continue</p>

        {{-- Current email display with change option --}}
        <div x-show="!editingEmail" class="mt-3">
            <p class="text-slate-900 font-medium">{{ auth()->user()->email }}</p>
            <button type="button" @click="editingEmail = true"
                class="mt-1 text-sm text-brand-600 hover:text-brand-700 font-medium hover:underline transition-colors">
                Change Email
            </button>
        </div>

        {{-- Inline email edit form --}}
        <div x-show="editingEmail" x-cloak class="mt-3">
            <form method="POST" action="{{ route('verification.update-email') }}" class="space-y-2 max-w-sm mx-auto">
                @csrf
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                    class="w-full rounded-lg border-slate-300 py-2 text-sm text-center focus:border-brand-500 focus:ring-brand-500 @error('email') border-red-300 @enderror"
                    placeholder="new@email.com" required autofocus>
                @error('email')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 py-2 px-4 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition-colors">
                        Update & Resend Code
                    </button>
                    <button type="button" @click="editingEmail = false"
                        class="py-2 px-4 rounded-lg border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
            A new verification code has been sent.
        </div>
    @endif

    @if (session('status') == 'email-updated')
        <div class="mb-4 rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-sm text-blue-700">
            Email updated! A new verification code has been sent to your new email.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.otp') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2 text-center">Verification Code</label>
            <input 
                type="text" 
                name="otp" 
                maxlength="6"
                inputmode="numeric"
                pattern="[0-9]*"
                value="{{ old('otp') }}"
                class="block w-full px-4 py-4 text-center text-2xl font-bold tracking-widest rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 focus:border-cta focus:ring-cta/20 transition-all @error('otp') border-red-300 @enderror" 
                placeholder="000000"
                autofocus
                autocomplete="off">
            
            @error('otp')
                <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
            @enderror
            
            <p class="mt-3 text-xs text-slate-400 text-center">
                Check your email for the 6-digit verification code
            </p>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transition-all transform hover:-translate-y-0.5 active:scale-[0.98]">
                Verify Email
            </button>
        </div>
    </form>

    <div class="mt-6 space-y-3">
        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="w-full rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-600 hover:bg-brand-100 transition-colors">
                Resend Verification Code
            </button>
        </form>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
