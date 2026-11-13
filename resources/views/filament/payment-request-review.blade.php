<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <div class="text-gray-500 font-medium">User</div>
            <div class="font-bold">{{ $record->user->name ?? 'N/A' }}</div>
            <div class="text-xs text-gray-400">{{ $record->user->email ?? '' }}</div>
        </div>
        <div>
            <div class="text-gray-500 font-medium">Plan</div>
            <div class="font-bold">{{ $record->plan->name ?? 'N/A' }}</div>
            <div class="text-xs text-gray-400">PKR {{ number_format($record->plan->price ?? 0) }}</div>
        </div>
        <div>
            <div class="text-gray-500 font-medium">Method</div>
            <div>{{ ucfirst(str_replace('_', ' ', $record->payment_method ?? 'N/A')) }}</div>
        </div>
        <div>
            <div class="text-gray-500 font-medium">Submitted</div>
            <div>{{ $record->created_at->format('d M Y, h:i A') }}</div>
        </div>
        @if($record->payment_reference)
        <div class="col-span-2">
            <div class="text-gray-500 font-medium">Reference / TID</div>
            <div class="font-mono bg-gray-100 px-2 py-1 rounded inline-block">{{ $record->payment_reference }}</div>
        </div>
        @endif
    </div>

    <div class="border-t pt-4">
        <div class="text-gray-500 font-medium mb-2">Payment Proof</div>
        @if($record->payment_proof_path)
            <div class="rounded-lg overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center p-2">
                <a href="{{ asset('storage/' . $record->payment_proof_path) }}" target="_blank" title="Click to view full size">
                    <img src="{{ asset('storage/' . $record->payment_proof_path) }}" alt="Payment Proof" class="max-w-full max-h-[400px] object-contain rounded">
                </a>
            </div>
            <div class="text-center mt-1">
                <a href="{{ asset('storage/' . $record->payment_proof_path) }}" target="_blank" class="text-xs text-primary-600 hover:underline">View Full Size</a>
            </div>
        @else
            <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                No payment proof uploaded.
            </div>
        @endif
    </div>

    <!-- Actions Footer in Modal -->
    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-4">
        <x-filament::button
            wire:click="triggerAction('reject', '{{ $record->id }}')"
            color="danger"
            icon="heroicon-o-x"
        >
            Reject
        </x-filament::button>

        <x-filament::button
            wire:click="triggerAction('approve', '{{ $record->id }}')"
            color="success"
            icon="heroicon-o-check"
        >
            Approve
        </x-filament::button>
        
        <x-filament::button
            color="secondary"
            x-on:click="close()"
            outlined
        >
            Close
        </x-filament::button>
    </div>
</div>
