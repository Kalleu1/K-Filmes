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
                
            </div>
        </div>

        {{-- PREVIEW HTML --}}
        <div id="share-preview" class="share-preview theme-noir" >
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
            <input type="hidden" name="theme" id="share-theme-input" value="noir">
            <button type="submit" class= "share-btn share-btn-primary">Gerar Imagem</button>
        </form>

            <button id="btn-instagram" class="share-btn insta" disabled>
                <i class="fa-brands fa-instagram"></i>
                Instagram
            </button>

            <button id="btn-whatsapp" class="share-btn whatsapp" disabled>
                <i class="fa-brands fa-whatsapp"></i>
                WhatsApp
            </button>

            <button id="btn-twitter" class="share-btn twitter" disabled>
                <i class="fa-brands fa-x-twitter"></i>
                Twitter / X
            </button>
        </div>

        <p class="share-info">Resolução: 1080×1920px </p>

        <div id="share-loading" class="share-loading hidden">
            <div class="loader"></div>
            <p>Gerando imagem…</p>
        </div>

    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const preview = document.getElementById("share-preview");
    const themeInput = document.getElementById("share-theme-input");
    const form = document.getElementById('share-form');
    const loading = document.getElementById('share-loading');
    const submitBtn = form.querySelector('button[type="submit"]');

    const btnInstagram = document.getElementById('btn-instagram');
    const btnWhatsapp  = document.getElementById('btn-whatsapp');
    const btnTwitter   = document.getElementById('btn-twitter');

    let selectedTheme = 'noir';
    let generatedImageUrl = null;

    // VERIFICAR SE É MOBILE
    const isMobile = /Android|iPhone|iPad/i.test(navigator.userAgent);
    const shareText = encodeURIComponent(
        ''
    );

    function applyTheme(theme) {
    preview.classList.add('is-transitioning');

    setTimeout(() => {
        preview.className = `share-preview theme-${theme}`;
        preview.classList.remove('is-transitioning');
    }, 120);
}

    // Troca de tema 
    document.querySelectorAll(".theme-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            document.querySelectorAll(".theme-btn")
                .forEach(b => b.classList.remove("selected"));

            btn.classList.add("selected");

            selectedTheme = btn.dataset.theme;
            applyTheme(selectedTheme);
            themeInput.value = selectedTheme;
        });
    });

    // Download
    form.addEventListener('submit', async function(e){
        e.preventDefault();

        loading.classList.remove('hidden');
        submitBtn.disabled = true;

        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token')
                }
            });

            const data = await res.json();

            if(data.success){
                generatedImageUrl = data.url;

                // download automático
                const link = document.createElement('a');
                link.href = generatedImageUrl;
                link.download = `filme_{{ $filme->id }}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // habilita botões de share
                btnInstagram.disabled = false;
                btnWhatsapp.disabled = false;
                btnTwitter.disabled = false;
            } else {
                alert('Erro ao gerar imagem: ' + data.message);
            }
        } catch (err) {
            alert('Erro ao gerar imagem.');
            console.error(err);
        } finally {
            submitBtn.disabled = false;
            loading.classList.add('hidden');
        }
    });

    // Instagram
    btnInstagram.addEventListener('click', () => {
        if (!generatedImageUrl) return;

        if (isMobile) {
            window.location.href = "instagram://story-camera";
        } else {
            window.open("https://www.instagram.com/", "_blank");
            alert(
                "No computador o Instagram não permite postar Stories.\n" +
                "A imagem já foi baixada, abra no celular para postar."
            );
        }
    });

    // WhatsApp
    btnWhatsapp.addEventListener('click', () => {
        if (!generatedImageUrl) return;

        const url = isMobile
            ? `whatsapp://send?text=${shareText}`
            : `https://web.whatsapp.com/send?text=${shareText}`;

        window.open(url, '_blank');
    });

    // 🔵 Twitter / X
    btnTwitter.addEventListener('click', () => {
        if (!generatedImageUrl) return;

        const twitterUrl =
            `https://twitter.com/intent/tweet?text=${shareText}`;

        window.open(twitterUrl, '_blank');
    });

    
});
</script>



@endsection
