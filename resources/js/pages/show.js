export default function initPersonalMovie() {
    const page = document.querySelector('.movie-page');
    if (!page) return;

    const ui = getUIElements();
    bindEditModal(ui);
    bindDeleteModal(ui);
    bindShare(ui);
    bindRating(ui);
    bindGlobalModalClose(ui);
    bindCloseButtons(ui);
    bindPosterSelector(ui);
    bindBackdropSelector(ui);
}




/* =========================
   UI ELEMENTS
========================= */

function getUIElements() {
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');

    // procurar rating apenas dentro do modal de edição para evitar conflitos com outras páginas
    const ratingInput = editModal ? editModal.querySelector('#nota') : null;
    const ratingDisplay = editModal ? editModal.querySelector('.rating-display') : null;

    return {
        editModal,
        deleteModal,
        openEditBtn: document.querySelector('[data-action="open-edit-modal"]'),
        openDeleteBtn: document.querySelector('[data-action="open-delete-modal"]'),
        openPosterSelectorBtn: document.querySelector('[data-action="open-poster-selector"]'),
        openBackdropSelectorBtn: document.querySelector('[data-action="open-backdrop-selector"]'),
        shareBtn: document.querySelector('[data-action="share-movie"]'),
        ratingInput,
        ratingDisplay
    };


}


/* =========================
   ACTIONS
========================= */

function bindEditModal(ui) {
    if (!ui.openEditBtn || !ui.editModal) return;
    ui.openEditBtn.addEventListener('click', () => openModal(ui.editModal));
}

function bindDeleteModal(ui) {
    if (!ui.openDeleteBtn || !ui.deleteModal) return;
    ui.openDeleteBtn.addEventListener('click', () => openModal(ui.deleteModal));
}

function bindGlobalModalClose(ui) {
    const modals = [ui.editModal, ui.deleteModal].filter(Boolean);

    // Clique fora do conteúdo (fechar)
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
    });

    // ESC para fechar todos
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') modals.forEach(closeModal);
    });
}

function openModal(modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

function bindCloseButtons() {
    // Delegação: fechar o modal correspondente ao botão
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="close-modal"]');
        if (!btn) return;
        const modal = btn.closest('.modal-overlay');
        if (modal) closeModal(modal);
    });
}

function bindShare(ui) {
    if (!ui.shareBtn) return;
    const shareUrl = ui.shareBtn.dataset.shareUrl;
    if (!shareUrl) {
        console.warn('Share URL não encontrada no botão');
        return;
    }
    ui.shareBtn.addEventListener('click', () => window.open(shareUrl, '_blank'));
}


/* =========================
   RATING
========================= */

function bindRating(ui) {
    if (!ui.ratingInput || !ui.ratingDisplay) return;

    // inicializar display
    ui.ratingDisplay.textContent = ui.ratingInput.value;

    ui.ratingInput.addEventListener('input', () => {
        // formatar com uma casa decimal
        const v = parseFloat(ui.ratingInput.value);
        ui.ratingDisplay.textContent = Number.isNaN(v) ? ui.ratingInput.value : v.toFixed(1);
    });
}

function bindPosterSelector(ui) {
    if (!ui.openPosterSelectorBtn) return;

    ui.openPosterSelectorBtn.addEventListener('click', () => {
        const tmdbId = ui.openPosterSelectorBtn.dataset.tmdbId;
        const currentPoster = ui.openPosterSelectorBtn.dataset.currentPoster;
        const filmeId = ui.openPosterSelectorBtn.dataset.filmeId;

        if (!tmdbId || !filmeId) return;


        const selector = new window.ArtworkSelector();
        selector.open({
            tmdbId: tmdbId,
            type: 'poster',
            current: currentPoster,
            onConfirm: (selectedPosterPath) => {
                fetch(`/filmes/${filmeId}/update-poster`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        poster_path: selectedPosterPath
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.new_url) {
                        const img = document.querySelector('.hero-poster');
                        if (img) {
                            img.src = data.new_url;
                        }
                        ui.openPosterSelectorBtn.dataset.currentPoster = selectedPosterPath;
                        window.location.reload();
                    }
                })
                .catch(err => {
                    console.error('Erro ao atualizar o pôster:', err);
                    alert('Erro ao salvar o pôster.');
                });
            }
        });
    });
}

function bindBackdropSelector(ui) {
    if (!ui.openBackdropSelectorBtn) return;

    ui.openBackdropSelectorBtn.addEventListener('click', () => {
        const tmdbId = ui.openBackdropSelectorBtn.dataset.tmdbId;
        const currentBackdrop = ui.openBackdropSelectorBtn.dataset.currentBackdrop;
        const filmeId = ui.openBackdropSelectorBtn.dataset.filmeId;

        if (!tmdbId || !filmeId) return;

        const selector = new window.ArtworkSelector();
        selector.open({
            tmdbId: tmdbId,
            type: 'backdrop',
            current: currentBackdrop,
            onConfirm: (selectedBackdropPath) => {
                fetch(`/filmes/${filmeId}/update-backdrop`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        backdrop_path: selectedBackdropPath
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.new_url) {
                        const heroSec = document.querySelector('.movie-hero');
                        if (heroSec) {
                            heroSec.style.backgroundImage = `url('${data.new_url}')`;
                        }
                        ui.openBackdropSelectorBtn.dataset.currentBackdrop = selectedBackdropPath;
                        window.location.reload();
                    }
                })
                .catch(err => {
                    console.error('Erro ao atualizar o backdrop:', err);
                    alert('Erro ao salvar o backdrop.');
                });
            }
        });
    });
}

