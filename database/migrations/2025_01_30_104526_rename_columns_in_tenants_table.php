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
            $table->renameColumn('business_type', 'business_types_id'); // Rename business_type to business_types_id
            $table->renameColumn('business_category', 'business_categories_id'); // Rename business_category to business_categories_id
            $table->renameColumn('state', 'state_id'); // Rename state to state_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->renameColumn('business_types_id', 'business_type'); // Reverse the rename
            $table->renameColumn('business_categories_id', 'business_category'); // Reverse the rename
            $table->renameColumn('state_id', 'state'); // Reverse the rename
        });
    }
};
