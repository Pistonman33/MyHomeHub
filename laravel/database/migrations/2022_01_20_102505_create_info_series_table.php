<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_series', function (Blueprint $table) {
            $table->increments('id');
            $table->string('originalTitle',150)->nullable(false);
            $table->string('title',150)->nullable(false);
            $table->string('genre',45)->nullable(true);
            $table->string('year',12)->nullable(true);
            $table->string('duration',50)->nullable(true);
            $table->text('actors')->nullable(true);
            $table->string('directors',255)->nullable(true);
            $table->text('synopsis')->nullable(true);
            $table->string('poster',20)->nullable(true);
            $table->boolean('valid_poster')->default(false)->nullable(false);
            $table->integer('allo_code')->nullable(false);
            $table->string('creators',255)->nullable(true);
            $table->string('yearStart',12)->nullable(true);
            $table->string('yearEnd',12)->nullable(true);
            $table->integer('seasonCount')->nullable(true)->unsigned();
            $table->integer('episodeCount')->nullable(true)->unsigned();
            $table->integer('lastSeasonNumber')->nullable(true)->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('info_series');
    }
}
