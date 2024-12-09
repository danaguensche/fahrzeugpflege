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
            $table->unsignedBigInteger('customer_id')->nullable(); 
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
