<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->unsignedBigInteger('fixture_id')->nullable()->after('section_id');
            $table->index('fixture_id', 'banners_fixture_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropIndex('banners_fixture_id_index');
            $table->dropColumn('fixture_id');
        });
    }
};
