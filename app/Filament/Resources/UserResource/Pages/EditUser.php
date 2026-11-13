<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\ProfileCompletionService;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-download')
                ->color('secondary')
                ->url(fn () => route('admin.users.download-pdf', ['user' => $this->record]))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $profile = $this->record->profile;

        if (!$profile) {
            return;
        }

        $completionScore = app(ProfileCompletionService::class)->calculate($profile);

        if ((int) $profile->completion_score !== $completionScore) {
            $profile->forceFill(['completion_score' => $completionScore])->saveQuietly();
        }
    }
}
