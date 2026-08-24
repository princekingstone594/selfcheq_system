<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The Evening Examen is a guided end-of-day ritual, distinct from the
     * freeform Journal. One entry per user per day.
     */
    public function up(): void
    {
        Schema::create('examens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // ⭐ Mood rating (1–5): emoji picker for "how did this day go"
            $table->unsignedTinyInteger('mood_rating')->nullable();

            // 🏔 One high point of the day (short)
            $table->string('high_point')->nullable();

            // 💭 Reflection — where did I feel closest to my purpose today?
            $table->text('reflection')->nullable();

            // 🙏 One thing I'm grateful for
            $table->string('gratitude')->nullable();

            // 🌙 "I release today's struggles"
            $table->boolean('released')->default(true);

            $table->date('date');
            $table->timestamps();

            // One examen per user per day
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examens');
    }
};