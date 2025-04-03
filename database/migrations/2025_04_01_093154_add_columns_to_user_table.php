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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phonenumber', 20)->nullable()->after('email');
            $table->string('addressline', 255)->nullable()->after('phonenumber');
            $table->string('postalcode', 10)->nullable()->after('addressline');
            $table->string('city', 255)->nullable()->after('postalcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn([
                'phonenumber',
                'addressline',
                'postalcode',
                'city'
            ]);
        });
    }
};
