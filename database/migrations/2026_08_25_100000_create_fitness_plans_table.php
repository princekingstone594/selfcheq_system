<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fitness_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('goal'); // lose_weight | build_muscle | endurance | general
            $table->string('level'); // beginner | intermediate | advanced
            $table->date('week_start');
            $table->json('plan'); // structured weekly workout + diet data
            $table->json('completed_days')->nullable(); // [0..6] indexes the user finished
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fitness_plans');
    }
};
