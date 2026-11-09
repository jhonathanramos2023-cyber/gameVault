<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\User;
use App\Models\Videojuego;
use Illuminate\Database\Seeder;
use RuntimeException;

class PerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $categoria = Categoria::query()->first();
        $usuario = User::query()->where('rol', 'colaborador')->first() ?? User::query()->where('rol', 'administrador')->first();

        if (! $categoria || ! $usuario) {
            throw new RuntimeException('Ejecuta DatabaseSeeder antes de PerformanceSeeder.');
        }

        Videojuego::factory()->count(500)->create([
            'categoria_id' => $categoria->id,
            'user_id' => $usuario->id,
        ]);
    }
}
