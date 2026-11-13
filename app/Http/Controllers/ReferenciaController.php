<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferenciaRequest;
use App\Models\Referencia;
use App\Models\Videojuego;
use Illuminate\Support\Facades\Gate;

class ReferenciaController extends Controller
{
    public function index(Videojuego $videojuego)
    {
        Gate::authorize('view', $videojuego);

        return view('referencias.index', [
            'videojuego' => $videojuego,
            'referencias' => $videojuego->referencias()->paginate(15),
        ]);
    }

    public function create(Videojuego $videojuego)
    {
        Gate::authorize('update', $videojuego);

        return view('referencias.form', ['videojuego' => $videojuego, 'referencia' => new Referencia()]);
    }

    public function store(ReferenciaRequest $request, Videojuego $videojuego)
    {
        Gate::authorize('update', $videojuego);
        
        $videojuego->referencias()->create($request->validated());

        return redirect()->route('referencias.index', $videojuego)->with('success', 'Referencia registrada.');
    }

    public function edit(Videojuego $videojuego, Referencia $referencia)
    {
        abort_unless($referencia->videojuego_id === $videojuego->id, 404);
        Gate::authorize('update', $videojuego);

        return view('referencias.form', compact('videojuego', 'referencia'));
    }

    public function update(ReferenciaRequest $request, Videojuego $videojuego, Referencia $referencia)
    {
        abort_unless($referencia->videojuego_id === $videojuego->id, 404);
        $referencia->update($request->validated());

        return redirect()->route('referencias.index', $videojuego)->with('success', 'Referencia actualizada.');
    }

    public function destroy(Videojuego $videojuego, Referencia $referencia)
    {
        abort_unless($referencia->videojuego_id === $videojuego->id, 404);
        Gate::authorize('update', $videojuego);
        $referencia->delete();

        return redirect()->route('referencias.index', $videojuego)->with('success', 'Referencia eliminada.');
    }
}
