<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'subscriptions_status_created_at_index');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'blog_posts_status_created_at_index');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'reports_status_created_at_index');
        });

        Schema::table('success_stories', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'success_stories_status_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_status_created_at_index');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex('blog_posts_status_created_at_index');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_status_created_at_index');
        });

        Schema::table('success_stories', function (Blueprint $table) {
            $table->dropIndex('success_stories_status_created_at_index');
        });
    }
};
