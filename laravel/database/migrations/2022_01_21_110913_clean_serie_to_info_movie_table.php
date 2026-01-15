<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CleanSerieToInfoMovieTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('info_movie', function (Blueprint $table) {
            $table->dropColumn('series');
            $table->dropColumn('creators');
            $table->dropColumn('yearStart');
            $table->dropColumn('yearEnd');
            $table->dropColumn('seasonCount');
            $table->dropColumn('episodeCount');
            $table->dropColumn('lastSeasonNumber');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('info_movie', function (Blueprint $table) {
            //
        });
    }
}
