export default function InitBiblioteca() {
    const root = document.querySelector('.library-page') || document.getElementById('library-page');
    if (!root) return;

    const filterBtns = Array.from(root.querySelectorAll('.filter-btn'));
    const form = root.querySelector('.library-search-bar');

    // Toggle dropdowns
    filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const filterType = btn.dataset.filter;
            const menu = root.querySelector(`.filter-menu[data-menu="${filterType}"]`);
            if (!menu) return;

            const isAlreadyActive = menu.classList.contains('active');
            
            // Close all other menus
            root.querySelectorAll('.filter-menu').forEach(m => {
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
        root.querySelectorAll('.filter-menu').forEach(m => m.classList.remove('active'));
        filterBtns.forEach(b => b.classList.remove('active'));
    });

    // Handle option selection
    const optionBtns = Array.from(root.querySelectorAll('.filter-select-option'));
    optionBtns.forEach(opt => {
        opt.addEventListener('click', (e) => {
            e.stopPropagation();
            const filterName = opt.dataset.filterName;
            const filterValue = opt.dataset.value;

            // Find matching hidden input
            const hiddenInput = form.querySelector(`input[name="${filterName}"]`);
            if (hiddenInput) {
                hiddenInput.value = filterValue;
            }

            // Submit form
            form.submit();
        });
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
