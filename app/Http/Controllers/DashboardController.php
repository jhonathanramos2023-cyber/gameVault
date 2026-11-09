<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Videojuego;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();
        $consulta = Videojuego::query()->with(['categoria', 'usuario'])->latest('updated_at');

        if (! $usuario->esAdministrador()) {
            $consulta->where('user_id', $usuario->id);
        }

        return view('dashboard', [
            'videojuegos' => $consulta->limit(8)->get(),
            'resumen' => $usuario->esAdministrador() ? [
                'registros' => Videojuego::count(),
                'borradores' => Videojuego::where('estado', 'borrador')->count(),
                'publicados' => Videojuego::where('estado', 'publicado')->count(),
                'usuarios_activos' => User::where('activo', true)->count(),
                'en_revision' => Videojuego::where('estado', 'en_revision')->count(),
            ] : [
                'registros' => Videojuego::where('user_id', $usuario->id)->count(),
                'borradores' => Videojuego::where('user_id', $usuario->id)->where('estado', 'borrador')->count(),
                'publicados' => Videojuego::where('user_id', $usuario->id)->where('estado', 'publicado')->count(),
                'usuarios_activos' => null,
                'en_revision' => Videojuego::where('user_id', $usuario->id)->where('estado', 'en_revision')->count(),
            ],
        ]);
    }
}
