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
            $table->foreignId('conversion_id')
            ->constrained('productunitconversions')
            ->onDelete('cascade');
        $table->foreignId('product_category_id')
            ->constrained('productcategories')
            ->onDelete('cascade');
        $table->foreignId('productpricings_id')
            ->constrained('productpricings')
            ->onDelete('cascade');
        $table->foreignId('wholesaleprice_id')
            ->constrained('wholesaleprices')
            ->onDelete('cascade');
        $table->foreignId('purchesprice_id')
            ->constrained('purches_prices')
            ->onDelete('cascade');
        $table->foreignId('tax_id')
            ->constrained('taxes')
            ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['conversion_id']);
            $table->dropColumn('conversion_id');
    
            $table->dropForeign(['product_category_id']);
            $table->dropColumn('product_category_id');
    
            $table->dropForeign(['productpricings_id']);
            $table->dropColumn('productpricings_id');
    
            $table->dropForeign(['wholesaleprice_id']);
            $table->dropColumn('wholesaleprice_id');
    
            $table->dropForeign(['purchesprice_id']);
            $table->dropColumn('purchesprice_id');
    
            $table->dropForeign(['tax_id']);
            $table->dropColumn('tax_id');
        });
    }
};
