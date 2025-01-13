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
        Schema::create('productbatchtrackings', function (Blueprint $table) {
            $table->id();
            $table->string('bathch_number');
            $table->string('manufacturing_date');
            $table->string('expiry_date');
            $table->integer('model_no');
            $table->string('size');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productbatchtrackings');
    }
};
