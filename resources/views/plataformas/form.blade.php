@extends('layouts.app')
@php($editando = $plataforma->exists)
@section('title', $editando ? 'Editar plataforma' : 'Nueva plataforma')
@section('content')
    <div class="row justify-content-center"><div class="col-12 col-lg-7"><section class="gv-panel">
        <p class="eyebrow">Administración</p><h1 class="h2 fw-bold">{{ $editando ? 'Editar plataforma' : 'Nueva plataforma' }}</h1>
        <form method="POST" action="{{ $editando ? route('plataformas.update', $plataforma) : route('plataformas.store') }}">
            @csrf @if($editando) @method('PUT') @endif
            <div class="mb-3"><label class="form-label" for="nombre">Nombre</label><input class="form-control" id="nombre" name="nombre" maxlength="120" required value="{{ old('nombre', $plataforma->nombre) }}"></div>
            <div class="mb-4"><label class="form-label" for="tipo">Tipo de plataforma</label><input class="form-control" id="tipo" name="tipo" maxlength="80" required placeholder="Escritorio, consola..." value="{{ old('tipo', $plataforma->tipo) }}"></div>
            <button class="btn btn-cyan" type="submit">{{ $editando ? 'Guardar cambios' : 'Crear plataforma' }}</button>
            <a class="btn btn-outline-light" href="{{ route('plataformas.index') }}">Cancelar</a>
        </form>
    </section></div></div>
@endsection
