<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuarios.index', [
            'usuarios' => User::where('rol', 'colaborador')->withCount('videojuegos')->orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('usuarios.form', ['usuario' => new User()]);
    }

    public function store(UsuarioRequest $request)
    {
        $datos = $request->validated();
        $datos['rol'] = 'colaborador';
        $datos['activo'] = true;
        User::create($datos);

        return redirect()->route('usuarios.index')->with('success', 'Cuenta de colaborador creada.');
    }

    public function edit(User $usuario)
    {
        abort_unless($usuario->rol === 'colaborador', 404);

        return view('usuarios.form', compact('usuario'));
    }

    public function update(UsuarioRequest $request, User $usuario)
    {
        abort_unless($usuario->rol === 'colaborador', 404);
        $datos = $request->validated();
        unset($datos['password_confirmation']);

        if (blank($datos['password'] ?? null)) {
            unset($datos['password']);
        } else {
            $datos['password'] = Hash::make($datos['password']);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('success', 'Cuenta de colaborador actualizada.');
    }

    public function cambiarEstado(User $usuario)
    {
        abort_unless($usuario->rol === 'colaborador', 404);
        abort_if($usuario->is(auth()->user()), 403, 'No puedes desactivar tu propia sesión.');
        $usuario->update(['activo' => ! $usuario->activo]);

        return back()->with('success', $usuario->activo ? 'Colaborador activado.' : 'Colaborador desactivado.');
    }
}
