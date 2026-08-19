<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('season_topscorers', function (Blueprint $table) {
            $table->dropColumn(['player_name', 'player_image', 'team_name', 'team_image']);
        });
    }

    public function down(): void
    {
        Schema::table('season_topscorers', function (Blueprint $table) {
            $table->string('player_name')->nullable()->after('total');
            $table->string('player_image')->nullable()->after('player_name');
            $table->string('team_name')->nullable()->after('player_image');
            $table->string('team_image')->nullable()->after('team_name');
        });
    }
};
