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
        Schema::table('images', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('job_id')->nullable()->after('car_id');
            $table->text('description')->nullable()->after('path');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->unsignedBigInteger('car_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['job_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('job_id');
            $table->dropColumn('description');
            $table->unsignedBigInteger('car_id')->nullable(false)->change();
        });
    }
};
