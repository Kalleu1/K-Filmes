@php
    $isHome = request()->routeIs('dashboard') && request('tab') !== 'config';
    $isSearch = request()->routeIs('filmes.busca') || request()->routeIs('filmes.buscarTmdb');
    $isLibrary = request()->routeIs('filmes.biblioteca')
        || request()->routeIs('filmes.buscarBiblioteca')
        || request()->routeIs('filmes.assistidos')
        || request()->routeIs('filmes.naoAssistidos');
    $isSettings = request()->routeIs('dashboard') && request('tab') === 'config';
@endphp

<nav class="bottom-nav" aria-label="Navegacao principal mobile">
    <a href="{{ route('dashboard') }}" class="bottom-nav__item {{ $isHome ? 'is-active' : '' }}" @if($isHome) aria-current="page" @endif>
        <i class="fa-solid fa-house bottom-nav__icon" aria-hidden="true"></i>
        <span class="bottom-nav__label">Inicio</span>
    </a>

    <a href="{{ route('filmes.busca') }}" class="bottom-nav__item {{ $isSearch ? 'is-active' : '' }}" @if($isSearch) aria-current="page" @endif>
        <i class="fa-solid fa-magnifying-glass bottom-nav__icon" aria-hidden="true"></i>
        <span class="bottom-nav__label">Buscar</span>
    </a>

    <a href="{{ route('filmes.biblioteca') }}" class="bottom-nav__item {{ $isLibrary ? 'is-active' : '' }}" @if($isLibrary) aria-current="page" @endif>
        <i class="fa-solid fa-clapperboard bottom-nav__icon" aria-hidden="true"></i>
        <span class="bottom-nav__label">Biblioteca</span>
    </a>

    <a href="{{ route('dashboard', ['tab' => 'config']) }}" class="bottom-nav__item {{ $isSettings ? 'is-active' : '' }}" @if($isSettings) aria-current="page" @endif>
        <i class="fa-solid fa-gear bottom-nav__icon" aria-hidden="true"></i>
        <span class="bottom-nav__label">Config</span>
    </a>
</nav>
