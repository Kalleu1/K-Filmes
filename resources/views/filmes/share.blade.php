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

        {{-- PREVIEW HTML --}}
        <div id="share-preview" class="share-preview theme-movie-backdrop">
            {{-- Fundo Backdrop Full-Screen --}}
            <div class="preview-bg-wrapper">
                <img src="{{ $backdropUrl }}" class="preview-bg-image preview-bg-backdrop" alt="" aria-hidden="true">
                <img src="{{ $posterUrl }}" class="preview-bg-image preview-bg-poster" alt="" aria-hidden="true">
                <div class="preview-bg-overlay"></div>
            </div>

            {{-- Conteúdo Central Minimalista --}}
            <div class="preview-content">
                {{-- Card do Poster --}}
                <div class="preview-poster-card">
                    <img src="{{ $posterUrl ?? ($filme->poster_url ?? $filme->poster) }}" class="preview-poster-img" alt="Poster {{ $filme->nome }}">
                </div>

                {{-- Informações do Filme (Estilo MUBI / Letterboxd na mesma linha) --}}
                <div class="preview-info">
                    <div class="preview-inline-meta">
                        <h2 class="preview-title">{{ $filme->nome }}</h2>
                        @if($filme->nota)
                        <span class="meta-separator">•</span>
                        <div class="preview-rating-badge">
                            <span class="star-icon">★</span>
                            <span class="rating-value">{{ number_format($filme->nota, 1, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- AÇÕES --}}
        <div class="share-actions">
            <form id="share-form" method="POST" data-loading action="{{ route('filme.share.generate', $filme->id) }}">
            @csrf
            <input type="hidden" name="theme" id="share-theme-input" value="movie-backdrop">
            <input type="hidden" name="custom_backdrop_url" id="share-custom-backdrop-input" value="">
            <input type="hidden" name="custom_poster_url" id="share-custom-poster-input" value="">
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

