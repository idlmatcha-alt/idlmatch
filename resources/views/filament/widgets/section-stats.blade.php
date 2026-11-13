<x-filament::widget class="filament-stats-overview-widget">
    <div {!! ($pollingInterval = $this->getPollingInterval()) ? "wire:poll.{$pollingInterval}" : '' !!}>
        <h2 class="mb-3 text-base font-bold tracking-tight text-gray-900 sm:text-lg">
            {{ $this->getSectionHeading() }}
        </h2>

        <x-filament::stats :columns="$this->getColumns()">
            @foreach ($this->getCachedCards() as $card)
                {{ $card }}
            @endforeach
        </x-filament::stats>
    </div>
</x-filament::widget>
