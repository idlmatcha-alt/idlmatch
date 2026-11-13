<?php

namespace App\Filament\Resources\BankPaymentResource\Pages;

use App\Filament\Resources\BankPaymentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBankPayment extends EditRecord
{
    protected static string $resource = BankPaymentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
