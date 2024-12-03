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
            $table->text('Fahrzeugklasse')->nullable();
            $table->text('Automarke')->nullable();
            $table->text('Typ')->nullable();
            $table->text('Farbe')->nullable();
            $table->text('Sonstiges')->nullable();
            $table->longText('image')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
