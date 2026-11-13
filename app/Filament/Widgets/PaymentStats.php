<?php

namespace App\Filament\Widgets;

use App\Models\Plan;
use App\Models\Report;
use App\Models\Subscription;
use App\Models\SuccessStory;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class PaymentStats extends SectionStats
{
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return 'Website overview';
    }

    protected function getCards(): array
    {
        $subscriptionsUrl = fn (array $filters = []) => route(
            'filament.resources.subscriptions.index',
            $filters ? ['tableFilters' => $filters] : []
        );

        $totalRevenue = DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->sum('plans.price');

        return [
            Card::make('Pending payments', Subscription::where('status', 'pending')->count())
                ->description('Awaiting bank-transfer approval')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->url($subscriptionsUrl(['status' => ['value' => 'pending']])),

            Card::make('Active subscriptions', Subscription::where('status', 'active')
                ->where('end_at', '>', now())
                ->count())
                ->description('Paid and not yet expired')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->url($subscriptionsUrl(['status' => ['value' => 'active']])),

            Card::make('Total revenue', 'PKR ' . number_format((float) $totalRevenue))
                ->description('From active paid subscriptions')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('primary')
                ->icon('heroicon-o-currency-pound')
                ->url($subscriptionsUrl()),

            Card::make('Rejected payments', Subscription::where('status', 'rejected')->count())
                ->description('Claims the team turned down')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->url($subscriptionsUrl(['status' => ['value' => 'rejected']])),

            Card::make('Membership plans', Plan::where('is_active', true)->count())
                ->description('Plans on sale right now')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('info')
                ->icon('heroicon-o-collection')
                ->url(route('filament.resources.plans.index')),

            Card::make('Open reports', Report::where('status', 'open')->count())
                ->description('Member complaints to investigate')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('danger')
                ->icon('heroicon-o-flag')
                ->url(route('filament.resources.reports.index', [
                    'tableFilters' => ['status' => ['value' => 'open']],
                ])),

            Card::make('Stories to review', SuccessStory::where('status', 'pending')->count())
                ->description('Waiting for publication')
                ->descriptionIcon('heroicon-o-arrow-narrow-right')
                ->color('warning')
                ->icon('heroicon-o-photograph')
                ->url(route('filament.resources.success-stories.index', [
                    'tableFilters' => ['status' => ['value' => 'pending']],
                ])),
        ];
    }
}
