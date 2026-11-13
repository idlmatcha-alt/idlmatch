<?php

namespace App\Mail;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OnboardingProfileSuggestion extends Mailable
{
    use Queueable, SerializesModels;

    public Profile $recipientProfile;
    public Profile $suggestedProfile;
    public ?int $matchPercentage;
    public string $campaign;

    public function __construct(
        Profile $recipientProfile,
        Profile $suggestedProfile,
        ?int $matchPercentage = null,
        string $campaign = 'initial'
    ) {
        $this->recipientProfile = $recipientProfile;
        $this->suggestedProfile = $suggestedProfile;
        $this->matchPercentage = $matchPercentage;
        $this->campaign = $campaign;
    }

    public function envelope(): Envelope
    {
        $subjectPrefix = match ($this->campaign) {
            'follow_up' => 'More Profile Suggestions For You',
            'daily_engagement' => 'Daily Profile Picks For You',
            default => 'New Profile Suggestion For You',
        };

        $matchSuffix = $this->matchPercentage !== null
            ? ' (' . $this->matchPercentage . '% Match)'
            : '';

        return new Envelope(
            subject: $subjectPrefix . $matchSuffix,
        );
    }

    public function content(): Content
    {
        $displayName = $this->suggestedProfile->first_name
            ?: ($this->suggestedProfile->user?->name ?? 'Suggested Profile');

        $recipientName = $this->recipientProfile->first_name
            ?: ($this->recipientProfile->user?->name ?? 'Member');

        $profileUrl = route('profiles.show', $this->suggestedProfile->display_id);

        $location = collect([
            $this->suggestedProfile->city,
            $this->suggestedProfile->country,
        ])->filter()->implode(', ');

        return new Content(
            markdown: 'emails.matches.profile_suggestion',
            with: [
                'recipientName' => $recipientName,
                'displayName' => $displayName,
                'displayId' => $this->suggestedProfile->display_id,
                'age' => $this->suggestedProfile->age,
                'profession' => $this->suggestedProfile->profession,
                'location' => $location,
                'matchPercentage' => $this->matchPercentage,
                'profileUrl' => $profileUrl,
                'campaign' => $this->campaign,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
