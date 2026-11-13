<?php

namespace App\Console\Commands;

use App\Models\Profile;
use App\Services\OnboardingProfileSuggestionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyProfileSuggestions extends Command
{
    protected $signature = 'profiles:send-daily-suggestions
                            {--min=5 : Minimum number of profiles to send}
                            {--max=10 : Maximum number of profiles to send}';

    protected $description = 'Send daily random opposite-gender profile suggestions to all active users.';

    public function handle(OnboardingProfileSuggestionService $service): int
    {
        $minCount = max(1, (int) $this->option('min'));
        $maxCount = max($minCount, (int) $this->option('max'));

        $this->info("Starting daily profile suggestions ({$minCount}-{$maxCount} per user)...");

        $sent = 0;
        $skipped = 0;
        $failed = 0;

        Profile::query()
            ->where('status', 'active')
            ->whereHas('user', function ($query) {
                $query->whereNotNull('email')
                    ->where('email', '!=', '');
            })
            ->with(['user', 'preferences'])
            ->chunk(100, function ($profiles) use ($service, $minCount, $maxCount, &$sent, &$skipped, &$failed) {
                foreach ($profiles as $profile) {
                    if (!$profile->user) {
                        $skipped++;
                        continue;
                    }

                    try {
                        $count = random_int($minCount, $maxCount);
                        $sentIds = $service->sendRandomSuggestions(
                            $profile,
                            $count,
                            [],
                            'daily_engagement'
                        );

                        if (count($sentIds) > 0) {
                            $sent++;
                            $this->line("  ✓ Sent " . count($sentIds) . " suggestions to profile #{$profile->id}");
                        } else {
                            $skipped++;
                            $this->line("  – No candidates found for profile #{$profile->id}");
                        }
                    } catch (\Throwable $e) {
                        $failed++;
                        Log::error("Daily profile suggestion failed for profile #{$profile->id}: " . $e->getMessage());
                        $this->error("  ✗ Failed for profile #{$profile->id}: " . $e->getMessage());
                    }
                }
            });

        $this->info("Done. Sent: {$sent}, Skipped: {$skipped}, Failed: {$failed}");

        return Command::SUCCESS;
    }
}
