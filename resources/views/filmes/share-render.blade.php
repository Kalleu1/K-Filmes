<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartilhar Filme</title>
</head>
<body class="theme-{{ $theme }}">
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
</body>
</html>
