<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="font-display text-2xl text-slate-900">Confirm password</h1>
        <p class="mt-2 text-sm text-slate-500">Please confirm your password to continue.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="mt-2 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Confirm') }}
        </x-primary-button>
    </form>
</x-guest-layout>
