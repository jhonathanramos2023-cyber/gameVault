<?php

namespace App\Models;

use Database\Factories\ReferenciaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referencia extends Model
{
    /** @use HasFactory<ReferenciaFactory> */
    use HasFactory;

    protected $fillable = ['videojuego_id', 'titulo', 'url', 'tipo_fuente', 'observaciones', 'fecha_consulta'];

    protected function casts(): array
    {
        return ['fecha_consulta' => 'date'];
    }

    public function videojuego(): BelongsTo
    {
        return $this->belongsTo(Videojuego::class);
    }
}
