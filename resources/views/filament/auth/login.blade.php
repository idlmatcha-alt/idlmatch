<form wire:submit.prevent="authenticate" class="space-y-4">
    {{ $this->form }}

    <x-filament::button type="submit" form="authenticate" class="w-full">
        {{ __('filament::login.buttons.submit.label') }}
    </x-filament::button>

    <div class="text-center -mt-2">
        <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500 hover:underline">
            {{ __('Forgot your password?') }}
        </a>
    </div>
</form>
