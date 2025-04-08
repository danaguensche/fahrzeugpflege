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
        Schema::create('car_groups', function (Blueprint $table) {
            $table->id()->primary()->autoIncrement(False);
            $table->string('title', 50)->nullable();
        });

        Schema::table('cars', function (Blueprint $table) {
            $table->unsignedBigInteger('Fahrzeugklasse')->change();
            $table->foreign('Fahrzeugklasse')->references('id')->on('car_groups')->restrictOnDelete()->cascadeOnUpdate();
        });
    
        Schema::create('services', function (Blueprint $table) {
            $table->id()->primary()->autoIncrement();
            $table->string('title', length: 150);
        });
        Schema::create('price_conditions', function (Blueprint $table) {
            $table->id()->primary()->autoIncrement();
            $table->enum('condition', ['einmalig', 'pro stunde', 'pro reifen']);
        });
        Schema::create('service_pricings', function (Blueprint $table) {
            $table->id()->primary()->autoIncrement();
            $table->foreignId('id_service')->constrained(
                table: 'services',
                column: 'id'
            )->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('id_car_group')->constrained(
                table: 'car_groups',
                column: 'id'
            )->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('price', 8, 2);
            $table->foreignId('id_price_condition')->constrained(
                table: 'price_conditions',
                column: 'id'
            )->restrictOnDelete()->cascadeOnUpdate();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->primary()->autoIncrement();
            $table->foreignId('id_customer')->constrained(
                table: 'customers',
                column: 'id'
            )->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('id_car')->constrained(
                'cars',
                'id'
            )->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId('id_service_pricing')->constrained(
                table: 'service_pricings',
                column: 'id'
            )->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamp('created_at')->useCurrent();
        });
        Schema::create('extra_charges', function (Blueprint $table) {
            $table->id()->primary()->autoIncrement();
            $table->foreignId('id_order')->constrained(
                table: 'orders',
                column: 'id'
            )->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('price', 8, 2);
            $table->foreignId('id_price_condition')->constrained(
                table: 'price_conditions',
                column: 'id'
            )->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign('cars_fahrzeugklasse_foreign');
            $table->dropIndex('cars_fahrzeugklasse_foreign');
            $table->text('Fahrzeugklasse')->nullable()->change();
        });
        Schema::dropIfExists('extra_charges');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('service_pricings');
        Schema::dropIfExists('services');
        Schema::dropIfExists('price_conditions');
        Schema::dropIfExists('car_groups');
        
    }
};
