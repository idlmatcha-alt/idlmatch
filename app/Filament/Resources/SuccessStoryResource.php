<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuccessStoryResource\Pages;
use App\Filament\Resources\SuccessStoryResource\RelationManagers;
use App\Models\SuccessStory;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuccessStoryResource extends Resource
{
    protected static ?string $model = SuccessStory::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationLabel = 'Success Stories';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 9;

    /** Makes stories findable from the search box at the top of the panel. */
    protected static ?string $recordTitleAttribute = 'couple_names';

    public static function getGloballySearchableAttributes(): array
    {
        return ['couple_names', 'groom_name', 'bride_name'];
    }

    public static function getNavigationBadge(): ?string
    {
        $admin = auth()->user();
        if (!$admin) {
            return null;
        }

        $query = static::getModel()::query()
            ->where('status', 'pending');

        if ($admin->admin_last_seen_success_stories_at) {
            $query->where('created_at', '>', $admin->admin_last_seen_success_stories_at);
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
                Forms\Components\TextInput::make('groom_name'),
                Forms\Components\TextInput::make('bride_name'),
                Forms\Components\Textarea::make('story')->rows(5)->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('cover_image')
                    ->disk('public')
                    ->directory('success-stories')
                    ->image()
                    ->visibility('public'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('groom_name')->searchable(),
                Tables\Columns\TextColumn::make('bride_name')->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
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
            'index' => Pages\ListSuccessStories::route('/'),
            'create' => Pages\CreateSuccessStory::route('/create'),
            'edit' => Pages\EditSuccessStory::route('/{record}/edit'),
        ];
    }
}
