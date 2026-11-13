<?php

namespace App\Mail;

use App\Models\Interest;
use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewInterestReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $interest;
    public $senderProfile;

    /**
     * Create a new message instance.
     */
    public function __construct(Interest $interest)
    {
        $this->interest = $interest;
        // Eager load sender profile if not already loaded
        $this->senderProfile = $interest->fromProfile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Interest Received - IDL Match',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.interests.received',
            with: [
                'senderName' => $this->senderProfile->first_name ?? 'Someone',
                'profileUrl' => route('profiles.show', $this->senderProfile->display_id),
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
