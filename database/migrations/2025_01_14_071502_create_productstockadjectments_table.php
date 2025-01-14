<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductstockadjectmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productstockadjectments', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_quantity')->nullable();
            $table->integer('priceperunit')->nullable();
            $table->boolean('addorreduct_product_stock')->default(true);
            $table->string('details')->nullable();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productstockadjectments');
    }
}
