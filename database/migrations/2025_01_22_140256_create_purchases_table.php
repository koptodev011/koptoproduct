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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->onDelete('cascade');
            $table->integer('phone_number');
            $table->integer('po_number');
            $table->string('po_date');
            $table->integer('total_amount');
            $table->integer('paid_amount');
            $table->foreignId('payment_type_id')->constrained('salespaymenttypes')->onDelete('cascade');
            $table->string('purchase_description')->nullable();
            $table->string('purchase_image')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
