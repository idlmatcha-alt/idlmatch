<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use App\Services\ReportModerationService;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationLabel = 'Reported Profiles';
    protected static ?string $navigationGroup = 'Profiles';
    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['reporter.user', 'target.user', 'resolver']);
    }

    public static function getNavigationBadge(): ?string
    {
        $admin = auth()->user();
        if (!$admin) {
            return null;
        }

        $query = static::getModel()::query()
            ->where('status', 'open');

        if ($admin->admin_last_seen_reports_at) {
            $query->where('created_at', '>', $admin->admin_last_seen_reports_at);
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
                Forms\Components\TextInput::make('category')->required(),
                Forms\Components\Textarea::make('details')->rows(4),
                Forms\Components\Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'safe' => 'Safe',
                        'banned' => 'Banned',
                        'reviewed' => 'Reviewed',
                        'closed' => 'Closed',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reporter.display_id')->label('Reporter'),
                Tables\Columns\TextColumn::make('target.display_id')->label('Target'),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('details')
                    ->label('Reason')
                    ->default('-')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'open',
                        'success' => 'safe',
                        'danger' => 'banned',
                        'warning' => 'reviewed',
                        'secondary' => 'closed',
                    ]),
                Tables\Columns\TextColumn::make('resolver.name')->label('Resolved By')->default('-'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y, h:i A'),
                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Resolved At')
                    ->formatStateUsing(fn ($state) => $state
                        ? \Illuminate\Support\Carbon::parse($state)->format('d M Y, h:i A')
                        : '-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'safe' => 'Safe',
                        'banned' => 'Banned',
                        'reviewed' => 'Reviewed',
                        'closed' => 'Closed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_reason')
                    ->label('View Reason')
                    ->icon('heroicon-o-document-text')
                    ->color('secondary')
                    ->modalHeading(fn (Report $record) => 'Reason: ' . ($record->target?->display_id ?? 'Reported Profile'))
                    ->modalContent(fn (Report $record): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                        '<div class="space-y-3 text-sm">'
                        . '<div><span class="font-semibold text-gray-700">Reporter:</span> '
                        . e($record->reporter?->display_id ?? '-') . '</div>'
                        . '<div><span class="font-semibold text-gray-700">Target:</span> '
                        . e($record->target?->display_id ?? '-') . '</div>'
                        . '<div><span class="font-semibold text-gray-700">Category:</span> '
                        . e($record->category ?? '-') . '</div>'
                        . '<div class="pt-1">'
                        . '<div class="font-semibold text-gray-700 mb-1">Reason Message</div>'
                        . '<div class="rounded-lg border border-gray-200 bg-gray-50 p-3 whitespace-pre-wrap">'
                        . e($record->details ?: 'No reason provided.')
                        . '</div>'
                        . '</div>'
                        . '</div>'
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
                Tables\Actions\Action::make('mark_safe')
                    ->label('Mark Safe')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Report $record) => $record->status === 'open')
                    ->action(function (Report $record): void {
                        app(ReportModerationService::class)->markSafe($record, auth()->user());
                        Filament::notify('success', 'Report marked as safe.');
                    }),
                Tables\Actions\Action::make('ban_user')
                    ->label('Ban User')
                    ->icon('heroicon-o-ban')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Ban reported user?')
                    ->modalSubheading('This will ban the reported account and send notification emails.')
                    ->visible(fn (Report $record) => $record->status === 'open')
                    ->action(function (Report $record): void {
                        app(ReportModerationService::class)->banTarget($record, auth()->user());
                        Filament::notify('success', 'User banned and report resolved.');
                    }),
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
            'index' => Pages\ListReports::route('/'),
        ];
    }    
}
