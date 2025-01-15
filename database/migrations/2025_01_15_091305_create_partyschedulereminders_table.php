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
        Schema::create('partyschedulereminders', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('reminder_frequency');
            $table->boolean('send_copy')->default(true);
            $table->foreignId('party_id')->constrained('parties')->onDelete('cascade');
            $table->boolean('isactive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partyschedulereminders');
    }
};
