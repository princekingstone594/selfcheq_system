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
        // Drop the global unique constraint on 'date' and make it unique per user
        Schema::table('journals', function (Blueprint $table) {
            $table->dropUnique(['date']);
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'date']);
            $table->unique(['date']);
        });
    }
};