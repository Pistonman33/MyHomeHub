<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('series', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',255)->nullable(false);
            $table->string('location',255)->nullable(true);
            $table->integer('fk_id_support');
            $table->unsignedInteger('fk_id_serie_info');
            $table->foreign('fk_id_support')->references('id')->on('supports')->onDelete('cascade')->nullable(false);
            $table->foreign('fk_id_serie_info')->references('id')->on('series')->onDelete('cascade')->nullable(true);            
            $table->timestamp('date_access')->nullable(true);
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
        Schema::dropIfExists('series');
    }
}
