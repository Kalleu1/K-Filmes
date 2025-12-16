export default function InitBiblioteca() {
    const root = document.getElementById("library-page");
    if (!root) return;

    const filterBtns  = root.querySelectorAll('.filter-btn');
    const filterMenus = root.querySelectorAll('.filter-menu');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();

            const filterType = this.dataset.filter;
            const menu = root.querySelector(`[data-menu="${filterType}"]`);

            if (!menu) return;

            // Fecha outros menus
            filterMenus.forEach(m => {
                if (m !== menu) m.classList.remove('active');
            });

            filterBtns.forEach(b => {
                if (b !== this) b.classList.remove('active');
            });

            // Toggle atual
            menu.classList.toggle('active');
            this.classList.toggle('active');
        });
    });

    // Clique fora fecha tudo
    document.addEventListener('click', () => {
        filterMenus.forEach(menu => menu.classList.remove('active'));
        filterBtns.forEach(btn => btn.classList.remove('active'));
    });

    // Clique dentro do menu não fecha
    filterMenus.forEach(menu => {
        menu.addEventListener('click', e => e.stopPropagation());
    });
}


// Função para favoritar/desfavoritar
document.addEventListener('click', async e => {
    const btn = e.target.closest('[data-filme-id]');
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
        }
    } catch (err) {
        console.error(err);
    }
});
