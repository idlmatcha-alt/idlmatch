<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('profiles', 'weight_kg')) {
                $table->string('weight_kg')->nullable()->after('height_cm');
            }
            if (!Schema::hasColumn('profiles', 'religiousness')) {
                $table->string('religiousness')->nullable()->after('disability');
            }
            if (!Schema::hasColumn('profiles', 'is_revert')) {
                $table->boolean('is_revert')->nullable()->after('religiousness');
            }
            if (!Schema::hasColumn('profiles', 'origin_country')) {
                $table->string('origin_country')->nullable()->after('country');
            }
            if (!Schema::hasColumn('profiles', 'origin_state')) {
                $table->string('origin_state')->nullable()->after('origin_country');
            }
            if (!Schema::hasColumn('profiles', 'origin_city')) {
                $table->string('origin_city')->nullable()->after('origin_state');
            }
            if (!Schema::hasColumn('profiles', 'willing_to_relocate')) {
                $table->string('willing_to_relocate')->nullable()->after('origin_city');
            }
            if (!Schema::hasColumn('profiles', 'looking_to_marry')) {
                $table->string('looking_to_marry')->nullable()->after('willing_to_relocate');
            }
        });

        Schema::table('preferences', function (Blueprint $table) {
            if (!Schema::hasColumn('preferences', 'desired_country')) {
                $table->string('desired_country')->nullable()->after('desired_height_max');
            }
            if (!Schema::hasColumn('preferences', 'desired_state')) {
                $table->string('desired_state')->nullable()->after('desired_country');
            }
            if (!Schema::hasColumn('preferences', 'desired_city')) {
                $table->string('desired_city')->nullable()->after('desired_state');
            }
            if (!Schema::hasColumn('preferences', 'desired_mother_tongue')) {
                $table->string('desired_mother_tongue')->nullable()->after('desired_city');
            }
            if (!Schema::hasColumn('preferences', 'desired_religion')) {
                $table->string('desired_religion')->nullable()->after('desired_mother_tongue');
            }
            if (!Schema::hasColumn('preferences', 'desired_sect')) {
                $table->string('desired_sect')->nullable()->after('desired_religion');
            }
            if (!Schema::hasColumn('preferences', 'desired_caste')) {
                $table->string('desired_caste')->nullable()->after('desired_sect');
            }
            if (!Schema::hasColumn('preferences', 'desired_marital_status')) {
                $table->string('desired_marital_status')->nullable()->after('desired_caste');
            }
            if (!Schema::hasColumn('preferences', 'desired_education')) {
                $table->string('desired_education')->nullable()->after('desired_marital_status');
            }
            if (!Schema::hasColumn('preferences', 'desired_profession')) {
                $table->string('desired_profession')->nullable()->after('desired_education');
            }
            if (!Schema::hasColumn('preferences', 'desired_religiousness')) {
                $table->string('desired_religiousness')->nullable()->after('desired_profession');
            }
            if (!Schema::hasColumn('preferences', 'desired_willing_to_relocate')) {
                $table->string('desired_willing_to_relocate')->nullable()->after('desired_religiousness');
            }
            if (!Schema::hasColumn('preferences', 'desired_living_arrangement')) {
                $table->string('desired_living_arrangement')->nullable()->after('desired_willing_to_relocate');
            }
            if (!Schema::hasColumn('preferences', 'desired_income')) {
                $table->string('desired_income')->nullable()->after('desired_living_arrangement');
            }
            if (!Schema::hasColumn('preferences', 'desired_disability')) {
                $table->string('desired_disability')->nullable()->after('desired_income');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $columns = [
                'weight_kg',
                'religiousness',
                'is_revert',
                'origin_country',
                'origin_state',
                'origin_city',
                'willing_to_relocate',
                'looking_to_marry',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('preferences', function (Blueprint $table) {
            $columns = [
                'desired_country',
                'desired_state',
                'desired_city',
                'desired_mother_tongue',
                'desired_religion',
                'desired_sect',
                'desired_caste',
                'desired_marital_status',
                'desired_education',
                'desired_profession',
                'desired_religiousness',
                'desired_willing_to_relocate',
                'desired_living_arrangement',
                'desired_income',
                'desired_disability',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('preferences', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
