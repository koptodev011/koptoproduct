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
        Schema::table('productcategories', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->after('id'); // Add tenant_id column
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productcategories', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']); // Drop foreign key
            $table->dropColumn('tenant_id'); // Drop column
        });
    }
};
