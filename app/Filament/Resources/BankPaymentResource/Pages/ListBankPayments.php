<?php

namespace App\Filament\Resources\BankPaymentResource\Pages;

use App\Filament\Resources\BankPaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListBankPayments extends ListRecords
{
    protected static string $resource = BankPaymentResource::class;

    protected function getActions(): array
    {
        return [
            // No create action usually for bank payments (submitted by users)
            // But if needed: Filament\Pages\Actions\CreateAction::make(),
        ];
    }
}
