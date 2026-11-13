<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriberPosterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $posterPath;

    public function __construct()
    {
        $this->posterPath = storage_path('app/public/newsletter/idlmatch-poster.jpg');
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Find Your Perfect Life Partner — IDLMatch.com',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.subscriber-poster',
        );
    }

    public function attachments()
    {
        return [];
    }
}
