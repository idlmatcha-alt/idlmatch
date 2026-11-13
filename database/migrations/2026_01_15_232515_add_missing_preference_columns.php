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
            // Add singular marital status if missing
            if (!Schema::hasColumn('preferences', 'desired_marital_status')) {
                $table->string('desired_marital_status')->nullable();
            }
            if (!Schema::hasColumn('preferences', 'desired_religion')) {
                $table->string('desired_religion')->nullable();
            }
            if (!Schema::hasColumn('preferences', 'desired_sect')) {
                $table->string('desired_sect')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('preferences', function (Blueprint $table) {
            $table->dropColumn(['desired_marital_status', 'desired_religion', 'desired_sect']);
        });
    }
};
