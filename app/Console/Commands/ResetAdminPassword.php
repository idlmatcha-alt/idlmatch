<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetAdminPassword extends Command
{
    protected $signature = 'user:reset-password {email} {password}';

    protected $description = 'Reset a users password';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($password);
        $user->save();

        $this->info("Password for user {$email} has been reset successfully.");
        return 0;
    }
}
