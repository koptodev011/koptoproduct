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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('invoice_no')->unique()->nullable()->after('id');
            $table->date('invoice_date')->nullable()->after('po_date');
            $table->date('due_date')->nullable()->after('invoice_date');
            $table->string('reference_no')->nullable()->after('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'invoice_date', 'due_date', 'reference_no']);
        });
    }
};
