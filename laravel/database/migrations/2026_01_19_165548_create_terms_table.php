<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['category', 'tag']);
            $table->timestamps();
        });

        Schema::create('post_term', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->unique(['post_id', 'term_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_term');
        Schema::dropIfExists('terms');
    }

};
