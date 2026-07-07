@php
    $isHome = request()->routeIs('dashboard') && request('tab') !== 'config';
    $isSearch = request()->routeIs('filmes.busca') || request()->routeIs('filmes.buscarTmdb');
    $isLibrary = request()->routeIs('filmes.biblioteca')
        || request()->routeIs('filmes.buscarBiblioteca')
        || request()->routeIs('filmes.assistidos')
        || request()->routeIs('filmes.naoAssistidos');
    $isDiscover = request()->routeIs('discover.*');
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

    <a href="{{route('filmes.filme-do-dia')}}">  <button id="navSortear" class="bottom-nav__item bottom-nav__item--primary" title="Sortear filme" aria-label="Sortear filme aleatório">
        <i class="fa-solid fa-dice bottom-nav__icon" aria-hidden="true"></i>
    </button>  </a>

    <a href="{{ route('filmes.biblioteca') }}" class="bottom-nav__item {{ $isLibrary ? 'is-active' : '' }}" @if($isLibrary) aria-current="page" @endif>
        <i class="fa-solid fa-clapperboard bottom-nav__icon" aria-hidden="true"></i>
        <span class="bottom-nav__label">Biblioteca</span>
    </a>

    <a href="{{ route('discover.index') }}" class="bottom-nav__item {{ $isDiscover ? 'is-active' : '' }}" @if($isDiscover) aria-current="page" @endif>
        <i class="fa-solid fa-compass bottom-nav__icon" aria-hidden="true"></i>
        <span class="bottom-nav__label">Descobrir</span>
    </a>
</nav>
