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
        Schema::create('rules', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->text('match_pattern');
            
            $table->string('libelle_template')->nullable();

            $table->unsignedInteger('category_id');

            $table->boolean('active')->default(true);

            $table->integer('priority')->default(100);

            $table->timestamps();

            $table->index('category_id');
            $table->index('active');
            $table->index('priority');

            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};