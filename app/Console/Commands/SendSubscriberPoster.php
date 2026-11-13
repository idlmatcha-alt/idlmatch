<?php

namespace App\Console\Commands;

use App\Mail\SubscriberPosterMail;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendSubscriberPoster extends Command
{
    protected $signature = 'newsletter:poster
        {--test= : Send the email to a single address instead of all subscribers}
        {--preview : Write an HTML preview to public/newsletter-preview.html without sending}';

    protected $description = 'Email the full IDLMatch poster to active subscribers.';

    public function handle()
    {
        $posterPath = storage_path('app/public/newsletter/idlmatch-poster.jpg');

        if (! is_file($posterPath)) {
            $this->error('Poster image missing at ' . $posterPath);
            return self::FAILURE;
        }

        if ($this->option('preview')) {
            $html = view('emails.subscriber-poster', [
                'posterPath' => $posterPath,
                'posterUrl' => url('storage/newsletter/idlmatch-poster.jpg'),
            ])->render();

            file_put_contents(public_path('newsletter-preview.html'), $html);
            $this->info('Preview written to ' . url('newsletter-preview.html'));

            return self::SUCCESS;
        }

        if ($to = $this->option('test')) {
            Mail::to($to)->send(new SubscriberPosterMail());
            $this->info("Test email sent to {$to} using mailer [" . config('mail.default') . '].');

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        $emails = User::query()
            ->where('status', 'active')
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->pluck('email')
            ->merge(Subscriber::query()->pluck('email'))
            ->map(fn ($email) => Str::lower(trim((string) $email)))
            ->filter()
            ->unique()
            ->values();

        $this->info('Sending to ' . $emails->count() . ' unique address(es)...');

        foreach ($emails->chunk(100) as $chunk) {
            foreach ($chunk as $email) {
                try {
                    Mail::to($email)->send(new SubscriberPosterMail());
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::error('Subscriber poster email failed', [
                        'email' => $email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("Subscriber poster sent to {$sent} address(es), {$failed} failed, using mailer [" . config('mail.default') . '].');

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
