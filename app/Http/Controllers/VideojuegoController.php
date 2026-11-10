<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideojuegoRequest;
use App\Models\Categoria;
use App\Models\Plataforma;
use App\Models\Videojuego;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideojuegoController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Videojuego::class);
        $consulta = Videojuego::with(['categoria', 'usuario', 'imagenes'])->latest('updated_at');

        if (! auth()->user()->esAdministrador()) {
            $consulta->where('user_id', auth()->id());
        }

        if ($busqueda = trim((string) request()->query('q'))) {
            $patron = '%'.$busqueda.'%';
            $consulta->where(function ($q) use ($patron): void {
                $q->whereRaw('LOWER(titulo) LIKE LOWER(?)', [$patron])
                    ->orWhereRaw('LOWER(descripcion) LIKE LOWER(?)', [$patron]);
            });
        }

        return view('videojuegos.index', ['videojuegos' => $consulta->paginate(15)]);
    }

    public function create()
    {
        Gate::authorize('create', Videojuego::class);

        return view('videojuegos.form', [
            'videojuego' => new Videojuego(['advertencia' => config('gamevault.advertencia')]),
            'categorias' => Categoria::orderBy('nombre')->get(),
            'plataformas' => Plataforma::orderBy('nombre')->get(),
        ]);
    }

    public function store(VideojuegoRequest $request)
    {
        $datos = $request->validated();
        $plataformas = $datos['plataformas'];
        $imagenes = $datos['imagenes'];
        $alternativos = $datos['textos_alternativos'] ?? [];
        unset($datos['plataformas']);
        unset($datos['imagenes'], $datos['textos_alternativos']);

        $videojuego = auth()->user()->videojuegos()->create([
            ...$datos,
            'slug' => $this->slugUnico($datos['titulo']),
            'estado' => 'borrador',
            'advertencia' => $datos['advertencia'] ?: config('gamevault.advertencia'),
        ]);
        $videojuego->plataformas()->sync($plataformas);
        $this->guardarImagenes($videojuego, $imagenes, $alternativos);

        return redirect()->route('videojuegos.edit', $videojuego)->with('success', 'Borrador guardado. Puedes agregar más imágenes o completar las referencias.');
    }

    public function show(Videojuego $videojuego)
    {
        Gate::authorize('view', $videojuego);
        $videojuego->load(['categoria', 'usuario', 'plataformas', 'imagenes', 'referencias']);

        return view('videojuegos.show', compact('videojuego'));
    }

    public function edit(Videojuego $videojuego)
    {
        Gate::authorize('update', $videojuego);
        $videojuego->load(['plataformas', 'imagenes', 'referencias']);

        return view('videojuegos.form', [
            'videojuego' => $videojuego,
            'categorias' => Categoria::orderBy('nombre')->get(),
            'plataformas' => Plataforma::orderBy('nombre')->get(),
        ]);
    }

    public function update(VideojuegoRequest $request, Videojuego $videojuego)
    {
        $datos = $request->validated();
        $plataformas = $datos['plataformas'];
        unset($datos['plataformas']);

        if ($datos['titulo'] !== $videojuego->titulo) {
            $datos['slug'] = $this->slugUnico($datos['titulo'], $videojuego->id);
        }

        $datos['advertencia'] = $datos['advertencia'] ?: config('gamevault.advertencia');
        $videojuego->update($datos);
        $videojuego->plataformas()->sync($plataformas);

        return redirect()->route('videojuegos.edit', $videojuego)->with('success', 'Ficha actualizada correctamente.');
    }

    public function destroy(Videojuego $videojuego)
    {
        Gate::authorize('delete', $videojuego);
        $disk = Storage::disk(config('gamevault.image_disk'));

        foreach ($videojuego->imagenes as $imagen) {
            $disk->delete($imagen->ruta);
        }

        $videojuego->delete();

        return redirect()->route('videojuegos.index')->with('success', 'Ficha y sus imágenes y referencias eliminadas.');
    }

    public function enviarRevision(Videojuego $videojuego)
    {
        Gate::authorize('enviarRevision', $videojuego);
        abort_if($videojuego->imagenes()->count() === 0, 422, 'Agrega al menos una imagen antes de enviar la ficha.');

        $videojuego->update(['estado' => 'en_revision']);

        return back()->with('success', 'Ficha enviada a revisión.');
    }

    public function publicar(Videojuego $videojuego)
    {
        Gate::authorize('publicar', $videojuego);
        $videojuego->update(['estado' => 'publicado']);

        return back()->with('success', 'Ficha publicada en el catálogo.');
    }

    public function devolver(Videojuego $videojuego)
    {
        Gate::authorize('devolver', $videojuego);
        $videojuego->update(['estado' => 'borrador']);

        return back()->with('success', 'Ficha devuelta al colaborador como borrador.');
    }

    public function despublicar(Videojuego $videojuego)
    {
        Gate::authorize('despublicar', $videojuego);
        $videojuego->update(['estado' => 'borrador']);

        return back()->with('success', 'Ficha despublicada y devuelta a borrador.');
    }

    public function retirar(Videojuego $videojuego)
    {
        Gate::authorize('retirar', $videojuego);
        $videojuego->update(['estado' => 'retirado']);

        return back()->with('success', 'Ficha retirada del catálogo.');
    }

    private function slugUnico(string $titulo, ?int $excepto = null): string
    {
        $base = Str::slug($titulo) ?: 'videojuego';
        $slug = $base;
        $sufijo = 2;

        while (Videojuego::where('slug', $slug)->when($excepto, fn ($q) => $q->whereKeyNot($excepto))->exists()) {
            $slug = $base.'-'.$sufijo++;
        }

        return $slug;
    }

    /** Las imágenes son entradas multimedia del registro, nunca archivos de juego. */
    private function guardarImagenes(Videojuego $videojuego, array $archivos, array $alternativos): void
    {
        $orden = (int) ($videojuego->imagenes()->max('orden') ?? -1) + 1;

        foreach ($archivos as $indice => $archivo) {
            $ruta = $archivo->store('videojuegos/'.$videojuego->id, config('gamevault.image_disk'));
            if (! $ruta) {
                throw new \RuntimeException('No se pudo guardar una de las imágenes.');
            }

            $texto = trim((string) ($alternativos[$indice] ?? ''));
            $videojuego->imagenes()->create([
                'ruta' => $ruta,
                'texto_alternativo' => $texto ?: $videojuego->titulo.' — imagen '.($indice + 1),
                'orden' => min(65535, $orden + $indice),
            ]);
        }
    }
}
