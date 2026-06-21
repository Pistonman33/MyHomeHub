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
        Schema::table('ctt_seasons', function (Blueprint $table) {
            // Points au début de la saison
            $table->decimal('starting_points', 8, 2)->nullable()->after('ranking');

            // Points actuels (évolutifs)
            $table->decimal('current_points', 8, 2)->nullable()->after('starting_points');

            // Ranking dans la saison
            $table->integer('ranking_belgium')->nullable()->after('current_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ctt_seasons', function (Blueprint $table) {
            $table->dropColumn(['starting_points', 'current_points', 'ranking']);
        });
    }
};