<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="font-display text-2xl text-slate-900">Reset your password</h1>
        <p class="mt-2 text-sm text-slate-500">We will send a reset link to your email.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-2 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Email Reset Link') }}
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Remembered your password?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Sign in</a>
    </p>
</x-guest-layout>
