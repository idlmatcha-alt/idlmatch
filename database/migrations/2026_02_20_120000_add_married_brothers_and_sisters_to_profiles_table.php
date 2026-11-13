<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('profiles', 'married_brothers_count')) {
                $table->unsignedTinyInteger('married_brothers_count')->default(0)->after('brothers_count');
            }

            if (!Schema::hasColumn('profiles', 'married_sisters_count')) {
                $table->unsignedTinyInteger('married_sisters_count')->default(0)->after('sisters_count');
            }
        });

        DB::table('profiles')
            ->select(['id', 'brothers_count', 'married_siblings_count'])
            ->orderBy('id')
            ->chunkById(200, function ($profiles): void {
                foreach ($profiles as $profile) {
                    $brothers = max(0, (int) ($profile->brothers_count ?? 0));
                    $marriedTotal = max(0, (int) ($profile->married_siblings_count ?? 0));

                    $marriedBrothers = min($brothers, $marriedTotal);
                    $marriedSisters = max(0, $marriedTotal - $marriedBrothers);

                    DB::table('profiles')
                        ->where('id', $profile->id)
                        ->update([
                            'married_brothers_count' => $marriedBrothers,
                            'married_sisters_count' => $marriedSisters,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (Schema::hasColumn('profiles', 'married_brothers_count')) {
                $table->dropColumn('married_brothers_count');
            }

            if (Schema::hasColumn('profiles', 'married_sisters_count')) {
                $table->dropColumn('married_sisters_count');
            }
        });
    }
};

