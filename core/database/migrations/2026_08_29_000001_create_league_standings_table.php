<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('league_standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained()->cascadeOnDelete();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->string('scope_key', 40)->default('overall');
            $table->string('group_name')->nullable();

            $table->unsignedSmallInteger('position')->default(0)->index();
            $table->unsignedSmallInteger('played')->default(0);
            $table->unsignedSmallInteger('won')->default(0);
            $table->unsignedSmallInteger('draw')->default(0);
            $table->unsignedSmallInteger('lost')->default(0);
            $table->unsignedSmallInteger('goals_for')->default(0);
            $table->unsignedSmallInteger('goals_against')->default(0);
            $table->smallInteger('goal_difference')->default(0);

            $table->smallInteger('base_points')->default(0);
            $table->smallInteger('fixture_points_adjustment')->default(0);
            $table->smallInteger('manual_points_adjustment')->default(0);
            $table->smallInteger('points')->default(0)->index();
            $table->unsignedSmallInteger('recent_form_points')->default(0);
            $table->string('form', 10)->nullable();
            $table->timestamp('calculated_at')->nullable()->index();
            $table->timestamps();

            $table->unique(
                ['league_id', 'season_id', 'scope_key', 'team_id'],
                'league_standings_scope_team_unique'
            );
            $table->index(['league_id', 'season_id', 'scope_key', 'position'], 'league_standings_table_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('league_standings');
    }
};
