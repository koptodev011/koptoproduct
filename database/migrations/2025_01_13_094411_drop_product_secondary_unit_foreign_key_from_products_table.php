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
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_secondary_unit']);  // Drop foreign key
            $table->dropColumn('product_secondary_unit');    // Drop column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_secondary_unit')->nullable();
            $table->foreign('product_secondary_unit')->references('id')->on('related_table')->onDelete('cascade');
        });
    }
};
