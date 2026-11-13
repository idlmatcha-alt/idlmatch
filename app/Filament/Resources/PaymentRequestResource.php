<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentRequestResource\Pages;
use App\Models\Plan;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PaymentRequestResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Transactions';

    protected static ?string $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 11;

    protected static ?string $slug = 'payment-requests';

    protected static ?string $modelLabel = 'Payment Request';

    protected static ?string $pluralModelLabel = 'Payment Requests';

    /**
     * Show only pending & rejected payment requests
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'plan'])
            ->whereIn('status', ['pending', 'rejected'])
            ->latest();
    }

    /**
     * Badge showing count of NEW pending payments
     */
    public static function getNavigationBadge(): ?string
    {
        $admin = auth()->user();
        if (!$admin) {
            return null;
        }

        $query = Subscription::query()->where('status', 'pending');

        if ($admin->admin_last_seen_subscriptions_at) {
            $query->where('created_at', '>', $admin->admin_last_seen_subscriptions_at);
        }

        $count = $query->count();
        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    /**
     * View/Edit form for a payment request
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User & Plan')
                    ->schema([
                        Forms\Components\Placeholder::make('user_name')
                            ->label('User')
                            ->content(fn($record) => $record?->user?->name . ' (' . $record?->user?->email . ')'),

                        Forms\Components\Placeholder::make('plan_name')
                            ->label('Requested Plan')
                            ->content(fn($record) => $record?->plan?->name . ' — PKR ' . number_format($record?->plan?->price ?? 0)),

                        Forms\Components\Placeholder::make('submitted_at')
                            ->label('Submitted At')
                            ->content(fn($record) => $record?->created_at?->format('d M Y, h:i A')),

                        Forms\Components\Placeholder::make('status')
                            ->label('Current Status')
                            ->content(fn($record) => ucfirst($record->status)),

                        Forms\Components\Placeholder::make('start_at')
                            ->label('Subscription Start')
                            ->content(fn($record) => $record?->start_at?->format('d M Y, h:i A'))
                            ->visible(fn($record) => $record?->status === 'active'),

                        Forms\Components\Placeholder::make('end_at')
                            ->label('Subscription End')
                            ->content(fn($record) => $record?->end_at?->format('d M Y, h:i A'))
                            ->visible(fn($record) => $record?->status === 'active'),
                    ])->columns(2),

                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\Placeholder::make('payment_method_display')
                            ->label('Payment Method')
                            ->content(fn($record) => ucfirst(str_replace('_', ' ', $record?->payment_method ?? 'N/A'))),

                        Forms\Components\Placeholder::make('payment_reference_display')
                            ->label('Reference / TID')
                            ->content(fn($record) => $record?->payment_reference ?? 'N/A'),

                        Forms\Components\Placeholder::make('payment_proof_preview')
                            ->label('Payment Proof Screenshot')
                            ->content(fn($record): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                                $record && $record->payment_proof_path
                                ? "<a href='" . asset('storage/' . $record->payment_proof_path) . "' target='_blank'><img src='" . asset('storage/' . $record->payment_proof_path) . "' class='max-w-md rounded shadow border' style='max-height:400px'></a>"
                                : "<span class='text-gray-400'>No payment proof uploaded</span>"
                            ))
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    /**
     * Table listing with Approve / Reject quick actions
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->description(fn($record) => $record->user?->email)
                    ->searchable(['users.name', 'users.email']),

                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')
                    ->description(fn($record) => 'PKR ' . number_format($record->plan?->price ?? 0)),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger' => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->formatStateUsing(fn($state) => ucfirst(str_replace('_', ' ', $state ?? ''))),



                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),

                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->options(Plan::pluck('name', 'id')),
            ])
            ->actions([
                // ✅ APPROVE
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->action(function (\App\Models\Subscription $record) {
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
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payment')
                    ->modalSubheading(fn($record) => 'Approve payment from ' . $record->user->name . '? This will cancel any existing active plan.')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn($record) => $record->status === 'pending'),

                // ❌ REJECT
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->action(function (\App\Models\Subscription $record, array $data) {
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
                    })
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x')
                    ->visible(fn($record) => $record->status === 'pending'),

                // 👁️ REVIEW
                Tables\Actions\Action::make('review')
                    ->label('Review Payment')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (\App\Models\Subscription $record) => PaymentRequestResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentRequests::route('/'),
            'edit' => Pages\EditPaymentRequest::route('/{record}/edit'),
        ];
    }

    /**
     * Disable create — payment requests come from users
     */
    public static function canCreate(): bool
    {
        return false;
    }
}
