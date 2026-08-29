<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->boolean('counts_for_standings')->default(true)->after('is_finished')->index();
            $table->unsignedSmallInteger('standing_home_score')->nullable()->after('ft_away_score');
            $table->unsignedSmallInteger('standing_away_score')->nullable()->after('standing_home_score');
            $table->smallInteger('standing_home_points_adjustment')->default(0)->after('standing_away_score');
            $table->smallInteger('standing_away_points_adjustment')->default(0)->after('standing_home_points_adjustment');
            $table->string('standing_adjustment_notes', 500)->nullable()->after('standing_away_points_adjustment');
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropIndex(['counts_for_standings']);
            $table->dropColumn([
                'counts_for_standings',
                'standing_home_score',
                'standing_away_score',
                'standing_home_points_adjustment',
                'standing_away_points_adjustment',
                'standing_adjustment_notes',
            ]);
        });
    }
};
