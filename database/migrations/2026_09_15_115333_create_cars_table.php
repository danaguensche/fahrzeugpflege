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

            $table->string('license_plate', 20)->unique();

            $table->foreignId('car_group_id')
                ->constrained('car_groups')
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

            $table->string('brand', 100)->nullable();
            $table->string('type', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
