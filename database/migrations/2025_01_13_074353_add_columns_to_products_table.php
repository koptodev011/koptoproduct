<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_base_unit')->nullable();
            $table->unsignedBigInteger('product_secondary_unit')->nullable();
            $table->unsignedBigInteger('product_category_id')->nullable();
            
            // Foreign key constraints
            $table->foreign('product_base_unit')->references('id')->on('productbaseunits')->onDelete('set null');
            $table->foreign('product_secondary_unit')->references('id')->on('productsecondaryunits')->onDelete('set null');
            $table->foreign('product_category_id')->references('id')->on('productcategories')->onDelete('set null');
        });

        // Make all fields nullable
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_name')->nullable()->change();
            $table->string('product_hsn')->nullable()->change();
            $table->string('item_code')->nullable()->change();
            $table->string('description')->nullable()->change();
            $table->integer('mrp')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_base_unit']);
            $table->dropForeign(['product_secondary_unit']);
            $table->dropForeign(['product_category_id']);
            $table->dropColumn(['product_base_unit', 'product_secondary_unit', 'product_category_id']);

            // Revert nullable fields
            $table->string('product_name')->nullable(false)->change();
            $table->string('product_hsn')->nullable(false)->change();
            $table->string('item_code')->nullable(false)->change();
            $table->string('description')->nullable(false)->change();
            $table->integer('mrp')->nullable(false)->change();
        });
    }
}
