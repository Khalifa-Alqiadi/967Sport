<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixture_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->nullable()->unique();
            $table->unsignedBigInteger('fixture_id')->index();
            $table->unsignedBigInteger('team_id')->nullable()->index();

            // goal/card actor. For the current phase this is the goal scorer.
            $table->unsignedBigInteger('player_id')->nullable()->index();
            $table->unsignedBigInteger('assist_player_id')->nullable()->index();

            // Reserved for the substitution phase requested for later.
            $table->unsignedBigInteger('player_in_id')->nullable()->index();
            $table->unsignedBigInteger('player_out_id')->nullable()->index();

            $table->string('type', 40)->index();
            $table->unsignedBigInteger('type_id')->nullable()->index();
            $table->unsignedBigInteger('sub_type_id')->nullable()->index();
            $table->unsignedBigInteger('period_id')->nullable()->index();

            $table->unsignedSmallInteger('minute')->nullable();
            $table->unsignedSmallInteger('extra_minute')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();

            $table->string('result', 20)->nullable();
            $table->string('info')->nullable();
            $table->string('addition')->nullable();
            $table->boolean('is_own_goal')->default(false);
            $table->boolean('is_penalty')->default(false);
            $table->boolean('rescinded')->default(false);
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['fixture_id', 'type', 'sort_order'], 'fixture_events_timeline_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixture_events');
    }
};
