<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Resources\Pages\ListRecords;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    public function mount(): void
    {
        parent::mount();

        $admin = auth()->user();
        if ($admin && $admin->isAdmin()) {
            $admin->forceFill(['admin_last_seen_reports_at' => now()])->save();
        }
    }

    protected function getActions(): array
    {
        return [];
    }
}
