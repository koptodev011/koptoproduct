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
        Schema::create('shippingaddresses', function (Blueprint $table) {
            $table->id();
            $table->string('shipping_address');
            $table->unsignedBigInteger('party_id');
            $table->foreign('party_id')->references('id')->on('parties');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippingaddresses');
    }
};
