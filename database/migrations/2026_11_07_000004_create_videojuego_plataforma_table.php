<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videojuego_plataforma', function (Blueprint $table) {
            $table->foreignId('videojuego_id')->constrained('videojuegos')->cascadeOnDelete();
            $table->foreignId('plataforma_id')->constrained('plataformas')->cascadeOnDelete();
            $table->primary(['videojuego_id', 'plataforma_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videojuego_plataforma');
    }
};
