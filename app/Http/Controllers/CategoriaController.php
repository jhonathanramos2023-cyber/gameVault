<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    public function index()
    {
        return view('categorias.index', ['categorias' => Categoria::withCount('videojuegos')->orderBy('nombre')->paginate(20)]);
    }

    public function create()
    {
        return view('categorias.form', ['categoria' => new Categoria()]);
    }

    public function store(CategoriaRequest $request)
    {
        $datos = $request->validated();
        unset($datos['slug']);
        $datos['slug'] = $this->slugUnico($datos['nombre']);
        Categoria::create($datos);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.form', compact('categoria'));
    }

    public function update(CategoriaRequest $request, Categoria $categoria)
    {
        $datos = $request->validated();
        unset($datos['slug']);

        if ($datos['nombre'] !== $categoria->nombre) {
            $datos['slug'] = $this->slugUnico($datos['nombre'], $categoria->id);
        }

        $categoria->update($datos);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->videojuegos()->exists()) {
            return back()->with('error', 'No se puede eliminar: la categoría tiene videojuegos asociados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada.');
    }

    private function slugUnico(string $nombre, ?int $excepto = null): string
    {
        $base = Str::slug($nombre) ?: 'categoria';
        $slug = $base;
        $sufijo = 2;

        while (Categoria::where('slug', $slug)->when($excepto, fn ($q) => $q->where('id', '!=', $excepto))->exists()) {
            $slug = $base.'-'.$sufijo++;
        }

        return $slug;
    }
}
