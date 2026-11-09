<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videojuegos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 180);
            $table->string('slug', 200)->unique();
            $table->text('descripcion');
            $table->text('informacion_tecnica')->nullable();
            $table->text('requisitos_conocidos')->nullable();
            $table->string('formato', 80);
            $table->string('tipo_proyecto', 100);
            $table->string('estado', 30)->default('borrador');
            $table->text('advertencia');
            $table->date('fecha_lanzamiento')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['estado', 'titulo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videojuegos');
    }
};
