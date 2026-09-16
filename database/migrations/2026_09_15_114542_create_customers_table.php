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
            $table->string('company', 255)->nullable();
            $table->string('firstname', 100)->nullable();
            $table->string('lastname', 100)->nullable();
            $table->string('email', 255)->nullable()->index();
            $table->string('phonenumber', 30)->nullable();
            $table->string('addressline', 255)->nullable();
            $table->string('postalcode', 5)->nullable();
            $table->string('city', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
