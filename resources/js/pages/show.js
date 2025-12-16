export default function initPersonalMovie() {
    
    const page = document.getElementById('personal-movie-page');
    if (!page) return;

    const shareUrl = page.dataset.shareUrl;
    const ui = getUIElements();

    bindEditModal(ui);
    bindDeleteModal(ui);
    bindShare(ui, shareUrl);
    bindRating(ui);
    bindGlobalModalClose(ui);
    bindCloseButtons(ui);
}


/* =========================
   UI ELEMENTS
========================= */

function getUIElements() {
    return {
        editModal: document.getElementById('editModal'),
        deleteModal: document.getElementById('deleteModal'),

        openEditBtn: document.querySelector('[data-action="open-edit-modal"]'),
        openDeleteBtn: document.querySelector('[data-action="open-delete-modal"]'),
        shareBtn: document.querySelector('[data-action="share-movie"]'),

        ratingInput: document.getElementById('nota'),
        ratingDisplay: document.querySelector('.rating-display'),

        
    };
}


/* =========================
   ACTIONS
========================= */

function bindEditModal(ui) {
    if (!ui.openEditBtn || !ui.editModal) return;

    ui.openEditBtn.addEventListener('click', () => {
        openModal(ui.editModal);
        
    });
}

function bindDeleteModal(ui) {
    if (!ui.openDeleteBtn || !ui.deleteModal) return;

    ui.openDeleteBtn.addEventListener('click', () => {
        openModal(ui.deleteModal);
    });
}

function bindGlobalModalClose(ui) {
    const modals = [ui.editModal, ui.deleteModal].filter(Boolean);

    // Clique fora
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });

    // ESC (um único listener)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modals.forEach(closeModal);
        }
    });
}

function openModal(modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modal) {
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

function bindCloseButtons(ui) {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action="close-modal"]');
        if (!btn) return;

        closeModal(ui.editModal);
        closeModal(ui.deleteModal);
    });
}

function bindShare(ui, shareUrl) {
    if (!ui.shareBtn) return;

    ui.shareBtn.addEventListener('click', () => {
        window.open(shareUrl, '_blank');
    });
}
/* =========================
   RATING
========================= */

function bindRating(ui) {
    if (!ui.ratingInput || !ui.ratingDisplay) return;

    ui.ratingInput.addEventListener('input', () => {
        ui.ratingDisplay.textContent = ui.ratingInput.value;
    });
}

