<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartilhar Filme</title>
</head>
<body class="theme-{{ $theme }}">
    <div id="share-preview" class="share-preview theme-{{ $theme }}">
        {{-- Fundo Backdrop / Poster Full-Screen --}}
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

            {{-- Informações do Filme (Título e Nota Numérica) --}}
            <div class="preview-info">
                <h2 class="preview-title">{{ $filme->nome }}</h2>

                @if($filme->nota)
                <div class="preview-rating-badge">
                    <span class="star-icon">★</span>
                    <span class="rating-value">{{ number_format($filme->nota, 1, ',', '.') }}</span>
                    <span class="rating-max">/ 10</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
