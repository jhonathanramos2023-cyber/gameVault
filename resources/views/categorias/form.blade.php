@extends('layouts.app')
@php($editando = $categoria->exists)
@section('title', $editando ? 'Editar categoría' : 'Nueva categoría')
@section('content')
    <div class="row justify-content-center"><div class="col-12 col-lg-7">
        <section class="gv-panel">
            <p class="eyebrow">Administración</p><h1 class="h2 fw-bold">{{ $editando ? 'Editar categoría' : 'Nueva categoría' }}</h1>
            <form method="POST" action="{{ $editando ? route('categorias.update', $categoria) : route('categorias.store') }}">
                @csrf @if($editando) @method('PUT') @endif
                <div class="mb-3"><label class="form-label" for="nombre">Nombre</label><input class="form-control" id="nombre" name="nombre" maxlength="120" required value="{{ old('nombre', $categoria->nombre) }}"></div>
                <div class="mb-4"><label class="form-label" for="descripcion">Descripción</label><textarea class="form-control" id="descripcion" name="descripcion" rows="4" maxlength="2000">{{ old('descripcion', $categoria->descripcion) }}</textarea></div>
                <button class="btn btn-cyan" type="submit">{{ $editando ? 'Guardar cambios' : 'Crear categoría' }}</button>
                <a class="btn btn-outline-light" href="{{ route('categorias.index') }}">Cancelar</a>
            </form>
        </section>
    </div></div>
@endsection
