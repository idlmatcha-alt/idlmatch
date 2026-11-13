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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('display_id')->unique();
            $table->string('gender', 20);
            $table->date('dob');
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->string('sect', 100)->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('education', 150)->nullable();
            $table->string('profession', 150)->nullable();
            $table->string('phone', 40)->nullable();
            $table->text('family_details')->nullable();
            $table->text('bio')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 120)->nullable();
            $table->json('privacy_json')->nullable();
            $table->unsignedTinyInteger('completion_score')->default(0);
            $table->timestamp('last_active_at')->nullable();
            $table->string('status')->default('under_review');
            $table->index(['status', 'gender']);
            $table->index(['country', 'city']);
            $table->index('last_active_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
};
