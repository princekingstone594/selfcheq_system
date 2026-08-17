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
            if (!Schema::hasColumn('users', 'streak')) {
                $table->integer('streak')->default(0);
            }
            if (!Schema::hasColumn('users', 'last_completed_date')) {
                $table->date('last_completed_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'streak')) {
                $table->dropColumn('streak');
            }
            if (Schema::hasColumn('users', 'last_completed_date')) {
                $table->dropColumn('last_completed_date');
            }
        });
    }
};
