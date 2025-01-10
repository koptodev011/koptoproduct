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
        Schema::create('productunitconversions', function (Blueprint $table) {
            $table->id();
            $table->integer('conversion_rate');
            $table->foreignId('baseunit_id')->constrained('productbaseunits')->onDelete('cascade');
            $table->foreignId('secondaryunit_id')->constrained('productsecondaryunits')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productunitconversions');
    }
};
