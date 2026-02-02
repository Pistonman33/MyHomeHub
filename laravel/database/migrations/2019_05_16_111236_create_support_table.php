<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supports', function (Blueprint $table) {
            $table->id(); // int AUTO_INCREMENT PRIMARY KEY
            $table->string('type', 10);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supports');
    }
};