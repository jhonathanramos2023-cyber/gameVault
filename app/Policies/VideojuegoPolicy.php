<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Videojuego;
/**
 * La Policy centraliza permisos y estados: el middleware organiza roles,
 * mientras esta clase decide qué registros puede modificar cada usuario.
 */
class VideojuegoPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->rol, ['administrador', 'colaborador'], true) && $user->activo;
    }

    public function view(User $user, Videojuego $videojuego): bool
    {
        return $videojuego->estado === 'publicado'
            || $user->esAdministrador()
            || ($user->rol === 'colaborador' && $videojuego->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->activo && in_array($user->rol, ['administrador', 'colaborador'], true);
    }

    public function update(User $user, Videojuego $videojuego): bool
    {
        return $user->activo && (
            $user->esAdministrador()
            || ($user->rol === 'colaborador'
                && $videojuego->user_id === $user->id
                && $videojuego->estado === 'borrador')
        );
    }

    public function delete(User $user, Videojuego $videojuego): bool
    {
        return $user->activo && $user->esAdministrador();
    }

    // El colaborador envía sus propios borradores; el administrador puede enviar cualquiera.
    public function enviarRevision(User $user, Videojuego $videojuego): bool
    {
        return $user->activo
            && $videojuego->estado === 'borrador'
            && ($user->esAdministrador()
                || ($user->rol === 'colaborador' && $videojuego->user_id === $user->id));
    }

    public function publicar(User $user, Videojuego $videojuego): bool
    {
        return $user->activo && $user->esAdministrador() && $videojuego->estado === 'en_revision';
    }

    public function devolver(User $user, Videojuego $videojuego): bool
    {
        return $user->activo && $user->esAdministrador() && $videojuego->estado === 'en_revision';
    }

    public function despublicar(User $user, Videojuego $videojuego): bool
    {
        return $user->activo && $user->esAdministrador() && $videojuego->estado === 'publicado';
    }

    public function retirar(User $user, Videojuego $videojuego): bool
    {
        return $user->activo && $user->esAdministrador() && $videojuego->estado !== 'retirado';
    }
}