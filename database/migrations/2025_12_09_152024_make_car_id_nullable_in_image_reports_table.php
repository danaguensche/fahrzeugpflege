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
        Schema::table('image_reports', function (Blueprint $table) {
            $table->foreignId('car_id')
                ->nullable()
                ->after('job_id')
                ->constrained('cars')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('image_reports', function (Blueprint $table) {
            $table->dropForeign(['car_id']);  // ✅ Foreign Key zuerst droppen
            $table->dropColumn('car_id');
        });
    }
};
