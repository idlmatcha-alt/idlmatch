<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanDatabaseForLaunch extends Command
{
    protected $signature = 'db:clean-for-launch {--force : Skip confirmation prompt}';

    protected $description = 'Clear all user data for go-live, keeping admin accounts, plans, locations, and blog posts.';

    public function handle(): int
    {
        $this->warn('⚠️  This will DELETE all non-admin user data from the database!');
        $this->info('The following will be PRESERVED:');
        $this->line('  ✓ Admin users (role = admin)');
        $this->line('  ✓ Plans');
        $this->line('  ✓ Countries, States, Cities');
        $this->line('  ✓ Blog posts');
        $this->newLine();
        $this->info('The following will be DELETED:');
        $this->line('  ✗ All non-admin users');
        $this->line('  ✗ All profiles, preferences, photos');
        $this->line('  ✗ All interests, favorites, blocks');
        $this->line('  ✗ All threads, messages');
        $this->line('  ✗ All reports, subscriptions');
        $this->line('  ✗ All success stories, saved searches, profile views');
        $this->line('  ✗ All queued jobs, failed jobs');

        if (!$this->option('force') && !$this->confirm('Are you sure you want to proceed?')) {
            $this->info('Aborted.');
            return Command::SUCCESS;
        }

        Schema::disableForeignKeyConstraints();

        // 1. Clear all user-interaction tables (order doesn't matter with FK disabled)
        $tablesToTruncate = [
            'messages',
            'threads',
            'interests',
            'favorites',
            'blocks',
            'reports',
            'saved_searches',
            'profile_views',
            'photos',
            'preferences',
            'subscriptions',
            'success_stories',
            'jobs',
            'failed_jobs',
            'personal_access_tokens',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("  ✓ Truncated: {$table}");
            }
        }

        // 2. Delete all profiles (soft-deleted and active)
        if (Schema::hasTable('profiles')) {
            DB::table('profiles')->truncate();
            $this->line('  ✓ Truncated: profiles');
        }

        // 3. Delete all non-admin users
        $adminCount = DB::table('users')->whereRaw("LOWER(TRIM(COALESCE(role, ''))) = 'admin'")->count();
        DB::table('users')->whereRaw("LOWER(TRIM(COALESCE(role, ''))) != 'admin'")->delete();
        $this->line("  ✓ Deleted non-admin users (kept {$adminCount} admin(s))");

        // 4. Reset auto-increment on profiles so new profiles start fresh
        if (Schema::hasTable('profiles')) {
            DB::statement('ALTER TABLE profiles AUTO_INCREMENT = 1');
        }

        Schema::enableForeignKeyConstraints();

        $this->newLine();
        $this->info('✅ Database cleaned successfully! Ready for go-live.');
        $this->info("   Admin accounts preserved: {$adminCount}");

        // Show preserved admin emails
        $admins = DB::table('users')->whereRaw("LOWER(TRIM(COALESCE(role, ''))) = 'admin'")->pluck('email');
        foreach ($admins as $email) {
            $this->line("   → {$email}");
        }

        return Command::SUCCESS;
    }
}
