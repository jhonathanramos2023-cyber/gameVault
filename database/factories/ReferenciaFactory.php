<?php

namespace Database\Factories;

use App\Models\Referencia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Referencia>
 */
class ReferenciaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'videojuego_id' => \App\Models\Videojuego::factory(),
            'titulo' => 'Sitio oficial del proyecto',
            'url' => 'https://example.org/',
            'tipo_fuente' => 'Sitio oficial',
            'observaciones' => fake()->optional()->sentence(),
            'fecha_consulta' => now()->toDateString(),
        ];
    }
}
