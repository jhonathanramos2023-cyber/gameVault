<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('videojuego_id')->constrained('videojuegos')->cascadeOnDelete();
            $table->string('titulo', 180);
            $table->text('url');
            $table->string('tipo_fuente', 80);
            $table->text('observaciones')->nullable();
            $table->date('fecha_consulta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referencias');
    }
};
