<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('image_reports', function (Blueprint $table) {
            $table->renameColumn('task_id', 'job_id');
        });
    }

    public function down()
    {
        Schema::table('image_reports', function (Blueprint $table) {
            $table->renameColumn('job_id', 'task_id');
        });
    }
};
