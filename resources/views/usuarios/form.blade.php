@extends('layouts.app')
@php($editando = $usuario->exists)
@section('title', $editando ? 'Editar colaborador' : 'Crear colaborador')
@section('content')
    <div class="row justify-content-center"><div class="col-12 col-lg-7"><section class="gv-panel">
        <p class="eyebrow">Administración</p><h1 class="h2 fw-bold">{{ $editando ? 'Editar colaborador' : 'Crear cuenta de colaborador' }}</h1>
        <p class="gv-muted">No hay registro público. Las contraseñas deben tener al menos 10 caracteres.</p>
        <form method="POST" action="{{ $editando ? route('usuarios.update', $usuario) : route('usuarios.store') }}">
            @csrf @if($editando) @method('PUT') @endif
            <div class="mb-3"><label class="form-label" for="name">Nombre</label><input class="form-control" id="name" name="name" maxlength="120" required value="{{ old('name', $usuario->name) }}"></div>
            <div class="mb-3"><label class="form-label" for="email">Correo electrónico</label><input class="form-control" id="email" name="email" type="email" maxlength="255" required value="{{ old('email', $usuario->email) }}"></div>
            <div class="mb-3"><label class="form-label" for="password">Contraseña {{ $editando ? '(deja en blanco para conservarla)' : '' }}</label><input class="form-control" id="password" name="password" type="password" minlength="10" {{ $editando ? '' : 'required' }} autocomplete="new-password"></div>
            <div class="mb-4"><label class="form-label" for="password_confirmation">Confirmar contraseña</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="10" {{ $editando ? '' : 'required' }} autocomplete="new-password"></div>
            <button class="btn btn-cyan" type="submit">{{ $editando ? 'Guardar cambios' : 'Crear colaborador' }}</button>
            <a class="btn btn-outline-light" href="{{ route('usuarios.index') }}">Cancelar</a>
        </form>
    </section></div></div>
@endsection
