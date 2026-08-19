<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('local_image')->nullable()->after('image_path');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->string('local_image')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('local_image');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn('local_image');
        });
    }
};
