<?php

namespace App\Jobs;

use App\Mail\OnboardingProfileSuggestion;
use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSingleProfileSuggestionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $recipientProfileId;
    public int $suggestedProfileId;
    public ?int $matchPercentage;
    public string $campaign;

    public function __construct(
        int $recipientProfileId,
        int $suggestedProfileId,
        ?int $matchPercentage = null,
        string $campaign = 'initial'
    ) {
        $this->recipientProfileId = $recipientProfileId;
        $this->suggestedProfileId = $suggestedProfileId;
        $this->matchPercentage = $matchPercentage;
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        $recipientProfile = Profile::with('user')->find($this->recipientProfileId);
        $suggestedProfile = Profile::with('user')->find($this->suggestedProfileId);

        $recipientEmail = trim((string) $recipientProfile?->user?->email);
        if (!$recipientProfile || !$suggestedProfile || $recipientEmail === '') {
            return;
        }

        Mail::to($recipientEmail)->send(
            new OnboardingProfileSuggestion(
                $recipientProfile,
                $suggestedProfile,
                $this->matchPercentage,
                $this->campaign
            )
        );
    }
}
