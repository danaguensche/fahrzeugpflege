<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auftraege', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mitarbeiter_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('azubi_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();

            $table->enum('status', [
                'ausstehend',
                'in_bearbeitung',
                'abgeschlossen',
            ])->default('ausstehend');

            $table->timestamp('scheduled_at')->nullable();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('car_id')
                ->constrained('cars')
                ->restrictOnDelete();

            $table->decimal('cleaning_time', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auftraege');
    }
};