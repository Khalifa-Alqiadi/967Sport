<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->integer('pen_home')->nullable()->after('ft_away_score');
            $table->integer('pen_away')->nullable()->after('pen_home');
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn(['pen_home', 'pen_away']);
        });
    }
};
