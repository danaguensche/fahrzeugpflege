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
        Schema::table('auftraege', function (Blueprint $table) {
            $table->dropColumn(['cleaning_start', 'cleaning_end']);
            $table->float('cleaning_time')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auftraege', function (Blueprint $table) {
            $table->dropColumn('cleaning_time');
            $table->dateTime('cleaning_start')->nullable();
            $table->dateTime('cleaning_end')->nullable();
        });
    }
};
