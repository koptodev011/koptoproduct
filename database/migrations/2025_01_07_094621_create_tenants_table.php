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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('Business Name');
            $table->string('business_type')->nullable();
            $table->string('business_address')->nullable();
            $table->integer('phone_number')->nullable();
            $table->string('business_category')->nullable();
            $table->string('TIN_number')->nullable();
            $table->string('state')->nullable();
            $table->string('business_email')->nullable();
            $table->integer('pin_code')->nullable();
            $table->string('business_logo')->nullable();
            $table->string('business_signature')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->boolean('isactive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
