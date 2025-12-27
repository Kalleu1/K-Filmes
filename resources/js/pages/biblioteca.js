export default function InitBiblioteca() {
    const root = document.querySelector('.library-page') || document.getElementById('library-page');
    if (!root) return;

    const filterBtns  = Array.from(root.querySelectorAll('.filter-btn'));
    const filterMenus = Array.from(root.querySelectorAll('.filter-menu'));

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();

            const filterType = this.dataset.filter;
            const menu = root.querySelector(`.filter-menu[data-menu="${filterType}"]`);

            if (!menu) return;

            const isActive = menu.classList.contains('active');

            // Fecha outros menus e atualiza aria
            filterMenus.forEach(m => {
                if (m !== menu) {
                    m.classList.remove('active');
                    m.setAttribute('aria-hidden', 'true');
                }
            });

            filterBtns.forEach(b => {
                if (b !== this) {
                    b.classList.remove('active');
                    b.setAttribute('aria-expanded', 'false');
                }
            });

            // Toggle atual com aria
            menu.classList.toggle('active', !isActive);
            this.classList.toggle('active', !isActive);
            menu.setAttribute('aria-hidden', String(isActive));
            this.setAttribute('aria-expanded', String(!isActive));

            // focus no primeiro campo do menu ao abrir
            if (!isActive) {
                const firstInput = menu.querySelector('input, select, textarea, button');
                if (firstInput) firstInput.focus();
            }
        });
    });

    // Clique fora fecha tudo
    document.addEventListener('click', () => {
        filterMenus.forEach(menu => {
            menu.classList.remove('active');
            menu.setAttribute('aria-hidden', 'true');
        });
        filterBtns.forEach(btn => {
            btn.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
        });
    });

    // Clique dentro do menu não fecha
    filterMenus.forEach(menu => {
        menu.addEventListener('click', e => e.stopPropagation());
    });
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
