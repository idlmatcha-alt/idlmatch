<?php

namespace App\Services;

use App\Jobs\SendSingleProfileSuggestionEmail;
use App\Mail\OnboardingProfileSuggestion;
use App\Models\Profile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OnboardingProfileSuggestionService
{
    /**
     * Queue initial onboarding suggestions across a short delivery window.
     *
     * @return array<int, int> Queued profile IDs.
     */
    public function queueInitialSuggestions(Profile $recipientProfile, int $count = 10, int $windowMinutes = 8): array
    {
        $recipientEmail = trim((string) $recipientProfile->user?->email);
        if ($recipientEmail === '' || $count <= 0) {
            return [];
        }

        $selection = $this->buildSelection($recipientProfile, $count, true, []);
        if ($selection->isEmpty()) {
            return [];
        }

        $total = $selection->count();
        $maxDelaySeconds = max(0, $windowMinutes * 60);
        $queuedProfileIds = [];

        foreach ($selection->values() as $index => $row) {
            /** @var Profile $candidate */
            $candidate = $row['profile'];
            $matchPercentage = $row['score'];

            $delaySeconds = $total <= 1
                ? 0
                : (int) floor(($maxDelaySeconds * $index) / max(1, $total - 1));

            SendSingleProfileSuggestionEmail::dispatch(
                $recipientProfile->id,
                $candidate->id,
                $matchPercentage,
                'initial'
            )
                ->onConnection('database')
                ->onQueue('emails')
                ->delay(now()->addSeconds($delaySeconds));

            $queuedProfileIds[] = $candidate->id;
        }

        return array_values(array_unique($queuedProfileIds));
    }

    /**
     * Send immediate onboarding suggestions (best matches first, fallback random).
     *
     * @return array<int, int> Sent profile IDs.
     */
    public function sendInitialSuggestions(Profile $recipientProfile, int $count = 10): array
    {
        return $this->sendSuggestions($recipientProfile, $count, true, [], 'initial');
    }

    /**
     * Send follow-up onboarding suggestions (random opposite-gender profiles).
     *
     * @param  array<int, int>  $excludeProfileIds
     * @return array<int, int> Sent profile IDs.
     */
    public function sendRandomSuggestions(
        Profile $recipientProfile,
        int $count,
        array $excludeProfileIds = [],
        string $campaign = 'follow_up'
    ): array {
        return $this->sendSuggestions($recipientProfile, $count, false, $excludeProfileIds, $campaign);
    }

    /**
     * @param  array<int, int>  $excludeProfileIds
     * @return array<int, int>
     */
    private function sendSuggestions(
        Profile $recipientProfile,
        int $count,
        bool $preferTopMatches,
        array $excludeProfileIds,
        string $campaign
    ): array {
        $recipientEmail = trim((string) $recipientProfile->user?->email);
        if ($recipientEmail === '' || $count <= 0) {
            return [];
        }

        $selection = $this->buildSelection($recipientProfile, $count, $preferTopMatches, $excludeProfileIds);
        if ($selection->isEmpty()) {
            return [];
        }

        $sentProfileIds = [];

        foreach ($selection as $row) {
            /** @var Profile $candidate */
            $candidate = $row['profile'];
            $matchPercentage = $row['score'];

            try {
                Mail::to($recipientEmail)->send(
                    new OnboardingProfileSuggestion($recipientProfile, $candidate, $matchPercentage, $campaign)
                );
                $sentProfileIds[] = $candidate->id;
            } catch (\Throwable $exception) {
                Log::error(
                    sprintf(
                        'Onboarding profile suggestion email failed (recipient_profile_id=%d, candidate_profile_id=%d): %s',
                        $recipientProfile->id,
                        $candidate->id,
                        $exception->getMessage()
                    )
                );
            }
        }

        return array_values(array_unique($sentProfileIds));
    }

    /**
     * @param  array<int, int>  $excludeProfileIds
     * @return Collection<int, array{profile: Profile, score: ?int}>
     */
    private function buildSelection(
        Profile $recipientProfile,
        int $count,
        bool $preferTopMatches,
        array $excludeProfileIds
    ): Collection {
        $targetGender = $this->oppositeGender($recipientProfile->gender);
        if ($targetGender === null || $count <= 0) {
            return collect();
        }

        $candidates = $this->candidateQuery($recipientProfile, $targetGender, $excludeProfileIds)
            ->with(['user', 'primaryPhoto'])
            ->limit(300)
            ->get();

        if ($candidates->isEmpty()) {
            return collect();
        }

        return $preferTopMatches
            ? $this->bestMatchSelection($recipientProfile, $candidates, $count)
            : $this->randomSelection($candidates, $count);
    }

    private function oppositeGender(?string $gender): ?string
    {
        $normalized = Str::lower(trim((string) $gender));

        if ($normalized === 'male') {
            return 'female';
        }

        if ($normalized === 'female') {
            return 'male';
        }

        return null;
    }

    /**
     * @param  array<int, int>  $excludeProfileIds
     */
    private function candidateQuery(Profile $recipientProfile, string $targetGender, array $excludeProfileIds)
    {
        $exclude = array_values(array_unique(array_filter(array_map('intval', $excludeProfileIds))));
        $exclude[] = $recipientProfile->id;

        return Profile::query()
            ->visibleToUsers()
            ->whereRaw("LOWER(TRIM(COALESCE(gender, ''))) = ?", [$targetGender])
            ->whereNotIn('id', $exclude)
            ->whereHas('user', function ($query) {
                $query->whereNotNull('email')
                    ->where('email', '!=', '');
            });
    }

    /**
     * @param  Collection<int, Profile>  $candidates
     * @return Collection<int, array{profile: Profile, score: ?int}>
     */
    private function bestMatchSelection(Profile $recipientProfile, Collection $candidates, int $count): Collection
    {
        $scored = $candidates
            ->map(function (Profile $candidate) use ($recipientProfile) {
                return [
                    'profile' => $candidate,
                    'score' => $this->safeMatchScore($candidate, $recipientProfile),
                ];
            })
            ->sortByDesc('score')
            ->values();

        $matched = $scored
            ->filter(fn (array $row) => (int) ($row['score'] ?? 0) > 0)
            ->take($count)
            ->values();

        if ($matched->count() >= $count) {
            return $matched;
        }

        $selectedIds = $matched
            ->pluck('profile.id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        $remaining = $candidates
            ->reject(fn (Profile $candidate) => in_array($candidate->id, $selectedIds, true))
            ->shuffle()
            ->take($count - $matched->count())
            ->values()
            ->map(fn (Profile $candidate) => [
                'profile' => $candidate,
                'score' => null,
            ]);

        return $matched->concat($remaining)->take($count)->values();
    }

    /**
     * @param  Collection<int, Profile>  $candidates
     * @return Collection<int, array{profile: Profile, score: ?int}>
     */
    private function randomSelection(Collection $candidates, int $count): Collection
    {
        return $candidates
            ->shuffle()
            ->take($count)
            ->values()
            ->map(fn (Profile $candidate) => [
                'profile' => $candidate,
                'score' => null,
            ]);
    }

    private function safeMatchScore(Profile $candidate, Profile $recipientProfile): int
    {
        try {
            $score = (int) $candidate->calculateMatchPercentage($recipientProfile);
            return max(0, min(100, $score));
        } catch (\Throwable $exception) {
            return 0;
        }
    }
}
