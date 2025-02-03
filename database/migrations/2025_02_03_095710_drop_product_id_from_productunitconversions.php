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
        Schema::table('productunitconversions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);  // Drop foreign key constraint
            $table->dropColumn('product_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productunitconversions', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');  // Recreate the column if needed
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');  // Recreate the foreign key constraint
        });
    }
};
