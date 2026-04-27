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
                <button class="theme-btn"
                data-theme="noir"
                style="background: linear-gradient(180deg, #111, #777575);"
                title="Noir Cinematográfico">
            </button>

            <button class="theme-btn"
                data-theme="deep-blue"
                style="background: linear-gradient(rgba(110,203,255,0.22));"
                title="Azul Profundo">
            </button>

            <button class="theme-btn"
                data-theme="amber-gold"
                style="background: linear-gradient(rgba(255, 193, 92, 0.22));"
                title="Dourado Vip">
            </button>

            <button
                class="theme-btn"
                data-theme="emerald-night"
                style="background: linear-gradient(180deg, #071814, #0e3a2b);"
                title="Emerald Night">
            </button>

            <button
                class="theme-btn"
                data-theme="crimson-velvet"
                style="background: linear-gradient(180deg, #2a0b12, #7a1f2b);"
                title="Crimson Velvet">
                
            </button>

            <button
                class="theme-btn"
                data-theme="golden-hour"
                style="background: linear-gradient(180deg, #acb61e, #cbf805);"
                title="Dia Ensolarado">
                
            </button>

            <button
                class="theme-btn"
                data-theme="quiet-light"
                style="background: linear-gradient(180deg, #bd3bab, #9443ca);"
                title="soft-bloom">
                
            </button>
                
            </div>
        </div>

        {{-- PREVIEW HTML --}}
        <div id="share-preview" class="share-preview theme-noir mode-default" >
            <img src="{{ $filme->poster }}" class="preview-poster" alt="Poster {{ $filme->nome }}">

            <div class="preview-title">{{ strtoupper($filme->nome) }}</div>

            @if($filme->nota)
            <div class="preview-rating">
                {{ number_format($filme->nota, 1, ',', '.') }}/10
            </div>
            @endif

            <div class="preview-footer">K-Filmes © 2025</div>
        </div>

        {{-- AÇÕES --}}
        <div class="share-actions">
            <form id="share-form" method="POST" data-loading action="{{ route('filme.share.generate', $filme->id) }}">
            @csrf
            <input type="hidden" name="theme" id="share-theme-input" value="noir">
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

@endsection

