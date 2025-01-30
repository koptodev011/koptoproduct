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
        Schema::table('productonlinestores', function (Blueprint $table) {
            $table->integer('online_store_price')->nullable()->change();
            $table->string('online_product_description')->nullable()->change();
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productonlinestores', function (Blueprint $table) {
            $table->integer('online_store_price')->nullable(false)->change();
            $table->string('online_product_description')->nullable(false)->change();
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
