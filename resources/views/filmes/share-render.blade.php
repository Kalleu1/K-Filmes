<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <title>Compartilhar Filme</title>
</head>
<body class="theme-{{ $theme }}">
    <div id="share-preview" class="share-preview theme-{{ $theme }}">
        {{-- Fundo Backdrop / Poster Full-Screen --}}
        @php
            $overlayOpacity = max(0, min(1, (1.5 - ($bgBrightness ?? 0.68)) / 1.2));
        @endphp
        <div class="preview-bg-wrapper">
            <img src="{{ $backdropUrl }}" class="preview-bg-image preview-bg-backdrop" alt="" aria-hidden="true" style="filter: blur({{ $bgBlur ?? 6 }}px) brightness({{ $bgBrightness ?? 0.68 }}) contrast(1.05); {{ ($bgType ?? 'backdrop') === 'poster' ? 'display: none !important;' : 'display: block !important;' }}">
            <img src="{{ $posterUrl }}" class="preview-bg-image preview-bg-poster" alt="" aria-hidden="true" style="filter: blur({{ $bgBlur ?? 6 }}px) brightness({{ $bgBrightness ?? 0.68 }}) contrast(1.05); {{ ($bgType ?? 'backdrop') === 'poster' ? 'display: block !important;' : 'display: none !important;' }}">
            <div class="preview-bg-overlay" style="opacity: {{ number_format($overlayOpacity, 2, '.', '') }};"></div>
        </div>

        {{-- Conteúdo Posicionado Livremente --}}
        <div class="preview-content">
            {{-- Card do Poster --}}
            <div class="preview-poster-card" style="left: {{ $posterX }}%; top: {{ $posterY }}%; transform: translate(-50%, -50%) scale({{ $posterScale }}); position: absolute;">
                <img src="{{ $posterUrl ?? ($filme->poster_url ?? $filme->poster) }}" class="preview-poster-img" alt="Poster {{ $filme->nome }}">
            </div>

            {{-- Informações do Filme --}}
            <div class="preview-info" style="left: {{ $titleX }}%; top: {{ $titleY }}%; transform: translate(-50%, -50%); position: absolute;">
                <div class="preview-inline-meta">
                    <h2 class="preview-title" style="transform: scale({{ $titleScale ?? 1.0 }}); transform-origin: center; {{ !($showTitle ?? true) ? 'display: none !important;' : '' }}">{{ $filme->nome }}</h2>
                    @if($filme->nota)
                    <span class="meta-separator" style="{{ !($showTitle ?? true) || !($showRating ?? true) ? 'display: none !important;' : '' }}">•</span>
                    <div class="preview-rating-badge" style="transform: scale({{ $ratingScale ?? 1.0 }}); transform-origin: center; {{ !($showRating ?? true) ? 'display: none !important;' : '' }}">
                        <div class="rating-display-number {{ ($ratingStyle ?? 'number') === 'stars' ? 'hidden' : '' }}">
                            <span class="star-icon">★</span>
                            <span class="rating-value">{{ number_format($filme->nota, 1, ',', '.') }}</span>
                        </div>
                        <div class="rating-display-stars {{ ($ratingStyle ?? 'number') === 'stars' ? '' : 'hidden' }}">
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
</body>
</html>
