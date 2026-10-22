export default function initColecoes() {
    const page = document.querySelector('.collections-page');
    const modal = document.getElementById('removeCollectionFilmModal');

    if (!page || !modal) return;

    bindCollectionFilmSearch(page);

    let activeForm = null;
    const filmName = modal.querySelector('[data-remove-film-name]');

    const closeModal = () => {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        activeForm = null;
    };

    page.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-action="open-remove-film-modal"]');
        if (!removeButton) return;

        activeForm = removeButton.closest('form');
        filmName.textContent = `"${removeButton.dataset.filmeName}"`;
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal || event.target.closest('[data-action="close-modal"]')) {
            closeModal();
        }

        function bindCollectionFilmSearch(page) {
            const input = page.querySelector('#collection-film-search');
            const availableFilms = page.querySelector('[data-collection-available-films]');
            const status = page.querySelector('[data-collection-search-status]');
            const emptyState = page.querySelector('[data-collection-search-empty]');

            if (!input || !availableFilms || !status || !emptyState) return;

            const films = [...availableFilms.querySelectorAll('[data-collection-film]')];
            const updateResults = () => {
                const query = input.value.trim().toLocaleLowerCase();
                let visibleCount = 0;

                films.forEach((film) => {
                    const visible = !query || film.dataset.filmName.includes(query);
                    film.hidden = !visible;
                    if (visible) visibleCount += 1;
                });

                status.textContent = `${visibleCount} ${visibleCount === 1 ? 'filme encontrado' : 'filmes encontrados'}`;
                emptyState.hidden = visibleCount !== 0;
            };

            input.addEventListener('input', updateResults);

            films.forEach((form) => {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    const button = form.querySelector('button');
                    const token = form.querySelector('input[name="_token"]')?.value;
                    const formData = new FormData(form);
                    button.disabled = true;
                    button.querySelector('span').textContent = 'Adicionando...';

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': token,
                            },
                            body: formData,
                        });

                        if (!response.ok) throw new Error('Não foi possível adicionar o filme.');

                        await response.json();
                        form.remove();
                        updateResults();
                    } catch (error) {
                        button.disabled = false;
                        button.querySelector('span').textContent = 'Adicionar';
                        window.alert(error.message);
                    }
                });
            });
        }
    });

    modal.querySelector('[data-action="confirm-remove-film"]').addEventListener('click', () => {
        if (activeForm) activeForm.submit();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
}
