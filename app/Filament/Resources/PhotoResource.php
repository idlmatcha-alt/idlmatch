<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhotoResource\Pages;
use App\Models\Photo;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PhotoResource extends Resource
{
    protected static ?string $model = Photo::class;

    protected static ?string $navigationIcon = 'heroicon-o-photograph';
    protected static ?string $navigationLabel = 'Profile Photos';
    protected static ?string $navigationGroup = 'Profiles';
    protected static ?int $navigationSort = 7;
    protected static bool $shouldRegisterNavigation = true;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('photos.id', function ($query) {
                $query->from('photos as p2')
                    ->selectRaw('COALESCE(MAX(CASE WHEN p2.is_primary = 1 THEN p2.id END), MAX(p2.id))')
                    ->whereNull('p2.deleted_at')
                    ->groupBy('p2.profile_id');
            })
            ->with([
                'profile.user',
                'profile.primaryPhoto',
                'profile.photos' => fn ($query) => $query
                    ->whereNull('deleted_at')
                    ->orderByDesc('is_primary')
                    ->orderByDesc('created_at'),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('message')
                    ->label('')
                    ->content('Photo editing is disabled. Use the list actions to view or delete profile photos.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('Profile Photo')
                    ->disk('public')
                    ->circular()
                    ->height(64)
                    ->width(64)
                    ->getStateUsing(fn (Photo $record) => $record->profile?->primaryPhoto?->path ?? $record->path),
                Tables\Columns\TextColumn::make('profile.display_id')
                    ->label('User ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->url(fn (Photo $record) => $record->profile?->display_id
                        ? route('profiles.show', $record->profile->display_id)
                        : null, true),
                Tables\Columns\TextColumn::make('profile.user.name')
                    ->label('Member')
                    ->searchable()
                    ->default('-'),
                Tables\Columns\ViewColumn::make('gallery_photos')
                    ->label('Gallery Photos')
                    ->view('filament.tables.columns.profile-gallery-preview'),
                Tables\Columns\TextColumn::make('photo_count')
                    ->label('Total')
                    ->alignCenter()
                    ->getStateUsing(fn (Photo $record) => (int) ($record->profile?->photos?->count() ?? 0)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('photo_status')
                    ->label('Any Photo Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('profile.photos', function (Builder $photoQuery) use ($data) {
                            $photoQuery->where('status', $data['value']);
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_photos')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading(fn (Photo $record) => 'Photos: ' . ($record->profile?->display_id ?? 'Profile'))
                    ->modalContent(fn (Photo $record): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                        view('filament.photos.profile-photos-modal', [
                            'profile' => $record->profile,
                        ])->render()
                    ))
                    ->action(function (): void {
                        // Modal-only action.
                    })
                    ->modalActions([
                        Tables\Actions\Modal\Actions\ButtonAction::make('close')
                            ->label('Close')
                            ->color('secondary')
                            ->cancel(),
                    ]),
                Tables\Actions\Action::make('delete_profile_photo')
                    ->label('Delete Profile Photo')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Photo $record) => (bool) $record->profile?->photos?->firstWhere('is_primary', true))
                    ->action(function (Photo $record): void {
                        $primaryPhoto = $record->profile?->photos?->firstWhere('is_primary', true);

                        if (!$primaryPhoto) {
                            Filament::notify('warning', 'No profile photo found.');
                            return;
                        }

                        $primaryPhoto->delete();
                        Filament::notify('success', 'Profile photo deleted.');
                    }),
                Tables\Actions\Action::make('delete_gallery_photos')
                    ->label('Delete Gallery Photos')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Photo $record) => ($record->profile?->photos?->where('is_primary', false)->count() ?? 0) > 0)
                    ->action(function (Photo $record): void {
                        $galleryPhotos = $record->profile?->photos?->where('is_primary', false);
                        $deletedCount = 0;

                        if ($galleryPhotos) {
                            foreach ($galleryPhotos as $photo) {
                                $photo->delete();
                                $deletedCount++;
                            }
                        }

                        Filament::notify('success', $deletedCount . ' gallery photo(s) deleted.');
                    }),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListPhotos::route('/'),
        ];
    }
}
