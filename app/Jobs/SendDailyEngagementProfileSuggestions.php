<?php

namespace App\Jobs;

use App\Models\Profile;
use App\Services\OnboardingProfileSuggestionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDailyEngagementProfileSuggestions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $profileId;
    public int $daysRemaining;
    public int $minCount;
    public int $maxCount;

    /**
     * @var array<int, int>
     */
    public array $excludeProfileIds;

    /**
     * @param  array<int, int>  $excludeProfileIds
     */
    public function __construct(
        int $profileId,
        int $daysRemaining = 7,
        int $minCount = 2,
        int $maxCount = 4,
        array $excludeProfileIds = []
    ) {
        $this->profileId = $profileId;
        $this->daysRemaining = max(1, $daysRemaining);
        $this->minCount = max(1, $minCount);
        $this->maxCount = max($this->minCount, $maxCount);
        $this->excludeProfileIds = array_values(array_unique(array_map('intval', $excludeProfileIds)));
    }

    public function handle(OnboardingProfileSuggestionService $service): void
    {
        $profile = Profile::with(['user', 'preferences'])->find($this->profileId);
        $recipientEmail = trim((string) $profile?->user?->email);

        if (!$profile || $recipientEmail === '') {
            return;
        }

        $count = random_int($this->minCount, $this->maxCount);
        $sentProfileIds = $service->sendRandomSuggestions(
            $profile,
            $count,
            $this->excludeProfileIds,
            'daily_engagement'
        );

        if ($this->daysRemaining <= 1) {
            return;
        }

        $nextExcludeProfileIds = array_values(array_unique(array_map(
            'intval',
            array_merge($this->excludeProfileIds, $sentProfileIds)
        )));

        if (count($nextExcludeProfileIds) > 500) {
            $nextExcludeProfileIds = array_slice($nextExcludeProfileIds, -500);
        }

        self::dispatch(
            $this->profileId,
            $this->daysRemaining - 1,
            $this->minCount,
            $this->maxCount,
            $nextExcludeProfileIds
        )
            ->onConnection('database')
            ->onQueue('emails')
            ->delay(now()->addDay());
    }
}
