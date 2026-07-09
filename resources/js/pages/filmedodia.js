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

    const sourceRadios = form.querySelectorAll('input[name="fonte"]');
    const dropdownBiblioteca = document.getElementById('dropdownBiblioteca');
    const dropdownTmdb = document.getElementById('dropdownTmdb');
    const generoInput = document.getElementById('generoInput');

    function toggleSource() {
        const activeRadio = form.querySelector('input[name="fonte"]:checked');
        const activeSource = activeRadio ? activeRadio.value : 'biblioteca';

        if (activeSource === 'biblioteca') {
            if (dropdownBiblioteca) dropdownBiblioteca.style.display = 'inline-block';
            if (dropdownTmdb) dropdownTmdb.style.display = 'none';
            updateHiddenGenre(dropdownBiblioteca);
        } else {
            if (dropdownBiblioteca) dropdownBiblioteca.style.display = 'none';
            if (dropdownTmdb) dropdownTmdb.style.display = 'inline-block';
            updateHiddenGenre(dropdownTmdb);
        }
    }

    function updateHiddenGenre(dropdownContainer) {
        if (!dropdownContainer) return;
        const activeOption = dropdownContainer.querySelector('.filter-select-option.active');
        if (activeOption && generoInput) {
            generoInput.value = activeOption.dataset.value;
        }
    }

    sourceRadios.forEach(radio => {
        radio.addEventListener('change', toggleSource);
    });

    // Handle Custom Dropdown Toggles
    const filterBtns = Array.from(form.querySelectorAll('.filter-btn'));
    filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const filterType = btn.dataset.filter;
            const menu = form.querySelector(`.filter-menu[data-menu="${filterType}"]`);
            if (!menu) return;

            const isAlreadyActive = menu.classList.contains('active');
            
            // Close other menus
            form.querySelectorAll('.filter-menu').forEach(m => {
                if (m !== menu) m.classList.remove('active');
            });
            filterBtns.forEach(b => {
                if (b !== btn) b.classList.remove('active');
            });

            if (!isAlreadyActive) {
                menu.classList.add('active');
                btn.classList.add('active');
            } else {
                menu.classList.remove('active');
                btn.classList.remove('active');
            }
        });
    });

    // Close menus on click outside
    document.addEventListener('click', () => {
        form.querySelectorAll('.filter-menu').forEach(m => m.classList.remove('active'));
        filterBtns.forEach(b => b.classList.remove('active'));
    });

    // Option selection
    const optionBtns = Array.from(form.querySelectorAll('.filter-select-option'));
    optionBtns.forEach(opt => {
        opt.addEventListener('click', (e) => {
            e.stopPropagation();
            const value = opt.dataset.value;

            // Set value to hidden input
            if (generoInput) {
                generoInput.value = value;
            }

            // Update active state in current list
            const menu = opt.closest('.filter-menu');
            if (menu) {
                menu.querySelectorAll('.filter-select-option').forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                menu.classList.remove('active');
            }
            
            // Remove active class from corresponding btn
            const filterType = menu ? menu.dataset.menu : null;
            if (filterType) {
                const btn = form.querySelector(`.filter-btn[data-filter="${filterType}"]`);
                if (btn) btn.classList.remove('active');
            }
        });
    });

    // Run once on load
    toggleSource();

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

    if (filmeSorteado.error) {
        ui.posterImg.classList.remove('animate-fade-out');
        return;
    }

    const trackList = filmes.length ? filmes : [{ id: filmeSorteado.id, titulo: filmeSorteado.nome, poster: filmeSorteado.poster }];

    montarRoleta(ui.rouletteTrack, trackList, filmeSorteado.poster);
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
        genero: formData.get('genero') || '',
        fonte: formData.get('fonte') || 'biblioteca'
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

    const repeatedPosters = [];
    for (let i = 0; i < 4; i++) {
        filmes.forEach(filme => {
            repeatedPosters.push(filme.poster);
        });
    }

    repeatedPosters.forEach(posterSrc => {
        track.appendChild(criarPoster(posterSrc));
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
    rouletteTrack.style.transition = 'none';
    void rouletteTrack.offsetWidth;

    await sleep(50);

    const posters = Array.from(rouletteTrack.children);
    if (posters.length === 0) return;

    const posterWidth = posters[0].offsetWidth || 80;
    const gap = 10;
    const N = posters.length;

    const trackWidth = N * posterWidth + (N - 1) * gap;
    const trackCenter = trackWidth / 2;
    const lastElementCenter = (N - 1) * (posterWidth + gap) + posterWidth / 2;
    const distance = lastElementCenter - trackCenter;

    const duracao = 4;

    rouletteTrack.style.transition =
        `transform ${duracao}s cubic-bezier(0.05, 0.9, 0.1, 1)`;

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
