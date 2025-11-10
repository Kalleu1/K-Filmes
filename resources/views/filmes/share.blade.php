@extends('layouts.app')

@section('content')
<div class="share-container">
    <div class="share-content">
        <h1 class="share-title">{{ $filme->nome }}</h1>

        <p class="share-subtitle">Imagem gerada para compartilhar nos stories</p>

        <div class="theme-selector">
    <p>Escolha o estilo do fundo:</p>
    <div class="theme-options">
        <button class="theme-btn" data-theme="default" style="background: linear-gradient(180deg, #012840, #274F73, #0E0D40);" title="Azul Clássico"></button>
        <button class="theme-btn" data-theme="noite-profunda" style="background: linear-gradient(180deg, #1E3C72, #2A5298);" title="Noite Profunda"></button>
        <button class="theme-btn" data-theme="grafite" style="background: linear-gradient(180deg, #232526, #414345);" title="Grafite"></button>
        <button class="theme-btn" data-theme="dourado" style="background: linear-gradient(180deg, #422E05, #996515, #FFD700);" title="Dourado"></button>
        <button class="theme-btn" data-theme="por-do-sol" style="background: linear-gradient(180deg, #FF5E62, #FFC371);" title="Pôr do Sol"></button>
        <button class="theme-btn" data-theme="esmeralda" style="background: linear-gradient(180deg, #004D40, #009688, #80CBC4);" title="Esmeralda"></button>
        <button class="theme-btn" data-theme="lavanda" style="background: linear-gradient(180deg, #6F4EA1, #AA84C6, #E6E6FA);" title="Lavanda"></button>
        <button class="theme-btn" data-theme="aurora" style="background: linear-gradient(180deg, #2575FC, #6A11CB);" title="Aurora"></button>
    </div>
</div>


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

document.querySelectorAll('.theme-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        // Marca visualmente o tema clicado
        document.querySelectorAll('.theme-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');

        // Envia o tema escolhido via Ajax
        const theme = btn.dataset.theme;
        const url = `/filmes/{{ $filme->id }}/share-image`;
        const formData = new FormData();
        formData.append('theme', theme);
        formData.append('_token', '{{ csrf_token() }}');

        // Efeito visual de carregamento
        const img = document.querySelector('.share-image');
        img.style.opacity = '0.4';
        img.style.transition = 'opacity 0.4s ease';

        const res = await fetch(url, {
            method: 'POST',
            body: formData,
        });

        if (res.ok) {
            const data = await res.json();
            img.src = data.url;
            img.onload = () => img.style.opacity = '1';
        } else {
            alert('Erro ao gerar imagem. Tente novamente.');
            img.style.opacity = '1';
        }
    });
});


</script>
@endsection
