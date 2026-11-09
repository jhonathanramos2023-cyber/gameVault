<?php

namespace Database\Factories;

use App\Models\Plataforma;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plataforma>
 */
class PlataformaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->bothify('Plataforma ##'),
            'tipo' => fake()->randomElement(['Escritorio', 'Consola', 'Móvil']),
        ];
    }
}
