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
        Schema::create('ctt_player_seasons', function (Blueprint $table) {
            $table->id();

            // lien logique saison + joueur
            $table->integer('year');
            $table->string('player_license');

            // stats joueur dans la saison
            $table->string('ranking')->nullable();
            $table->decimal('starting_points', 8, 2)->nullable();
            $table->decimal('current_points', 8, 2)->nullable();
            $table->integer('ranking_belgium')->nullable();
            $table->timestamps();

            // éviter doublons
            $table->unique(['year', 'player_license']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ctt_player_seasons');
    }
};