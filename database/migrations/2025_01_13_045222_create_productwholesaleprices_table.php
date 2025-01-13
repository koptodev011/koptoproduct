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
        Schema::create('productwholesaleprices', function (Blueprint $table) {
            $table->id();
            $table->integer('whole_sale_price');
            $table->boolean('withorwithouttax')->default(true);
            $table->integer('wholesale_min_quantity');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productwholesaleprices');
    }
};
