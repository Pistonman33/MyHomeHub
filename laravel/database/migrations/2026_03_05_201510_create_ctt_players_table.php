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
        Schema::create('ctt_players', function (Blueprint $table) {
            $table->unsignedBigInteger('license')->primary();
            $table->string('firstname');
            $table->string('lastname');

            $table->string('ranking')->nullable(); // E4
            $table->string('status')->nullable();  // A
            $table->string('club')->nullable(); // BBW179

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ctt_players', function (Blueprint $table) {
            Schema::dropIfExists('ctt_players');
        });
    }
};