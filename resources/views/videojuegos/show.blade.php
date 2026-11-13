@extends('layouts.app')
@section('title', $videojuego->titulo)
@section('content')
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
        <div><p class="eyebrow mb-1">Ficha interna · {{ $videojuego->categoria->nombre }}</p><h1 class="h2 fw-bold">{{ $videojuego->titulo }}</h1><span class="badge badge-status status-{{ $videojuego->estado }}">{{ str_replace('_', ' ', ucfirst($videojuego->estado)) }}</span></div>
        <a class="btn btn-outline-light" href="{{ route('videojuegos.index') }}">Volver</a>
    </div>
    <section class="gv-panel mb-4">
        <p class="classification">{{ $videojuego->tipo_proyecto }} · {{ $videojuego->formato }}</p>
        <p class="lead">{{ $videojuego->descripcion }}</p>
        <div class="gv-warning">{{ $videojuego->advertencia }}</div>
        @if($videojuego->url_iso)
            <div class="mt-3">
                <a href="{{ $videojuego->url_iso }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-info">Ir a plataforma del juego ↗</a>
            </div>
        @endif
        <div class="d-flex flex-wrap gap-2 mt-3">@foreach($videojuego->plataformas as $plataforma)<span class="badge text-bg-dark border border-secondary-subtle">{{ $plataforma->nombre }}</span>@endforeach</div>
        <div class="small gv-muted mt-3">Registró {{ $videojuego->usuario->name }} · {{ $videojuego->updated_at->format('d/m/Y H:i') }}</div>
    </section>
    <div class="row g-3 mb-4">
        @foreach($videojuego->imagenes as $imagen)
            <div class="col-6 col-lg-4"><img class="detail-image" src="{{ \Illuminate\Support\Facades\Storage::disk(config('gamevault.image_disk'))->url($imagen->ruta) }}" alt="{{ $imagen->texto_alternativo }}"></div>
        @endforeach
    </div>
    <section class="gv-panel mb-4">
        <h2 class="h5">Referencias externas</h2>
        @forelse($videojuego->referencias as $referencia)
            <p class="mb-2"><a href="{{ $referencia->url }}" target="_blank" rel="noopener noreferrer">{{ $referencia->titulo }} ↗</a> <span class="small gv-muted">· {{ $referencia->tipo_fuente }}</span></p>
        @empty
            <p class="gv-muted mb-0">No hay referencias registradas.</p>
        @endforelse
        <a class="btn btn-sm btn-outline-info mt-3" href="{{ route('referencias.index', $videojuego) }}">Gestionar referencias</a>
    </section>
    @can('update', $videojuego)<a class="btn btn-cyan" href="{{ route('videojuegos.edit', $videojuego) }}">Editar ficha</a>@endcan
@endsection
