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
        Schema::create('producttaxrates', function (Blueprint $table) {
            $table->id();
            $table->string('product_tax_name');
            $table->integer('product_tax_rate');
            $table->foreignId('product_tax_group_id')->constrained('producttaxgroups'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producttaxrates');
    }
};
