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
        Schema::table('productstocks', function (Blueprint $table) {
            $table->integer('product_stock')->nullable()->change();
            $table->integer('at_price')->nullable()->change();
            $table->integer('min_stock')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productstocks', function (Blueprint $table) {
            $table->integer('product_stock')->nullable(false)->change();
            $table->integer('at_price')->nullable(false)->change();
            $table->integer('min_stock')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
