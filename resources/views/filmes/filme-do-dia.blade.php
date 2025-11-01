@extends('layouts.app')

@section('content')
<div class="filme-dia-page">

    {{-- Poster inicial (placeholder) --}}
    <div class="poster-container" id="posterContainer">
        <img id="filmePoster" src="{{ asset('images/placeholder-poster.png') }}" 
             alt="Poster do Filme" class="poster-img">
        
        {{-- Container da roleta sobreposta --}}
        <div class="roulette-container" id="rouletteContainer">
            <div class="roulette-track" id="rouletteTrack"></div>
        </div>
    </div>

    {{-- Botão principal --}}
    <form id="filmeDoDiaForm" class="filme-dia-form">
        @csrf
        <button type="submit" class="btn-sortear">🎲 Sortear Filme</button>

        <div class="filters">
            <input type="number" name="ano" placeholder="Ano" min="1900" max="{{ date('Y') }}">
            <input type="text" name="diretor" placeholder="Diretor">
            <input type="text" name="genero" placeholder="Gênero">
        </div>

        <div class="fonte">
            <label><input type="radio" name="fonte" value="biblioteca" checked> Minha Biblioteca</label>
            <label><input type="radio" name="fonte" value="tmdb"> TMDB</label>
        </div>
    </form>
</div>

<script>
document.getElementById('filmeDoDiaForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const posterImg = document.getElementById('filmePoster');
    const rouletteContainer = document.getElementById('rouletteContainer');
    const rouletteTrack = document.getElementById('rouletteTrack');
    const formData = new FormData(e.target);

    // Fade-out do poster
    posterImg.classList.add('animate-fade-out');

    // Busca filme sorteado PRIMEIRO
    const sorteio = await fetch("{{ route('filme-do-dia.sortear') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': formData.get('_token') },
        body: formData
    });
    const filmeSorteado = await sorteio.json();

    // Busca filmes aleatórios para a roleta
    const res = await fetch("{{ route('filme-do-dia.aleatorios') }}");
    const data = await res.json();
    const filmes = data.results || [];
    if (!filmes.length) return;

    // Limpa a pista
    rouletteTrack.innerHTML = '';

    // Cria elementos da roleta
    filmes.forEach(filme => {
        const img = document.createElement('img');
        img.src = filme.poster;
        img.className = 'roulette-poster';
        rouletteTrack.appendChild(img);
    });

    // ADICIONA O FILME SORTEADO COMO ÚLTIMO
    const finalImg = document.createElement('img');
    finalImg.src = filmeSorteado.poster;
    finalImg.className = 'roulette-poster';
    rouletteTrack.appendChild(finalImg);

    // Mostra roleta
    rouletteContainer.style.display = 'flex';
    rouletteTrack.style.transform = 'translateX(0)';

    // Força reflow
    void rouletteTrack.offsetWidth;

    await new Promise(res => setTimeout(res, 100));

    // Calcula a posição do último filme
    // largura do poster (80px) + gap (10px) = 90px por item
    const itemWidth = 145;
    const lastItemIndex = filmes.length; // índice do último (será adicionado depois)
    const distance = lastItemIndex * itemWidth;

    // Duração MAIS LONGA (5 segundos)
    const duracao = 5;
    rouletteTrack.style.transition = `transform ${duracao}s cubic-bezier(0.1, 0.7, 0.1, 1)`; 

    
    setTimeout(() => {
        rouletteTrack.style.transform = `translateX(-${distance}px)`;
    }, 50);

    // Espera o fim da animação
    await new Promise(res => setTimeout(res, duracao * 1000 + 100));

    // Oculta roleta
    rouletteContainer.style.display = 'none';

    // Mostra poster final
    posterImg.src = filmeSorteado.poster;
    posterImg.title = filmeSorteado.nome;
    posterImg.style.cursor = 'pointer';
    posterImg.onclick = () => {
        window.location.href = filmeSorteado.fonte === 'biblioteca' 
            ? `/filmes/${filmeSorteado.id}` 
            : `/filmes/tmdb/${filmeSorteado.id}`;
    };

    posterImg.classList.remove('animate-fade-out');
    posterImg.classList.add('animate-fade-in');
    
    setTimeout(() => posterImg.classList.remove('animate-fade-in'), 800);
});

</script>
@endsection
