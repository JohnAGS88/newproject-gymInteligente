<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_checkins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('branch_id')->constrained('branches')->restrictOnDelete();
            $table->string('type', 16)->default('qr');
            $table->string('device_id')->nullable();
            $table->string('token_jti', 64)->unique();
            $table->timestamp('checked_in_at')->nullable();
            $table->string('status', 16)->default('valid');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_checkins');
    }
};