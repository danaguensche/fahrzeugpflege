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
            $table->unsignedBigInteger('trainer_id')->nullable()->after('id');
            $table->foreign('trainer_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('trainee_id')->nullable()->after('trainer_id');
            $table->foreign('trainee_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auftraege', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropColumn('trainer_id');
            $table->dropForeign(['trainee_id']);
            $table->dropColumn('trainee_id');
        });
    }
};
