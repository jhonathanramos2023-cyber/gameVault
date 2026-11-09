<?php

namespace App\Models;

use Database\Factories\PlataformaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plataforma extends Model
{
    /** @use HasFactory<PlataformaFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'tipo'];

    public function videojuegos(): BelongsToMany
    {
        return $this->belongsToMany(Videojuego::class, 'videojuego_plataforma');
    }
}
