@extends('layouts.base')
@include('components.header')

@section('content')
<div class="filme-dia-page"
     id="filme-dia-page"
     data-sortear-url="{{ route('filme-do-dia.sortear') }}"
     data-aleatorios-url="{{ route('filme-do-dia.aleatorios') }}">

    
    {{-- Poster inicial (placeholder) --}}
    <div class="poster-container" id="posterContainer">
        <img id="filmePoster" src="{{ $placeholderPoster }}" 
             alt="Poster do Filme" class="poster-img">
        
        {{-- Container da roleta sobreposta --}}
        <div class="roulette-container" id="rouletteContainer">
            <div class="roulette-track" id="rouletteTrack"></div>
        </div>
    </div>

    {{-- Botão principal --}}
    <form id="filmeDoDiaForm" class="filme-dia-form">
        @csrf

        
        <button type="submit" class="btn-sortear">🎲 Sortear Filme</button>
        

        <input type="hidden" name="genero" id="generoInput" value="">

        <div class="filters">
            

            <input type="number" name="ano" placeholder="Ano" min="1900" max="{{ date('Y') }}">
            <input type="text" name="diretor" placeholder="Diretor">

            {{-- Gênero para Biblioteca --}}
            <div class="filter-dropdown" id="dropdownBiblioteca">
                <button type="button" class="filter-btn" data-filter="genero_biblioteca">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="filter-label">Gênero</span>
                    <svg class="filter-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"/>
                    </svg>
                </button>
                <div class="filter-menu" data-menu="genero_biblioteca">
                    <div class="filter-options-list">
                        <button type="button" class="filter-select-option active" data-value="">Gênero: Todos</button>
                        @foreach($bibliotecaGeneros as $g)
                            <button type="button" class="filter-select-option" data-value="{{ $g }}">{{ $g }}</button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Gênero para TMDB --}}
            <div class="filter-dropdown" id="dropdownTmdb" style="display: none;">
                <button type="button" class="filter-btn" data-filter="genero_tmdb">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span class="filter-label">Gênero</span>
                    <svg class="filter-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"/>
                    </svg>
                </button>
                <div class="filter-menu" data-menu="genero_tmdb">
                    <div class="filter-options-list">
                        <button type="button" class="filter-select-option active" data-value="">Gênero: Todos</button>
                        @foreach($tmdbGeneros as $g)
                            <button type="button" class="filter-select-option" data-value="{{ $g['id'] }}">{{ $g['name'] }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="fonte">
            <x-back-button context="icon" href="{{ route('dashboard') }}"/>
            <label><input type="radio" name="fonte" value="biblioteca" checked> Minha Biblioteca</label>

            <!-- <label><input type="radio" name="fonte" value="tmdb"> TMDB</label> -->
        </div>
    </form>
</div>


@endsection
