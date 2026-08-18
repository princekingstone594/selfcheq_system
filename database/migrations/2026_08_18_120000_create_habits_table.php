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
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('emoji')->nullable();
            $table->text('description')->nullable();
            $table->integer('target_value')->nullable()->default(1);
            $table->string('unit')->nullable()->default('times');
            $table->string('frequency')->default('daily');
            $table->boolean('is_active')->default(true);
            $table->time('reminder_time')->nullable();
            $table->integer('xp_reward')->default(10);
            $table->timestamps();
        });

        Schema::create('habit_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('value')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_completions');
        Schema::dropIfExists('habits');
    }
};
