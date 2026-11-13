<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';

    protected static ?string $navigationLabel = 'Plans';

    protected static ?string $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('price')->numeric()->required()->prefix('PKR'),
                        Forms\Components\TextInput::make('currency')->required()->default('PKR'),
                        Forms\Components\TextInput::make('duration_days')
                            ->numeric()
                            ->required()
                            ->default(45)
                            ->label('Duration (Days)'),
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ])->columns(2),
                
                Forms\Components\Section::make('Plan Limits & Features')
                    ->schema([
                        Forms\Components\TextInput::make('limits_json.profile_views')
                            ->numeric()
                            ->required()
                            ->default(10)
                            ->label('Profile Views Limit')
                            ->helperText('Number of profiles user can view'),
                        Forms\Components\Toggle::make('limits_json.contact_access')
                            ->label('Contact Access')
                            ->helperText('Can user see contact details of matches'),
                        Forms\Components\Toggle::make('limits_json.verified_badge')
                            ->label('Verified Badge')
                            ->helperText('Does user get a verified badge displayed'),
                        Forms\Components\Toggle::make('limits_json.urgent_support')
                            ->label('Urgent Support')
                            ->helperText('Priority customer support access'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }    
}
