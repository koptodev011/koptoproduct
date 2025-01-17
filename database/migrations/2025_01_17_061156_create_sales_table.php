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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->boolean('sale_type')->default(true);
            $table->foreignId('party_id')->constrained('parties')->onDelete('cascade');
            $table->string('billing_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->integer('po_number')->nullable();
            $table->string('po_date')->nullable();
            $table->integer('tax_amount')->nullable();
            $table->integer('received_amount')->nullable();
            $table->integer('payment_type')->nullable();
            $table->string('sale_description')->nullable();
            $table->string('sale_image')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
