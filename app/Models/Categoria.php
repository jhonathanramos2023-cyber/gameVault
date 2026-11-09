<?php

namespace App\Models;

use Database\Factories\CategoriaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    /** @use HasFactory<CategoriaFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'slug', 'descripcion'];

    /** La FK impide borrar una categoría que todavía tiene fichas. */
    public function videojuegos(): HasMany
    {
        return $this->hasMany(Videojuego::class);
    }
}
