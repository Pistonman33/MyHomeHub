<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSeriesToInfoMovie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('info_movie', function (Blueprint $table) {
          $table->integer('episodeCount')->nullable(true)->unsigned()->after('seasonCount');
          $table->integer('lastSeasonNumber')->nullable(true)->unsigned()->after('episodeCount');
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
