<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificationResource\Pages;
use App\Models\Profile;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class VerificationResource extends Resource
{
    protected static ?string $model = Profile::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationLabel = 'Pending Verification';
    
    protected static ?string $navigationGroup = 'Profiles';
    
    protected static ?int $navigationSort = 5;
    
    // Use a custom slug to avoid conflict with ProfileResource
    protected static ?string $slug = 'verifications';

    public static function getNavigationBadge(): ?string
    {
        $admin = auth()->user();
        if (!$admin) {
            return null;
        }

        $query = static::getEloquentQuery();

        if ($admin->admin_last_seen_verifications_at) {
            $query->where('updated_at', '>', $admin->admin_last_seen_verifications_at);
        }

        $count = $query->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        // Show only profiles where CNIC has been uploaded but not yet verified
        return parent::getEloquentQuery()
            ->whereNotNull('cnic_front')
            ->where('cnic_verified', false)
            ->whereHas('user', function ($q) {
                $q->nonAdmin();
            })
            ->with('user');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Info')
                    ->schema([
                        Forms\Components\TextInput::make('display_id')->label('Profile ID')->disabled(),
                        Forms\Components\Placeholder::make('user_name')
                            ->label('Name')
                            ->content(fn ($record) => $record->user?->name ?? 'N/A'),
                        Forms\Components\Placeholder::make('user_email')
                            ->label('Email')
                            ->content(fn ($record) => $record->user?->email ?? 'N/A'),
                        Forms\Components\TextInput::make('mobile_no')->label('Contact Number')->disabled(),
                    ])->columns(4),
                Forms\Components\Section::make('Verification Documents')
                    ->schema([
                        Forms\Components\Placeholder::make('selfie_preview')
                            ->label('Selfie')
                            ->content(fn ($record): HtmlString => new HtmlString($record->selfie ? "<img src='" . asset('storage/' . $record->selfie) . "' class='max-w-xs rounded shadow'>" : "No selfie uploaded")),
                        Forms\Components\Placeholder::make('cnic_front_preview')
                            ->label('CNIC Front')
                            ->content(fn ($record): HtmlString => new HtmlString($record->cnic_front ? "<img src='" . asset('storage/' . $record->cnic_front) . "' class='max-w-xs rounded shadow'>" : "No front uploaded")),
                        Forms\Components\Placeholder::make('cnic_back_preview')
                            ->label('CNIC Back')
                            ->content(fn ($record): HtmlString => new HtmlString($record->cnic_back ? "<img src='" . asset('storage/' . $record->cnic_back) . "' class='max-w-xs rounded shadow'>" : "No back uploaded")),
                    ])->columns(3),
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('cnic_verified')
                            ->label('CNIC Verified Badge'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_id')->label('Profile ID')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Name')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email')->searchable(),
                Tables\Columns\IconColumn::make('cnic_verified')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('updated_at')->label('Submitted')->date()->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Verify CNIC')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update([
                            'verification_status' => 'verified',
                            'cnic_verified' => true,
                            'cnic_verified_at' => now(),
                            'rejection_reason' => null,
                        ]);
                        
                        // Send email notification to user
                        try {
                            \Illuminate\Support\Facades\Mail::to($record->user->email)
                                ->send(new \App\Mail\ProfileVerified($record));
                        } catch (\Exception $e) {
                            // Log error but don't fail the verification
                            \Illuminate\Support\Facades\Log::error('Failed to send ProfileVerified email: ' . $e->getMessage());
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Verify CNIC')
                    ->modalSubheading('Are you sure you want to verify this user\'s CNIC?'),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x')
                    ->color('danger')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'verification_status' => 'rejected',
                            'cnic_front' => null,
                            'cnic_back' => null,
                            'cnic_verified' => false,
                            'cnic_verified_at' => null,
                            'rejection_reason' => $data['reason'] ?? null,
                        ]);
                    })
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Reject Verification')
                    ->modalSubheading('This will clear the CNIC documents. The user will need to re-upload.'),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve_selected')
                    ->label('Verify Selected')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $record->update([
                                'verification_status' => 'verified',
                                'cnic_verified' => true,
                                'cnic_verified_at' => now(),
                                'rejection_reason' => null,
                            ]);
                        }
                    })
                    ->requiresConfirmation(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerifications::route('/'),
            'view' => Pages\ViewVerification::route('/{record}'),
        ];
    }
}
