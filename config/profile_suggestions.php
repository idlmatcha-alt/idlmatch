<?php

return [
    'initial' => [
        'count' => (int) env('PROFILE_SUGGESTIONS_INITIAL_COUNT', 5),
        'window_minutes' => (int) env('PROFILE_SUGGESTIONS_INITIAL_WINDOW_MINUTES', 8),
    ],

    'daily_engagement' => [
        'enabled' => (bool) env('PROFILE_SUGGESTIONS_DAILY_ENABLED', true),
        'start_after_days' => (int) env('PROFILE_SUGGESTIONS_DAILY_START_AFTER_DAYS', 1),
        'campaign_days' => (int) env('PROFILE_SUGGESTIONS_DAILY_CAMPAIGN_DAYS', 7),
        'min_count' => (int) env('PROFILE_SUGGESTIONS_DAILY_MIN_COUNT', 5),
        'max_count' => (int) env('PROFILE_SUGGESTIONS_DAILY_MAX_COUNT', 10),
    ],
];
