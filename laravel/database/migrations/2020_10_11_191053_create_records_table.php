<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->unsignedInteger('id', true); // int unsigned AUTO_INCREMENT PRIMARY KEY
            $table->float('montant', 9, 2);
            $table->dateTime('date');
            $table->string('libelle', 200)->nullable();
            $table->text('details')->nullable();
            $table->unsignedInteger('mouvement');
            $table->boolean('retrait'); // tinyint unsigned
            $table->unsignedBigInteger('fk_id_categorie')->nullable();
            $table->boolean('validate');
            $table->boolean('deleted');
            $table->unsignedInteger('fk_id_compte')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexes
            $table->index('fk_id_categorie', 'FK_records_categorie');
            $table->index('fk_id_compte', 'FK_records_compte');

            // Foreign keys (MySQL uniquement, SQLite ignore mais Laravel autorise)
            $table->foreign('fk_id_categorie')->references('id')->on('categories')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('fk_id_compte')->references('compteid')->on('comptes')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};