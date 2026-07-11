/**
 * ArtworkSelector - Componente reutilizável para escolha de pôsteres e backdrops oficiais do TMDB.
 */
export default class ArtworkSelector {
    constructor() {
        this.tmdbId = null;
        this.currentArtwork = null;
        this.onConfirm = null;
        this.artworks = [];
        this.currentIndex = 0;
        this.type = 'poster'; // 'poster' ou 'backdrop'
        this.isTransitioning = false;

        this.init();
    }

    init() {
        let modal = document.getElementById('posterSelectorModal');
        if (!modal) {
            // Criação dinâmica como fallback, caso o Blade não seja incluído
            modal = document.createElement('div');
            modal.id = 'posterSelectorModal';
            modal.className = 'poster-selector-modal';
            modal.setAttribute('aria-hidden', 'true');
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-labelledby', 'posterSelectorTitle');
            modal.innerHTML = `
                <div class="poster-selector-content">
                    <div class="poster-selector-header">
                        <h2 id="posterSelectorTitle" class="poster-selector-title">Escolha um pôster</h2>
                        <button type="button" class="poster-selector-close" aria-label="Fechar">&times;</button>
                    </div>
                    <div class="poster-selector-body">
                        <div class="poster-selector-carousel-container">
                            <button type="button" class="poster-selector-arrow prev" aria-label="Anterior">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <div class="poster-selector-poster-frame">
                                <img src="" alt="Poster" class="poster-selector-poster-image">
                            </div>
                            <button type="button" class="poster-selector-arrow next" aria-label="Próximo">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="poster-selector-empty hidden">
                            Nenhum pôster alternativo disponível para este filme.
                        </div>
                        <div class="poster-selector-counter">0 / 0</div>
                    </div>
                    <div class="poster-selector-footer">
                        <button type="button" class="poster-selector-btn btn-cancel">Cancelar</button>
                        <button type="button" class="poster-selector-btn btn-save">Salvar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        this.modal = modal;
        this.posterImg = modal.querySelector('.poster-selector-poster-image');
        this.counter = modal.querySelector('.poster-selector-counter');
        this.prevBtn = modal.querySelector('.poster-selector-arrow.prev');
        this.nextBtn = modal.querySelector('.poster-selector-arrow.next');
        this.saveBtn = modal.querySelector('.btn-save');
        this.cancelBtn = modal.querySelector('.btn-cancel');
        this.closeBtn = modal.querySelector('.poster-selector-close');

        this.bindEvents();
    }

    bindEvents() {
        this.prevBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.previous();
        });
        
        this.nextBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.next();
        });

        this.saveBtn.addEventListener('click', () => this.save());
        this.cancelBtn.addEventListener('click', () => this.close());
        
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.close());
        }

        // Fechar ao clicar no overlay escuro
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Eventos de teclado (Esc para fechar, Setas para navegar)
        document.addEventListener('keydown', (e) => {
            if (!this.modal.classList.contains('active')) return;

            if (e.key === 'Escape') {
                this.close();
            } else if (e.key === 'ArrowLeft') {
                this.previous();
            } else if (e.key === 'ArrowRight') {
                this.next();
            }
        });
    }

    async open(tmdbIdOrOpts, currentArtwork = null, onConfirm = null, type = 'poster') {
        let tmdbId = tmdbIdOrOpts;
        this.type = type;
        this.currentArtwork = currentArtwork;
        this.onConfirm = onConfirm;

        // Suporte para passagem de parâmetros via Objeto (ex: ArtworkSelector.open({ tmdbId, type: 'backdrop', onConfirm }))
        if (typeof tmdbIdOrOpts === 'object' && tmdbIdOrOpts !== null) {
            tmdbId = tmdbIdOrOpts.tmdbId;
            this.type = tmdbIdOrOpts.type || 'poster';
            this.currentArtwork = tmdbIdOrOpts.current || tmdbIdOrOpts.currentPoster || null;
            this.onConfirm = tmdbIdOrOpts.onConfirm;
        }

        if (!tmdbId) {
            console.error('O TMDB ID é obrigatório para abrir o seletor.');
            return;
        }

        this.tmdbId = tmdbId;
        this.artworks = [];
        this.currentIndex = 0;
        this.isTransitioning = false;

        // Trava o botão de salvar enquanto carrega a primeira imagem
        if (this.saveBtn) {
            this.saveBtn.disabled = true;
        }

        this.showGlobalLoader();

        try {
            await this.loadArtworks();

            if (this.artworks.length === 0) {
                this.hideGlobalLoader();
                const artName = this.type === 'backdrop' ? 'backdrop' : 'pôster';
                alert(`Nenhum ${artName} alternativo disponível para este filme.`);
                return;
            }

            // Selecionar o item atual se informado
            if (this.currentArtwork) {
                const idx = this.artworks.findIndex(item => {
                    const path = item.file_path || item.poster_path;
                    return path === this.currentArtwork;
                });
                if (idx !== -1) {
                    this.currentIndex = idx;
                }
            }

            // Pré-carrega o item da posição atual antes de exibir o modal
            const initialArtworkUrl = this.artworks[this.currentIndex].preview_url;
            try {
                await this.preloadImage(initialArtworkUrl);
            } catch (e) {
                // Ignora falhas de imagens individuais para fluidez do fluxo
            }

            this.updateUI();
            this.hideGlobalLoader();

            // Libera o botão salvar agora que a imagem atual carregou
            if (this.saveBtn) {
                this.saveBtn.disabled = false;
            }

            // Configurar modal conforme o tipo
            this.modal.classList.remove('artwork-type-poster', 'artwork-type-backdrop');
            this.modal.classList.add(`artwork-type-${this.type}`);

            // Atualizar o título do modal
            const titleEl = this.modal.querySelector('.poster-selector-title');
            if (titleEl) {
                titleEl.textContent = this.type === 'backdrop' ? 'Escolha um backdrop' : 'Escolha um pôster';
            }

            // Abrir modal
            this.modal.classList.add('active');
            this.modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            // Iniciar pré-carregamento das imagens adjacentes em background
            this.preloadAdjacent();
        } catch (error) {
            console.error('Erro ao buscar/pré-carregar artes:', error);
            this.hideGlobalLoader();
            alert('Não foi possível carregar as imagens.');
        }
    }

    close() {
        this.modal.classList.remove('active');
        this.modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    async loadArtworks() {
        const endpoint = this.type === 'backdrop' ? 'backdrops' : 'posters';
        const response = await fetch(`/tmdb/movie/${this.tmdbId}/${endpoint}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Falha na requisição de imagens');
        }

        const data = await response.json();
        const list = data[endpoint];
        if (data.success && Array.isArray(list)) {
            this.artworks = list;
        } else {
            this.artworks = [];
        }
    }

    preloadImage(url) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.src = url;
            img.onload = () => resolve(url);
            img.onerror = () => reject(new Error(`Erro ao pré-carregar imagem: ${url}`));
        });
    }

    updateUI() {
        const emptyEl = this.modal.querySelector('.poster-selector-empty');
        const carouselEl = this.modal.querySelector('.poster-selector-carousel-container');

        if (this.artworks.length === 0) {
            carouselEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            if (emptyEl) {
                const artName = this.type === 'backdrop' ? 'backdrop' : 'pôster';
                emptyEl.textContent = `Nenhum ${artName} alternativo disponível para este filme.`;
            }
            this.counter.classList.add('hidden');
            return;
        }

        carouselEl.classList.remove('hidden');
        emptyEl.classList.add('hidden');

        const current = this.artworks[this.currentIndex];
        this.posterImg.src = current.preview_url;

        this.updateCounter();

        // Se houver apenas 1 item, oculta as setas e o contador
        if (this.artworks.length <= 1) {
            this.prevBtn.classList.add('hidden');
            this.nextBtn.classList.add('hidden');
            this.counter.classList.add('hidden');
        } else {
            this.prevBtn.classList.remove('hidden');
            this.nextBtn.classList.remove('hidden');
            this.counter.classList.remove('hidden');
        }
    }

    updateCounter() {
        if (this.counter) {
            this.counter.textContent = `${this.currentIndex + 1} / ${this.artworks.length}`;
        }
    }

    next() {
        if (this.artworks.length <= 1 || this.isTransitioning) return;
        const nextIndex = (this.currentIndex + 1) % this.artworks.length;
        this.navigateTo(nextIndex);
    }

    previous() {
        if (this.artworks.length <= 1 || this.isTransitioning) return;
        const prevIndex = (this.currentIndex - 1 + this.artworks.length) % this.artworks.length;
        this.navigateTo(prevIndex);
    }

    async navigateTo(newIndex) {
        if (this.isTransitioning) return;
        this.isTransitioning = true;
        this.currentIndex = newIndex;

        // Desabilita o botão salvar temporariamente durante a transição
        if (this.saveBtn) {
            this.saveBtn.disabled = true;
        }

        // Efeito de Fade Out da imagem
        this.posterImg.classList.add('fade-out');

        // Espera a animação de fade out (180ms)
        await new Promise(resolve => setTimeout(resolve, 180));

        const current = this.artworks[this.currentIndex];
        this.posterImg.src = current.preview_url;

        // Aguarda carregar a nova imagem antes de exibi-la para evitar flicker branco
        try {
            await this.preloadImage(current.preview_url);
        } catch (e) {
            // Ignora falhas individuais silenciosamente
        }

        // Habilita o botão salvar
        if (this.saveBtn) {
            this.saveBtn.disabled = false;
        }

        // Efeito de Fade In
        this.posterImg.classList.remove('fade-out');
        this.updateCounter();

        // Espera a animação de fade in concluir (180ms)
        await new Promise(resolve => setTimeout(resolve, 180));
        this.isTransitioning = false;

        // Inicia o pré-carregamento do novo range adjacente em background
        this.preloadAdjacent();
    }

    preloadAdjacent() {
        if (this.artworks.length === 0) return;

        const indicesToPreload = [];
        const len = this.artworks.length;

        // Janela de pré-carregamento: +1, +2, -1, -2 (circular)
        const offsets = [1, 2, -1, -2];
        offsets.forEach(offset => {
            const index = (this.currentIndex + offset + len) % len;
            if (!indicesToPreload.includes(index) && index !== this.currentIndex) {
                indicesToPreload.push(index);
            }
        });

        // Executa o pré-carregamento no browser em background
        indicesToPreload.forEach(idx => {
            const url = this.artworks[idx].preview_url;
            if (url) {
                const img = new Image();
                img.src = url;
            }
        });
    }

    getSelectedArtwork() {
        if (this.artworks.length === 0) return null;
        return this.artworks[this.currentIndex];
    }

    save() {
        const selected = this.getSelectedArtwork();
        if (selected && this.onConfirm) {
            const path = selected.file_path || selected.poster_path;
            this.onConfirm(path);
        }
        this.close();
    }

    showGlobalLoader() {
        let loader = document.getElementById('posterSelectorGlobalLoader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'posterSelectorGlobalLoader';
            loader.className = 'poster-selector-global-loader';
            loader.innerHTML = `
                <div class="poster-selector-global-loader-content">
                    <div class="spinner"></div>
                    <p>Buscando imagens...</p>
                </div>
            `;
            document.body.appendChild(loader);
        }
        
        const textEl = loader.querySelector('p');
        if (textEl) {
            textEl.textContent = 'Buscando imagens...';
        }
        
        loader.classList.add('active');
    }

    hideGlobalLoader() {
        const loader = document.getElementById('posterSelectorGlobalLoader');
        if (loader) {
            loader.classList.remove('active');
        }
    }
}
