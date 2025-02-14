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
        Schema::create('tenant_units', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('Business Name');
           
            $table->unsignedBigInteger('business_type_id')->nullable();
            $table->foreign('business_type_id')->references('id')->on('business_types');

            $table->string('business_address')->nullable();
            $table->string('phone_number')->nullable();

            $table->unsignedBigInteger('business_category_id')->nullable();
            $table->foreign('business_category_id')->references('id')->on('business_categories');

            $table->string('TIN_number')->nullable();

            $table->unsignedBigInteger('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states');

            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities');

            $table->string('business_email')->nullable();
            $table->integer('pin_code')->nullable();
            $table->string('business_logo')->nullable();
            $table->string('business_signature')->nullable();

            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants');
            
            $table->boolean('isactive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_units');
    }
};
