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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('company');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('phonenumber');
            $table->string('adressline');
            $table->string('postalcode');
            $table->string('city');
        });
    }
    

    public function down(): void
    {
        //
    }
};
