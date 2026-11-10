<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImagenRequest;
use App\Models\ImagenVideojuego;
use App\Models\Videojuego;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ImagenController extends Controller
{
    public function store(ImagenRequest $request, Videojuego $videojuego)
    {
        $datos = $request->validated();
        $orden = (int) ($datos['orden'] ?? ($videojuego->imagenes()->max('orden') + 1));

        foreach ($datos['imagenes'] as $indice => $archivo) {
            $ruta = $archivo->store('videojuegos/'.$videojuego->id, config('gamevault.image_disk'));
            if (! $ruta) {
                throw new RuntimeException('No se pudo guardar una de las imágenes.');
            }

            $alternativo = trim((string) ($datos['textos_alternativos'][$indice] ?? ''));
            ImagenVideojuego::create([
                'videojuego_id' => $videojuego->id,
                'ruta' => $ruta,
                'texto_alternativo' => $alternativo ?: $videojuego->titulo.' — imagen '.($indice + 1),
                'orden' => min(65535, $orden + $indice),
            ]);
        }

        return back()->with('success', 'Imágenes cargadas correctamente.');
    }

    public function destroy(ImagenVideojuego $imagen)
    {
        $videojuego = $imagen->videojuego;
        Gate::authorize('update', $videojuego);

        try {
            Storage::disk(config('gamevault.image_disk'))->delete($imagen->ruta);
            $imagen->delete();

            return back()->with('success', 'Imagen eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la imagen. Intente nuevamente.');
        }
    }
}
