<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnknownLogin extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ipAddress;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ipAddress
     * @return void
     */
    public function __construct($user, $ipAddress)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Unknown Login',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.auth.unknown_login',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
