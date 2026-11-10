<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Catálogo educativo de proyectos abiertos y preservación de videojuegos.')">
    <title>@yield('title', 'GameVault') · GameVault</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body @php
$fondo = '';
if (request()->routeIs('catalogo.*')) {
    $fondo = 'principal';
} elseif (request()->routeIs('login')) {
    $fondo = 'login';
} elseif (request()->routeIs('dashboard')) {
    $fondo = 'panel';
} elseif (request()->routeIs('videojuegos.*')) {
    $fondo = 'registros';
} elseif (request()->routeIs('categorias.*')) {
    $fondo = 'categorias';
} elseif (request()->routeIs('plataformas.*')) {
    $fondo = 'plataformas';
} elseif (request()->routeIs('usuarios.*')) {
    $fondo = 'colaboradores';
} elseif (request()->routeIs('referencias.*')) {
    $fondo = 'referencias';
}
@endphp data-fondo="{{ $fondo }}">
@if ($fondo && file_exists(public_path("img/fondos/{$fondo}.jpg")))
<div class="fondo-seccion {{ $fondo }}" style="background-image: url('{{ asset("img/fondos/{$fondo}.jpg") }}');"></div>
@endif
<nav class="navbar navbar-expand-lg gv-navbar sticky-top">
    <div class="container gv-container">
        <a class="navbar-brand gv-brand d-flex align-items-center" href="{{ route('catalogo.index') }}">
            <span class="brand-mark">GV</span><span>GAMEVAULT</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#gvNavigation" aria-controls="gvNavigation" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="gvNavigation">
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <a class="nav-link" href="{{ route('catalogo.index') }}">Catálogo</a>
                @auth
                    <a class="nav-link" href="{{ route('dashboard') }}">Panel</a>
                    <a class="nav-link" href="{{ route('videojuegos.index') }}">Registros</a>
                    @if (auth()->user()->esAdministrador())
                        <a class="nav-link" href="{{ route('categorias.index') }}">Categorías</a>
                        <a class="nav-link" href="{{ route('plataformas.index') }}">Plataformas</a>
                        <a class="nav-link" href="{{ route('usuarios.index') }}">Colaboradores</a>
                    @endif
                    <span class="small gv-muted px-lg-2">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-light" type="submit">Cerrar sesión</button>
                    </form>
                @else
                    <a class="btn btn-sm btn-outline-light ms-lg-2" href="{{ route('login') }}">Acceso de equipo</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="gv-main">
    <div class="container gv-container py-4 py-lg-5">
        @if (session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Revisa los datos antes de continuar.</strong>
                <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </div>
</main>

<footer class="gv-footer py-4">
    <div class="container gv-container">
        <div class="gv-warning mb-3">
            Las referencias externas pertenecen a terceros. GameVault no analiza ni certifica la seguridad, legalidad ni disponibilidad de sus sitios y archivos, y no ofrece asesoría legal.
        </div>
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 small gv-muted">
            <span>Proyecto de cátedra · Universidad Luterana Salvadoreña</span>
            <span>Herramientas: Laravel · Breeze · Bootstrap</span>
        </div>
    </div>
</footer>
@stack('scripts')
</body>
</html>
