<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportBannedNotice extends Mailable
{
    use Queueable, SerializesModels;

    public Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report->loadMissing(['target.user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your account has been blocked',
        );
    }

    public function content(): Content
    {
        $supportPhone = '923122294445';
        $targetProfile = $this->report->target;

        $supportText = 'Assalam o Alaikum. My account was blocked. Please review my case. Profile ID: '
            . ($targetProfile?->display_id ?? 'N/A');

        return new Content(
            markdown: 'emails.reports.banned-notice',
            with: [
                'memberName' => $targetProfile?->first_name ?: ($targetProfile?->display_id ?? 'Member'),
                'profileId' => $targetProfile?->display_id ?? 'N/A',
                'supportWhatsappUrl' => 'https://api.whatsapp.com/send/?phone=' . $supportPhone . '&text=' . urlencode($supportText) . '&type=phone_number&app_absent=0',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
