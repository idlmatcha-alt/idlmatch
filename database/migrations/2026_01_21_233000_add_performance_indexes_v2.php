<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $indexes = [
                'profiles_religion_index' => 'religion',
                'profiles_sect_index' => 'sect',
                'profiles_caste_index' => 'caste',
                'profiles_education_index' => 'education',
                'profiles_profession_index' => 'profession',
                'profiles_income_index' => 'income',
            ];

            foreach ($indexes as $indexName => $column) {
                if (DB::getDriverName() === 'sqlite') {
                    $exists = collect(DB::select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]))->count() > 0;
                    if (!$exists) {
                        $table->index($column, $indexName);
                    }
                    continue;
                }

                $indexExists = collect(DB::select("SHOW INDEXES FROM profiles WHERE Key_name = ?", [$indexName]))->count() > 0;
                if (!$indexExists) {
                    $table->index($column, $indexName);
                }
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            $indexName = 'messages_sender_profile_id_read_at_index';
            if (DB::getDriverName() === 'sqlite') {
                $exists = collect(DB::select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]))->count() > 0;
                if (!$exists) {
                    $table->index(['sender_profile_id', 'read_at'], $indexName);
                }
            } else {
                $indexExists = collect(DB::select("SHOW INDEXES FROM messages WHERE Key_name = ?", [$indexName]))->count() > 0;
                if (!$indexExists) {
                    $table->index(['sender_profile_id', 'read_at'], $indexName);
                }
            }
        });

        Schema::table('interests', function (Blueprint $table) {
            $indexName = 'interests_to_profile_id_read_at_index';
            if (DB::getDriverName() === 'sqlite') {
                $exists = collect(DB::select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]))->count() > 0;
                if (!$exists) {
                    $table->index(['to_profile_id', 'read_at'], $indexName);
                }
            } else {
                $indexExists = collect(DB::select("SHOW INDEXES FROM interests WHERE Key_name = ?", [$indexName]))->count() > 0;
                if (!$indexExists) {
                    $table->index(['to_profile_id', 'read_at'], $indexName);
                }
            }
        });

        Schema::table('profile_views', function (Blueprint $table) {
            $indexName = 'profile_views_viewer_id_created_at_index';
            if (DB::getDriverName() === 'sqlite') {
                $exists = collect(DB::select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]))->count() > 0;
                if (!$exists) {
                    $table->index(['viewer_id', 'created_at'], $indexName);
                }
            } else {
                $indexExists = collect(DB::select("SHOW INDEXES FROM profile_views WHERE Key_name = ?", [$indexName]))->count() > 0;
                if (!$indexExists) {
                    $table->index(['viewer_id', 'created_at'], $indexName);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $indexes = ['profiles_religion_index', 'profiles_sect_index', 'profiles_caste_index', 'profiles_education_index', 'profiles_profession_index', 'profiles_income_index'];
            foreach ($indexes as $index) {
                try {
                    $table->dropIndex($index);
                } catch (\Exception $e) {
                }
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            try {
                $table->dropIndex('messages_sender_profile_id_read_at_index');
            } catch (\Exception $e) {
            }
        });

        Schema::table('interests', function (Blueprint $table) {
            try {
                $table->dropIndex('interests_to_profile_id_read_at_index');
            } catch (\Exception $e) {
            }
        });

        Schema::table('profile_views', function (Blueprint $table) {
            try {
                $table->dropIndex('profile_views_viewer_id_created_at_index');
            } catch (\Exception $e) {
            }
        });
    }
};
