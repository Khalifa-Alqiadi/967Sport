<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('season_topscorers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('season_id')->index();
            $table->unsignedBigInteger('player_id')->index();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('type', 30)->index();
            $table->unsignedSmallInteger('position')->default(0);
            $table->unsignedSmallInteger('total')->default(0);
            $table->string('player_name')->nullable();
            $table->string('player_image')->nullable();
            $table->string('team_name')->nullable();
            $table->string('team_image')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->timestamps();

            $table->unique(['season_id', 'player_id', 'type']);
            $table->index(['season_id', 'type', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_topscorers');
    }
};
