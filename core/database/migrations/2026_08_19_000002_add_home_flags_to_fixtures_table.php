<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->boolean('is_home')->default(false)->index()->after('is_finished');
            $table->boolean('is_slider')->default(false)->index()->after('is_home');
        });
    }

    public function down(): void
    {
        Schema::table('fixtures', function (Blueprint $table) {
            $table->dropIndex(['is_home']);
            $table->dropIndex(['is_slider']);
            $table->dropColumn(['is_home', 'is_slider']);
        });
    }
};
