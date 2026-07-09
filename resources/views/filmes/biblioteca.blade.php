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
        <form action="{{ route('filmes.biblioteca') }}" method="GET" class="library-search-bar">
            {{-- Inputs Ocultos para Filtros --}}
            <input type="hidden" name="genero" value="{{ request('genero') }}">
            <input type="hidden" name="assistido" value="{{ request('assistido') }}">
            <input type="hidden" name="nota" value="{{ request('nota') }}">
            <input type="hidden" name="plataforma" value="{{ request('plataforma') }}">
            <input type="hidden" name="ano_lancamento" value="{{ request('ano_lancamento') }}">
            <input type="hidden" name="ordenacao" value="{{ request('ordenacao', 'recentes') }}">

            {{-- Busca principal --}}
            <div class="main-search">
                <div class="search-container">
                    <input 
                        type="text" 
                        name="q" 
                        class="search-input" 
                        placeholder="Buscar na biblioteca..."
                        value="{{ request('q') }}" 
                        autocomplete="off"
                    >
                    <button type="submit" class="search-btn" title="Pesquisar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Filtros minimalistas --}}
            <div class="filters-minimal">
                {{-- Filtro por Gênero --}}
                <div class="filter-dropdown">
                    <button type="button" class="filter-btn" data-filter="genero">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span class="filter-label">Gênero</span>
                        <svg class="filter-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"/>
                        </svg>
                    </button>
                    <div class="filter-menu" data-menu="genero">
                        <div class="filter-options-list">
                            <button type="button" class="filter-select-option {{ request('genero') == '' ? 'active' : '' }}" data-filter-name="genero" data-value="">Todos os gêneros</button>
                            @foreach(['Ação', 'Animação', 'Aventura', 'Comédia', 'Crime', 'Documentário', 'Drama', 'Fantasia', 'Ficção Científica', 'Mistério', 'Romance', 'Suspense', 'Terror'] as $g)
                                <button type="button" class="filter-select-option {{ request('genero') === $g ? 'active' : '' }}" data-filter-name="genero" data-value="{{ $g }}">{{ $g }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>

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
                        <div class="filter-options-list">
                            <button type="button" class="filter-select-option {{ request('assistido') === null || request('assistido') === '' ? 'active' : '' }}" data-filter-name="assistido" data-value="">Status: Todos</button>
                            <button type="button" class="filter-select-option {{ request('assistido') === '1' ? 'active' : '' }}" data-filter-name="assistido" data-value="1">Assistidos</button>
                            <button type="button" class="filter-select-option {{ request('assistido') === '0' ? 'active' : '' }}" data-filter-name="assistido" data-value="0">Não assistidos</button>
                        </div>
                    </div>
                </div>

                {{-- Filtro por Nota --}}
                <div class="filter-dropdown">
                    <button type="button" class="filter-btn" data-filter="nota">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <span class="filter-label">Nota</span>
                        <svg class="filter-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"/>
                        </svg>
                    </button>
                    <div class="filter-menu" data-menu="nota">
                        <div class="filter-options-list">
                            <button type="button" class="filter-select-option {{ request('nota') === null || request('nota') === '' ? 'active' : '' }}" data-filter-name="nota" data-value="">Nota: Todas</button>
                            <button type="button" class="filter-select-option {{ request('nota') === '8' ? 'active' : '' }}" data-filter-name="nota" data-value="8">8+</button>
                            <button type="button" class="filter-select-option {{ request('nota') === '7' ? 'active' : '' }}" data-filter-name="nota" data-value="7">7+</button>
                            <button type="button" class="filter-select-option {{ request('nota') === '6' ? 'active' : '' }}" data-filter-name="nota" data-value="6">6+</button>
                            <button type="button" class="filter-select-option {{ request('nota') === 'none' ? 'active' : '' }}" data-filter-name="nota" data-value="none">Sem nota</button>
                        </div>
                    </div>
                </div>


                {{-- Filtro por Ano (se houver) --}}
                @if(!empty($anos))
                <div class="filter-dropdown">
                    <button type="button" class="filter-btn" data-filter="ano_lancamento">
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
                    <div class="filter-menu" data-menu="ano_lancamento">
                        <div class="filter-options-list">
                            <button type="button" class="filter-select-option {{ request('ano_lancamento') == '' ? 'active' : '' }}" data-filter-name="ano_lancamento" data-value="">Ano: Todos</button>
                            @foreach($anos as $a)
                                <button type="button" class="filter-select-option {{ request('ano_lancamento') == $a ? 'active' : '' }}" data-filter-name="ano_lancamento" data-value="{{ $a }}">{{ $a }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Filtro por Ordenação --}}
                <div class="filter-dropdown">
                    <button type="button" class="filter-btn" data-filter="ordenacao">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                        <span class="filter-label">Ordenar</span>
                        <svg class="filter-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6,9 12,15 18,9"/>
                        </svg>
                    </button>
                    <div class="filter-menu" data-menu="ordenacao">
                        <div class="filter-options-list">
                            <button type="button" class="filter-select-option {{ request('ordenacao', 'recentes') === 'recentes' ? 'active' : '' }}" data-filter-name="ordenacao" data-value="recentes">Mais recentes</button>
                            <button type="button" class="filter-select-option {{ request('ordenacao') === 'antigos' ? 'active' : '' }}" data-filter-name="ordenacao" data-value="antigos">Mais antigos</button>
                            <button type="button" class="filter-select-option {{ request('ordenacao') === 'nota_max' ? 'active' : '' }}" data-filter-name="ordenacao" data-value="nota_max">Maior nota</button>
                            <button type="button" class="filter-select-option {{ request('ordenacao') === 'nota_min' ? 'active' : '' }}" data-filter-name="ordenacao" data-value="nota_min">Menor nota</button>
                            <button type="button" class="filter-select-option {{ request('ordenacao') === 'nome_az' ? 'active' : '' }}" data-filter-name="ordenacao" data-value="nome_az">Nome (A-Z)</button>
                            <button type="button" class="filter-select-option {{ request('ordenacao') === 'nome_za' ? 'active' : '' }}" data-filter-name="ordenacao" data-value="nome_za">Nome (Z-A)</button>
                            <button type="button" class="filter-select-option {{ request('ordenacao') === 'ano' ? 'active' : '' }}" data-filter-name="ordenacao" data-value="ano">Ano</button>
                            <button type="button" class="filter-select-option {{ request('ordenacao') === 'data_assistido' ? 'active' : '' }}" data-filter-name="ordenacao" data-value="data_assistido">Data assistido</button>
                        </div>
                    </div>
                </div>

                {{-- Botão limpar filtros --}}
                @if(request()->hasAny(['q', 'genero', 'assistido', 'nota', 'plataforma', 'ano_lancamento', 'ordenacao']) && (request('q') || request('genero') || request('assistido') !== null || request('nota') || request('plataforma') || request('ano_lancamento') || request('ordenacao') !== 'recentes'))
                    <a href="{{ route('filmes.biblioteca') }}" class="clear-filters">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        Limpar
                    </a>
                @endif
            </div>
        </form>

        {{-- Resultados --}}
        <div class="library-results">

            <x-back-button context="icon" href="{{ route('dashboard') }}"/>
            @if($filmes->total() > 0)
                <div class="results-count">
                   Filmes Encontrados: {{ $filmes->total() }} filme{{ $filmes->total() !== 1 ? 's' : '' }}
                </div>
            @endif

            

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
                            @if(request()->hasAny(['q', 'genero', 'assistido', 'nota', 'plataforma', 'ano_lancamento', 'ordenacao']) && (request('q') || request('genero') || request('assistido') !== null || request('nota') || request('plataforma') || request('ano_lancamento') || request('ordenacao') !== 'recentes'))
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