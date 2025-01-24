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
        Schema::create('paymentouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->onDelete('cascade');
            $table->foreignId('payment_type_id')->constrained('salespaymenttypes')->onDelete('cascade');
            $table->string('paymentout_description')->nullable();
            $table->string('paymentout_image')->nullable();
            $table->integer('paid_amount');
            $table->string('status')->default('Payment-Out');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymentouts');
    }
};
