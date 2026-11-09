@extends('layouts.app')
@section('title', 'Plataformas')
@section('content')
    <div class="d-flex justify-content-between align-items-end gap-3 mb-4"><div><p class="eyebrow mb-1">Administración</p><h1 class="h2 fw-bold mb-1">Plataformas</h1><p class="gv-muted mb-0">Las plataformas con fichas asociadas no se pueden eliminar.</p></div><a href="{{ route('plataformas.create') }}" class="btn btn-cyan">Nueva plataforma</a></div>
    <section class="gv-panel"><div class="table-responsive"><table class="table gv-table align-middle mb-0">
        <thead><tr><th>Nombre</th><th>Tipo</th><th>Registros</th><th></th></tr></thead>
        <tbody>@forelse($plataformas as $plataforma)
            <tr><td class="fw-semibold">{{ $plataforma->nombre }}</td><td>{{ $plataforma->tipo }}</td><td>{{ $plataforma->videojuegos_count }}</td>
                <td class="text-nowrap"><a class="btn btn-sm btn-outline-info" href="{{ route('plataformas.edit', $plataforma) }}">Editar</a>
                    @if($plataforma->videojuegos_count === 0)<form class="d-inline" method="POST" action="{{ route('plataformas.destroy', $plataforma) }}" onsubmit="return confirm('¿Eliminar esta plataforma?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button></form>@endif
                </td></tr>
        @empty<tr><td colspan="4" class="text-center gv-muted py-4">No hay plataformas todavía.</td></tr>@endforelse</tbody>
    </table></div><div class="mt-3">{{ $plataformas->links() }}</div></section>
@endsection
