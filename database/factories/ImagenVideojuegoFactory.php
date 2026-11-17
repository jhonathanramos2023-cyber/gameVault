<?php

namespace Database\Factories;

use App\Models\ImagenVideojuego;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImagenVideojuego>
 */
class ImagenVideojuegoFactory extends Factory
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
            'ruta' => 'placeholders/ficha.svg',
            'texto_alternativo' => 'Ilustración generada para una ficha de GameVault',
            'orden' => 0,
            'tipo' => 'archivo',
        ];
    }
}
