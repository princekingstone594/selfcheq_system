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
        Schema::table('users', function (Blueprint $table) {
            $table->string('theme')->default('dark')->after('onboarding_complete');
            $table->boolean('notifications_enabled')->default(true)->after('theme');
            $table->boolean('contacts_enabled')->default(true)->after('notifications_enabled');
            $table->boolean('reminders_enabled')->default(true)->after('contacts_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['theme', 'notifications_enabled', 'contacts_enabled', 'reminders_enabled']);
        });
    }
};