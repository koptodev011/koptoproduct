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
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->bigInteger('mobile_number')->nullable()->change();
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->string('token')->nullable()->change();
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->change();
            $table->text('user_agent')->nullable()->change();
            $table->longText('payload')->nullable()->change();
            $table->integer('last_activity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
            $table->bigInteger('mobile_number')->nullable(false)->change();
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->string('token')->nullable(false)->change();
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable(false)->change();
            $table->text('user_agent')->nullable(false)->change();
            $table->longText('payload')->nullable(false)->change();
            $table->integer('last_activity')->nullable(false)->change();
        });
    }
};
