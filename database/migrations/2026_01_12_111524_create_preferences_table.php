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
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('desired_age_min')->nullable();
            $table->unsignedTinyInteger('desired_age_max')->nullable();
            $table->unsignedSmallInteger('desired_height_min')->nullable();
            $table->unsignedSmallInteger('desired_height_max')->nullable();
            $table->json('desired_countries')->nullable();
            $table->json('desired_cities')->nullable();
            $table->json('desired_sects')->nullable();
            $table->json('desired_education')->nullable();
            $table->json('desired_professions')->nullable();
            $table->json('desired_marital_statuses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preferences');
    }
};
