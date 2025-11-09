@extends('layouts.app')

@section('content')
<div class="share-container">
    <div class="share-content">
        <h1 class="share-title">{{ $filme->nome }}</h1>

        <p class="share-subtitle">Imagem gerada para compartilhar nos stories</p>

        <div class="share-image-wrapper">
            <img src="{{ $url }}" alt="{{ $filme->nome }}" class="share-image">
        </div>

        <div class="share-actions">
            <a href="{{ $url }}" download class="share-btn share-btn-primary">
                <span class="share-btn-icon">↓</span>
                Fazer Download
            </a>

            <button class="share-btn share-btn-secondary" onclick="copyToClipboard('{{ $url }}')">
                <span class="share-btn-icon">⎘</span>
                Copiar Link
            </button>
        </div>

        <p class="share-info">
            Resolução: 1080x1920px (Ideal para Stories)
        </p>
    </div>
</div>

<script>
    function copyToClipboard(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Link copiado para a área de transferência!');
        }).catch(err => {
            console.error('Erro ao copiar:', err);
        });
    }
</script>
@endsection
