<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('job_id')->after('id');

            $table->foreign('job_id')->references('id')->on('auftraege')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
            $table->dropColumn('job_id');
        });
    }
};
