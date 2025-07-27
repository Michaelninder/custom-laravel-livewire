<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Or a composite primary key
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('badge_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'badge_id']); // Ensure a user only gets a badge once
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};