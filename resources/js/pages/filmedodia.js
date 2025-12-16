export default function initFilmeDoDia() {
    const page = document.getElementById('filme-dia-page');
    if (!page) return;

    

    const form = document.getElementById('filmeDoDiaForm');
    if (!form) return;

    
    const urls = {
        sortear: page.dataset.sortearUrl,
        aleatorios: page.dataset.aleatoriosUrl,
    };

    const ui = getUIElements();

    
    form.addEventListener('submit', (e) =>
        handleSubmit(e, form, ui, urls)
    );
}

/* =========================
   HANDLER PRINCIPAL
========================= */

async function handleSubmit(e, form, ui, urls) {
    e.preventDefault();

    const formData = new FormData(form);

    fadeOutPoster(ui.posterImg);

    const filmeSorteado = await sortearFilme(formData, urls.sortear);
    const filmes = await buscarFilmesAleatorios(formData, urls.aleatorios);

    if (!filmes.length) return;

    montarRoleta(ui.rouletteTrack, filmes, filmeSorteado.poster);
    await animarRoleta(ui);

    mostrarFilmeSorteado(ui.posterImg, filmeSorteado);
}

/* =========================
   FETCH
========================= */

async function sortearFilme(formData, url) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': formData.get('_token')
        },
        body: formData
    });

    return res.json();
}

async function buscarFilmesAleatorios(formData, url) {
    const params = new URLSearchParams({
        ano: formData.get('ano') || '',
        diretor: formData.get('diretor') || '',
        genero: formData.get('genero') || ''
    });

    const res = await fetch(`${url}?${params}`);
    const data = await res.json();

    return data.results || [];
}

/* =========================
   UI
========================= */

function getUIElements() {
    return {
        posterImg: document.getElementById('filmePoster'),
        rouletteContainer: document.getElementById('rouletteContainer'),
        rouletteTrack: document.getElementById('rouletteTrack'),
    };
}

function fadeOutPoster(poster) {
    poster.classList.add('animate-fade-out');
}

function montarRoleta(track, filmes, posterFinal) {
    track.innerHTML = '';

    filmes.forEach(filme => {
        track.appendChild(criarPoster(filme.poster));
    });

    track.appendChild(criarPoster(posterFinal));
}

function criarPoster(src) {
    const img = document.createElement('img');
    img.src = src;
    img.className = 'roulette-poster';
    return img;
}

async function animarRoleta({ rouletteContainer, rouletteTrack }) {
    rouletteContainer.style.display = 'flex';
    rouletteTrack.style.transform = 'translateX(0)';
    void rouletteTrack.offsetWidth;

    await sleep(100);

    const itemWidth = 145;
    const totalItems = rouletteTrack.children.length - 1;
    const distance = totalItems * itemWidth;
    const duracao = 5;

    rouletteTrack.style.transition =
        `transform ${duracao}s cubic-bezier(0.1, 0.7, 0.1, 1)`;

    rouletteTrack.style.transform = `translateX(-${distance}px)`;

    await sleep(duracao * 1000 + 100);

    rouletteContainer.style.display = 'none';
}

function mostrarFilmeSorteado(poster, filme) {
    poster.src = filme.poster;
    poster.title = filme.nome;
    poster.style.cursor = 'pointer';

    poster.onclick = () => {
        window.location.href =
            filme.fonte === 'biblioteca'
                ? `/filmes/${filme.id}`
                : `/filmes/tmdb/${filme.id}`;
    };

    poster.classList.remove('animate-fade-out');
    poster.classList.add('animate-fade-in');

    setTimeout(() => {
        poster.classList.remove('animate-fade-in');
    }, 800);
}

/* =========================
   UTILS
========================= */

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}
