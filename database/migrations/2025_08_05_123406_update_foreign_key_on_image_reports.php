<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('image_reports', function (Blueprint $table) {
            // Fremdschlüssel zuerst löschen
            $table->dropForeign('image_reports_task_id_foreign'); // exakter FK-Name!

            // Neuen Fremdschlüssel hinzufügen, der auf 'auftraege' zeigt
            $table->foreign('job_id')
                ->references('id')->on('auftraege')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('image_reports', function (Blueprint $table) {
            // Neuen Fremdschlüssel entfernen
            $table->dropForeign(['job_id']);

            // Alten Fremdschlüssel wieder hinzufügen, falls nötig
            $table->foreign('job_id')
                ->references('id')->on('jobs')
                ->onDelete('cascade');
        });
    }
};
