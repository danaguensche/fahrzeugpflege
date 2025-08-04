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
        Schema::table('comments', function (Blueprint $table) {
            // Drop the old foreign key constraint if it exists
            $table->dropForeign(['job_id']);

            // Rename the column
            $table->renameColumn('job_id', 'order_id');

            // Add the new foreign key constraint referencing the 'auftraege' table
            $table->foreign('order_id')->references('id')->on('auftraege')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['order_id']);

            // Rename the column back
            $table->renameColumn('order_id', 'job_id');

            // Add the old foreign key constraint back
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
        });
    }
};