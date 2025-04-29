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
        Schema::table('price_conditions', function (Blueprint $table) {
            //
            $table->string('condition', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_conditions', function (Blueprint $table) {
            //
            $table->enum('condition', ['einmalig', 'pro stunde', 'pro reifen'])->change();
        });
    }
};
