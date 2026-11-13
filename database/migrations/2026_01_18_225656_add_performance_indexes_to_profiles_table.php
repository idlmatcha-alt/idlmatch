<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Add indexes for frequently queried columns (if not exists)
            $indexes = [
                'profiles_status_index' => 'status',
                'profiles_gender_index' => 'gender',
                'profiles_city_index' => 'city',
                'profiles_country_index' => 'country',
                'profiles_marital_status_index' => 'marital_status',
                'profiles_dob_index' => 'dob',
                'profiles_verification_status_index' => 'verification_status',
            ];
            
            foreach ($indexes as $indexName => $column) {
                if (DB::getDriverName() === 'sqlite') {
                    $exists = collect(DB::select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]))->count() > 0;
                    if (!$exists) {
                        $table->index($column, $indexName);
                    }
                    continue;
                }

                // Use raw SQL to check for index existence (works on MySQL/MariaDB without DBAL)
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
                'profiles_status_index',
                'profiles_gender_index',
                'profiles_city_index',
                'profiles_country_index',
                'profiles_marital_status_index',
                'profiles_dob_index',
                'profiles_verification_status_index',
            ];
            
            foreach ($indexes as $indexName) {
                try {
                    $table->dropIndex($indexName);
                } catch (\Exception $e) {
                    // Index doesn't exist, skip
                }
            }
        });
    }
};
