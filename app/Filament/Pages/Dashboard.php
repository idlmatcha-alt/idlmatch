<?php

namespace App\Filament\Pages;

use App\Support\AdminDashboard;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

/**
 * The admin home screen, laid out to match the approved reference:
 * greeting + system pill, four stat cards with sparklines, a growth chart,
 * a gender donut, recent users, recent activity, quick actions, system
 * status and the promo card.
 *
 * Every figure comes from App\Support\AdminDashboard, which reads the live
 * database — nothing on this screen is hard-coded.
 */
class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = -2;

    protected static ?string $title = 'Dashboard';

    /* ---------------------------------------------------------------- screen data */

    public function payload(): array
    {
        return AdminDashboard::payload();
    }

    /** First word of the signed-in admin's name, used in the greeting. */
    public function adminName(): string
    {
        $name = trim((string) (Auth::user()->name ?? 'Admin'));

        return $name !== '' ? str($name)->explode(' ')->first() : 'Admin';
    }

    public function greeting(): string
    {
        $hour = (int) now()->format('G');

        return match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };
    }

    /** True when every service in the system-status list answers. */
    public function isFullyOnline(): bool
    {
        $rows = $this->payload()['system'] ?? [];

        return count($rows) > 0 && collect($rows)->every(fn (array $row) => (bool) $row['ok']);
    }

    /**
     * The clickable stat sections built in an earlier pass (member overview /
     * website overview). Kept below the reference screen so the money and
     * report figures stay one click away.
     */
    public function overviewWidgets(): array
    {
        return $this->getWidgets();
    }

    public function overviewColumns(): int|array
    {
        return $this->getColumns();
    }

    /** Absolute URL of a panel route, used by the "view all" links. */
    public function panelUrl(string $name, array $parameters = []): string
    {
        return route($name, $parameters);
    }

    /* ---------------------------------------------------------------- chart maths */

    public const TONES = [
        'blue' => '#2563eb',
        'green' => '#16a34a',
        'rose' => '#e11d48',
        'amber' => '#f59e0b',
        'violet' => '#7c3aed',
        'slate' => '#64748b',
    ];

    public function toneColor(?string $tone): string
    {
        return self::TONES[$tone] ?? self::TONES['slate'];
    }

    /** Inline custom properties for a coloured icon tile (tone + 10% wash). */
    public function toneStyle(?string $tone): string
    {
        $hex = $this->toneColor($tone);

        return "--tone: {$hex}; --tone-soft: {$hex}1a;";
    }

    /**
     * Mini trend line for a stat card.
     *
     * @param  array<int, int>  $series
     * @return array<string, mixed>
     */
    public function sparkline(array $series, int $width = 116, int $height = 40): array
    {
        $series = array_values($series);
        $count = count($series);

        if ($count === 0) {
            return ['width' => $width, 'height' => $height, 'line' => '', 'area' => '', 'dot' => null];
        }

        $max = max($series);
        $min = min($series);
        $span = max(1, $max - $min);

        $points = [];

        foreach ($series as $index => $value) {
            $x = $count > 1 ? ($index / ($count - 1)) * $width : $width / 2;
            $y = $height - 3 - (($value - $min) / $span) * ($height - 8);

            $points[] = [round($x, 1), round($y, 1)];
        }

        $line = implode(' ', array_map(fn (array $p) => $p[0] . ',' . $p[1], $points));

        return [
            'width' => $width,
            'height' => $height,
            'line' => $line,
            'area' => '0,' . $height . ' ' . $line . ' ' . $width . ',' . $height,
            'dot' => end($points),
        ];
    }

    /**
     * Two-line growth chart with grid, axis labels and dots.
     *
     * @param  array<string, mixed>  $growth
     * @return array<string, mixed>
     */
    public function growthChart(array $growth): array
    {
        $width = 700;
        $height = 280;
        $padLeft = 46;
        $padRight = 14;
        $padTop = 16;
        $padBottom = 34;

        $labels = $growth['labels'] ?? [];
        $count = max(1, count($labels));

        $max = (int) ($growth['max'] ?? 1);
        // round the axis up to a friendly step so the grid lines are readable
        $step = $this->niceStep($max);
        $top = max($step, (int) ceil($max / $step) * $step);

        $plotWidth = $width - $padLeft - $padRight;
        $plotHeight = $height - $padTop - $padBottom;

        $xAt = fn (int $index): float => round(
            $count > 1 ? $padLeft + ($index / ($count - 1)) * $plotWidth : $padLeft + $plotWidth / 2,
            1
        );

        $yAt = fn (int $value): float => round($padTop + $plotHeight - ($value / $top) * $plotHeight, 1);

        $build = function (array $series) use ($xAt, $yAt, $padTop, $plotHeight): array {
            $points = [];

            foreach (array_values($series) as $index => $value) {
                $points[] = [$xAt($index), $yAt((int) $value)];
            }

            $baseline = $padTop + $plotHeight;

            return [
                'line' => implode(' ', array_map(fn (array $p) => $p[0] . ',' . $p[1], $points)),
                'area' => count($points)
                    ? $points[0][0] . ',' . $baseline . ' '
                        . implode(' ', array_map(fn (array $p) => $p[0] . ',' . $p[1], $points))
                        . ' ' . end($points)[0] . ',' . $baseline
                    : '',
                'dots' => $points,
            ];
        };

        $grid = [];
        for ($value = 0; $value <= $top; $value += $step) {
            $grid[] = ['y' => $yAt($value), 'label' => number_format($value)];
        }

        // show roughly six x labels, always including the last one
        $every = max(1, (int) ceil($count / 6));
        $xLabels = [];
        foreach ($labels as $index => $label) {
            if ($index % $every === 0 || $index === $count - 1) {
                $xLabels[] = ['x' => $xAt((int) $index), 'label' => $label];
            }
        }

        return [
            'width' => $width,
            'height' => $height,
            'plot' => ['x' => $padLeft, 'y' => $padTop, 'width' => $plotWidth, 'height' => $plotHeight],
            'grid' => $grid,
            'x_labels' => $xLabels,
            'series' => [
                ['color' => $this->toneColor('blue'), 'label' => 'Total Users', 'data' => $build($growth['total_users'] ?? [])],
                ['color' => $this->toneColor('green'), 'label' => 'Active Users', 'data' => $build($growth['active_users'] ?? [])],
            ],
        ];
    }

    /**
     * Donut segments drawn with stroke-dasharray on a single circle radius.
     *
     * @param  array<string, mixed>  $gender
     * @return array<string, mixed>
     */
    public function donut(array $gender, int $size = 190, int $stroke = 30): array
    {
        $radius = ($size - $stroke) / 2;
        $circumference = 2 * M_PI * $radius;
        $total = max(1, (int) ($gender['total'] ?? 0));

        $offset = 0.0;
        $segments = [];

        foreach ($gender['segments'] ?? [] as $segment) {
            $share = ((int) $segment['value']) / $total;
            $length = $share * $circumference;

            $segments[] = [
                'label' => $segment['label'],
                'color' => $this->toneColor($segment['tone'] ?? 'slate'),
                'dash' => round($length, 2) . ' ' . round($circumference - $length, 2),
                'offset' => round(-$offset, 2),
            ];

            $offset += $length;
        }

        return [
            'size' => $size,
            'radius' => $radius,
            'stroke' => $stroke,
            'center' => $size / 2,
            'segments' => $segments,
        ];
    }

    /** Round an axis maximum to 1/2/5 x 10^n so grid labels stay clean. */
    protected function niceStep(int $max): int
    {
        if ($max <= 5) {
            return 1;
        }

        $magnitude = 10 ** (strlen((string) $max) - 1);

        foreach ([1, 2, 2.5, 5, 10] as $multiplier) {
            $step = (int) ($magnitude * $multiplier / 4);
            if ($step > 0 && $max / $step <= 6) {
                return max(1, $step);
            }
        }

        return max(1, (int) ($magnitude / 2));
    }

    /* ---------------------------------------------------------------- chrome */

    /** The reference screen brings its own heading, so Filament's is skipped. */
    public function getHeading(): string|Htmlable
    {
        return '';
    }
}
