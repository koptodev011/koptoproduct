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
        Schema::table('paymentins', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_unit_id')->nullable()->after('id');
            $table->foreign('tenant_unit_id')->references('id')->on('tenant_units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paymentins', function (Blueprint $table) {
            $table->dropForeign(['tenant_unit_id']);
            $table->dropColumn('tenant_unit_id');
        });
    }
};
