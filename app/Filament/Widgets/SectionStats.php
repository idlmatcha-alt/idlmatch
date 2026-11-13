<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;

/**
 * A stats overview widget that renders a section heading above the cards,
 * so the dashboard reads as "Member overview" / "Website overview" blocks
 * like the reference design in /admin/31.png.
 */
abstract class SectionStats extends StatsOverviewWidget
{
    protected static string $view = 'filament.widgets.section-stats';

    public function getSectionHeading(): string
    {
        return $this->getHeading();
    }

    abstract public function getHeading(): string;
}
