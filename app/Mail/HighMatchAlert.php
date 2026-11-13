<?php

namespace App\Mail;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HighMatchAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $matchProfile;
    public $matchPercentage;

    /**
     * Create a new message instance.
     */
    public function __construct(Profile $matchProfile, int $matchPercentage)
    {
        $this->matchProfile = $matchProfile;
        $this->matchPercentage = $matchPercentage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We Found a High Match for You! (' . $this->matchPercentage . '%)',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.matches.high_alert',
            with: [
                'matchName' => $this->matchProfile->first_name ?? 'Profile',
                'matchPercentage' => $this->matchPercentage,
                'profileUrl' => route('profiles.show', $this->matchProfile->display_id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
