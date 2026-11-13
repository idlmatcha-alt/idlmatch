<?php

namespace App\Filament\Resources\PaymentRequestResource\Pages;

use App\Filament\Resources\PaymentRequestResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentRequest extends EditRecord
{
    protected static string $resource = PaymentRequestResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            // ✅ APPROVE
            Actions\Action::make('approve')
                ->label('Approve')
                ->action(function () {
                    $record = $this->record;
                    
                    // 1. Deactivate ANY other active subscriptions for this user
                    \App\Models\Subscription::where('user_id', $record->user_id)
                        ->where('id', '!=', $record->id)
                        ->where('status', 'active')
                        ->update([
                            'status' => 'cancelled',
                            'end_at' => now(),
                        ]);

                    // 2. Activate the new subscription
                    $durationDays = $record->plan->duration_days ?? 45;

                    $record->update([
                        'status' => 'active',
                        'start_at' => now(),
                        'end_at' => now()->addDays($durationDays),
                    ]);

                    try {
                        if (class_exists(\App\Mail\SubscriptionApproved::class)) {
                            \Illuminate\Support\Facades\Mail::to($record->user->email)
                                ->send(new \App\Mail\SubscriptionApproved($record));
                        }
                    } catch (\Exception $e) {}

                    \Filament\Facades\Filament::notify('success', 'Payment approved. Previous plans cancelled.');
                    $this->refreshFormData(['status', 'start_at', 'end_at']);
                })
                ->requiresConfirmation()
                ->modalHeading('Approve Payment')
                ->modalSubheading(fn() => 'Approve payment from ' . $this->record->user->name . '? This will cancel any existing active plan.')
                ->color('success')
                ->icon('heroicon-o-check')
                ->visible(fn() => $this->record->status === 'pending'),

            // ❌ REJECT
            Actions\Action::make('reject')
                ->label('Reject')
                ->action(function (array $data) {
                    $record = $this->record;
                    $record->update([
                        'status' => 'rejected',
                        'payment_meta' => array_merge(
                            $record->payment_meta ?? [],
                            ['rejection_reason' => $data['reason'], 'rejected_at' => now()->toISOString()]
                        ),
                    ]);

                    try {
                        if (class_exists(\App\Mail\SubscriptionRejected::class)) {
                            \Illuminate\Support\Facades\Mail::to($record->user->email)
                                ->send(new \App\Mail\SubscriptionRejected($record, $data['reason']));
                        }
                    } catch (\Exception $e) {}

                    \Filament\Facades\Filament::notify('success', 'Payment rejected.');
                    $this->refreshFormData(['status']);
                })
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Rejection Reason')
                        ->required()
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-x')
                ->visible(fn() => $this->record->status === 'pending'),
        ];
    }
}
