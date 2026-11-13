<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Subscriptions';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'plan']);
    }

    public static function getNavigationBadge(): ?string
    {
        $admin = auth()->user();
        if (!$admin) {
            return null;
        }

        $query = static::getModel()::query()
            ->where('status', 'pending');

        if ($admin->admin_last_seen_subscriptions_at) {
            $query->where('created_at', '>', $admin->admin_last_seen_subscriptions_at);
        }

        $count = $query->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')->numeric()->required(),
                Forms\Components\TextInput::make('plan_id')->numeric()->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'rejected' => 'Rejected',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('payment_method'),
                Forms\Components\Placeholder::make('payment_proof_preview')
                    ->label('Payment Proof')
                    ->content(fn($record): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                        $record && $record->payment_proof_path
                        ? "<a href='" . asset('storage/' . $record->payment_proof_path) . "' target='_blank'><img src='" . asset('storage/' . $record->payment_proof_path) . "' class='max-w-md rounded shadow border' style='max-height:400px'></a>"
                        : "No payment proof uploaded"
                    )),
                Forms\Components\DateTimePicker::make('start_at'),
                Forms\Components\DateTimePicker::make('end_at'),
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
                        'secondary' => 'expired',
                    ]),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\ImageColumn::make('payment_proof_path')
                    ->disk('public')
                    ->label('Payment Proof')
                    ->width(100)
                    ->height(100)
                    ->square()
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Submitted At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'rejected' => 'Rejected',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
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
                    ->visible(fn(Subscription $record) => $record->status === 'pending')
                    ->button(),

                Tables\Actions\Action::make('reject')
                    ->action(function (Subscription $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'payment_meta' => ['rejection_reason' => $data['reason']]
                        ]);

                        try {
                            \Illuminate\Support\Facades\Mail::to($record->user->email)
                                ->send(new \App\Mail\SubscriptionRejected($record, $data['reason']));
                        } catch (\Throwable $e) {
                            report($e);
                        }
                    })
                    ->form([
                        Forms\Components\Textarea::make('reason')->required()->label('Rejection Reason'),
                    ])
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn(Subscription $record) => $record->status === 'pending')
                    ->button(),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
