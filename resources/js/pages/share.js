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

    let selectedTheme = 'noir';
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
            document.querySelectorAll(".theme-btn")
                .forEach(b => b.classList.remove("selected"));

            btn.classList.add("selected");

            selectedTheme = btn.dataset.theme;
            applyTheme(selectedTheme);
            themeInput.value = selectedTheme;
        });
    });

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
