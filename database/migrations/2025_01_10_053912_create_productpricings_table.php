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
            $table->boolean('withorwithout_tax')->default(true);
            $table->integer('discount');
            $table->boolean('percentageoramount')->default(true);
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
