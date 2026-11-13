<?php

namespace App\Filament\Resources\VerificationResource\Pages;

use App\Filament\Resources\VerificationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVerifications extends ListRecords
{
    protected static string $resource = VerificationResource::class;

    public function mount(): void
    {
        parent::mount();

        $admin = auth()->user();
        if ($admin && $admin->isAdmin()) {
            $admin->forceFill(['admin_last_seen_verifications_at' => now()])->save();
        }
    }

    protected function getActions(): array
    {
        return [];
    }
    
    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No pending verifications';
    }
    
    protected function getTableEmptyStateDescription(): ?string
    {
        return 'All verification requests have been processed.';
    }
    
    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-check-circle';
    }
}
