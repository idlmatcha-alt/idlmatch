<x-filament::page>
    <form wire:submit.prevent="updateEmail" class="space-y-6">
        {{ $this->form }}
        
        <div class="flex flex-wrap gap-4">
            <x-filament::button type="button" wire:click="updateEmail">
                Update Email
            </x-filament::button>
            
            <x-filament::button type="button" wire:click="updatePassword" color="success">
                Update Password
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
