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
        Schema::table('routines', function (Blueprint $table) {
            $table->unsignedBigInteger('reference_id')->nullable()->after('frequency');
            $table->string('reference_type')->nullable()->after('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->dropColumn(['reference_id', 'reference_type']);
        });
    }
};
