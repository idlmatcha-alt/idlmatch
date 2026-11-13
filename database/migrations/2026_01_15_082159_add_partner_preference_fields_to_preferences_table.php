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
        Schema::table('preferences', function (Blueprint $table) {
            if (!Schema::hasColumn('preferences', 'desired_education')) {
                $table->string('desired_education')->nullable();
            }
            if (!Schema::hasColumn('preferences', 'desired_marital_status')) {
                $table->string('desired_marital_status')->nullable();
            }
            if (!Schema::hasColumn('preferences', 'desired_city')) {
                $table->string('desired_city')->nullable();
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
        Schema::table('preferences', function (Blueprint $table) {
            $table->dropColumn(['desired_education', 'desired_marital_status', 'desired_city']);
        });
    }
};
