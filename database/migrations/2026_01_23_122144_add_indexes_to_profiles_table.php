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
                'profiles_gender_index' => 'gender',
                'profiles_marital_status_index' => 'marital_status',
                'profiles_religion_index' => 'religion',
                'profiles_city_index' => 'city',
                'profiles_profession_index' => 'profession',
                'profiles_status_index' => 'status',
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $indexes = [
                'profiles_gender_index',
                'profiles_marital_status_index',
                'profiles_religion_index',
                'profiles_city_index',
                'profiles_profession_index',
                'profiles_status_index',
            ];

            foreach ($indexes as $indexName) {
                try {
                    $table->dropIndex($indexName);
                } catch (\Exception $e) {
                    // Index doesn't exist
                }
            }
        });
    }
};
