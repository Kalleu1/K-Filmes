export default function InitBiblioteca() {
    const root = document.querySelector('.library-page') || document.getElementById('library-page');
    if (!root) return;

    const filterBtns = Array.from(root.querySelectorAll('.filter-btn'));
    const overlayContainer = document.createElement('div');
    overlayContainer.classList.add('filters-overlay');
    root.querySelector('.filters-minimal').appendChild(overlayContainer);

    // Abre overlay com o conteúdo do filtro clicado
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();

            const filterType = this.dataset.filter;
            const menu = root.querySelector(`.filter-menu[data-menu="${filterType}"]`);
            if (!menu) return;

            const isActive = overlayContainer.classList.contains('active') && overlayContainer.dataset.activeFilter === filterType;

            // Fecha overlay se estiver aberto
            overlayContainer.classList.remove('active');
            overlayContainer.innerHTML = '';
            filterBtns.forEach(b => b.classList.remove('active'));
            filterBtns.forEach(b => b.setAttribute('aria-expanded', 'false'));

            if (!isActive) {
                // Copia conteúdo do menu para o overlay
                overlayContainer.innerHTML = menu.innerHTML;
                overlayContainer.classList.add('active');
                overlayContainer.dataset.activeFilter = filterType;

                // Ajusta posição vertical
                const rect = root.querySelector('.filters-minimal').getBoundingClientRect();
                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                overlayContainer.style.top = rect.bottom + scrollTop + -0 + 'px'; // 5px de espaçamento
                overlayContainer.style.left = rect.left + rect.width / 2 + 'px';
                overlayContainer.style.transform = 'translateX(-50%)';

                // Marca botão ativo
                this.classList.add('active');
                this.setAttribute('aria-expanded', 'true');

                // Focus no primeiro input/button
                const firstInput = overlayContainer.querySelector('input, select, textarea, button');
                if (firstInput) firstInput.focus();
            }
        });
    });

    // Fecha overlay ao clicar fora
    document.addEventListener('click', () => {
        overlayContainer.classList.remove('active');
        overlayContainer.innerHTML = '';
        filterBtns.forEach(b => {
            b.classList.remove('active');
            b.setAttribute('aria-expanded', 'false');
        });
    });

    // Não fecha ao clicar dentro do overlay
    overlayContainer.addEventListener('click', e => e.stopPropagation());
}





// Função para favoritar/desfavoritar (aplica apenas em .favorite-btn)
document.addEventListener('click', async e => {
    const btn = e.target.closest('.favorite-btn[data-filme-id]');
    if (!btn) return;

    const filmeId = btn.dataset.filmeId;
    const isFavorited = btn.classList.contains('favorited');

    try {
        const response = await fetch(`/filmes/${filmeId}/favorito`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify({ favorito: !isFavorited })
        });

        if (response.ok) {
            btn.classList.toggle('favorited');
        } else {
            console.error('Falha ao atualizar favorito', response.status);
        }
    } catch (err) {
        console.error(err);
    }
});
