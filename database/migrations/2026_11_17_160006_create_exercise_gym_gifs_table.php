<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_gym_gifs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('muscle_group')->nullable();
            $table->string('equipment')->nullable();
            $table->string('difficulty', 32)->nullable();
            $table->string('gif_url');
            $table->json('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_gym_gifs');
    }
};