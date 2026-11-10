@extends('layouts.app')
@section('title', 'Acceso del equipo')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <section class="gv-panel login-panel">
                <p class="eyebrow">Área editorial</p>
                <h1 class="h2 fw-bold mb-2 typewriter-title">ACCESO RESTRINGIDO</h1>
                <p class="gv-muted mb-4">Inicia sesión para gestionar tus registros o revisar el catálogo.</p>
                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">> Correo electrónico</label>
                        <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username">
                        @error('email')<div class="text-danger small mt-1">> {{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">> Contraseña</label>
                        <input id="password" name="password" type="password" class="form-control" required autocomplete="current-password">
                        @error('password')<div class="text-danger small mt-1">> {{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-cyan w-100" type="submit">> INICIAR SESIÓN</button>
                </form>
                <p class="small gv-muted mt-3 mb-0">> Las cuentas son creadas por la administración. No hay registro público.</p>
            </section>
        </div>
    </div>
@endsection
