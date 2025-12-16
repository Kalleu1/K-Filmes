@extends('layouts.app')

@section('content')
<div class="personal-movie-page"
     id="personal-movie-page">
    {{-- Background com backdrop do filme --}}
    <div class="personal-backdrop" style="background-image: url('{{ $backdropUrl ?? ($filme->poster_banner ? asset('storage/posters_banners/' . $filme->poster_banner) : '') }}');"></div>
    <div class="personal-backdrop-overlay"></div>

    {{-- Conteúdo principal --}}
    <main class="personal-main">
        <div class="personal-back-button-container">
            <x-back-button />
        </div>

        <div class="personal-content">
            {{-- Seção Hero Pessoal --}}
            <div class="personal-hero">
                <div class="personal-poster-container">
                    <img src="{{ $posterUrl ?? ($filme->poster ? asset('storage/posters/' . $filme->poster) : asset('img/poster-placeholder.png')) }}" 
                         alt="Poster de {{ $filme->nome ?? 'Sem título' }}" 
                         class="personal-poster">
                    
                    {{-- Badge de status --}}
                    @if($filme->assistido)
                        <div class="status-badge watched">
                            Assistido
                        </div>
                    @endif

                    {{-- Sua nota --}}
                    @if($filme->nota)
                        <div class="personal-rating-badge">
                            <span class="star-icon">⭐</span>
                            <span class="rating-score">{{ number_format($filme->nota, 1) }}</span>
                        </div>
                    @endif



                </div>

                <div class="personal-info">
                    <h1 class="personal-title">{{ $filme->nome ?? 'Sem título' }}</h1>
                    
                    {{-- Informações pessoais --}}
                    <div class="personal-meta">
                        @if($filme->data_assistida)
                        <div class="meta-item-personal">
                            <span class="meta-icon">📅</span>
                            <div class="meta-content">
                                <span class="meta-label">Assistido em</span>
                                <span class="meta-value">{{ \Carbon\Carbon::parse($filme->data_assistida)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        @endif

                        @if($filme->plataforma)
                        <div class="meta-item-personal">
                            <span class="meta-icon">📺</span>
                            <div class="meta-content">
                                <span class="meta-label">Plataforma</span>
                                <span class="meta-value">{{ $filme->plataforma }}</span>
                            </div>
                        </div>
                        @endif

                        @if($filme->diretor)
                        <div class="meta-item-personal">
                            <span class="meta-icon">🎬</span>
                            <div class="meta-content">
                                <span class="meta-label">Diretor</span>
                                <span class="meta-value">{{ $filme->diretor }}</span>
                            </div>
                        </div>
                        @endif

                        @if($filme->genero)
                        <div class="meta-item-personal">
                            <span class="meta-icon">🎭</span>
                            <div class="meta-content">
                                <span class="meta-label">Gênero</span>
                                <span class="meta-value">{{ $filme->genero }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Botões de ação pessoais --}}
                    <div class="personal-actions">
                        <button class="btn-primary-personal" data-action="open-edit-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                            </svg>
                        </button>
                        

                        <div class="personal-actions-header">

                            <button class="btn-delete" title="Remover da biblioteca" data-action="open-delete-modal">
                                <!-- Heroicon Trash -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>

                            </button>

                            <button class="btn-secondary-personal" data-action="share-movie">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                            </svg>

                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Seção de comentários pessoais --}}
            @if($filme->comentarios)
            <div class="personal-review">
                <h2 class="review-title">
                    Suas Impressões
                </h2>
                <div class="review-content">
                    <blockquote class="personal-comment">
                        "{{ $filme->comentarios }}"
                    </blockquote>
                    <div class="review-meta">
                        <span class="review-author">Você</span>
                        <span class="review-date">{{ $filme->data_assistida ? \Carbon\Carbon::parse($filme->data_assistida)->format('d/m/Y') : 'Data não informada' }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Sinopse --}}
            @if($filme->descricao)
            <div class="personal-synopsis">
                <h2 class="synopsis-title">Sinopse</h2>
                <p class="synopsis-text">{{ $filme->descricao }}</p>
            </div>
            @endif

            {{-- Timeline pessoal --}}
            <div class="personal-timeline">
                <h2 class="timeline-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12,6 12,12 16,14"/>
                    </svg>
                    Sua Jornada com este Filme
                </h2>
                <div class="timeline-content">
                    <div class="timeline-item">
                        <div class="timeline-marker added"></div>
                        <div class="timeline-info">
                            <span class="timeline-action">Adicionado à biblioteca</span>
                            <span class="timeline-date">{{ $filme->created_at ? $filme->created_at->format('d/m/Y') : 'Data não disponível' }}</span>
                        </div>
                    </div>
                    @if($filme->data_assistida)
                    <div class="timeline-item">
                        <div class="timeline-marker watched"></div>
                        <div class="timeline-info">
                            <span class="timeline-action">Assistido</span>
                            <span class="timeline-date">{{ \Carbon\Carbon::parse($filme->data_assistida)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @endif
                    @if($filme->nota)
                    <div class="timeline-item">
                        <div class="timeline-marker rated"></div>
                        <div class="timeline-info">
                            <span class="timeline-action">Avaliado com {{ $filme->nota }}/10</span>
                            <span class="timeline-date">{{ $filme->updated_at ? $filme->updated_at->format('d/m/Y') : 'Data não disponível' }}</span>
                        </div>
                    </div>
                    @endif
                </div>
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

{{-- Modal para editar informações --}}
<div id="editModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Editar Informações</h2>
            <button class="modal-close" data-action="close-modal">
            </button>
        </div>
        
        <div class="modal-body">
            <form action="{{ route('filmes.update', $filme->id) }}" method="POST" class="edit-form">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="nota" class="form-label">
                        <span class="label-icon">⭐</span>
                        Sua Nota (0-10)
                    </label>
                    <div class="rating-input">
                        <input type="range" name="nota" id="nota" min="0" max="10" step="0.1" value="{{ $filme->nota ?? 5 }}" class="rating-slider">
                        <span class="rating-display">{{ $filme->nota ?? 5 }}</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="data_assistida" class="form-label">
                        <span class="label-icon">📅</span>
                        Data que Assistiu
                    </label>
                    <input type="date" name="data_assistida" id="data_assistida" class="form-input" value="{{ $filme->data_assistida }}">
                </div>

                <div class="form-group">
                    <label for="plataforma" class="form-label">
                        <span class="label-icon">📺</span>
                        Plataforma
                    </label>
                    <select name="plataforma" id="plataforma" class="form-select">
                        <option value="">Selecione uma plataforma</option>
                        <option value="Netflix" {{ $filme->plataforma == 'Netflix' ? 'selected' : '' }}>Netflix</option>
                        <option value="Amazon Prime" {{ $filme->plataforma == 'Amazon Prime' ? 'selected' : '' }}>Amazon Prime</option>
                        <option value="Disney+" {{ $filme->plataforma == 'Disney+' ? 'selected' : '' }}>Disney+</option>
                        <option value="HBO Max" {{ $filme->plataforma == 'HBO Max' ? 'selected' : '' }}>HBO Max</option>
                        <option value="Paramount+" {{ $filme->plataforma == 'Paramount+' ? 'selected' : '' }}>Paramount+</option>
                        <option value="Apple TV+" {{ $filme->plataforma == 'Apple TV+' ? 'selected' : '' }}>Apple TV+</option>
                        <option value="Cinema" {{ $filme->plataforma == 'Cinema' ? 'selected' : '' }}>Cinema</option>
                        <option value="Outro" {{ $filme->plataforma == 'Outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comentarios" class="form-label">
                        <span class="label-icon">💬</span>
                        Suas Impressões
                    </label>
                    <textarea name="comentarios" id="comentarios" class="form-textarea" 
                              placeholder="O que você achou do filme? Compartilhe sua opinião...">{{ $filme->comentarios }}</textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" data-action="close-modal">Cancelar</button>
                    <button type="submit" class="btn-save">
                        Salvar Alterações
                    </button>
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
            <button class="modal-close" data-action="close-modal">
            </button>
        </div>
        
        <div class="modal-body">
            <div class="delete-warning">
                <h3>Tem certeza?</h3>
                <p>Esta ação removerá "{{ $filme->nome }}" da sua biblioteca permanentemente, incluindo sua nota, comentários e histórico.</p>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" data-action="close-modal">Cancelar</button>
                <form action="{{ route('filmes.destroy', $filme->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete-confirm">
                        Sim, Remover
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection