<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id(); // int AUTO_INCREMENT PRIMARY KEY
            $table->string('title', 255);
            $table->string('location', 255)->nullable();
            $table->unsignedBigInteger('fk_id_support');
            $table->unsignedBigInteger('fk_id_movie_info')->nullable();
            $table->timestamp('date_access')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexes
            $table->index('fk_id_support');
            $table->index('fk_id_movie_info');

            // Foreign keys (MySQL uniquement, SQLite ignore mais Laravel autorise)
            $table->foreign('fk_id_support')->references('id')->on('supports')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('fk_id_movie_info')->references('id')->on('info_movie')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};