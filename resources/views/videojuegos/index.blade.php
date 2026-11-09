@extends('layouts.app')
@section('title', 'Registros')
@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><p class="eyebrow mb-1">Archivo editorial</p><h1 class="h2 fw-bold mb-1">Registros de videojuegos</h1><p class="gv-muted mb-0">{{ auth()->user()->esAdministrador() ? 'Administra fichas y decisiones editoriales.' : 'Solo tú puedes ver y editar tus propios borradores.' }}</p></div>
        <a href="{{ route('videojuegos.create') }}" class="btn btn-cyan">Crear ficha</a>
    </div>
    <section class="gv-panel">
        @if ($videojuegos->isEmpty())
            <div class="empty-state"><h2 class="h5">No hay registros todavía.</h2><p class="gv-muted">Crea una ficha para iniciar la documentación.</p><a href="{{ route('videojuegos.create') }}" class="btn btn-outline-info">Crear ficha</a></div>
        @else
            <div class="table-responsive">
                <table class="table gv-table align-middle">
                    <thead><tr><th>Título / clasificación</th>@if(auth()->user()->esAdministrador())<th>Colaborador</th>@endif<th>Estado</th><th>Categoría</th><th>Imágenes</th><th>Actualizado</th><th></th></tr></thead>
                    <tbody>
                    @foreach ($videojuegos as $videojuego)
                        <tr>
                            <td><a class="text-light fw-semibold" href="{{ route('videojuegos.show', $videojuego) }}">{{ $videojuego->titulo }}</a><div class="small gv-muted">{{ $videojuego->tipo_proyecto }}</div></td>
                            @if(auth()->user()->esAdministrador())<td>{{ $videojuego->usuario->name }}</td>@endif
                            <td><span class="badge badge-status status-{{ $videojuego->estado }}">{{ str_replace('_', ' ', ucfirst($videojuego->estado)) }}</span></td>
                            <td>{{ $videojuego->categoria->nombre }}</td>
                            <td>{{ $videojuego->imagenes->count() }}</td>
                            <td class="small gv-muted">{{ $videojuego->updated_at->diffForHumans() }}</td>
                            <td class="text-nowrap"><a class="btn btn-sm btn-outline-info" href="{{ route('videojuegos.show', $videojuego) }}">Abrir</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $videojuegos->links() }}</div>
        @endif
    </section>
@endsection
