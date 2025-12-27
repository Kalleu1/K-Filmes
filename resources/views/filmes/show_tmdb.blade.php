@extends('layouts.app')

@section('content')

    <div class="tmdb-movie-details-page"
         id="movie-details-page">
        {{-- Background com backdrop do filme --}}
        
        <div class="tmdb-movie-backdrop-overlay"></div>
        
        {{-- Conteúdo principal --}}
        <main class="tmdb-movie-main">
            <x-back-button context="page" />

            <div class="movieTmdb-content">
                {{-- Poster e informações principais --}}
                <div class="tmdb-movie-hero">
                    <div class="tmdb-movie-poster-container">
                        <img src="{{ $posterUrl ?? asset('img/poster-placeholder.png') }}" 
                            alt="Poster de {{ $tmdbData['title'] ?? 'Sem título' }}" 
                            class="tmdb-movie-poster">
                    </div>

                    <div class="tmdb-movie-info">
                        <h1 class="tmdb-movie-title">{{ $tmdbData['title'] ?? 'Sem título' }}</h1>
                        
                        @if(!empty($tmdbData['tagline']))
                            <p class="tmdb-movie-tagline">"{{ $tmdbData['tagline'] }}"</p>
                        @endif

                        <div class="tmdb-movie-meta">
                            <div class="tmdb-meta-item">
                                <span class="tmdb-meta-icon">🎬</span>
                                <span>{{ $director ?? 'Diretor desconhecido' }}</span>
                            </div>
                            <div class="tmdb-meta-item">
                                <span class="tmdb-meta-icon">📅</span>
                                <span>{{ !empty($tmdbData['release_date']) ? date('Y', strtotime($tmdbData['release_date'])) : 'Ano desconhecido' }}</span>
                            </div>
                            <div class="tmdb-meta-item">
                                <span class="tmdb-meta-icon">⏱️</span>
                                <span>{{ !empty($tmdbData['runtime']) ? $tmdbData['runtime'] . ' min' : 'Duração desconhecida' }}</span>
                            </div>
                            @if(!empty($genres))
                                <div class="tmdb-meta-item">
                                    <span class="tmdb-meta-icon">🎭</span>
                                    <span>{{ $genres }}</span>
                                </div>
                            @endif

                            {{-- Exibição da nota da TMDB (estilo circular inline) --}}
                            @if(isset($tmdbRating))
                                @php
                                    $percent = round($tmdbRating * 10); // TMDB vem de 0–10 → 0–100%
                                    if ($percent >= 70) {
                                        $color = '#21d07a'; // verde
                                    } elseif ($percent >= 40) {
                                        $color = '#d2d531'; // amarelo
                                    } else {
                                        $color = '#db2360'; // vermelho
                                    }
                                @endphp

                                <div class="tmdb-meta-item tmdb-tmdb-meta">
                                    <div class="tmdb-score">
                                        <div class="tmdb-circle" style="--percent: {{ $percent }}; --color: {{ $color }}">
                                            <span>{{ $percent }}<small>%</small></span>
                                        </div>
                                    </div>
                                    <span class="tmdb-label">TMDB</span>
                                </div>
                            @endif
                        </div>


                        {{-- Botões de ação --}}
                        <div class="tmdb-movie-actions">
                            {{-- Botão para adicionar sem assistir --}}
                            <form action="{{ route('filmes.saveTmdb', $tmdbData['id']) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="assistido" value="0">
                                <button type="submit" class="btn-secondary btn-watchlist">
                                    Adicionar à Biblioteca
                                </button>
                            </form>

                            {{-- Botão que abre o modal de assistido --}}
                            <button class="btn-primary btn-watched" data-action="open-watched-modal">
                                Adicionar como Assistido
                            </button>

                            <button class="btn-delete" title="Remover da biblioteca" data-action="open-delete-modal">
                                <!-- Heroicon Trash -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>

                            </button>
                        </div>
                        
                    </div>

                    
                </div>

                {{-- Sinopse --}}
                @if(!empty($tmdbData['overview']))
                    <div class="tmdb-movie-overview">
                        <h2 class="section-title">Sinopse</h2>
                        <p class="tmdb-overview-text">{{ $tmdbData['overview'] }}</p>
                    </div>
                @endif

                {{-- Informações adicionais --}}
                <div class="tmdb-movie-details-grid">
                    @if(!empty($tmdbData['production_companies']))
                        <div class="tmdb-detail-card">
                            <h3 class="tmdb-detail-title">Produção</h3>
                            <div class="tmdb-production-companies">
                                @foreach(array_slice($tmdbData['production_companies'], 0, 3) as $company)
                                    <span class="tmdb-company-name">{{ $company['name'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($tmdbData['production_countries']))
                        <div class="tmdb-detail-card">
                            <h3 class="tmdb-detail-title">País</h3>
                            <p>{{ collect($tmdbData['production_countries'])->pluck('name')->join(', ') }}</p>
                        </div>
                    @endif

                    @if(!empty($tmdbData['spoken_languages']))
                        <div class="tmdb-detail-card">
                            <h3 class="tmdb-detail-title">Idiomas</h3>
                            <p>{{ collect($tmdbData['spoken_languages'])->pluck('name')->join(', ') }}</p>
                        </div>
                    @endif

                    @if(!empty($tmdbData['budget']) && $tmdbData['budget'] > 0)
                        <div class="tmdb-detail-card">
                            <h3 class="tmdb-detail-title">Orçamento</h3>
                            <p>${{ number_format($tmdbData['budget']) }}</p>
                        </div>
                    @endif
                </div>

                {{-- Filmes Similares --}}
            @if(!empty($similarMovies))
            <div class="tmdb-similar-movies-section">
                <h2>Filmes Similares</h2>
                <div class="tmdb-similar-movies-list tmdb-dashboard_grid">
                    @foreach($similarMovies as $similar)
                        <x-filmecard :filme="$similar" :campos="['poster']" />
                    @endforeach
                </div>
            </div>
            @endif

                {{-- Filmes do mesmo diretor --}}
            @if(!empty($directorMovies))
            <div class="tmdb-director-movies-section">
                <h2>Filmes do mesmo diretor</h2>
                <div class="tmdb-similar-movies-list tmdb-dashboard_grid" >
                    @foreach($directorMovies as $movie)
                        <x-filmecard :filme="$movie" :campos="['poster']" />
                    @endforeach
                </div>
            </div>
            @endif
            </div>
        </main>
    </div>

    {{-- Modal para marcar como assistido --}}
    <div id="watchedModal" class="tmdb-modal-overlay">
        <div class="tmdb-modal-content">
            <div class="tmdb-modal-header">
                <h2 class="tmdb-modal-title">Marcar como Assistido</h2>
                <button class="tmdb-modal-close" data-action="close-modal">
                </button>
            </div>
            
            <div class="tmdb-modal-body">
                <form action="{{ route('filmes.saveTmdb', $tmdbData['id']) }}" method="POST" class="tmdb-watched-form">
                    @csrf
                    <input type="hidden" name="assistido" value="1">

                    <div class="tmdb-form-group">
                        <label for="nota" class="tmdb-form-label">
                            <span class="tmdb-label-icon">⭐</span>
                            Sua Nota (0-10)
                        </label>

                        
                        <div class="tmdb-rating-input">
                            <input type="range" name="nota" id="nota" min="0" max="10" step="0.1" value="5" class="tmdb-rating-slider">
                            <span class="tmdb-rating-display">5.0</span>
                        </div>
                    </div>

                    <div class="tmdb-form-group">
                        <label for="data_assistida" class="tmdb-form-label">
                            <span class="tmdb-label-icon">📅</span>
                            Data que Assistiu
                        </label>
                        <input type="date" name="data_assistida" id="data_assistida" class="tmdb-form-input" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="tmdb-form-group">
                        <label for="plataforma" class="tmdb-form-label">
                            <span class="tmdb-label-icon">📺</span>
                            Plataforma
                        </label>
                        <select name="plataforma" id="plataforma" class="tmdb-form-select">
                            <option value="">Selecione uma plataforma</option>
                            <option value="Netflix">Netflix</option>
                            <option value="Amazon Prime">Amazon Prime</option>
                            <option value="Disney+">Disney+</option>
                            <option value="HBO Max">HBO Max</option>
                            <option value="Paramount+">Paramount+</option>
                            <option value="Apple TV+">Apple TV+</option>
                            <option value="Cinema">Cinema</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="tmdb-form-group">
                        <label for="comentarios" class="tmdb-form-label">
                            <span class="tmdb-label-icon">💬</span>
                            Seus Comentários
                        </label>
                        <textarea name="comentarios" id="comentarios" class="tmdb-form-textarea" 
                                placeholder="O que você achou do filme? Compartilhe sua opinião..."></textarea>
                    </div>

                    <div class="tmdb-modal-actions">
                        <button type="button" class="tmdb-btn-cancel" data-action="close-modal">Cancelar</button>
                        <button type="submit" class="tmdb-btn-save">
                            Salvar como Assistido
                        </button>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal de confirmação para deletar --}}
    <div id="deleteModal" class="tmdb-modal-overlay">
        <div class="tmdb-modal-content tmdb-delete-modal">
            <div class="tmdb-modal-header">
                <h2 class="tmdb-modal-title">Remover da Biblioteca</h2>
                <button class="tmdb-modal-close" data-action="close-modal"></button>
            </div>
            <div class="tmdb-modal-body">
                <div class="tmdb-delete-warning">
                    <h3>Tem certeza?</h3>
                    <p>Esta ação removerá "{{ $tmdbData['title'] ?? 'este filme' }}" da sua biblioteca permanentemente.</p>
                </div>
                <div class="tmdb-modal-actions">
                    <button type="button" class="tmdb-btn-cancel" data-action="close-modal"></button>Cancelar</button>
                    @if($filme)
                        <form action="{{ route('filmes.destroy', $filme->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="tmdb-btn-delete-confirm">
                                Sim, Remover
                            </button>
                        </form>
                    @else
                        <button type="button" class="tmdb-btn-delete-confirm" disabled>
                            Filme não está na biblioteca
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection