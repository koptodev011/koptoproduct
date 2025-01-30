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
        Schema::table('productpricings', function (Blueprint $table) {
            $table->integer('sale_price')->nullable()->change();
            $table->boolean('withorwithouttax')->nullable()->change();
            $table->integer('discount')->nullable()->change();
            $table->boolean('percentageoramount')->nullable()->change();
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productpricings', function (Blueprint $table) {
            $table->integer('sale_price')->nullable(false)->change();
            $table->boolean('withorwithouttax')->nullable(false)->change();
            $table->integer('discount')->nullable(false)->change();
            $table->boolean('percentageoramount')->nullable(false)->change();
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
