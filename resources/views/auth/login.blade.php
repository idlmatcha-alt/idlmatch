<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-10 text-center">
        <h1 class="font-display text-3xl font-bold text-slate-900 leading-tight">Welcome back</h1>
        <p class="mt-3 text-slate-500 font-medium">Please enter your details to sign in</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username"
                class="block w-full px-4 py-4 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 shadow-sm focus:border-cta focus:ring focus:ring-cta/20 focus:ring-offset-0 transition-all duration-200 placeholder:text-slate-400"
                placeholder="Enter your email">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div x-data="{ show: false }">
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-cta hover:text-cta-hover transition-colors"
                        href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                    class="block w-full px-4 py-4 rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 shadow-sm focus:border-cta focus:ring focus:ring-cta/20 focus:ring-offset-0 transition-all duration-200 placeholder:text-slate-400 pr-10"
                    placeholder="••••••••">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                    <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="relative flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox" name="remember"
                    class="peer h-5 w-5 rounded-md border-slate-300 text-cta focus:ring-cta/20 transition-all duration-200">
                <span class="ml-3 text-sm font-medium text-slate-600 peer-hover:text-slate-900 transition-colors">Keep
                    me logged in</span>
            </label>
        </div>

        <div>
            <button type="submit"
                class="w-full flex justify-center py-4 px-6 rounded-full bg-cta hover:bg-cta-hover text-white text-lg font-bold shadow-lg shadow-cta/30 transform transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98] outline-none">
                Sign In
            </button>
        </div>
    </form>

    <div class="mt-4">
        <a href="{{ route('auth.google') }}"
            class="w-full flex items-center justify-center gap-3 py-4 px-6 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-lg font-bold shadow-sm transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.98]">
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
            Sign in with Google
        </a>
    </div>

    <div class="mt-8 pt-8 border-t border-slate-100 text-center">
        <p class="text-slate-500 font-medium">
            New to Ideal Marriage Center?
            <a href="{{ route('register') }}" class="ml-1 font-bold text-cta hover:text-cta-hover transition-colors">
                Join Free Today
            </a>
        </p>
    </div>
</x-guest-layout>