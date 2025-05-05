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
            $table->dropForeign('cars_fahrzeugklasse_foreign');
            $table->dropIndex('cars_fahrzeugklasse_foreign');
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->foreign('Fahrzeugklasse')->references('id')->on('car_group_subgroups')->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign('cars_fahrzeugklasse_foreign');
            $table->dropIndex('cars_fahrzeugklasse_foreign');
            $table->foreign('Fahrzeugklasse')->references('id')->on('car_groups')->restrictOnDelete()->cascadeOnUpdate();
            //
        });
    }
};
