<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportBanThankYou extends Mailable
{
    use Queueable, SerializesModels;

    public Report $report;

    public function __construct(Report $report)
    {
        $this->report = $report->loadMissing(['reporter.user', 'target.user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thanks for your report - Action taken',
        );
    }

    public function content(): Content
    {
        $targetProfile = $this->report->target;

        return new Content(
            markdown: 'emails.reports.thank-you',
            with: [
                'reporterName' => $this->report->reporter?->first_name ?: ($this->report->reporter?->display_id ?? 'Member'),
                'targetDisplayId' => $targetProfile?->display_id ?? 'N/A',
                'targetProfileUrl' => $targetProfile?->display_id ? route('profiles.show', $targetProfile->display_id) : null,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
