<?php

namespace App\Models;

use Database\Factories\VideojuegoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Videojuego extends Model
{
    /** @use HasFactory<VideojuegoFactory> */
    use HasFactory;

    protected $fillable = [
        'titulo', 'slug', 'descripcion', 'informacion_tecnica', 'requisitos_conocidos',
        'formato', 'tipo_proyecto', 'estado', 'advertencia', 'fecha_lanzamiento',
        'categoria_id', 'user_id',
    ];

    protected function casts(): array
    {
        return ['fecha_lanzamiento' => 'date'];
    }

    /** Cada ficha pertenece a una categoría y a quien la registró. */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Relación editorial muchos-a-muchos con plataformas. */
    public function plataformas(): BelongsToMany
    {
        return $this->belongsToMany(Plataforma::class, 'videojuego_plataforma');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenVideojuego::class)->orderBy('orden')->orderBy('id');
    }

    public function referencias(): HasMany
    {
        return $this->hasMany(Referencia::class)->latest('id');
    }

    public function getPortadaAttribute(): ?ImagenVideojuego
    {
        return $this->relationLoaded('imagenes')
            ? $this->imagenes->first()
            : $this->imagenes()->orderBy('orden')->orderBy('id')->first();
    }
}
