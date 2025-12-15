@extends('layouts.app')

@section('content')

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
            <form id="share-form" method="POST" action="{{ route('filme.share.generate', $filme->id) }}">
            @csrf
            <input type="hidden" name="theme" id="share-theme-input" value="noir"> <!-- tema selecionado dinamicamente -->
            <button type="submit">Gerar Imagem</button>
        </form>

            <button id="btn-copy" class="share-btn share-btn-secondary">
                Copiar Link
            </button>
        </div>

        <p class="share-info">Resolução: 1080×1920px </p>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const preview = document.getElementById("share-preview");
    const themeInput = document.getElementById("share-theme-input");
    let selectedTheme = 'noir';

    document.querySelectorAll(".theme-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.querySelectorAll(".theme-btn")
                .forEach(b => b.classList.remove("selected"));

            btn.classList.add("selected");

            selectedTheme = btn.dataset.theme;

            preview.className = `share-preview theme-${selectedTheme}`;

            themeInput.value = selectedTheme;
        });
    });

document.getElementById('share-form').addEventListener('submit', async function(e){
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    const res = await fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': formData.get('_token')
        }
    });

    const data = await res.json();
    if(data.success){
        // Criar link temporário para download
        const link = document.createElement('a');
        link.href = data.url;
        link.download = `filme_${{{ $filme->id }}}.png`; // nome do arquivo
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else {
        alert('Erro ao gerar imagem: ' + data.message);
    }
});
});
</script>

@endsection
