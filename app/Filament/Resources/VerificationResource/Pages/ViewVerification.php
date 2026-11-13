<?php

namespace App\Filament\Resources\VerificationResource\Pages;

use App\Filament\Resources\VerificationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVerification extends ViewRecord
{
    protected static string $resource = VerificationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve Verification')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->update([
                        'verification_status' => 'verified',
                        'cnic_verified' => true,
                        'cnic_verified_at' => now(),
                        'rejection_reason' => null,
                    ]);

                    try {
                        \Illuminate\Support\Facades\Mail::to($this->record->user->email)
                            ->send(new \App\Mail\ProfileVerified($this->record));
                    } catch (\Throwable $e) {
                        report($e);
                    }

                    $this->notify('success', 'Verification approved successfully!');
                    $this->redirect(VerificationResource::getUrl('index'));
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record->verification_status === 'pending'),
            Actions\Action::make('reject')
                ->label('Reject Verification')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->action(function (array $data) {
                    $this->record->update([
                        'verification_status' => 'rejected',
                        'cnic_verified' => false,
                        'cnic_verified_at' => null,
                        'rejection_reason' => $data['reason'] ?? null,
                    ]);
                    $this->notify('success', 'Verification rejected.');
                    $this->redirect(VerificationResource::getUrl('index'));
                })
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Rejection Reason')
                        ->required()
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->visible(fn () => $this->record->verification_status === 'pending'),
        ];
    }
}
