@extends('layouts.app')
@section('title', 'Colaboradores')
@section('content')
    <div class="d-flex justify-content-between align-items-end gap-3 mb-4"><div><p class="eyebrow mb-1">Administración</p><h1 class="h2 fw-bold mb-1">Colaboradores</h1><p class="gv-muted mb-0">Solo administración puede crear cuentas y cambiar su estado.</p></div><a href="{{ route('usuarios.create') }}" class="btn btn-cyan">Crear colaborador</a></div>
    <section class="gv-panel"><div class="table-responsive"><table class="table gv-table align-middle mb-0">
        <thead><tr><th>Nombre</th><th>Correo</th><th>Fichas</th><th>Estado</th><th></th></tr></thead>
        <tbody>@forelse($usuarios as $usuario)
            <tr><td class="fw-semibold">{{ $usuario->name }}</td><td>{{ $usuario->email }}</td><td>{{ $usuario->videojuegos_count }}</td><td><span class="badge {{ $usuario->activo ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</span></td>
                <td class="text-nowrap"><a class="btn btn-sm btn-outline-info" href="{{ route('usuarios.edit', $usuario) }}">Editar</a>
                    <form class="d-inline" method="POST" action="{{ route('usuarios.estado', $usuario) }}">@csrf @method('PATCH')<button class="btn btn-sm {{ $usuario->activo ? 'btn-outline-warning' : 'btn-outline-success' }}" type="submit">{{ $usuario->activo ? 'Desactivar' : 'Activar' }}</button></form>
                </td></tr>
        @empty<tr><td colspan="5" class="text-center gv-muted py-4">No hay colaboradores.</td></tr>@endforelse</tbody>
    </table></div><div class="mt-3">{{ $usuarios->links() }}</div></section>
@endsection
