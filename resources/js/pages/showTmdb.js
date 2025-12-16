// resources/js/pages/movieDetails.js
export default function initMovieDetails() {
    const page = document.getElementById('movie-details-page');
    if (!page) return;

    const ui = getUIElements();

    bindWatchedModal(ui);
    bindDeleteModal(ui);
    bindCloseButtons(ui);
    bindRating(ui);
}

function getUIElements() {
    return {
        watchedModal: document.getElementById('watchedModal'),
        deleteModal: document.getElementById('deleteModal'),

        openWatchedBtn: document.querySelector('[data-action="open-watched-modal"]'),
        openDeleteBtn: document.querySelector('[data-action="open-delete-modal"]'),

        closeBtns: document.querySelectorAll('[data-action="close-modal"]'),

        ratingInput: document.getElementById('nota'),
        ratingDisplay: document.querySelector('.rating-display'),
    };
}

function openModal(modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modal) {
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

function bindWatchedModal(ui) {
    if (!ui.openWatchedBtn || !ui.watchedModal) return;

    ui.openWatchedBtn.addEventListener('click', () => {
        openModal(ui.watchedModal);
    });

    bindOverlayClose(ui.watchedModal);
}

function bindDeleteModal(ui) {
    if (!ui.openDeleteBtn || !ui.deleteModal) return;

    ui.openDeleteBtn.addEventListener('click', () => {
        openModal(ui.deleteModal);
    });

    bindOverlayClose(ui.deleteModal);
}

function bindOverlayClose(modal) {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal(modal);
        }
    });
}

function bindCloseButtons(ui) {
    ui.closeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(ui.watchedModal);
            closeModal(ui.deleteModal);
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal(ui.watchedModal);
            closeModal(ui.deleteModal);
        }
    });
}

function bindRating(ui) {
    if (!ui.ratingInput || !ui.ratingDisplay) return;

    ui.ratingInput.addEventListener('input', () => {
        ui.ratingDisplay.textContent = ui.ratingInput.value;
    });
}
