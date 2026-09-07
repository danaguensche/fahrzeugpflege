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
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['Fahrzeugklasse']);
            
            $table->string('Fahrzeugklasse')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->unsignedInteger('Fahrzeugklasse')->nullable()->change();
            
            $table->foreign('Fahrzeugklasse')->references('id')->on('cargroups')->onDelete('set null');
        });
    }
};