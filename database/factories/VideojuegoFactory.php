<?php

namespace Database\Factories;

use App\Models\Videojuego;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Videojuego>
 */
class VideojuegoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titulo = rtrim(fake()->unique()->sentence(3), '.');

        return [
            'titulo' => $titulo,
            'slug' => Str::slug($titulo).'-'.fake()->unique()->numberBetween(100, 99999),
            'descripcion' => fake()->paragraph(),
            'informacion_tecnica' => fake()->optional()->sentence(),
            'requisitos_conocidos' => fake()->optional()->paragraph(),
            'formato' => fake()->randomElement(['crack/modificación', 'ROM', 'imagen ISO', 'repack', 'port o adaptación', 'emulación', 'reconstrucción/ingeniería inversa', 'preservación digital', 'otro']),
            'tipo_proyecto' => fake()->randomElement(['crack/modificación', 'ROM', 'imagen ISO', 'repack', 'port o adaptación', 'emulación', 'reconstrucción/ingeniería inversa', 'preservación digital', 'otro']),
            'estado' => 'borrador',
            'advertencia' => 'GameVault no aloja archivos y no certifica sitios o archivos de terceros.',
            'fecha_lanzamiento' => fake()->optional()->date(),
            'categoria_id' => Categoria::factory(),
            'user_id' => User::factory(),
        ];
    }
}
