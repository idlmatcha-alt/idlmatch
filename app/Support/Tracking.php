<?php

namespace App\Support;

class Tracking
{
    /**
     * Queue a conversion event for the next rendered page. Controllers redirect
     * after a form posts, so the pixel call has to survive that hop.
     */
    public static function meta(string $event, array $params = []): void
    {
        if (!config('analytics.meta_pixel_id')) {
            return;
        }

        session()->push('tracking.meta', ['event' => $event, 'params' => $params]);
    }

    public static function consumeMeta(): array
    {
        return (array) session()->pull('tracking.meta', []);
    }
}
