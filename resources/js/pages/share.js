import Toast from '../components/toastComp/Toast';

export default function initShare() {
    const preview = document.getElementById("share-preview");
    if (!preview) return; // 🔥 impede rodar em outras páginas

    const themeInput = document.getElementById("share-theme-input");
    const form = document.getElementById('share-form');
    const loading = document.getElementById('page-loading');
    const submitBtn = form.querySelector('button[type="submit"]');

    const btnInstagram = document.getElementById('btn-instagram');
    const btnWhatsapp  = document.getElementById('btn-whatsapp');
    const btnTwitter   = document.getElementById('btn-twitter');
    const shareButtons = [btnInstagram, btnWhatsapp, btnTwitter];

    let selectedTheme = 'movie-backdrop';
    let generatedImageUrl = null;

    const isMobile = /Android|iPhone|iPad/i.test(navigator.userAgent);
    const shareText = encodeURIComponent('');
    let lastPreShareToastAt = 0;

    function setShareButtonsDisabled(disabled) {
        shareButtons.forEach(btn => {
            if (btn) btn.disabled = disabled;
        });
    }

    function showToast(payload, fallbackType = 'info', fallbackMessage = '') {
        if (!payload) {
            if (fallbackMessage) {
                Toast.show({ type: fallbackType, message: fallbackMessage });
            }
            return;
        }

        // Compatibilidade com respostas que enviam { toast: { toasts: [...] } }
        if (Array.isArray(payload.toasts)) {
            payload.toasts.forEach(toast => Toast.show(toast));
            return;
        }

        Toast.show(payload);
    }

    function notifyGenerateFirst() {
        const now = Date.now();
        if (now - lastPreShareToastAt < 1200) return;

        lastPreShareToastAt = now;
        Toast.show({
            type: 'info',
            message: 'Gere a imagem antes de compartilhar.',
            timeout: 2500,
        });
    }

    function applyTheme(theme) {
        preview.classList.add('is-transitioning');

        setTimeout(() => {
            preview.className = `share-preview theme-${theme}`;
            preview.classList.remove('is-transitioning');
        }, 120);
    }

    document.querySelectorAll(".theme-btn").forEach(btn => {
        btn.addEventListener("click", () => {
            if (btn.classList.contains('theme-btn-edit-backdrop') || btn.classList.contains('theme-btn-edit-poster')) return;

            document.querySelectorAll(".theme-btn")
                .forEach(b => b.classList.remove("selected"));

            btn.classList.add("selected");

            selectedTheme = btn.dataset.theme;
            applyTheme(selectedTheme);
            themeInput.value = selectedTheme;
        });
    });

    // Seletor temporário de backdrop para o compartilhamento
    const editBackdropBtn = document.querySelector('[data-action="open-share-backdrop-selector"]');
    if (editBackdropBtn) {
        editBackdropBtn.addEventListener('click', () => {
            const tmdbId = editBackdropBtn.dataset.tmdbId;
            if (!tmdbId) return;

            if (typeof window.ArtworkSelector === 'function') {
                const selector = new window.ArtworkSelector();
                selector.open({
                    tmdbId: tmdbId,
                    type: 'backdrop',
                    current: '',
                    onConfirm: (selectedPath) => {
                        if (!selectedPath) return;

                        const fullUrl = selectedPath.startsWith('http')
                            ? selectedPath
                            : `https://image.tmdb.org/t/p/w1280${selectedPath.startsWith('/') ? '' : '/'}${selectedPath}`;

                        // 1. Atualizar imagem de fundo no preview
                        const bgImg = preview.querySelector('.preview-bg-image.preview-bg-backdrop, .preview-bg-image');
                        if (bgImg) {
                            bgImg.src = fullUrl;
                        }

                        // 2. Atualizar miniaturas dos botões de backdrop
                        const backdropBtns = document.querySelectorAll('.theme-btn-backdrop');
                        backdropBtns.forEach(btn => {
                            btn.style.backgroundImage = `url('${fullUrl}')`;
                        });

                        const activeBackdropBtn = document.querySelector('.theme-btn-backdrop');
                        if (activeBackdropBtn) {
                            activeBackdropBtn.click();
                        }

                        // 3. Atualizar campo oculto custom_backdrop_url no formulário
                        const customBackdropInput = document.getElementById('share-custom-backdrop-input');
                        if (customBackdropInput) {
                            customBackdropInput.value = fullUrl;
                        }

                        showToast({
                            type: 'success',
                            message: 'Fundo do story alterado para este compartilhamento!',
                            timeout: 3000
                        });
                    }
                });
            }
        });
    }

    // Seletor temporário de pôster para o compartilhamento (inclui pôsteres sem título)
    const editPosterBtn = document.querySelector('[data-action="open-share-poster-selector"]');
    if (editPosterBtn) {
        editPosterBtn.addEventListener('click', () => {
            const tmdbId = editPosterBtn.dataset.tmdbId;
            if (!tmdbId) return;

            if (typeof window.ArtworkSelector === 'function') {
                const selector = new window.ArtworkSelector();
                selector.open({
                    tmdbId: tmdbId,
                    type: 'poster',
                    current: '',
                    onConfirm: (selectedPath) => {
                        if (!selectedPath) return;

                        const fullUrl = selectedPath.startsWith('http')
                            ? selectedPath
                            : `https://image.tmdb.org/t/p/w500${selectedPath.startsWith('/') ? '' : '/'}${selectedPath}`;

                        // 1. Atualizar imagem do poster no card do preview e no fundo full-poster
                        const posterImg = preview.querySelector('.preview-poster-img');
                        if (posterImg) {
                            posterImg.src = fullUrl;
                        }

                        const bgPosterImg = preview.querySelector('.preview-bg-poster');
                        if (bgPosterImg) {
                            bgPosterImg.src = fullUrl;
                        }

                        const fullPosterBtns = document.querySelectorAll('.theme-btn-full-poster');
                        fullPosterBtns.forEach(btn => {
                            btn.style.backgroundImage = `url('${fullUrl}')`;
                        });

                        // 2. Atualizar campo oculto custom_poster_url no formulário
                        const customPosterInput = document.getElementById('share-custom-poster-input');
                        if (customPosterInput) {
                            customPosterInput.value = fullUrl;
                        }

                        showToast({
                            type: 'success',
                            message: 'Pôster do story alterado para este compartilhamento!',
                            timeout: 3000
                        });
                    }
                });
            }
        });
    }

    form.addEventListener('submit', async function(e){
        e.preventDefault();
        loading.classList.remove('hidden');
        submitBtn.disabled = true;
        setShareButtonsDisabled(true);

        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content
                }
            });

            let data = {};

            try {
                data = await res.json();
            } catch {
                data = {};
            }

            if (!res.ok) {
                showToast(data.toast, 'error', 'Nao foi possivel gerar a imagem agora. Tente novamente.');
                return;
            }

            if(data.success){
                generatedImageUrl = data.url;
                showToast(data.toast);

                const link = document.createElement('a');
                link.href = generatedImageUrl;
                link.download = 'share.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

            }
        } catch {
            Toast.show({
                type: 'error',
                message: 'Erro de conexao ao gerar a imagem.',
                timeout: 5000,
            });
        } finally {
            submitBtn.disabled = false;
            setShareButtonsDisabled(false);
            loading.classList.add('hidden');
        }
    });

    btnInstagram.addEventListener('click', () => {
        if (!generatedImageUrl) {
            notifyGenerateFirst();
            return;
        }

        if (isMobile) {
            window.location.href = "instagram://story-camera";
        } else {
            window.open("https://www.instagram.com/", "_blank");
            alert("No PC, use o celular para postar Stories.");
        }
    });

    btnWhatsapp.addEventListener('click', () => {
        if (!generatedImageUrl) {
            notifyGenerateFirst();
            return;
        }

        const url = isMobile
            ? `whatsapp://send?text=${shareText}`
            : `https://web.whatsapp.com/send?text=${shareText}`;

        window.open(url, '_blank');
    });

    btnTwitter.addEventListener('click', () => {
        if (!generatedImageUrl) {
            notifyGenerateFirst();
            return;
        }

        window.open(
            `https://twitter.com/intent/tweet?text=${shareText}`,
            '_blank'
        );
    });
}
