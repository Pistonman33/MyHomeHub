<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ctt_player_points_history', function (Blueprint $table) {

            // ✔ NEW PRIMARY KEY
            $table->bigIncrements('id');

            // ✔ relations
            $table->unsignedBigInteger('match_id');
            $table->string('player_license');

            // ✔ data
            $table->decimal('delta_points', 8, 2);
            $table->decimal('opponent_points', 8, 2);

            $table->timestamps();

            // ✔ indexes
            $table->unique(['match_id', 'player_license'], 'ctt_pph_match_player_unique');
            $table->index('player_license');

            // ✔ foreign key
            $table->foreign('match_id')
                ->references('id')
                ->on('ctt_matches')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ctt_player_points_history');
    }
};