<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    public function mount(): void
    {
        parent::mount();

        $admin = auth()->user();
        if ($admin && $admin->isAdmin()) {
            $admin->forceFill(['admin_last_seen_subscriptions_at' => now()])->save();
        }
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
