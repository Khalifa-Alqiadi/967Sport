<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->unsignedSmallInteger('ht_home_score')->nullable()->after('away_score');
            $table->unsignedSmallInteger('ht_away_score')->nullable()->after('ht_home_score');
            $table->unsignedSmallInteger('et_home_score')->nullable()->after('ft_away_score');
            $table->unsignedSmallInteger('et_away_score')->nullable()->after('et_home_score');
            $table->unsignedSmallInteger('first_half_added_time')->nullable()->after('minute');
            $table->unsignedSmallInteger('second_half_added_time')->nullable()->after('first_half_added_time');
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn([
                'ht_home_score',
                'ht_away_score',
                'et_home_score',
                'et_away_score',
                'first_half_added_time',
                'second_half_added_time',
            ]);
        });
    }
};
