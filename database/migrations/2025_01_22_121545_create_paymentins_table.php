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
        Schema::create('paymentins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->onDelete('cascade');
            $table->foreignId('payment_type_id')->constrained('salespaymenttypes')->onDelete('cascade');
            $table->string('add_description')->nullable();
            $table->string('paymentin_image')->nullable();
            $table->integer('received_amount');
            $table->string('type')->default('Payment-In');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paymentins');
    }
};
