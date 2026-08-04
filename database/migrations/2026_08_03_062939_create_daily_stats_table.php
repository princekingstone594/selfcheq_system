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
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();


            $table->integer('score')->default(0);
            $table->integer('tasks_completed')->default(0);
            $table->integer('tasks_total')->default(0);
            $table->integer('focus_minutes')->default(0);
            $table->integer('mood')->nullable();
            $table->boolean('journaled')->default(false);

            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
    }
};
