<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('Kennzeichen');
            $table->text('Fahrzeugklasse');
            $table->text('Automarke');
            $table->text('Typ');
            $table->text('Farbe');
            $table->text('Sonstiges');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
