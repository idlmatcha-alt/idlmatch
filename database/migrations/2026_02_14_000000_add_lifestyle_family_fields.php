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
            // Lifestyle & Appearance
            $table->string('complexion')->nullable()->after('weight_kg');
            $table->string('body_type')->nullable()->after('complexion');
            $table->string('eating_habits')->nullable()->after('body_type');
            $table->string('smoking_habits')->nullable()->after('eating_habits');
            $table->string('drinking_habits')->nullable()->after('smoking_habits');
            $table->text('hobbies')->nullable()->after('drinking_habits');

            // Family Details
            $table->string('family_values')->nullable()->after('family_details');
            $table->string('family_type')->nullable()->after('family_values');
            $table->string('family_status')->nullable()->after('family_type'); // Middle Class, Upper Middle, etc
        });

        Schema::table('preferences', function (Blueprint $table) {
            $table->string('desired_complexion')->nullable()->after('desired_disability');
            $table->string('desired_body_type')->nullable()->after('desired_complexion');
            $table->string('desired_family_values')->nullable()->after('desired_body_type');
            $table->string('desired_eating_habits')->nullable()->after('desired_family_values');
            $table->string('desired_smoking_habits')->nullable()->after('desired_eating_habits');
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
            $columns = [
                'complexion', 'body_type', 'eating_habits', 'smoking_habits', 
                'drinking_habits', 'hobbies', 'family_values', 'family_type', 'family_status'
            ];
            
            if (DB::getDriverName() === 'sqlite') {
                 // SQLite doesn't support dropping multiple columns in one go usually, but Laravel might handle it.
                 // Safe way is one by one or just let Laravel try.
                 // For now, standard drop.
                 foreach ($columns as $column) {
                     if (Schema::hasColumn('profiles', $column)) {
                         $table->dropColumn($column);
                     }
                 }
            } else {
                $table->dropColumn($columns);
            }
        });

        Schema::table('preferences', function (Blueprint $table) {
            $columns = [
                'desired_complexion', 'desired_body_type', 'desired_family_values',
                'desired_eating_habits', 'desired_smoking_habits'
            ];
            
            if (DB::getDriverName() === 'sqlite') {
                 foreach ($columns as $column) {
                     if (Schema::hasColumn('preferences', $column)) {
                         $table->dropColumn($column);
                     }
                 }
            } else {
                $table->dropColumn($columns);
            }
        });
    }
};
