<?php

namespace App\Filament\Resources\SuccessStoryResource\Pages;

use App\Filament\Resources\SuccessStoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuccessStories extends ListRecords
{
    protected static string $resource = SuccessStoryResource::class;

    public function mount(): void
    {
        parent::mount();

        $admin = auth()->user();
        if ($admin && $admin->isAdmin()) {
            $admin->forceFill(['admin_last_seen_success_stories_at' => now()])->save();
        }
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
