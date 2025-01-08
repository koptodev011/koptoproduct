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
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('business_type')->nullable()->change();
            $table->unsignedBigInteger('business_category')->nullable()->change();
            $table->unsignedBigInteger('state')->nullable()->change();

            $table->foreign('business_type')->references('id')->on('business_types');
            $table->foreign('business_category')->references('id')->on('business_categories');
            $table->foreign('state')->references('id')->on('states');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['business_type']);
            $table->dropForeign(['business_category']);
            $table->dropForeign(['state']);
        });
    }
};
