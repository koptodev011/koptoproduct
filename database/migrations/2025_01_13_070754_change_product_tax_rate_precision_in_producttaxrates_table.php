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
        Schema::table('producttaxrates', function (Blueprint $table) {
            Schema::table('producttaxrates', function (Blueprint $table) {
                $table->decimal('product_tax_rate', 8, 3)->change(); // Set precision and scale
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producttaxrates', function (Blueprint $table) {
            $table->decimal('product_tax_rate', 8, 3)->change();
        });
    }
};
