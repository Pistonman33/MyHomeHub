<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comptes', function (Blueprint $table) {
            $table->unsignedInteger('compteid', true); // AUTO_INCREMENT PRIMARY KEY
            $table->string('num', 16);
            $table->string('nom', 45);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comptes');
    }
};