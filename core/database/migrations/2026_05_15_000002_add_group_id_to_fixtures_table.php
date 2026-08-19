<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->unsignedBigInteger('stage_id')->nullable()->after('round_id')->index();
            $table->unsignedBigInteger('group_id')->nullable()->after('stage_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropColumn(['stage_id', 'group_id']);
        });
    }
};
