@extends('layouts.base')

@section('content')
<div style="min-height: 100vh; padding: 4rem 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #0c0c0e; color: #eaeaea; font-family: system-ui, sans-serif;">
    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 20px; padding: 3rem; max-width: 600px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.5); text-align: center;">
        <h1 style="font-family: 'Varela Round', sans-serif; font-size: 2rem; color: #ffffff; margin-top: 0; margin-bottom: 1rem; letter-spacing: -0.02em;">
            Testar Seletor de Backdrop
        </h1>
        <p style="color: #a0a0a5; font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.5;">
            Insira o ID do filme da TMDB para buscar os backdrops (imagens de fundo) oficiais. Exemplos:<br>
            <strong>550</strong> (Clube da Luta), <strong>27205</strong> (A Origem), <strong>157336</strong> (Interestelar).
        </p>

        <div style="display: flex; flex-direction: column; gap: 1rem; align-items: stretch; margin-bottom: 2rem;">
            <input type="number" id="tmdb-id-input" value="550" placeholder="TMDB Movie ID" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 30px; padding: 0.8rem 1.5rem; color: #fff; font-size: 1rem; outline: none; text-align: center; transition: border-color 0.3s;">
            <button id="open-selector-btn" style="background: #FFD700; color: #1B263B; border: none; border-radius: 30px; padding: 0.8rem 1.5rem; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                Abrir Seletor de Backdrop
            </button>
        </div>

        <div id="result-box" style="display: none; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 2rem; text-align: center;">
            <h3 style="color: #FFD700; margin-top: 0; font-size: 1.1rem; margin-bottom: 1rem;">Resultado da Seleção:</h3>
            <p style="font-family: monospace; background: rgba(0,0,0,0.3); padding: 0.5rem; border-radius: 8px; font-size: 0.9rem; overflow-x: auto; margin-bottom: 1.5rem;" id="result-poster-path"></p>
            <div style="display: flex; justify-content: center; width: 100%;">
                <img id="result-poster-img" src="" alt="Backdrop Selecionado" style="max-width: 100%; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); aspect-ratio: 16/9; object-fit: cover;">
            </div>
        </div>
    </div>
</div>

{{-- Inclui o componente Blade do seletor de pôsteres (que agora é o Artwork Selector) --}}
<x-poster-selector />
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('open-selector-btn');
    const input = document.getElementById('tmdb-id-input');
    const resultBox = document.getElementById('result-box');
    const resultPath = document.getElementById('result-poster-path');
    const resultImg = document.getElementById('result-poster-img');

    if (btn) {
        btn.addEventListener('click', () => {
            const tmdbId = parseInt(input.value);
            if (isNaN(tmdbId) || tmdbId <= 0) {
                alert('Por favor, insira um TMDB ID válido.');
                return;
            }

            // Instancia o componente ArtworkSelector
            const selector = new window.ArtworkSelector();
            
            // Abre o seletor configurado para o tipo 'backdrop'
            selector.open({
                tmdbId: tmdbId,
                type: 'backdrop',
                current: null,
                onConfirm: (filePath) => {
                    resultPath.textContent = filePath;
                    resultImg.src = `https://image.tmdb.org/t/p/w780${filePath}`;
                    resultBox.style.display = 'block';
                }
            });
        });
    }
});
</script>
@endpush
