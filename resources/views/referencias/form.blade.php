@extends('layouts.app')
@php($editando = $referencia->exists)
@section('title', $editando ? 'Editar referencia' : 'Nueva referencia')
@section('content')
    @if(!auth()->user()->esAdministrador() && $videojuego->estado !== 'borrador')
        <div class="row justify-content-center"><div class="col-12 col-lg-8"><section class="gv-panel">
            <div class="alert alert-warning">
                Esta ficha está en revisión o publicada; solo el administrador puede devolverla a borrador.
            </div>
            <a class="btn btn-outline-light" href="{{ route('referencias.index', $videojuego) }}">Volver</a>
        </section></div></div>
    @else
        <div class="row justify-content-center"><div class="col-12 col-lg-8"><section class="gv-panel">
            <p class="eyebrow">Referencia documental</p><h1 class="h2 fw-bold">{{ $editando ? 'Editar fuente externa' : 'Añadir fuente externa' }}</h1><p class="gv-muted">Asociada a {{ $videojuego->titulo }}. GameVault no analiza ni certifica los destinos.</p>
            <form method="POST" action="{{ $editando ? route('referencias.update', [$videojuego, $referencia]) : route('referencias.store', $videojuego) }}">
                @csrf @if($editando) @method('PUT') @endif
                <div class="mb-3"><label class="form-label" for="titulo">Título de la fuente</label><input id="titulo" name="titulo" class="form-control" maxlength="180" required value="{{ old('titulo', $referencia->titulo) }}"></div>
                <div class="mb-3"><label class="form-label" for="url">URL (HTTP o HTTPS)</label><input id="url" name="url" type="url" class="form-control" maxlength="2048" required placeholder="https://sitio-oficial.example" value="{{ old('url', $referencia->url) }}"></div>
                <div class="mb-3"><label class="form-label" for="tipo_fuente">Tipo de fuente</label><select id="tipo_fuente" name="tipo_fuente" class="form-select" required>@foreach(config('gamevault.fuentes') as $fuente)<option value="{{ $fuente }}" @selected(old('tipo_fuente', $referencia->tipo_fuente ?: 'Sitio oficial') === $fuente)>{{ $fuente }}</option>@endforeach</select></div>
                <div class="mb-3"><label class="form-label" for="observaciones">Observaciones</label><textarea id="observaciones" name="observaciones" rows="3" class="form-control" maxlength="3000">{{ old('observaciones', $referencia->observaciones) }}</textarea></div>
                <div class="mb-4"><label class="form-label" for="fecha_consulta">Fecha de consulta</label><input id="fecha_consulta" name="fecha_consulta" type="date" class="form-control" value="{{ old('fecha_consulta', $referencia->fecha_consulta?->format('Y-m-d')) }}"></div>
                <button class="btn btn-cyan" type="submit">{{ $editando ? 'Guardar cambios' : 'Guardar referencia' }}</button>
                <a class="btn btn-outline-light" href="{{ route('referencias.index', $videojuego) }}">Cancelar</a>
            </form>
        </section></div></div>
    @endif
@endsection
