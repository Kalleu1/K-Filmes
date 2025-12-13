@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/share.css') }}">
<script src="{{ asset('js/share.js') }}" defer></script>

<div class="share-container">

    <div class="share-content">
        <h1 class="share-title">{{ $filme->nome }}</h1>
        <p class="share-subtitle">Imagem gerada para compartilhar nos stories</p>

        {{-- Seleção de temas --}}
        <div class="theme-selector">
            <p>Escolha o estilo do fundo:</p>

            <div class="theme-options">
                {{-- Botões de tema (visuais) --}}
                <button class="theme-btn"
                data-theme="noir"
                style="background: linear-gradient(180deg, #111, #000);"
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
                
            </div>
        </div>

        {{-- PREVIEW HTML --}}
        <div id="share-preview" class="share-preview" aria-live="polite">
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
            <button id="btn-download" class="share-btn share-btn-primary">
                Fazer Download
            </button>

            <button id="btn-copy" class="share-btn share-btn-secondary">
                Copiar Link
            </button>
        </div>

        <p class="share-info">Resolução: 1080×1920px (Ideal para Stories)</p>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const preview = document.getElementById("share-preview");

    document.querySelectorAll(".theme-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document
                .querySelectorAll(".theme-btn")
                .forEach(b => b.classList.remove("selected"));

            btn.classList.add("selected");

            const theme = btn.dataset.theme;
            preview.className = `share-preview theme-${theme}`;
        });
    });
});



</script>
@endsection
