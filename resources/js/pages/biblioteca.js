export default function InitBiblioteca() {
    const root = document.querySelector('.library-page') || document.getElementById('library-page');
    if (!root) return;

    const filterBtns = Array.from(root.querySelectorAll('.filter-btn'));
    
    // Cria o overlay apenas para uso em mobile
    const overlayContainer = document.createElement('div');
    overlayContainer.classList.add('filters-overlay');
    const filtersMinimal = root.querySelector('.filters-minimal');
    if (filtersMinimal) {
        filtersMinimal.appendChild(overlayContainer);
    }

    // Função para fechar todos os menus locais (desktop)
    function closeAllLocalMenus() {
        const localMenus = Array.from(root.querySelectorAll('.filter-menu'));
        localMenus.forEach(m => m.classList.remove('active'));
    }

    // Abre overlay ou menu com o conteúdo do filtro clicado
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();

            const isMobile = window.innerWidth <= 768;
            const filterType = this.dataset.filter;
            const menu = root.querySelector(`.filter-menu[data-menu="${filterType}"]`);
            if (!menu) return;

            if (isMobile) {
                // --- COMPORTAMENTO MOBILE (OVERLAY) ---
                closeAllLocalMenus();
                const isActive = overlayContainer.classList.contains('active') && overlayContainer.dataset.activeFilter === filterType;

                overlayContainer.classList.remove('active');
                overlayContainer.innerHTML = '';
                filterBtns.forEach(b => b.classList.remove('active'));
                filterBtns.forEach(b => b.setAttribute('aria-expanded', 'false'));

                if (!isActive) {
                    overlayContainer.innerHTML = menu.innerHTML;
                    overlayContainer.classList.add('active');
                    overlayContainer.dataset.activeFilter = filterType;

                    const rect = filtersMinimal.getBoundingClientRect();
                    const scrollTop = window.scrollY || document.documentElement.scrollTop;
                    overlayContainer.style.top = (rect.bottom + scrollTop) + 'px';
                    overlayContainer.style.left = (rect.left + rect.width / 2) + 'px';
                    overlayContainer.style.transform = 'translateX(-50%) translateY(0)';

                    this.classList.add('active');
                    this.setAttribute('aria-expanded', 'true');

                    const firstInput = overlayContainer.querySelector('input, select, textarea, button');
                    if (firstInput) firstInput.focus();
                }
            } else {
                // --- COMPORTAMENTO DESKTOP (LOCAL DROPDOWN) ---
                overlayContainer.classList.remove('active');
                overlayContainer.innerHTML = '';

                const isAlreadyActive = menu.classList.contains('active');
                
                // Limpa outros botões e menus locais
                filterBtns.forEach(b => {
                    if (b !== this) {
                        b.classList.remove('active');
                        b.setAttribute('aria-expanded', 'false');
                    }
                });
                closeAllLocalMenus();

                if (!isAlreadyActive) {
                    menu.classList.add('active');
                    this.classList.add('active');
                    this.setAttribute('aria-expanded', 'true');
                    
                    const firstInput = menu.querySelector('input, select, textarea, button');
                    if (firstInput) firstInput.focus();
                } else {
                    this.classList.remove('active');
                    this.setAttribute('aria-expanded', 'false');
                }
            }
        });
    });

    // Fecha tudo ao clicar fora
    document.addEventListener('click', () => {
        // Mobile
        overlayContainer.classList.remove('active');
        overlayContainer.innerHTML = '';
        
        // Desktop
        closeAllLocalMenus();
        
        filterBtns.forEach(b => {
            b.classList.remove('active');
            b.setAttribute('aria-expanded', 'false');
        });
    });

    // Impede fechar ao clicar dentro do overlay mobile
    overlayContainer.addEventListener('click', e => e.stopPropagation());

    // Impede fechar ao clicar dentro dos menus desktop locales
    const localMenus = Array.from(root.querySelectorAll('.filter-menu'));
    localMenus.forEach(m => {
        m.addEventListener('click', e => e.stopPropagation());
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
