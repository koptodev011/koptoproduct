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
            $table->dropForeign(['product_secondary_unit_id']);
            $table->foreign('product_secondary_unit_id')
                  ->references('id')
                  ->on('productbaseunits') 
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productunitconversions', function (Blueprint $table) {
              $table->dropForeign(['product_secondary_unit_id']);
              $table->foreign('product_secondary_unit_id')
                    ->references('id')
                    ->on('productbaseunits') 
                    ->onDelete('cascade');
        });
    }
};
