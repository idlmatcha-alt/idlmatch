<?php

namespace App\Filament\Widgets;

use App\Models\Profile;
use App\Models\SuccessStory;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Carbon;

class AdminStats extends SectionStats
{
    protected static ?int $sort = 1;

    public function getHeading(): string
    {
        return 'Member overview';
    }

    protected function getCards(): array
    {
        $profilesUrl = fn (array $filters = []) => route(
            'filament.resources.profiles.index',
            $filters ? ['tableFilters' => $filters] : []
        );

        return [
            Card::make('Pending approvals', Profile::where('verification_status', 'pending')->count())
                ->description('CNIC / documents awaiting review')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->url($profilesUrl(['verification_status' => ['value' => 'pending']])),

            Card::make('Verified members', Profile::where('verification_status', 'verified')->count())
                ->description('Documents checked by the team')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('info')
                ->icon('heroicon-o-shield-check')
                ->url($profilesUrl(['verification_status' => ['value' => 'verified']])),

            Card::make('Active profiles', Profile::where('status', 'active')->count())
                ->description('Visible in member search')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('success')
                ->icon('heroicon-o-users')
                ->url($profilesUrl(['status' => ['value' => 'active']])),

            Card::make('Banned profiles', Profile::where('status', 'banned')->count())
                ->description('Blocked from the platform')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('danger')
                ->icon('heroicon-o-user-group')
                ->url($profilesUrl(['status' => ['value' => 'banned']])),

            Card::make('Happy couples', SuccessStory::where('status', 'approved')->count())
                ->description('Published success stories')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('primary')
                ->icon('heroicon-o-heart')
                ->url(route('filament.resources.success-stories.index', [
                    'tableFilters' => ['status' => ['value' => 'approved']],
                ])),

            Card::make('New this week', $this->weeklyTotals()['total'])
                ->description('Profiles created in the last 7 days')
                ->color('success')
                ->icon('heroicon-o-user')
                ->chart($this->weeklyTotals()['series'])
                ->url($profilesUrl()),
        ];
    }

    /**
     * @return array{total: int, series: array<int, int>}
     */
    protected function weeklyTotals(): array
    {
        $series = [];
        $total = 0;

        for ($day = 6; $day >= 0; $day--) {
            $date = Carbon::today()->subDays($day);

            $count = Profile::whereDate('created_at', $date)->count();
            $series[] = $count;
            $total += $count;
        }

        return ['total' => $total, 'series' => $series];
    }
}
