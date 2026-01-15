<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeriesToInfoMovie extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('info_movie', function (Blueprint $table) {
          $table->boolean('series')->default(false)->nullable(false)->after('allo_code');
          $table->string('creators',255)->nullable(true)->after('series');
          $table->string('yearStart',12)->nullable(true)->after('creators');
          $table->string('yearEnd',12)->nullable(true)->after('yearStart');
          $table->integer('seasonCount')->nullable(true)->unsigned()->after('yearEnd');
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
