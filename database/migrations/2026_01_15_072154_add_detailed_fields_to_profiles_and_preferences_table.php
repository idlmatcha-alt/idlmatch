<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->unsignedSmallInteger('weight_kg')->nullable()->after('height_cm');
            $table->string('disability')->nullable()->after('weight_kg');
            $table->string('college')->nullable()->after('education');
            $table->string('university')->nullable()->after('college');
            $table->string('job_type')->nullable()->after('profession'); // private/govt
            $table->string('income')->nullable()->after('job_type');
            $table->string('nature_of_job')->nullable()->after('income');
            $table->string('religion')->default('Islam')->after('sect');
            $table->string('caste')->nullable()->after('religion');
            $table->string('home_status')->nullable()->after('city'); // own/rent
            $table->string('home_size')->nullable()->after('home_status');
            $table->string('home_location')->nullable()->after('home_size');
            $table->text('other_properties')->nullable()->after('home_location');
            $table->string('father_name')->nullable()->after('family_details');
            $table->string('father_occupation')->nullable()->after('father_name');
            $table->string('mother_occupation')->default('Housewife')->after('father_occupation');
            $table->unsignedTinyInteger('brothers_count')->default(0)->after('mother_occupation');
            $table->unsignedTinyInteger('sisters_count')->default(0)->after('brothers_count');
            $table->unsignedTinyInteger('married_siblings_count')->default(0)->after('sisters_count');
            $table->string('home_town')->nullable()->after('city');
            $table->string('mobile_no')->nullable()->after('phone');
        });

        Schema::table('preferences', function (Blueprint $table) {
            $table->string('desired_caste')->nullable()->after('desired_marital_statuses');
            $table->text('other_requirements')->nullable()->after('desired_caste');
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
            $table->dropColumn([
                'weight_kg', 'disability', 'college', 'university', 'job_type', 
                'income', 'nature_of_job', 'religion', 'caste', 'home_status', 
                'home_size', 'home_location', 'other_properties', 'father_name', 
                'father_occupation', 'mother_occupation', 'brothers_count', 
                'sisters_count', 'married_siblings_count', 'home_town', 'mobile_no'
            ]);
        });

        Schema::table('preferences', function (Blueprint $table) {
            $table->dropColumn(['desired_caste', 'other_requirements']);
        });
    }
};
