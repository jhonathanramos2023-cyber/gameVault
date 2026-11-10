@extends('layouts.app')

@section('title', 'Catálogo de proyectos abiertos')
@section('description', 'Explora fichas educativas sobre proyectos de videojuegos abiertos, preservación y emulación.')

@section('content')
    <section class="gv-hero mb-4 mb-lg-5">
        <div class="scanline"></div>
        <p class="eyebrow mb-3">Archivo documental · acceso abierto</p>
        <h1 class="display-title display-4 mb-3 glitch-text">GAMEVAULT</h1>
        <p class="lead gv-muted mb-0 typewriter" style="max-width: 690px">Proyectos que mantienen vivos los videojuegos.</p>
    </section>

    <section class="gv-panel mb-4">
        <form action="{{ route('catalogo.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-lg-4">
                <label for="q" class="form-label">Buscar en el archivo</label>
                <input id="q" name="q" value="{{ request('q') }}" class="form-control" type="search" placeholder="Título o palabras clave">
            </div>
            <div class="col-6 col-lg-2">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select id="categoria_id" name="categoria_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-lg-2">
                <label for="plataforma_id" class="form-label">Plataforma</label>
                <select id="plataforma_id" name="plataforma_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach ($plataformas as $plataforma)
                        <option value="{{ $plataforma->id }}" @selected(request('plataforma_id') == $plataforma->id)>{{ $plataforma->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-lg-2">
                <label for="formato" class="form-label">Formato</label>
                <select id="formato" name="formato" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($formatos as $formato)
                        <option value="{{ $formato }}" @selected(request('formato') === $formato)>{{ ucfirst($formato) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-lg-2">
                <label for="tipo_proyecto" class="form-label">Tipo</label>
                <select id="tipo_proyecto" name="tipo_proyecto" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($tipos as $tipo)
                        <option value="{{ $tipo }}" @selected(request('tipo_proyecto') === $tipo)>{{ ucfirst($tipo) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-grid d-sm-flex gap-2">
                <button class="btn btn-cyan" type="submit">Aplicar filtros</button>
                <a class="btn btn-outline-light" href="{{ route('catalogo.index') }}">Limpiar</a>
                <span class="small gv-muted text-sm-end ms-sm-auto">{{ $videojuegos->total() }} fichas publicadas</span>
            </div>
        </form>
    </section>

    <section aria-labelledby="catalog-heading">
        <div class="d-flex align-items-end justify-content-between mb-3">
            <div><p class="eyebrow mb-1">Biblioteca curada</p><h2 id="catalog-heading" class="h3 mb-0">Explorar proyectos</h2></div>
        </div>
        @if ($videojuegos->count())
            <div class="row g-3 g-lg-4">
                @foreach ($videojuegos as $videojuego)
                    @php($portada = $videojuego->portada)
                    <div class="col-12 col-sm-6 col-xl-4 catalog-card">
                        <article class="card gv-card tilt-card">
                            <a href="{{ route('catalogo.show', $videojuego->slug) }}" class="cover-frame" aria-label="Ver ficha de {{ $videojuego->titulo }}">
                                @if ($portada)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('gamevault.image_disk'))->url($portada->ruta) }}" alt="{{ $portada->texto_alternativo }}">
                                @else
                                    <span class="cover-fallback">GV / ARCHIVO</span>
                                @endif
                            </a>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                    <span class="classification">{{ $videojuego->tipo_proyecto }}</span>
                                    <span class="small gv-muted">{{ $videojuego->categoria->nombre }}</span>
                                </div>
                                <h3 class="h5"><a class="text-light" href="{{ route('catalogo.show', $videojuego->slug) }}">{{ $videojuego->titulo }}</a></h3>
                                <p class="small gv-muted flex-grow-1">{{ \Illuminate\Support\Str::limit($videojuego->descripcion, 140) }}</p>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @foreach ($videojuego->plataformas as $plataforma)
                                        <span class="badge text-bg-dark border border-secondary-subtle">{{ $plataforma->nombre }}</span>
                                    @endforeach
                                </div>
                                <a class="btn btn-sm btn-outline-info align-self-start" href="{{ route('catalogo.show', $videojuego->slug) }}">Consultar ficha <span aria-hidden="true">→</span></a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $videojuegos->links() }}</div>
        @else
            <div class="empty-state gv-panel">
                <p class="eyebrow">Sin coincidencias</p>
                <h3 class="h5">No encontramos fichas con esos criterios.</h3>
                <p class="gv-muted mb-3">Prueba con otras palabras o limpia los filtros.</p>
                <a class="btn btn-outline-info" href="{{ route('catalogo.index') }}">Ver todo el catálogo</a>
            </div>
        @endif
    </section>
@endsection
