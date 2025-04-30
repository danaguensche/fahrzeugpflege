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
        //
        Schema::create('order_extra_charges', function (Blueprint $table) {
            $table->id()->primary()->autoIncrement();
            $table->foreignId('id_order')->constrained(
                table: 'orders',
                column: 'id'
            )->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('id_extra_charge')->constrained(
                table: 'extra_charges',
                column: 'id'
            )->restrictOnDelete()->cascadeOnUpdate();
        });

        Schema::table('extra_charges', function (Blueprint $table) {
            $table->dropForeign('extra_charges_id_order_foreign');
            $table->dropIndex('extra_charges_id_order_foreign');
            $table->dropColumn('id_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('order_extra_charges');
        Schema::table('extra_charges', function (Blueprint $table) {
            $table->foreignId('id_order')->after('id')->constrained(
                table: 'orders',
                column: 'id'
            )->cascadeOnDelete()->cascadeOnUpdate();
        });
    }
};
