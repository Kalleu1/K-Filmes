@extends('layouts.app')

@section('content')
<div class="search-page">
    <x-back-button />

    <div class="searchContainer">
        {{-- Header da busca --}}
        <div class="search-header">
            <h1 class="search-title">🔍 Buscar Filmes</h1>
            <p class="search-subtitle">Descubra novos filmes para sua coleção</p>
        </div>

        {{-- Formulário de busca --}}
        <form action="{{ route('filmes.buscarTmdb') }}" method="GET" class="search-form">
            <div class="searchInput-group">
                <input 
                    type="text" 
                    name="q" 
                    class="searchInput" 
                    placeholder="Digite o nome do filme..." 
                    value="{{ $query ?? '' }}"
                    autocomplete="off"
                >
                <button type="submit" class="search-button">
                    <span class="search-button-text">Buscar</span>
                    <span class="search-button-icon">🔍</span>
                </button>
            </div>
        </form>

        {{-- Resultados --}}
        @isset($results)
            <div class="search-results">
                <div class="results-header">
                    <h2 class="results-title">Resultados para "<span class="query-highlight">{{ $query }}</span>"</h2>
                    <span class="results-count">{{ count($results) }} filme(s) encontrado(s)</span>
                </div>

                <div class="search-grid">
                    @forelse($results as $result)
                        @php
                            $filme = (object) [
                                'tmdb_id' => $result->tmdb_id ?? null,
                                'nome' => $result->nome ?? 'Sem título',
                                'poster_url' => !empty($result->poster_url)
                                    ? $result->poster_url
                                    : asset('img/poster-placeholder.png'),
                                'ano' => !empty($result->release_date)
                                    ? substr($result->release_date, 0, 4)
                                    : 'Desconhecido',
                            ];
                        @endphp

                        {{-- Card de filme customizado para busca --}}
                        <div class="search-movie-card">
                            <a href="{{ isset($filme->id) 
                                        ? route('filmes.show', $filme->id) 
                                        : (isset($filme->tmdb_id) ? route('filmes.showTmdb', $filme->tmdb_id) : '#') }}"
                            class="movie-link">
                            
                            <div class="movie-poster-container">
                                <img src="{{ $filme->poster_url }}" alt="{{ $filme->nome }}" class="movie-poster">
                                <div class="movie-overlay">
                                    <span class="view-details">Ver Detalhes</span>
                                </div>
                            </div>

                            </a>
                        </div>


                    @empty
                        <div class="no-results">
                            <div class="no-results-icon">🎬</div>
                            <h3>Nenhum filme encontrado</h3>
                            <p>Tente buscar com palavras-chave diferentes</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            {{-- Estado inicial --}}
            <div class="search-empty-state">
                <div class="empty-state-icon">🎭</div>
                <h3>Pronto para descobrir novos filmes?</h3>
                <p>Digite o nome de um filme na barra de busca acima</p>
            </div>
        @endisset
    </div>
</div>
@endsection