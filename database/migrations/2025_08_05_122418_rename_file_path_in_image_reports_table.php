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
            $table->renameColumn('file_path', 'path');
        });
    }

    public function down()
    {
        Schema::table('image_reports', function (Blueprint $table) {
            $table->renameColumn('path', 'file_path');
        });
    }
};
