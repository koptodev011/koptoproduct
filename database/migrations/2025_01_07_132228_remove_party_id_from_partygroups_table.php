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
        Schema::table('partygroups', function (Blueprint $table) {
            $table->dropForeign(['party_id']);
            $table->dropColumn('party_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partygroups', function (Blueprint $table) {
            $table->unsignedBigInteger('party_id');
            $table->foreign('party_id')->references('id')->on('parties');
        });
    }
};
