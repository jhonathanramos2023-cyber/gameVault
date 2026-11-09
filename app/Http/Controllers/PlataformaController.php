<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlataformaRequest;
use App\Models\Plataforma;

class PlataformaController extends Controller
{
    public function index()
    {
        return view('plataformas.index', ['plataformas' => Plataforma::withCount('videojuegos')->orderBy('nombre')->paginate(20)]);
    }

    public function create()
    {
        return view('plataformas.form', ['plataforma' => new Plataforma()]);
    }

    public function store(PlataformaRequest $request)
    {
        Plataforma::create($request->validated());

        return redirect()->route('plataformas.index')->with('success', 'Plataforma creada.');
    }

    public function edit(Plataforma $plataforma)
    {
        return view('plataformas.form', compact('plataforma'));
    }

    public function update(PlataformaRequest $request, Plataforma $plataforma)
    {
        $plataforma->update($request->validated());

        return redirect()->route('plataformas.index')->with('success', 'Plataforma actualizada.');
    }

    public function destroy(Plataforma $plataforma)
    {
        if ($plataforma->videojuegos()->exists()) {
            return back()->with('error', 'No se puede eliminar: la plataforma tiene videojuegos asociados.');
        }

        $plataforma->delete();

        return redirect()->route('plataformas.index')->with('success', 'Plataforma eliminada.');
    }
}
