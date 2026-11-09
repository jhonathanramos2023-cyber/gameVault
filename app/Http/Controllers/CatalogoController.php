<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Plataforma;
use App\Models\Videojuego;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Videojuego::query()
            ->where('estado', 'publicado')
            ->with(['categoria', 'plataformas', 'imagenes']);

        if ($busqueda = trim((string) $request->query('q'))) {
            $patron = '%'.$busqueda.'%';
            $consulta->where(function ($q) use ($patron): void {
                $q->whereRaw('LOWER(titulo) LIKE LOWER(?)', [$patron])
                    ->orWhereRaw('LOWER(descripcion) LIKE LOWER(?)', [$patron])
                    ->orWhereRaw('LOWER(informacion_tecnica) LIKE LOWER(?)', [$patron]);
            });
        }

        foreach (['categoria_id', 'formato', 'tipo_proyecto'] as $filtro) {
            if ($request->filled($filtro)) {
                $consulta->where($filtro, $request->query($filtro));
            }
        }

        if ($request->filled('plataforma_id')) {
            $consulta->whereHas('plataformas', fn ($q) => $q->where('plataformas.id', $request->query('plataforma_id')));
        }

        return view('catalogo.index', [
            'videojuegos' => $consulta->latest('videojuegos.created_at')->paginate(12)->withQueryString(),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'plataformas' => Plataforma::orderBy('nombre')->get(),
            'formatos' => Videojuego::query()->where('estado', 'publicado')->distinct()->orderBy('formato')->pluck('formato'),
            'tipos' => Videojuego::query()->where('estado', 'publicado')->distinct()->orderBy('tipo_proyecto')->pluck('tipo_proyecto'),
        ]);
    }

    public function show(Videojuego $videojuego)
    {
        abort_unless($videojuego->estado === 'publicado', 404);
        $videojuego->load(['categoria', 'plataformas', 'imagenes', 'referencias']);

        return view('catalogo.show', compact('videojuego'));
    }
}
