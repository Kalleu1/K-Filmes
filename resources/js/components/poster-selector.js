/**
 * PosterSelector - Componente reutilizável para escolha de pôsteres oficiais do TMDB.
 */
export default class PosterSelector {
    constructor() {
        this.tmdbId = null;
        this.currentPoster = null;
        this.onConfirm = null;
        this.posters = [];
        this.currentIndex = 0;

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
                            <div class="poster-selector-poster-frame">
                                <img src="" alt="Poster" class="poster-selector-poster-image">
                                <button type="button" class="poster-selector-arrow prev" aria-label="Anterior">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </button>
                                <button type="button" class="poster-selector-arrow next" aria-label="Próximo">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </button>
                            </div>
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

    async open(tmdbId, currentPoster = null, onConfirm = null) {
        if (!tmdbId) {
            console.error('O TMDB ID é obrigatório para abrir o seletor.');
            return;
        }

        this.tmdbId = tmdbId;
        this.currentPoster = currentPoster;
        this.onConfirm = onConfirm;
        this.posters = [];
        this.currentIndex = 0;

        this.showGlobalLoader();

        try {
            await this.loadPosters();

            if (this.posters.length === 0) {
                this.hideGlobalLoader();
                alert('Nenhum pôster alternativo disponível para este filme.');
                return;
            }

            // Selecionar o pôster atual se informado
            if (this.currentPoster) {
                const idx = this.posters.findIndex(p => p.poster_path === this.currentPoster);
                if (idx !== -1) {
                    this.currentIndex = idx;
                }
            }

            // Pré-carrega o pôster da posição atual antes de exibir o modal
            const initialPosterUrl = this.posters[this.currentIndex].preview_url;
            await this.preloadImage(initialPosterUrl);

            this.updateUI();
            this.hideGlobalLoader();

            // Abrir modal
            this.modal.classList.add('active');
            this.modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        } catch (error) {
            console.error('Erro ao buscar/pré-carregar pôsteres:', error);
            this.hideGlobalLoader();
            alert('Não foi possível carregar os pôsteres.');
        }
    }

    close() {
        this.modal.classList.remove('active');
        this.modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    async loadPosters() {
        const response = await fetch(`/tmdb/movie/${this.tmdbId}/posters`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Falha na requisição de imagens');
        }

        const data = await response.json();
        if (data.success && Array.isArray(data.posters)) {
            this.posters = data.posters;
        } else {
            this.posters = [];
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
        if (this.posters.length === 0) {
            this.modal.querySelector('.poster-selector-carousel-container').classList.add('hidden');
            this.modal.querySelector('.poster-selector-empty').classList.remove('hidden');
            this.counter.classList.add('hidden');
            return;
        }

        this.modal.querySelector('.poster-selector-carousel-container').classList.remove('hidden');
        this.modal.querySelector('.poster-selector-empty').classList.add('hidden');

        const current = this.posters[this.currentIndex];
        this.posterImg.src = current.preview_url;

        this.counter.textContent = `${this.currentIndex + 1} / ${this.posters.length}`;

        // Se houver apenas 1 pôster, oculta as setas e o contador
        if (this.posters.length <= 1) {
            this.prevBtn.classList.add('hidden');
            this.nextBtn.classList.add('hidden');
            this.counter.classList.add('hidden');
        } else {
            this.prevBtn.classList.remove('hidden');
            this.nextBtn.classList.remove('hidden');
            this.counter.classList.remove('hidden');
        }
    }

    next() {
        if (this.posters.length <= 1) return;
        this.currentIndex = (this.currentIndex + 1) % this.posters.length;
        this.updateUI();
    }

    previous() {
        if (this.posters.length <= 1) return;
        this.currentIndex = (this.currentIndex - 1 + this.posters.length) % this.posters.length;
        this.updateUI();
    }

    getSelectedPoster() {
        if (this.posters.length === 0) return null;
        return this.posters[this.currentIndex];
    }

    save() {
        const selected = this.getSelectedPoster();
        if (selected && this.onConfirm) {
            this.onConfirm(selected.poster_path);
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
                    <p>Buscando pôsteres...</p>
                </div>
            `;
            document.body.appendChild(loader);
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
