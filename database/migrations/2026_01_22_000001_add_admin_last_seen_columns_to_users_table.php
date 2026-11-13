<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('admin_last_seen_users_at')->nullable();
            $table->timestamp('admin_last_seen_subscriptions_at')->nullable();
            $table->timestamp('admin_last_seen_blog_posts_at')->nullable();
            $table->timestamp('admin_last_seen_reports_at')->nullable();
            $table->timestamp('admin_last_seen_verifications_at')->nullable();
            $table->timestamp('admin_last_seen_success_stories_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'admin_last_seen_users_at',
                'admin_last_seen_subscriptions_at',
                'admin_last_seen_blog_posts_at',
                'admin_last_seen_reports_at',
                'admin_last_seen_verifications_at',
                'admin_last_seen_success_stories_at',
            ]);
        });
    }
};
