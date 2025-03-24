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
        Schema::create('car_group_subgroups', function (Blueprint $table) {
            $table->id()->primary()->autoIncrement();
            $table->string('title', 100);
            $table->foreignId('id_car_group')->constrained(
                'car_groups',
                'id'
            )->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_group_subgroups');
    }
};
