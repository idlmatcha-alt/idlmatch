<?php

namespace App\Console\Commands;


use App\Models\Profile;
use App\Mail\HighMatchAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendMatchAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match:send-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email alerts for matches with >80% compatibility.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting match alerts...');
        
        // chunking to avoid memory issues
        Profile::where('status', 'active')->chunk(100, function ($profiles) {
            foreach ($profiles as $profile) {
                if (!$profile->user) continue;

                try {
                    // Find potential matches
                    $matches = Profile::where('id', '!=', $profile->id)
                        ->where('status', 'active')
                        ->where('gender', '!=', $profile->gender) // Basic matching
                        ->get();

                    foreach ($matches as $match) {
                        $percentage = $this->calculateMatchPercentage($profile, $match);

                        if ($percentage >= 80) {
                            // Check if we already alerted (optional: prevents spamming same match)
                            // For MVP, we'll just send it. ideally we'd track sent alerts.
                            
                            // Send Email
                            Mail::to($profile->user->email)->send(new HighMatchAlert($match, $percentage));
                            $this->info("Sent alert to {$profile->id} for match {$match->id} ({$percentage}%)");
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to process match alerts for profile {$profile->id}: " . $e->getMessage());
                }
            }
        });

        $this->info('Match alerts completed.');
        return Command::SUCCESS;
    }

    private function calculateMatchPercentage(Profile $p1, Profile $p2): int
    {
        // Reuse the logic from Profile model or replicate it here
        // If Profile::calculateMatchPercentage($other) exists, use it.
        // Assuming it's instance method based on previous context.
        return $p1->calculateMatchPercentage($p2);
    }
}

