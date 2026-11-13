@extends('layouts.app')
@section('title', 'Referencias')
@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><p class="eyebrow mb-1">Fuentes externas</p><h1 class="h2 fw-bold mb-1">Referencias: {{ $videojuego->titulo }}</h1><p class="gv-muted mb-0">Documentación externa únicamente. No se comprueba ni descarga el contenido de los enlaces.</p></div>
        <div class="d-flex gap-2"><a href="{{ route('videojuegos.edit', $videojuego) }}" class="btn btn-outline-light">Volver a la ficha</a>@if(auth()->user()->esAdministrador() || $videojuego->estado === 'borrador')<a href="{{ route('referencias.create', $videojuego) }}" class="btn btn-cyan">Añadir referencia</a>@endif</div>
    </div>
    
    @if(!auth()->user()->esAdministrador() && $videojuego->estado !== 'borrador')
        <section class="gv-panel mb-4">
            <div class="alert alert-warning">
                Esta ficha está en revisión o publicada; solo el administrador puede devolverla a borrador.
            </div>
        </section>
    @endif
    
    <section class="gv-panel">
        @forelse($referencias as $referencia)
            <article class="border-bottom border-secondary-subtle py-3">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div><span class="eyebrow">{{ $referencia->tipo_fuente }}</span><h2 class="h5 mt-1">{{ $referencia->titulo }}</h2><a class="reference-link" href="{{ $referencia->url }}" target="_blank" rel="noopener noreferrer">{{ $referencia->url }} ↗</a>
                        @if($referencia->observaciones)<p class="gv-muted mt-2 mb-0">{{ $referencia->observaciones }}</p>@endif
                        @if($referencia->fecha_consulta)<div class="small gv-muted mt-1">Consultada: {{ $referencia->fecha_consulta->format('d/m/Y') }}</div>@endif
                    </div>
                    @if(auth()->user()->esAdministrador() || $videojuego->estado === 'borrador')
                        <div class="d-flex gap-2 align-items-start"><a class="btn btn-sm btn-outline-info" href="{{ route('referencias.edit', [$videojuego, $referencia]) }}">Editar</a>
                            <form method="POST" action="{{ route('referencias.destroy', [$videojuego, $referencia]) }}" onsubmit="return confirm('¿Eliminar esta referencia?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button></form>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="empty-state"><h2 class="h5">Sin referencias todavía.</h2><p class="gv-muted">Agrega sitios oficiales, documentación o fuentes de contexto.</p>@if(auth()->user()->esAdministrador() || $videojuego->estado === 'borrador')<a class="btn btn-outline-info" href="{{ route('referencias.create', $videojuego) }}">Añadir referencia</a>@endif</div>
        @endforelse
        <div class="mt-3">{{ $referencias->links() }}</div>
    </section>
@endsection
