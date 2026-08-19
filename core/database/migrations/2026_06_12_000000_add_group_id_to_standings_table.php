<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standings', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable()->index()->after('round_id');
        });
    }

    public function down(): void
    {
        Schema::table('standings', function (Blueprint $table) {
            $table->dropColumn('group_id');
        });
    }
};
