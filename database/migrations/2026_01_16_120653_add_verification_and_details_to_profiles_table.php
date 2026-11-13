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
            $table->string('major_course')->nullable()->after('education');
            $table->string('cnic_front')->nullable()->after('privacy_json');
            $table->string('cnic_back')->nullable()->after('cnic_front');
            $table->string('selfie')->nullable()->after('cnic_back');
            $table->timestamp('cnic_verified_at')->nullable()->after('selfie');
            $table->string('verification_status')->default('pending')->after('cnic_verified_at'); // pending, verified, rejected
            $table->text('rejection_reason')->nullable()->after('verification_status');
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
                'major_course', 
                'cnic_front', 
                'cnic_back', 
                'selfie', 
                'cnic_verified_at', 
                'verification_status', 
                'rejection_reason'
            ]);
        });
    }
};
