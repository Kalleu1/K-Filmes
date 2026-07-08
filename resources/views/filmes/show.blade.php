@extends('layouts.base')


@section('content')


    <div class="movie-page" style=" --accent-rgb: {{ $colorThemeData['primary'] ?? '34,34,34' }};
                                    --accent-secondary-rgb: {{ $colorThemeData['secondary'] ?? '18,18,18' }};">

        {{-- HERO — O FILME --}}
        <section class="movie-hero"
            style="background-image: url('{{ $backdropUrl ?? ($filme->poster_banner ? asset('storage/posters_banners/' . $filme->poster_banner) : '') }}');">

        
            <div class="movie-hero-overlay"></div>

            <div class="movie-hero-content">

                <div class="hero-left">
                    
                    <div class="hero-poster-wrapper">
                        

                        <img
                            src="{{ $posterUrl ?? ($filme->poster ? asset('storage/posters/' . $filme->poster) : asset('img/poster-placeholder.png')) }}"
                            alt="Poster de {{ $filme->nome }}"
                            class="hero-poster">

                        {{-- Badges sobre o poster --}}
                        @if($filme->nota)
                            <div class="badge badge-rating">
                                ⭐ {{ number_format($filme->nota, 1) }}
                            </div>
                        @endif

                        <button
                            class="favorite-btn {{ $filme->favorito ? 'favorited' : '' }}"
                            data-filme-id="{{ $filme->id }}"
                            aria-label="Favoritar filme"
                        >
                            <i class="fa-solid fa-heart"></i>
                        </button>

                    
                        

                    <h1 class="movie-title">{{ $filme->nome }}</h1>

                    <div class="movie-subinfo">
                        @if($filme->ano_lancamento)
                            <span>{{ $filme->ano_lancamento }}</span>
                        @endif

                        @if($filme->diretor)
                            <span>• {{ $filme->diretor }}</span>
                        @endif
                    </div>

                    <div class="hero-actions">

                        <x-back-button context="icon" />
                        <x-back-to-dashboard context="icon"/>
                        
                        <button class="btn-icon" data-action="open-edit-modal">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <button class="btn-icon" data-action="open-delete-modal">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                        <button class="btn-icon"
                            data-action="share-movie"
                            data-share-url="{{ route('filme.share.preview', $filme->id) }}">
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>

                        
                    </div>

                    
                    </div>
                </div>


            </div>
        </section>

        {{-- ============================= --}}
        {{-- TRANSIÇÃO --}}
        {{-- ============================= --}}
        <div class="hero-transition"></div>

        <main class="movie-content">

            {{-- ============================= --}}
            {{-- DIÁRIO — MINHAS IMPRESSÕES --}}
            {{-- ============================= --}}
            @if($filme->comentarios)
                <section class="movie-diary">
                    <h2>Minhas Impressões</h2>

                    <blockquote>
                        “{{ $filme->comentarios }}”
                    </blockquote>

                    @if($filme->data_assistida)
                        <span class="diary-date">
                            Assistido em {{ \Carbon\Carbon::parse($filme->data_assistida)->format('d/m/Y') }}
                        </span>
                    @endif
                </section>
            @endif

            {{-- ============================= --}}
            {{-- AVALIAÇÃO --}}
            {{-- ============================= --}}
            <section class="movie-experience">
                <div class="experience-grid">

                    @if($filme->nota)
                        <div class="experience-item">
                            <span class="label">Minha Nota</span>
                            <strong>{{ number_format($filme->nota, 1) }}/10</strong>
                        </div>
                    @endif

                    @if($filme->plataforma)
                        <div class="experience-item">
                            <span class="label">Onde assisti</span>
                            <strong>{{ $filme->plataforma }}</strong>
                        </div>
                    @endif

                    @if($filme->data_assistida)
                        <div class="experience-item">
                            <span class="label">Quando</span>
                            <strong>{{ \Carbon\Carbon::parse($filme->data_assistida)->format('d/m/Y') }}</strong>
                        </div>
                    @endif

                </div>
            </section>

            {{-- ============================= --}}
            {{-- LINHA DO TEMPO --}}
            {{-- ============================= --}}
            <section class="movie-timeline">
                <h2>Sua jornada com este filme</h2>

                <ul>
                    <li>
                        <span class="dot"></span>
                        Adicionado à biblioteca em {{ $filme->created_at->format('d/m/Y') }}
                    </li>

                    @if($filme->data_assistida)
                        <li>
                            <span class="dot"></span>
                            Assistido em {{ \Carbon\Carbon::parse($filme->data_assistida)->format('d/m/Y') }}
                        </li>
                    @endif

                    @if($filme->nota)
                        <li>
                            <span class="dot"></span>
                            Avaliado
                        </li>
                    @endif
                </ul>
            </section>

            {{-- ============================= --}}
            {{-- CONTEXTO DO FILME --}}
            {{-- ============================= --}}
            @if($filme->descricao)
                <section class="movie-synopsis">
                    <h2>Sobre o filme</h2>
                    <p>{{ $filme->descricao }}</p>
                </section>
            @endif

            {{-- ============================= --}}
            {{-- FILMES RELACIONADOS --}}
            {{-- ============================= --}}
            @if(!empty($similarMovies))
                <section class="movie-related">
                    <h2>Filmes Similares</h2>

                    <div class="dashboard_grid">
                        @foreach($similarMovies as $similar)
                            <x-filmecard :filme="$similar" :campos="['poster']" />
                        @endforeach
                    </div>
                </section>
            @endif

            @if(!empty($directorMovies))
                <section class="movie-related">
                    <h2>Do mesmo diretor</h2>

                    <div class="dashboard_grid">
                        @foreach($directorMovies as $movie)
                            <x-filmecard :filme="$movie" :campos="['poster']" />
                        @endforeach
                    </div>
                </section>
            @endif

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