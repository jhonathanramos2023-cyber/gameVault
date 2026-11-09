@extends('layouts.app')
@section('title', 'Categorías')
@section('content')
    <div class="d-flex justify-content-between align-items-end gap-3 mb-4"><div><p class="eyebrow mb-1">Administración</p><h1 class="h2 fw-bold mb-1">Categorías</h1><p class="gv-muted mb-0">Las categorías con fichas asociadas no se pueden eliminar.</p></div><a href="{{ route('categorias.create') }}" class="btn btn-cyan">Nueva categoría</a></div>
    <section class="gv-panel">
        <div class="table-responsive"><table class="table gv-table align-middle mb-0">
            <thead><tr><th>Nombre</th><th>Slug</th><th>Registros</th><th></th></tr></thead>
            <tbody>@forelse($categorias as $categoria)
                <tr><td><strong>{{ $categoria->nombre }}</strong><div class="small gv-muted">{{ $categoria->descripcion }}</div></td><td class="small">{{ $categoria->slug }}</td><td>{{ $categoria->videojuegos_count }}</td>
                    <td class="text-nowrap"><a class="btn btn-sm btn-outline-info" href="{{ route('categorias.edit', $categoria) }}">Editar</a>
                        @if($categoria->videojuegos_count === 0)<form class="d-inline" method="POST" action="{{ route('categorias.destroy', $categoria) }}" onsubmit="return confirm('¿Eliminar esta categoría?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button></form>@endif
                    </td>
                </tr>
            @empty<tr><td colspan="4" class="text-center gv-muted py-4">No hay categorías todavía.</td></tr>@endforelse</tbody>
        </table></div>
        <div class="mt-3">{{ $categorias->links() }}</div>
    </section>
@endsection
