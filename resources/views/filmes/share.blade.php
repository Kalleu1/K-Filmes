@extends('layouts.base')
@include('components.header')
@section('content')

<div class="share-container">

    <div class="share-content">
        <h1 class="share-title">{{ $filme->nome }}</h1>
        <p class="share-subtitle">Imagem gerada para compartilhar nos stories</p>

        {{-- Seleção de temas --}}
        <div class="theme-selector">
            <p>Escolha o estilo do fundo:</p>

            <div class="theme-options">
                <button class="theme-btn theme-btn-backdrop selected"
                    data-theme="movie-backdrop"
                    style="background-image: url('{{ $backdropUrl }}'); background-size: cover; background-position: center;"
                    title="Banner do Filme (com Poster)">
                    <i class="fa-solid fa-image"></i>
                </button>

                <button class="theme-btn theme-btn-full-poster"
                    data-theme="full-poster"
                    style="background-image: url('{{ $posterUrl }}'); background-size: cover; background-position: center;"
                    title="Pôster Full-Screen (HD)">
                    <i class="fa-solid fa-expand"></i>
                </button>

                <button class="theme-btn theme-btn-custom-canvas"
                    data-theme="custom-canvas"
                    style="background: linear-gradient(135deg, #ff9900, #ff0055);"
                    title="Modo Personalizado (Drag & Drop Livre)">
                    <i class="fa-solid fa-sliders"></i>
                </button>

                @if($filme->tmdb_id)
                <button type="button" class="theme-btn theme-btn-edit-backdrop"
                    data-action="open-share-backdrop-selector"
                    data-filme-id="{{ $filme->id }}"
                    data-tmdb-id="{{ $filme->tmdb_id }}"
                    title="Trocar Foto de Fundo para o Story">
                    <i class="fa-solid fa-camera"></i>
                </button>

                <button type="button" class="theme-btn theme-btn-edit-poster"
                    data-action="open-share-poster-selector"
                    data-filme-id="{{ $filme->id }}"
                    data-tmdb-id="{{ $filme->tmdb_id }}"
                    title="Trocar Pôster do Story (inclui opções sem título)">
                    <i class="fa-solid fa-file-image"></i>
                </button>
                @endif

                <button class="theme-btn"
                    data-theme="noir"
                    style="background: linear-gradient(180deg, #111, #777575);"
                    title="Noir Cinematográfico">
                </button>

                <button class="theme-btn"
                    data-theme="deep-blue"
                    style="background: linear-gradient(180deg, #0b1a2d, #1e3a5f);"
                    title="Azul Profundo">
                </button>

                <button class="theme-btn"
                    data-theme="amber-gold"
                    style="background: linear-gradient(180deg, #2b1d07, #664614);"
                    title="Dourado Vip">
                </button>

                <button class="theme-btn"
                    data-theme="emerald-night"
                    style="background: linear-gradient(180deg, #071814, #0e3a2b);"
                    title="Emerald Night">
                </button>

                <button class="theme-btn"
                    data-theme="crimson-velvet"
                    style="background: linear-gradient(180deg, #2a0b12, #7a1f2b);"
                    title="Crimson Velvet">
                </button>

                <button class="theme-btn"
                    data-theme="golden-hour"
                    style="background: linear-gradient(180deg, #3d3b07, #787510);"
                    title="Dia Ensolarado">
                </button>

                <button class="theme-btn"
                    data-theme="quiet-light"
                    style="background: linear-gradient(180deg, #bd3bab, #9443ca);"
                    title="Soft Bloom">
                </button>
            </div>
        </div>

        {{-- HINT DRAG --}}
        <p id="canvas-drag-hint" class="canvas-drag-hint hidden"><i class="fa-solid fa-arrows-up-down-left-right"></i> Arraste o pôster ou o título para posicionar livremente</p>

        {{-- PREVIEW HTML --}}
        <div id="share-preview" class="share-preview theme-movie-backdrop">
            {{-- Fundo Backdrop Full-Screen --}}
            <div class="preview-bg-wrapper">
                <img src="{{ $backdropUrl }}" class="preview-bg-image preview-bg-backdrop" alt="" aria-hidden="true">
                <img src="{{ $posterUrl }}" class="preview-bg-image preview-bg-poster" alt="" aria-hidden="true">
                <div class="preview-bg-overlay"></div>
            </div>

            {{-- Conteúdo Central Minimalista Posicionado Livremente --}}
            <div class="preview-content">
                {{-- Card do Poster (Draggable) --}}
                <div id="drag-poster-card" class="preview-poster-card canvas-draggable" data-drag-type="poster">
                    <img src="{{ $posterUrl ?? ($filme->poster_url ?? $filme->poster) }}" class="preview-poster-img" alt="Poster {{ $filme->nome }}" draggable="false">
                </div>

                {{-- Informações do Filme (Draggable) --}}
                <div id="drag-title-card" class="preview-info canvas-draggable" data-drag-type="title">
                    <div class="preview-inline-meta">
                        <h2 class="preview-title">{{ $filme->nome }}</h2>
                        @if($filme->nota)
                        <span class="meta-separator">•</span>
                        <div class="preview-rating-badge">
                            <div class="rating-display-number">
                                <span class="star-icon">★</span>
                                <span class="rating-value">{{ number_format($filme->nota, 1, ',', '.') }}</span>
                            </div>
                            <div class="rating-display-stars hidden">
                                @php
                                    $rounded = (int) round($filme->nota ?? 0);
                                    $fullStars = (int) floor($rounded / 2);
                                    $hasHalfStar = ($rounded % 2 === 1);
                                    $emptyStars = max(0, 5 - $fullStars - ($hasHalfStar ? 1 : 0));
                                @endphp
                                <span class="stars-wrapper">
                                    @for($i = 0; $i < $fullStars; $i++)
                                        <i class="fa-solid fa-star star-full"></i>
                                    @endfor
                                    @if($hasHalfStar)
                                        <i class="fa-solid fa-star-half-stroke star-half"></i>
                                    @endif
                                    @for($i = 0; $i < $emptyStars; $i++)
                                        <i class="fa-regular fa-star star-empty"></i>
                                    @endfor
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTROLES DO CANVAS (Escala, Desfoque, Brilho e Reset) --}}
        <div id="canvas-controls-bar" class="canvas-controls hidden">
            <div class="canvas-controls-header">
                <span class="canvas-controls-title"><i class="fa-solid fa-sliders"></i> Ajustes do Story</span>
                <button type="button" id="btn-reset-canvas" class="canvas-reset-btn" title="Resetar Posições e Ajustes">
                    <i class="fa-solid fa-rotate-left"></i> Resetar
                </button>
            </div>

            <div class="canvas-control-item">
                <div class="canvas-control-label-row">
                    <label class="canvas-control-label">
                        <i class="fa-solid fa-image"></i> Imagem do Fundo
                    </label>
                    <div class="canvas-toggle-group">
                        <button type="button" id="btn-bg-type-backdrop" class="canvas-toggle-btn active" data-type="backdrop">
                            <i class="fa-solid fa-film"></i> Backdrop
                        </button>
                        <button type="button" id="btn-bg-type-poster" class="canvas-toggle-btn" data-type="poster">
                            <i class="fa-solid fa-file-image"></i> Pôster
                        </button>
                    </div>
                </div>
            </div>

            <div class="canvas-control-item">
                <div class="canvas-control-label-row">
                    <label class="canvas-control-label">
                        <i class="fa-solid fa-eye"></i> Exibir Elementos
                    </label>
                    <div class="canvas-toggle-group">
                        <button type="button" id="btn-toggle-title" class="canvas-toggle-btn active" data-target="title">
                            <i class="fa-solid fa-font"></i> Título
                        </button>
                        <button type="button" id="btn-toggle-rating" class="canvas-toggle-btn active" data-target="rating">
                            <i class="fa-solid fa-star"></i> Nota
                        </button>
                    </div>
                </div>
            </div>

            <div class="canvas-control-item">
                <div class="canvas-control-label-row">
                    <label class="canvas-control-label">
                        <i class="fa-solid fa-star"></i> Estilo da Nota
                    </label>
                    <div class="canvas-toggle-group">
                        <button type="button" id="btn-rating-style-number" class="canvas-toggle-btn active" data-style="number">
                            <i class="fa-solid fa-hashtag"></i> Número
                        </button>
                        <button type="button" id="btn-rating-style-stars" class="canvas-toggle-btn" data-style="stars">
                            <i class="fa-solid fa-star"></i> 5 Estrelas
                        </button>
                    </div>
                </div>
            </div>

            <div class="canvas-control-item">
                <div class="canvas-control-label-row">
                    <label for="share-poster-scale-slider" class="canvas-control-label">
                        <i class="fa-solid fa-up-right-and-down-left-from-center"></i> Tamanho do Pôster
                    </label>
                    <span id="poster-scale-value" class="canvas-control-val">100%</span>
                </div>
                <input type="range" id="share-poster-scale-slider" min="0.3" max="1.4" step="0.05" value="1.0" class="canvas-slider">
            </div>

            <div class="canvas-control-item">
                <div class="canvas-control-label-row">
                    <label for="share-title-scale-slider" class="canvas-control-label">
                        <i class="fa-solid fa-font"></i> Tamanho do Título
                    </label>
                    <span id="title-scale-value" class="canvas-control-val">100%</span>
                </div>
                <input type="range" id="share-title-scale-slider" min="0.5" max="2.0" step="0.05" value="1.0" class="canvas-slider">
            </div>

            <div class="canvas-control-item">
                <div class="canvas-control-label-row">
                    <label for="share-rating-scale-slider" class="canvas-control-label">
                        <i class="fa-solid fa-star"></i> Tamanho da Nota
                    </label>
                    <span id="rating-scale-value" class="canvas-control-val">100%</span>
                </div>
                <input type="range" id="share-rating-scale-slider" min="0.5" max="2.0" step="0.05" value="1.0" class="canvas-slider">
            </div>

            <div class="canvas-control-item">
                <div class="canvas-control-label-row">
                    <label for="share-bg-blur-slider" class="canvas-control-label">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Desfoque do Fundo
                    </label>
                    <span id="bg-blur-value" class="canvas-control-val">6px</span>
                </div>
                <input type="range" id="share-bg-blur-slider" min="0" max="16" step="1" value="6" class="canvas-slider">
            </div>

            <div class="canvas-control-item">
                <div class="canvas-control-label-row">
                    <label for="share-bg-brightness-slider" class="canvas-control-label">
                        <i class="fa-solid fa-sun"></i> Brilho do Fundo
                    </label>
                    <span id="bg-brightness-value" class="canvas-control-val">68%</span>
                </div>
                <input type="range" id="share-bg-brightness-slider" min="0.3" max="1.5" step="0.05" value="0.68" class="canvas-slider">
            </div>
        </div>

        {{-- AÇÕES --}}
        <div class="share-actions">
            <form id="share-form" method="POST" data-loading action="{{ route('filme.share.generate', $filme->id) }}">
            @csrf
            <input type="hidden" name="theme" id="share-theme-input" value="movie-backdrop">
            <input type="hidden" name="custom_backdrop_url" id="share-custom-backdrop-input" value="">
            <input type="hidden" name="custom_poster_url" id="share-custom-poster-input" value="">
            <input type="hidden" name="poster_x" id="share-poster-x-input" value="50">
            <input type="hidden" name="poster_y" id="share-poster-y-input" value="40">
            <input type="hidden" name="poster_scale" id="share-poster-scale-input" value="1.0">
            <input type="hidden" name="title_x" id="share-title-x-input" value="50">
            <input type="hidden" name="title_y" id="share-title-y-input" value="85">
            <input type="hidden" name="title_scale" id="share-title-scale-input" value="1.0">
            <input type="hidden" name="rating_scale" id="share-rating-scale-input" value="1.0">
            <input type="hidden" name="bg_type" id="share-bg-type-input" value="backdrop">
            <input type="hidden" name="show_title" id="share-show-title-input" value="1">
            <input type="hidden" name="show_rating" id="share-show-rating-input" value="1">
            <input type="hidden" name="rating_style" id="share-rating-style-input" value="number">
            <input type="hidden" name="bg_blur" id="share-bg-blur-input" value="6">
            <input type="hidden" name="bg_brightness" id="share-bg-brightness-input" value="0.68">
            <button type="submit" class= "share-btn share-btn-primary">Gerar Imagem</button>
        </form>

            <button id="btn-instagram" class="share-btn insta">
                <i class="fa-brands fa-instagram"></i>
                Instagram
            </button>

            <button id="btn-whatsapp" class="share-btn whatsapp">
                <i class="fa-brands fa-whatsapp"></i>
                WhatsApp
            </button>

            <button id="btn-twitter" class="share-btn twitter">
                <i class="fa-brands fa-x-twitter"></i>
                Twitter / X
            </button>
        </div>

        <p class="share-actions-hint">Gere a imagem antes de compartilhar.</p>

        <p class="share-info">Resolução: 1080×1920px </p>

        <x-loading-overlay id="page-loading" text="Carregando..." />

    </div>
</div>

<x-poster-selector />

@endsection

