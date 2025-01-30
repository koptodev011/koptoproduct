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
        Schema::table('productunitconversions', function (Blueprint $table) {
            $table->foreignId('product_base_unit_id')->nullable()->change();
            $table->foreignId('product_secondary_unit_id')->nullable()->change();
            $table->integer('conversion_rate')->nullable()->change();
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productunitconversions', function (Blueprint $table) {
            $table->foreignId('product_base_unit_id')->nullable(false)->change();
            $table->foreignId('product_secondary_unit_id')->nullable(false)->change();
            $table->integer('conversion_rate')->nullable(false)->change();
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
