@extends('layouts.app')

@section('title', $videojuego->titulo)
@section('description', \Illuminate\Support\Str::limit($videojuego->descripcion, 155))

@section('content')
    <a href="{{ route('catalogo.index') }}" class="small">&larr; Volver al catálogo</a>
    <div class="row g-4 mt-1">
        <div class="col-12 col-lg-7">
            @if ($videojuego->imagenes->isNotEmpty())
                <div class="gv-panel">
                    @foreach ($videojuego->imagenes as $imagen)
                        <img class="detail-image mb-3" src="{{ \Illuminate\Support\Facades\Storage::disk(config('gamevault.image_disk'))->url($imagen->ruta) }}" alt="{{ $imagen->texto_alternativo }}">
                    @endforeach
                </div>
            @else
                <div class="cover-frame rounded-4"><span class="cover-fallback">GV / ARCHIVO</span></div>
            @endif
        </div>
        <div class="col-12 col-lg-5">
            <p class="eyebrow">{{ $videojuego->categoria->nombre }} · {{ $videojuego->formato }}</p>
            <h1 class="display-5 fw-bold glitch-title">{{ $videojuego->titulo }}</h1>
            <span class="badge badge-status status-publicado mb-2">Publicado</span>
            <p class="classification mb-3">{{ $videojuego->tipo_proyecto }}</p>
            <p class="lead gv-muted">{{ $videojuego->descripcion }}</p>
            @if($videojuego->url_iso)
                <div class="my-3">
                    <a href="{{ $videojuego->url_iso }}" target="_blank" rel="noopener noreferrer" class="btn btn-cyan w-100">Ir a plataforma del juego ↗</a>
                </div>
            @endif
            <div class="gv-warning my-4">{{ $videojuego->advertencia }}</div>
            <div class="gv-panel mb-3">
                <h2 class="h6 text-uppercase gv-muted">Plataformas documentadas</h2>
                <div class="d-flex flex-wrap gap-2">
                    @forelse ($videojuego->plataformas as $plataforma)
                        <span class="badge text-bg-dark border border-secondary-subtle">{{ $plataforma->nombre }}</span>
                    @empty
                        <span class="gv-muted">Sin plataformas registradas.</span>
                    @endforelse
                </div>
            </div>
            @if ($videojuego->fecha_lanzamiento)
                <p><span class="gv-muted">Fecha documentada:</span> {{ $videojuego->fecha_lanzamiento->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-12 col-lg-6">
            <section class="gv-panel h-100">
                <h2 class="h5">Información técnica</h2>
                <p class="mb-0 gv-muted">{{ $videojuego->informacion_tecnica ?: 'No hay información técnica documentada.' }}</p>
            </section>
        </div>
        <div class="col-12 col-lg-6">
            <section class="gv-panel h-100">
                <h2 class="h5">Requisitos conocidos</h2>
                <p class="mb-0 gv-muted">{{ $videojuego->requisitos_conocidos ?: 'No hay requisitos conocidos documentados.' }}</p>
            </section>
        </div>
        <div class="col-12">
            <section class="gv-panel">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                    <div><p class="eyebrow mb-1">Fuentes externas</p><h2 class="h5 mb-0">Referencias documentales</h2></div>
                    <span class="small gv-muted">Enlaces informativos; GameVault no verifica su disponibilidad.</span>
                </div>
                <div class="row g-3">
                    @forelse ($videojuego->referencias as $referencia)
                        <div class="col-12 col-md-6">
                            <article class="p-3 rounded-3 border border-secondary-subtle h-100 terminal-reference">
                                <span class="eyebrow">> {{ $referencia->tipo_fuente }}</span>
                                <h3 class="h6 mt-2">{{ $referencia->titulo }}</h3>
                                @if ($referencia->observaciones)<p class="small gv-muted">> {{ $referencia->observaciones }}</p>@endif
                                @if ($referencia->fecha_consulta)<p class="small gv-muted">> Consultada: {{ $referencia->fecha_consulta->format('d/m/Y') }}</p>@endif
                                <a class="reference-link" href="{{ $referencia->url }}" target="_blank" rel="noopener noreferrer">> {{ $referencia->url }} <span aria-hidden="true">↗</span></a>
                            </article>
                        </div>
                    @empty
                        <div class="col-12"><p class="gv-muted mb-0">> No hay referencias externas publicadas.</p></div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
