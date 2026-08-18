<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bible_chapters', function (Blueprint $table) {
            $table->id();
            $table->string('reference'); // e.g. "Psalms 91"
            $table->text('content');    // Full KJV chapter text
            $table->text('declaration')->nullable(); // Personalized first-person version
            $table->string('category')->default('declaration'); // declaration, reading, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bible_chapters');
    }
};
