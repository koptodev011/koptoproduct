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
        Schema::create('purchaseproducts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->foreignId('unit_id')->constrained('productbaseunits')->onDelete('cascade');
            $table->integer('priceperunit');
            $table->integer('discount_percentage')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->integer('tax_percentage')->nullable();
            $table->integer('tax_amount')->nullable();
            $table->integer('total_amount');
            $table->foreignId('purchase_id')->constrained('purchases')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchaseproducts');
    }
};
