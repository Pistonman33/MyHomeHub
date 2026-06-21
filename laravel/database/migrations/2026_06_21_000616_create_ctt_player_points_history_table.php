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
        Schema::create('ctt_player_points_history', function (Blueprint $table) {
            $table->unsignedBigInteger('match_id');
            
            $table->decimal('delta_points', 8, 2);
            $table->decimal('opponent_points', 8, 2);
            
            $table->timestamps();

            $table->primary('match_id');

            $table->foreign('match_id')
                ->references('id')
                ->on('ctt_matches')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ctt_player_points_history');
    }
};