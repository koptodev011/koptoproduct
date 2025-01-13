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
        Schema::create('productpricings', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_price');
            $table->boolean('withorwithouttax')->default(true);
            $table->integer('discount')->default(0);
            $table->boolean('percentageoramount')->default(true);
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productpricings');
    }
};
