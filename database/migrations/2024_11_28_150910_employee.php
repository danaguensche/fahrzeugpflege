<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('phonenumber')->nullable();
            $table->string('addressline')->nullable();
            $table->string('postalcode')->nullable();
            $table->string('city')->nullable();
            $table->string('password');
            $table->integer('user_id');
            // $table->foreign('user_id')->references('id')->on('users');
            $table->rememberToken();
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
