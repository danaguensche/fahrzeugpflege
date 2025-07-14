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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phonenumber')->nullable()->change();
            $table->string('addressline')->nullable()->change();
            $table->string('postalcode')->nullable()->change();
            $table->string('city')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('phonenumber')->nullable(false)->change();
            $table->string('addressline')->nullable(false)->change();
            $table->string('postalcode')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
        });
    }
};
