<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaches', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('team_id')->nullable()->index();
            $table->unsignedBigInteger('country_id')->nullable()->index();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('common_name')->nullable();
            $table->string('image_path')->nullable();
            $table->string('local_image')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
