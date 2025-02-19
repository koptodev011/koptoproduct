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
            $table->string('upload_document')->nullable(); // Column for uploading documents
            $table->decimal('total_amount', 10, 2); // Column for total amount
            $table->unsignedBigInteger('tenant_unit_id'); // Foreign key column
            $table->foreign('tenant_unit_id')->references('id')->on('tenant_units')->onDelete('cascade');
            $table->dropForeign(['user_id']); // Drop foreign key constraint
            $table->dropColumn('user_id'); // Drop user_id column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['tenant_unit_id']); // Drop foreign key
            $table->dropColumn(['upload_document', 'total_amount', 'tenant_unit_id']); // Remove added columns
            $table->unsignedBigInteger('user_id'); // Re-add user_id column
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
