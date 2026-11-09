<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imagenes_videojuego', function (Blueprint $table) {
            $table->id();
            $table->foreignId('videojuego_id')->constrained('videojuegos')->cascadeOnDelete();
            $table->string('ruta', 500);
            $table->string('texto_alternativo', 255);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
            $table->index(['videojuego_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imagenes_videojuego');
    }
};
