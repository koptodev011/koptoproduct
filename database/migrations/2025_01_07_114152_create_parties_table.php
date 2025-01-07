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
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->string('party_name');
            $table->string('TIN_number')->nullable();
            $table->string('email')->unique();
            $table->string('billing_address')->nullable();
            $table->integer('opening_balance')->nullable();
            $table->boolean('topayortorecive')->default(true);
            $table->integer('creditlimit')->nullable();
            $table->boolean('isactive')->default(true);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tenant_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tenant_id')->references('id')->on('tenants');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
