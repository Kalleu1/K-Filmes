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

    const canvasHint = document.getElementById('canvas-drag-hint');
    const canvasControlsBar = document.getElementById('canvas-controls-bar');

    function applyTheme(theme) {
        preview.classList.add('is-transitioning');

        setTimeout(() => {
            preview.className = `share-preview theme-${theme}`;
            preview.classList.remove('is-transitioning');

            if (theme === 'custom-canvas') {
                if (canvasHint) canvasHint.classList.remove('hidden');
                if (canvasControlsBar) canvasControlsBar.classList.remove('hidden');
                updateBgFilter();
            } else {
                if (canvasHint) canvasHint.classList.add('hidden');
                if (canvasControlsBar) canvasControlsBar.classList.add('hidden');
            }
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

    // Seletor temporário de fundo para o compartilhamento (inclui backdrops e pôsteres)
    const editBackdropBtn = document.querySelector('[data-action="open-share-backdrop-selector"]');
    if (editBackdropBtn) {
        editBackdropBtn.addEventListener('click', () => {
            const tmdbId = editBackdropBtn.dataset.tmdbId;
            if (!tmdbId) return;

            if (typeof window.ArtworkSelector === 'function') {
                const selector = new window.ArtworkSelector();
                selector.open({
                    tmdbId: tmdbId,
                    type: 'all',
                    current: '',
                    onConfirm: (selectedPath) => {
                        if (!selectedPath) return;

                        const fullUrl = selectedPath.startsWith('http')
                            ? selectedPath
                            : `https://image.tmdb.org/t/p/w1280${selectedPath.startsWith('/') ? '' : '/'}${selectedPath}`;

                        // 1. Atualizar imagem de fundo no preview (apenas as camadas do fundo)
                        const bgBackdropImg = preview.querySelector('.preview-bg-backdrop');
                        const bgPosterImg   = preview.querySelector('.preview-bg-poster');

                        if (bgBackdropImg) bgBackdropImg.src = fullUrl;
                        if (bgPosterImg)   bgPosterImg.src = fullUrl;

                        // 2. Atualizar miniaturas dos botões de backdrop
                        const backdropBtns = document.querySelectorAll('.theme-btn-backdrop');
                        backdropBtns.forEach(btn => {
                            btn.style.backgroundImage = `url('${fullUrl}')`;
                        });

                        // 3. Atualizar campo oculto custom_backdrop_url no formulário
                        const customBackdropInput = document.getElementById('share-custom-backdrop-input');
                        if (customBackdropInput) {
                            customBackdropInput.value = fullUrl;
                        }

                        showToast({
                            type: 'success',
                            message: 'Fundo do story alterado com sucesso!',
                            timeout: 3000
                        });
                    }
                });
            }
        });
    }

    // Seletor temporário de pôster para o compartilhamento (card da frente)
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

                        // 1. Atualizar APENAS a imagem do card de pôster flutuante na frente
                        const posterImg = preview.querySelector('.preview-poster-img');
                        if (posterImg) {
                            posterImg.src = fullUrl;
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
                            message: 'Pôster do story alterado com sucesso!',
                            timeout: 3000
                        });
                    }
                });
            }
        });
    }

    // ==========================================
    // CANVAS DRAG & DROP + SCALE & BG CONTROLS
    // ==========================================
    const dragPosterCard = document.getElementById('drag-poster-card');
    const dragTitleCard  = document.getElementById('drag-title-card');
    const scaleSlider    = document.getElementById('share-poster-scale-slider');
    const titleScaleSlider = document.getElementById('share-title-scale-slider');
    const ratingScaleSlider = document.getElementById('share-rating-scale-slider');
    const bgBlurSlider   = document.getElementById('share-bg-blur-slider');
    const bgBrightnessSlider = document.getElementById('share-bg-brightness-slider');
    const resetCanvasBtn = document.getElementById('btn-reset-canvas');

    const btnBgTypeBackdrop = document.getElementById('btn-bg-type-backdrop');
    const btnBgTypePoster   = document.getElementById('btn-bg-type-poster');
    const btnToggleTitle    = document.getElementById('btn-toggle-title');
    const btnToggleRating   = document.getElementById('btn-toggle-rating');
    const btnRatingStyleNumber = document.getElementById('btn-rating-style-number');
    const btnRatingStyleStars  = document.getElementById('btn-rating-style-stars');

    const posterXInput    = document.getElementById('share-poster-x-input');
    const posterYInput    = document.getElementById('share-poster-y-input');
    const posterScaleInput= document.getElementById('share-poster-scale-input');
    const titleXInput     = document.getElementById('share-title-x-input');
    const titleYInput     = document.getElementById('share-title-y-input');
    const titleScaleInput = document.getElementById('share-title-scale-input');
    const ratingScaleInput = document.getElementById('share-rating-scale-input');
    const bgTypeInput     = document.getElementById('share-bg-type-input');
    const showTitleInput  = document.getElementById('share-show-title-input');
    const showRatingInput = document.getElementById('share-show-rating-input');
    const ratingStyleInput = document.getElementById('share-rating-style-input');
    const bgBlurInput     = document.getElementById('share-bg-blur-input');
    const bgBrightnessInput = document.getElementById('share-bg-brightness-input');

    let currentScale = 1.0;
    let currentTitleScale = 1.0;
    let currentRatingScale = 1.0;
    let currentBlur = 6;
    let currentBrightness = 0.68;
    let currentBgType = 'backdrop';
    let isTitleVisible = true;
    let isRatingVisible = true;
    let currentRatingStyle = 'number';

    const scaleValueSpan = document.getElementById('poster-scale-value');
    const titleScaleValueSpan = document.getElementById('title-scale-value');
    const ratingScaleValueSpan = document.getElementById('rating-scale-value');
    const blurValueSpan  = document.getElementById('bg-blur-value');
    const brightnessValueSpan = document.getElementById('bg-brightness-value');

    function updateControlBadges() {
        if (scaleValueSpan) scaleValueSpan.textContent = `${Math.round(currentScale * 100)}%`;
        if (titleScaleValueSpan) titleScaleValueSpan.textContent = `${Math.round(currentTitleScale * 100)}%`;
        if (ratingScaleValueSpan) ratingScaleValueSpan.textContent = `${Math.round(currentRatingScale * 100)}%`;
        if (blurValueSpan) blurValueSpan.textContent = `${Math.round(currentBlur)}px`;
        if (brightnessValueSpan) brightnessValueSpan.textContent = `${Math.round(currentBrightness * 100)}%`;
    }

    function setBgType(type) {
        currentBgType = type;
        if (bgTypeInput) bgTypeInput.value = type;

        const bgBackdropImg = preview.querySelector('.preview-bg-backdrop');
        const bgPosterImg   = preview.querySelector('.preview-bg-poster');

        if (type === 'poster') {
            if (bgBackdropImg) bgBackdropImg.style.setProperty('display', 'none', 'important');
            if (bgPosterImg) bgPosterImg.style.setProperty('display', 'block', 'important');
            btnBgTypeBackdrop?.classList.remove('active');
            btnBgTypePoster?.classList.add('active');
        } else {
            if (bgBackdropImg) bgBackdropImg.style.setProperty('display', 'block', 'important');
            if (bgPosterImg) bgPosterImg.style.setProperty('display', 'none', 'important');
            btnBgTypeBackdrop?.classList.add('active');
            btnBgTypePoster?.classList.remove('active');
        }
    }

    function setRatingStyle(style) {
        currentRatingStyle = style;
        if (ratingStyleInput) ratingStyleInput.value = style;

        const ratingNumEl   = preview.querySelector('.rating-display-number');
        const ratingStarsEl = preview.querySelector('.rating-display-stars');

        if (style === 'stars') {
            if (ratingNumEl) ratingNumEl.classList.add('hidden');
            if (ratingStarsEl) ratingStarsEl.classList.remove('hidden');
            btnRatingStyleNumber?.classList.remove('active');
            btnRatingStyleStars?.classList.add('active');
        } else {
            if (ratingNumEl) ratingNumEl.classList.remove('hidden');
            if (ratingStarsEl) ratingStarsEl.classList.add('hidden');
            btnRatingStyleNumber?.classList.add('active');
            btnRatingStyleStars?.classList.remove('active');
        }
    }

    function updateElementVisibility() {
        const previewTitle = preview.querySelector('.preview-title');
        const previewRatingBadge = preview.querySelector('.preview-rating-badge');
        const metaSeparator = preview.querySelector('.meta-separator');

        if (previewTitle) {
            previewTitle.style.setProperty('display', isTitleVisible ? '' : 'none', 'important');
        }
        if (previewRatingBadge) {
            previewRatingBadge.style.setProperty('display', isRatingVisible ? '' : 'none', 'important');
        }
        if (metaSeparator) {
            const showSep = isTitleVisible && isRatingVisible;
            metaSeparator.style.setProperty('display', showSep ? '' : 'none', 'important');
        }

        if (showTitleInput) showTitleInput.value = isTitleVisible ? '1' : '0';
        if (showRatingInput) showRatingInput.value = isRatingVisible ? '1' : '0';

        if (isTitleVisible) {
            btnToggleTitle?.classList.add('active');
        } else {
            btnToggleTitle?.classList.remove('active');
        }

        if (isRatingVisible) {
            btnToggleRating?.classList.add('active');
        } else {
            btnToggleRating?.classList.remove('active');
        }
    }

    btnBgTypeBackdrop?.addEventListener('click', () => setBgType('backdrop'));
    btnBgTypePoster?.addEventListener('click', () => setBgType('poster'));

    btnRatingStyleNumber?.addEventListener('click', () => setRatingStyle('number'));
    btnRatingStyleStars?.addEventListener('click', () => setRatingStyle('stars'));

    btnToggleTitle?.addEventListener('click', () => {
        isTitleVisible = !isTitleVisible;
        updateElementVisibility();
    });

    btnToggleRating?.addEventListener('click', () => {
        isRatingVisible = !isRatingVisible;
        updateElementVisibility();
    });

    function updateTitleRatingStyles() {
        const previewTitle = preview.querySelector('.preview-title');
        const previewRatingBadge = preview.querySelector('.preview-rating-badge');

        if (previewTitle) {
            previewTitle.style.transform = `scale(${currentTitleScale})`;
            previewTitle.style.transformOrigin = 'center';
        }
        if (previewRatingBadge) {
            previewRatingBadge.style.transform = `scale(${currentRatingScale})`;
            previewRatingBadge.style.transformOrigin = 'center';
        }

        if (titleScaleInput) titleScaleInput.value = currentTitleScale.toFixed(2);
        if (ratingScaleInput) ratingScaleInput.value = currentRatingScale.toFixed(2);
        updateControlBadges();
    }

    function updateBgFilter() {
        const bgImages = preview.querySelectorAll('.preview-bg-image');
        const bgOverlay = preview.querySelector('.preview-bg-overlay');

        bgImages.forEach(img => {
            img.style.filter = `blur(${currentBlur}px) brightness(${currentBrightness}) contrast(1.05)`;
        });

        if (bgOverlay) {
            // Quanto maior o brilho (ate 1.5), menor a sombra do overlay (ate 0.0)
            const overlayOpacity = Math.max(0, Math.min(1, (1.5 - currentBrightness) / 1.2));
            bgOverlay.style.opacity = overlayOpacity.toFixed(2);
        }

        if (bgBlurInput) bgBlurInput.value = currentBlur;
        if (bgBrightnessInput) bgBrightnessInput.value = currentBrightness;
        updateControlBadges();
    }

    function initDraggable(el, type) {
        if (!el) return;

        let isDragging = false;

        function getPercentageCoords(e) {
            const rect = preview.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;

            let xPct = ((clientX - rect.left) / rect.width) * 100;
            let yPct = ((clientY - rect.top) / rect.height) * 100;

            // Limitar dentro dos limites do canvas (5% a 95%)
            xPct = Math.max(5, Math.min(95, xPct));
            yPct = Math.max(5, Math.min(95, yPct));

            return { xPct, yPct };
        }

        function onPointerDown(e) {
            if (selectedTheme !== 'custom-canvas') return;
            e.preventDefault();
            isDragging = true;
            el.classList.add('is-dragging');

            if (e.pointerId !== undefined && el.setPointerCapture) {
                try { el.setPointerCapture(e.pointerId); } catch {}
            }
        }

        function onPointerMove(e) {
            if (!isDragging) return;

            const { xPct, yPct } = getPercentageCoords(e);

            if (type === 'poster') {
                el.style.left = `${xPct}%`;
                el.style.top = `${yPct}%`;
                el.style.transform = `translate(-50%, -50%) scale(${currentScale})`;
                if (posterXInput) posterXInput.value = xPct.toFixed(2);
                if (posterYInput) posterYInput.value = yPct.toFixed(2);
            } else {
                el.style.left = `${xPct}%`;
                el.style.top = `${yPct}%`;
                el.style.transform = `translate(-50%, -50%)`;
                if (titleXInput) titleXInput.value = xPct.toFixed(2);
                if (titleYInput) titleYInput.value = yPct.toFixed(2);
            }
        }

        function onPointerUp(e) {
            if (!isDragging) return;
            isDragging = false;
            el.classList.remove('is-dragging');

            if (e.pointerId !== undefined && el.releasePointerCapture) {
                try { el.releasePointerCapture(e.pointerId); } catch {}
            }
        }

        el.addEventListener('pointerdown', onPointerDown);
        window.addEventListener('pointermove', onPointerMove);
        window.addEventListener('pointerup', onPointerUp);
        window.addEventListener('pointercancel', onPointerUp);
    }

    initDraggable(dragPosterCard, 'poster');
    initDraggable(dragTitleCard, 'title');

    // Slider de Escala do Pôster
    if (scaleSlider && dragPosterCard) {
        scaleSlider.addEventListener('input', (e) => {
            currentScale = parseFloat(e.target.value);
            dragPosterCard.style.transform = `translate(-50%, -50%) scale(${currentScale})`;

            if (posterScaleInput) {
                posterScaleInput.value = currentScale.toFixed(2);
            }
            updateControlBadges();
        });
    }

    // Sliders de Tamanho do Título e da Nota
    if (titleScaleSlider) {
        titleScaleSlider.addEventListener('input', (e) => {
            currentTitleScale = parseFloat(e.target.value);
            updateTitleRatingStyles();
        });
    }

    if (ratingScaleSlider) {
        ratingScaleSlider.addEventListener('input', (e) => {
            currentRatingScale = parseFloat(e.target.value);
            updateTitleRatingStyles();
        });
    }

    // Sliders de Desfoque e Brilho do Fundo
    if (bgBlurSlider) {
        bgBlurSlider.addEventListener('input', (e) => {
            currentBlur = parseFloat(e.target.value);
            updateBgFilter();
        });
    }

    if (bgBrightnessSlider) {
        bgBrightnessSlider.addEventListener('input', (e) => {
            currentBrightness = parseFloat(e.target.value);
            updateBgFilter();
        });
    }

    // Resetar Posições e Controles
    if (resetCanvasBtn) {
        resetCanvasBtn.addEventListener('click', () => {
            currentScale = 1.0;
            currentTitleScale = 1.0;
            currentRatingScale = 1.0;
            currentBlur = 6;
            currentBrightness = 0.68;

            setBgType('backdrop');
            setRatingStyle('number');
            isTitleVisible = true;
            isRatingVisible = true;
            updateElementVisibility();

            if (scaleSlider) scaleSlider.value = 1.0;
            if (titleScaleSlider) titleScaleSlider.value = 1.0;
            if (ratingScaleSlider) ratingScaleSlider.value = 1.0;
            if (bgBlurSlider) bgBlurSlider.value = 6;
            if (bgBrightnessSlider) bgBrightnessSlider.value = 0.68;

            updateTitleRatingStyles();
            updateBgFilter();

            if (dragPosterCard) {
                dragPosterCard.style.left = '50%';
                dragPosterCard.style.top = '40%';
                dragPosterCard.style.transform = 'translate(-50%, -50%) scale(1)';
            }
            if (dragTitleCard) {
                dragTitleCard.style.left = '50%';
                dragTitleCard.style.top = '85%';
                dragTitleCard.style.transform = 'translate(-50%, -50%)';
            }

            if (posterXInput) posterXInput.value = 50;
            if (posterYInput) posterYInput.value = 40;
            if (posterScaleInput) posterScaleInput.value = 1.0;
            if (titleXInput) titleXInput.value = 50;
            if (titleYInput) titleYInput.value = 85;

            showToast({
                type: 'info',
                message: 'Posições e ajustes de fundo resetados!',
                timeout: 2000
            });
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
