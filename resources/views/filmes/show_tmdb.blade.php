@extends('layouts.app')

@section('content')
    <div class="movie-details-page">
        {{-- Background com backdrop do filme --}}
        <div class="movie-backdrop" style="background-image: url('{{ $backdropUrl ?? '' }}');"></div>
        <div class="movie-backdrop-overlay"></div>
        
        {{-- Conteúdo principal --}}
        <main class="movie-main">
            <x-back-button />

            <div class="movie-content">
                {{-- Poster e informações principais --}}
                <div class="movie-hero">
                    <div class="movie-poster-container">
                        <img src="{{ $posterUrl ?? asset('img/poster-placeholder.png') }}" 
                            alt="Poster de {{ $tmdbData['title'] ?? 'Sem título' }}" 
                            class="movie-poster">
                    </div>

                    <div class="movie-info">
                        <h1 class="movie-title">{{ $tmdbData['title'] ?? 'Sem título' }}</h1>
                        
                        @if(!empty($tmdbData['tagline']))
                            <p class="movie-tagline">"{{ $tmdbData['tagline'] }}"</p>
                        @endif

                        <div class="movie-meta">
                            <div class="meta-item">
                                <span class="meta-icon">🎬</span>
                                <span>{{ $director ?? 'Diretor desconhecido' }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon">📅</span>
                                <span>{{ !empty($tmdbData['release_date']) ? date('Y', strtotime($tmdbData['release_date'])) : 'Ano desconhecido' }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon">⏱️</span>
                                <span>{{ !empty($tmdbData['runtime']) ? $tmdbData['runtime'] . ' min' : 'Duração desconhecida' }}</span>
                            </div>
                            @if(!empty($genres))
                                <div class="meta-item">
                                    <span class="meta-icon">🎭</span>
                                    <span>{{ $genres }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Botões de ação --}}
                        <div class="movie-actions">
                            {{-- Botão para adicionar sem assistir --}}
                            <form action="{{ route('filmes.saveTmdb', $tmdbData['id']) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="assistido" value="0">
                                <button type="submit" class="btn-secondary btn-watchlist">
                                    Adicionar à Biblioteca
                                </button>
                            </form>

                            {{-- Botão que abre o modal de assistido --}}
                            <button class="btn-primary btn-watched" onclick="openWatchedModal()">
                                Adicionar como Assistido
                            </button>

                            <button class="btn-delete" title="Remover da biblioteca" onclick="confirmDelete()">
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
                    <div class="movie-overview">
                        <h2 class="section-title">Sinopse</h2>
                        <p class="overview-text">{{ $tmdbData['overview'] }}</p>
                    </div>
                @endif

                {{-- Informações adicionais --}}
                <div class="movie-details-grid">
                    @if(!empty($tmdbData['production_companies']))
                        <div class="detail-card">
                            <h3 class="detail-title">Produção</h3>
                            <div class="production-companies">
                                @foreach(array_slice($tmdbData['production_companies'], 0, 3) as $company)
                                    <span class="company-name">{{ $company['name'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($tmdbData['production_countries']))
                        <div class="detail-card">
                            <h3 class="detail-title">País</h3>
                            <p>{{ collect($tmdbData['production_countries'])->pluck('name')->join(', ') }}</p>
                        </div>
                    @endif

                    @if(!empty($tmdbData['spoken_languages']))
                        <div class="detail-card">
                            <h3 class="detail-title">Idiomas</h3>
                            <p>{{ collect($tmdbData['spoken_languages'])->pluck('name')->join(', ') }}</p>
                        </div>
                    @endif

                    @if(!empty($tmdbData['budget']) && $tmdbData['budget'] > 0)
                        <div class="detail-card">
                            <h3 class="detail-title">Orçamento</h3>
                            <p>${{ number_format($tmdbData['budget']) }}</p>
                        </div>
                    @endif
                </div>

                {{-- Filmes Similares --}}
            @if(!empty($similarMovies))
            <div class="similar-movies-section">
                <h2>Filmes Similares</h2>
                <div class="similar-movies-list dashboard_grid">
                    @foreach($similarMovies as $similar)
                        <x-filmecard :filme="$similar" :campos="['poster']" />
                    @endforeach
                </div>
            </div>
            @endif

                {{-- Filmes do mesmo diretor --}}
            @if(!empty($directorMovies))
            <div class="director-movies-section">
                <h2>Filmes do mesmo diretor</h2>
                <div class="similar-movies-list dashboard_grid" >
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
    <div id="watchedModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Marcar como Assistido</h2>
                <button class="modal-close" onclick="closeWatchedModal()">
                </button>
            </div>
            
            <div class="modal-body">
                <form action="{{ route('filmes.saveTmdb', $tmdbData['id']) }}" method="POST" class="watched-form">
                    @csrf
                    <input type="hidden" name="assistido" value="1">

                    <div class="form-group">
                        <label for="nota" class="form-label">
                            <span class="label-icon">⭐</span>
                            Sua Nota (0-10)
                        </label>

                        
                        <div class="rating-input">
                            <input type="range" name="nota" id="nota" min="0" max="10" step="0.1" value="5" class="rating-slider">
                            <span class="rating-display">5.0</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="data_assistida" class="form-label">
                            <span class="label-icon">📅</span>
                            Data que Assistiu
                        </label>
                        <input type="date" name="data_assistida" id="data_assistida" class="form-input" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label for="plataforma" class="form-label">
                            <span class="label-icon">📺</span>
                            Plataforma
                        </label>
                        <select name="plataforma" id="plataforma" class="form-select">
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

                    <div class="form-group">
                        <label for="comentarios" class="form-label">
                            <span class="label-icon">💬</span>
                            Seus Comentários
                        </label>
                        <textarea name="comentarios" id="comentarios" class="form-textarea" 
                                placeholder="O que você achou do filme? Compartilhe sua opinião..."></textarea>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" onclick="closeWatchedModal()">Cancelar</button>
                        <button type="submit" class="btn-save">
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
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content delete-modal">
            <div class="modal-header">
                <h2 class="modal-title">Remover da Biblioteca</h2>
                <button class="modal-close" onclick="closeDeleteModal()"></button>
            </div>
            <div class="modal-body">
                <div class="delete-warning">
                    <h3>Tem certeza?</h3>
                    <p>Esta ação removerá "{{ $tmdbData['title'] ?? 'este filme' }}" da sua biblioteca permanentemente.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancelar</button>
                    @if($filme)
                        <form action="{{ route('filmes.destroy', $filme->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-confirm">
                                Sim, Remover
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn-delete-confirm" disabled>
                            Filme não está na biblioteca
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    // Modal functions
    function openWatchedModal() {
        document.getElementById('watchedModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeWatchedModal() {
        document.getElementById('watchedModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function confirmDelete() {
        document.getElementById('deleteModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Rating slider
    document.getElementById('nota').addEventListener('input', function() {
        document.querySelector('.rating-display').textContent = this.value;
    });

    // Close modal on outside click
    document.getElementById('watchedModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeWatchedModal();
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeWatchedModal();
            closeDeleteModal();
        }
    });
    </script>
@endsection