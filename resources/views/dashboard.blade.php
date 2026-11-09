@extends('layouts.app')
@section('title', 'Panel editorial')
@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><p class="eyebrow mb-1">Espacio de trabajo</p><h1 class="h2 fw-bold mb-1">Panel editorial</h1><p class="gv-muted mb-0">Hola, {{ auth()->user()->name }}. Este es el estado de tus registros.</p></div>
        <a class="btn btn-cyan" href="{{ route('videojuegos.create') }}">Nuevo registro <span aria-hidden="true">+</span></a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="gv-stat"><span class="small gv-muted">Registros</span><strong>{{ $resumen['registros'] }}</strong><span class="small gv-muted">en el sistema</span></div></div>
        <div class="col-6 col-lg-3"><div class="gv-stat"><span class="small gv-muted">Borradores</span><strong>{{ $resumen['borradores'] }}</strong><span class="small gv-muted">pendientes de enviar</span></div></div>
        <div class="col-6 col-lg-3"><div class="gv-stat"><span class="small gv-muted">En revisión</span><strong>{{ $resumen['en_revision'] }}</strong><span class="small gv-muted">esperando decisión</span></div></div>
        <div class="col-6 col-lg-3"><div class="gv-stat"><span class="small gv-muted">{{ auth()->user()->esAdministrador() ? 'Usuarios activos' : 'Publicados' }}</span><strong>{{ auth()->user()->esAdministrador() ? $resumen['usuarios_activos'] : $resumen['publicados'] }}</strong><span class="small gv-muted">{{ auth()->user()->esAdministrador() ? 'colaboradores y administración' : 'visibles en catálogo' }}</span></div></div>
    </div>
    <section class="gv-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div><p class="eyebrow mb-1">Actividad reciente</p><h2 class="h5 mb-0">{{ auth()->user()->esAdministrador() ? 'Últimos registros' : 'Tus últimos registros' }}</h2></div>
            <a href="{{ route('videojuegos.index') }}">Ver todos</a>
        </div>
        @if ($videojuegos->isEmpty())
            <div class="empty-state"><h3 class="h6">Todavía no hay registros.</h3><p class="gv-muted mb-3">Crea una ficha documental para comenzar.</p><a class="btn btn-outline-info" href="{{ route('videojuegos.create') }}">Crear primer registro</a></div>
        @else
            <div class="table-responsive">
                <table class="table gv-table align-middle mb-0">
                    <thead><tr><th>Título</th><th>Estado</th><th>Categoría</th><th>Actualización</th><th></th></tr></thead>
                    <tbody>@foreach ($videojuegos as $videojuego)
                        <tr>
                            <td><a href="{{ route('videojuegos.show', $videojuego) }}" class="text-light fw-semibold">{{ $videojuego->titulo }}</a>@if(auth()->user()->esAdministrador())<div class="small gv-muted">{{ $videojuego->usuario->name }}</div>@endif</td>
                            <td><span class="badge badge-status status-{{ $videojuego->estado }}">{{ str_replace('_', ' ', ucfirst($videojuego->estado)) }}</span></td>
                            <td>{{ $videojuego->categoria->nombre }}</td>
                            <td class="small gv-muted">{{ $videojuego->updated_at->diffForHumans() }}</td>
                            <td><a class="btn btn-sm btn-outline-info" href="{{ route('videojuegos.edit', $videojuego) }}">Abrir</a></td>
                        </tr>
                    @endforeach</tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
