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
        Schema::create('ctt_matches', function (Blueprint $table) {

            $table->id(); 
            
            $table->unsignedBigInteger('match_unique_id')->nullable();
            $table->string('match_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            // Player (PK = license)
            $table->unsignedBigInteger('player_license');

            $table->foreign('player_license')
                ->references('license')
                ->on('ctt_players')
                ->cascadeOnDelete();

            // Season (PK = year)
            $table->integer('season_year');

            $table->foreign('season_year')
                ->references('year')
                ->on('ctt_seasons')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Match Info
            |--------------------------------------------------------------------------
            */

            $table->date('date');

            $table->enum('competition_type', ['C', 'T']);          // C = championnat, T = tournoi

            /*
            |--------------------------------------------------------------------------
            | Opponent Info
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('opponent_license');
            $table->string('opponent_firstname');
            $table->string('opponent_lastname');
            $table->string('opponent_ranking')->nullable();
            $table->string('opponent_club')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Tournament
            |--------------------------------------------------------------------------
            */

            $table->string('tournament_name')->nullable();
            $table->string('tournament_serie')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Result
            |--------------------------------------------------------------------------
            */

            $table->enum('result', ['V', 'D']);
            $table->integer('set_for');
            $table->integer('set_against');
            $table->integer('ranking_diff')->nullable();
            
            $table->string('ranking_evaluation_category')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes & Constraints
            |--------------------------------------------------------------------------
            */

            // avoid duplicate data entry for same player, same match, same opponent
            $table->unique(
                ['player_license', 'match_unique_id', 'opponent_license'],
                'unique_player_match'
            );

            // Indexes
            $table->index('date');
            $table->index('season_year');
            $table->index('result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ctt_matches');
    }
};