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
        Schema::create('ctt_seasons', function (Blueprint $table) {
            $table->integer('year')->primary(); 
            $table->string('name')->nullable();
            $table->string('ranking')->nullable();

            $table->boolean('is_current')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ctt_seasons');
    }
};