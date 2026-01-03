@extends('layouts.base')
@include('components.header')

@section('content')
<div class="library-page" style="background: transparent;">
    <div class="library-container">
        {{-- Header da biblioteca --}}
        <div class="library-header">
            <h1 class="library-title">📚 Minha Biblioteca</h1>
            <p class="library-subtitle">Sua coleção pessoal</p>
        </div>

        {{-- Barra de busca e filtros minimalistas --}}
        <div class="library-search-bar">
            {{-- Busca principal --}}
            <form action="{{ route('filmes.buscarBiblioteca') }}" method="GET" class="main-search">
                <div class="search-container">
                    <input 
                        type="text" 
                        name="q" 
                        class="search-input" 
                        placeholder="Buscar na biblioteca..."
                        value="{{ $query ?? '' }}" 
                        autocomplete="off"
                    >
                    <button type="submit" class="search-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                    </button>
                </div>
            </form>

            {{-- Filtros minimalistas --}}
            <div class="filters-minimal">
                {{-- Filtro por Status --}}
                <div class="filter-dropdown">
                    <button type="button" class="filter-btn" data-filter="status">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4"/>
                            <circle cx="12" cy="12" r="10"/>
                        </svg>
                        <span class="filter-label">Status</span>
                        <svg class="filter-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"/>
                        </svg>
                    </button>
                    <div class="filter-menu" data-menu="status">
                        <form method="GET" action="{{ route('filmes.biblioteca') }}">
                            <label class="filter-option">
                                <input type="radio" name="assistido" value="" {{ !request('assistido') ? 'checked' : '' }}>
                                <span>Todos</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="assistido" value="1" {{ request('assistido') === '1' ? 'checked' : '' }}>
                                <span>Assistidos</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="assistido" value="0" {{ request('assistido') === '0' ? 'checked' : '' }}>
                                <span>Não assistidos</span>
                            </label>
                            <button type="submit" class="apply-filter">Aplicar</button>
                        </form>
                    </div>
                </div>

                {{-- Filtro por Diretor --}}
                <div class="filter-dropdown">
                    <button type="button" class="filter-btn" data-filter="director">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span class="filter-label">Diretor</span>
                        <svg class="filter-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"/>
                        </svg>
                    </button>
                    <div class="filter-menu" data-menu="director">
                        <form method="GET" action="{{ route('filmes.biblioteca') }}">
                            <input type="text" name="diretor" placeholder="Nome do diretor..." value="{{ request('diretor') }}" class="filter-input">
                            <button type="submit" class="apply-filter">Aplicar</button>
                        </form>
                    </div>
                </div>

                {{-- Filtro por Gênero --}}
                <div class="filter-dropdown">
                    <button type="button" class="filter-btn" data-filter="genre">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span class="filter-label">Gênero</span>
                        <svg class="filter-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"/>
                        </svg>
                    </button>
                    <div class="filter-menu" data-menu="genre">
                        <form method="GET" action="{{ route('filmes.biblioteca') }}">
                            <input type="text" name="genero" placeholder="Ex: Ação, Drama..." value="{{ request('genero') }}" class="filter-input">
                            <button type="submit" class="apply-filter">Aplicar</button>
                        </form>
                    </div>
                </div>

                {{-- Filtro por Ano --}}
                <div class="filter-dropdown">
                    <button type="button" class="filter-btn" data-filter="year">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span class="filter-label">Ano</span>
                        <svg class="filter-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"/>
                        </svg>
                    </button>
                    <div class="filter-menu" data-menu="year">
                        <form method="GET" action="{{ route('filmes.biblioteca') }}">
                            <input type="number" name="ano_lancamento" placeholder="2024" value="{{ request('ano_lancamento') }}" min="1900" max="2030" class="filter-input">
                            <button type="submit" class="apply-filter">Aplicar</button>
                        </form>
                    </div>
                </div>

                {{-- Botão limpar filtros --}}
                @if(request()->hasAny(['assistido', 'favorito', 'generos', 'ano_lancamento', 'diretor']))
                    <a href="{{ route('filmes.biblioteca') }}" class="clear-filters">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        Limpar
                    </a>
                @endif
            </div>
        </div>

        {{-- Resultados --}}
        <div class="library-results">
            @if($filmes->total() > 0)
                <div class="results-count">
                   Filmes Encontrados: {{ $filmes->total() }} filme{{ $filmes->total() !== 1 ? 's' : '' }}
                </div>
            @endif

            <x-back-button context="page" href="{{ route('dashboard') }}"/>

            {{-- Grid de filmes --}}
            <div class="library-grid">
                @forelse($filmes as $filme)
                    <div class="library-movie-item">
                        <div class="movie-poster-wrapper">
                            <x-filmecard :filme="$filme" :campos="['poster']" />
                            
                            {{-- Indicadores discretos --}}
                            <div class="movie-indicators">
                                @if($filme->data_assistida)
                                    <div class="indicator-watched">✓</div>
                                @else
                                    <div class="indicator-not-watched">○</div>
                                @endif
                                
                                @if($filme->nota)
                                    <div class="indicator-rating">{{ number_format($filme->nota, 1) }}</div>
                                @endif
                            </div>

                            {{-- Botão de favorito --}}
                            <button class="favorite-btn {{ $filme->favorito ? 'favorited' : '' }}" 
                                data-filme-id="{{ $filme->id }}"    
                                onclick="toggleFavorite({{ $filme->id }})" 
                                    >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $filme->favorito ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="library-empty">
                        <div class="empty-icon">📚</div>
                        <h3>Nenhum filme encontrado</h3>
                        <p>
                            @if(request()->hasAny(['assistido', 'favorito', 'generos', 'ano_lancamento', 'diretor']))
                                <a href="{{ route('filmes.biblioteca') }}" class="empty-link">Limpar filtros</a>
                            @else
                                <a href="{{ route('filmes.busca') }}" class="empty-link">Busque filmes para adicionar</a>
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

                        @if ($filmes->hasPages())
                            <div class="library-pagination">
                                <x-pagination :paginator="$filmes" />
                            </div>
                        @endif
        </div>
    </div>
</div>


@endsection