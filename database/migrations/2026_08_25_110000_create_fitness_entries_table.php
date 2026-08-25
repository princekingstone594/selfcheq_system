<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fitness_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // nutrition | workout | gym
            $table->string('title');
            $table->text('details')->nullable(); // e.g. meals, exercises, macros
            $table->unsignedTinyInteger('day_of_week')->nullable(); // 1=Mon..7=Sun, null = any day
            $table->foreignId('linked_task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->foreignId('linked_routine_id')->nullable()->constrained('routines')->nullOnDelete();
            $table->boolean('is_done')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fitness_entries');
    }
};
