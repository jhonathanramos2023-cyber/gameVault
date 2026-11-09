<?php

namespace App\Models;

use Database\Factories\ImagenVideojuegoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagenVideojuego extends Model
{
    /** @use HasFactory<ImagenVideojuegoFactory> */
    use HasFactory;

    protected $table = 'imagenes_videojuego';

    protected $fillable = ['videojuego_id', 'ruta', 'texto_alternativo', 'orden'];

    public function videojuego(): BelongsTo
    {
        return $this->belongsTo(Videojuego::class);
    }
}
