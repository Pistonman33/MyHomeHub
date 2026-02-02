<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_movie', function (Blueprint $table) {
            $table->id(); // int AUTO_INCREMENT PRIMARY KEY
            $table->string('originalTitle', 150);
            $table->string('title', 150);
            $table->string('genre', 45)->nullable();
            $table->string('year', 12)->nullable();
            $table->string('duration', 50)->nullable();
            $table->mediumText('actors')->nullable();
            $table->string('directors', 255)->nullable();
            $table->mediumText('synopsis')->nullable();
            $table->string('poster', 20)->nullable();
            $table->boolean('valid_poster')->default(false);
            $table->integer('allo_code');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_movie');
    }
};