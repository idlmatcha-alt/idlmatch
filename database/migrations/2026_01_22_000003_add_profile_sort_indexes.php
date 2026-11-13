<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'profiles_status_created_at_index');
            $table->index(['status', 'last_active_at'], 'profiles_status_last_active_at_index');
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->index('blocker_profile_id', 'blocks_blocker_profile_id_index');
            $table->index('blocked_profile_id', 'blocks_blocked_profile_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex('profiles_status_created_at_index');
            $table->dropIndex('profiles_status_last_active_at_index');
        });

        Schema::table('blocks', function (Blueprint $table) {
            $table->dropIndex('blocks_blocker_profile_id_index');
            $table->dropIndex('blocks_blocked_profile_id_index');
        });
    }
};
