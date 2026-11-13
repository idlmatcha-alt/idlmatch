<?php

namespace App\Filament\Resources\PaymentRequestResource\Pages;

use App\Filament\Resources\PaymentRequestResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentRequests extends ListRecords
{
    protected static string $resource = PaymentRequestResource::class;

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
        return [];
    }

    public function triggerAction(string $action, string $record)
    {
        $this->mountTableAction($action, $record);
    }
}
