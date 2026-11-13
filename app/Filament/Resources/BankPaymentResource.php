<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankPaymentResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class BankPaymentResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-cash';

    protected static ?string $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationLabel = 'Bank Payments';

    protected static ?string $slug = 'payments/bank';

    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('payment_method', ['manual', 'bank_transfer', 'jazzcash', 'easypaisa'])
            ->with(['user', 'plan', 'user.profile'])
            ->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\Placeholder::make('user_name')
                            ->label('User')
                            ->content(fn($record) => $record?->user?->profile?->first_name ?? $record?->user?->email ?? 'N/A'),
                        Forms\Components\Placeholder::make('plan_name')
                            ->label('Plan')
                            ->content(fn($record) => $record?->plan?->name ?? 'N/A'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'rejected' => 'Rejected',
                            ]),
                        Forms\Components\Placeholder::make('payment_proof_preview')
                            ->label('Payment Proof')
                            ->content(fn($record): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                                $record && $record->payment_proof_path
                                ? "<img src='" . asset('storage/' . $record->payment_proof_path) . "' class='max-w-md rounded shadow'>"
                                : "No payment proof uploaded"
                            )),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')->label('User')->searchable(),
                Tables\Columns\TextColumn::make('plan.name')->label('Plan'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Submitted At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_proof')
                    ->label('View Proof')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading('Payment Proof')
                    ->modalContent(fn($record) => new \Illuminate\Support\HtmlString(
                        $record?->payment_proof_path
                            ? "<div class='flex justify-center'><img src='" . asset('storage/' . $record->payment_proof_path) . "' class='max-w-full rounded shadow' style='max-height: 80vh;'></div>"
                            : "<div class='text-sm text-gray-500'>No payment proof uploaded.</div>"
                    ))
                    ->action(function () {}) // Empty action to preventing error
                    ->modalActions([
                        Tables\Actions\Modal\Actions\ButtonAction::make('close')->label('Close')->color('secondary')->cancel(),
                    ]),

                Tables\Actions\Action::make('approve')
                    ->action(function (Subscription $record) {
                        Subscription::where('user_id', $record->user_id)
                            ->where('id', '!=', $record->id)
                            ->where('status', 'active')
                            ->update([
                                'status' => 'cancelled',
                                'end_at' => now(),
                            ]);

                        $record->update([
                            'status' => 'active',
                            'start_at' => now(),
                            'end_at' => now()->addDays($record->plan->duration_days ?? 45),
                        ]);

                        try {
                            \Illuminate\Support\Facades\Mail::to($record->user->email)
                                ->send(new \App\Mail\SubscriptionApproved($record));
                        } catch (\Throwable $e) {
                            report($e);
                        }
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn(Subscription $record) => $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->action(function (Subscription $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'payment_meta' => ['rejection_reason' => $data['reason']]
                        ]);
                    })
                    ->form([
                        Forms\Components\Textarea::make('reason')->required()->label('Rejection Reason'),
                    ])
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn(Subscription $record) => $record->status === 'pending'),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankPayments::route('/'),
            'edit' => Pages\EditBankPayment::route('/{record}/edit'),
        ];
    }
}
