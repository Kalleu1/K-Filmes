/**
 * Módulo Descobrir
 */

// 1. Desativar restauração de scroll automática do navegador
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}

// Gerenciador centralizado de estado da página
const DiscoverState = {
    getSelectedMovie() {
        return sessionStorage.getItem('discover:selectedMovie');
    },
    getSavedCollection() {
        return sessionStorage.getItem('discover:collection');
    },
    save(movieId, collectionId) {
        sessionStorage.setItem('discover:selectedMovie', movieId || '');
        sessionStorage.setItem('discover:collection', collectionId || '');
    },
    clear() {
        sessionStorage.removeItem('discover:selectedMovie');
        sessionStorage.removeItem('discover:collection');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('Módulo Descobrir inicializado com sucesso!');

    // Helper para inicializar detector de carregamento em cartazes individuais
    const initImageFadeIn = (img) => {
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            img.addEventListener('load', () => {
                img.classList.add('loaded');
            });
            img.addEventListener('error', () => {
                img.classList.add('loaded');
            });
        }
    };

    // 2. Configurar carrosséis e setas
    const carousels = document.querySelectorAll('.collection-carousel-wrapper');
    carousels.forEach(wrapper => {
        const carousel = wrapper.querySelector('.collection-carousel');
        const prevBtn = wrapper.querySelector('.carousel-control.prev');
        const nextBtn = wrapper.querySelector('.carousel-control.next');

        if (carousel && prevBtn && nextBtn) {
            const scrollAmount = 480;

            prevBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });

            nextBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            const toggleButtons = () => {
                const scrollLeft = carousel.scrollLeft;
                const maxScrollLeft = carousel.scrollWidth - carousel.clientWidth;

                prevBtn.style.opacity = scrollLeft > 5 ? '1' : '0';
                prevBtn.style.pointerEvents = scrollLeft > 5 ? 'auto' : 'none';

                nextBtn.style.opacity = scrollLeft < maxScrollLeft - 5 ? '1' : '0';
                nextBtn.style.pointerEvents = scrollLeft < maxScrollLeft - 5 ? 'auto' : 'none';
            };

            carousel.addEventListener('scroll', toggleButtons);
            window.addEventListener('resize', toggleButtons);

            setTimeout(toggleButtons, 150);
        }
    });

    // 3. Monitoramento de carregamento de imagens para fade-in suave
    const moviePosters = document.querySelectorAll('.discover-page .filme-poster');
    moviePosters.forEach(initImageFadeIn);

    // 4. Salvar ID do filme e coleção ao clicar em um card de filme
    document.addEventListener('click', (event) => {
        const card = event.target.closest('[data-movie-id]');
        if (card) {
            const movieId = card.getAttribute('data-movie-id');
            if (movieId) {
                const collectionPage = document.querySelector('.discover-collection-page');
                const collectionId = collectionPage ? collectionPage.dataset.collectionId : '';
                DiscoverState.save(movieId, collectionId);
            }
        }
    });

    // 5. Fluxo de Restauração baseada no Filme Selecionado e Paginação Infinita
    const gridContainer = document.querySelector('.discover-movies-grid');
    const collectionPage = document.querySelector('.discover-collection-page');

    if (gridContainer && collectionPage) {
        const collectionId = collectionPage.dataset.collectionId;
        const totalPages = parseInt(collectionPage.dataset.totalPages, 10) || 1;
        
        let currentPage = 1;
        let isLoading = false;

        // Verificar se estamos retornando para esta coleção específica
        const selectedMovieId = DiscoverState.getSelectedMovie();
        const savedCollection = DiscoverState.getSavedCollection();
        const isReturning = selectedMovieId && savedCollection === collectionId;

        // Criar indicador visual de carregamento (Spinner)
        const loader = document.createElement('div');
        loader.className = 'discover-loader';
        loader.innerHTML = '<div class="spinner"></div>';
        gridContainer.parentNode.appendChild(loader);

        // Função para carregar a página seguinte
        const loadNextPage = (onComplete) => {
            if (currentPage >= totalPages) {
                if (onComplete) onComplete();
                return;
            }

            isLoading = true;
            loader.classList.add('loading');

            const nextPage = currentPage + 1;
            const url = `/descobrir/colecao/${collectionId}?page=${nextPage}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Falha no carregamento dos dados.');
                    return response.json();
                })
                .then(data => {
                    if (data.html && data.html.trim() !== '') {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data.html;

                        while (tempDiv.firstElementChild) {
                            const child = tempDiv.firstElementChild;
                            gridContainer.appendChild(child);

                            const poster = child.querySelector('.filme-poster');
                            if (poster) {
                                initImageFadeIn(poster);
                            }
                        }

                        currentPage = data.page;
                        collectionPage.dataset.currentPage = currentPage;

                        if (currentPage >= totalPages) {
                            loader.style.display = 'none';
                        }
                    }
                    if (onComplete) onComplete();
                })
                .catch(error => {
                    console.error('Erro na paginação progressiva:', error);
                    if (onComplete) onComplete();
                })
                .finally(() => {
                    isLoading = false;
                    loader.classList.remove('loading');
                });
        };

        // Inicializar o IntersectionObserver para a paginação infinita
        const initObserver = () => {
            if (currentPage >= totalPages) {
                loader.style.display = 'none';
                return;
            }

            const observerOptions = {
                root: null,
                rootMargin: '250px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !isLoading && currentPage < totalPages) {
                        loadNextPage();
                    }
                });
            }, observerOptions);

            observer.observe(loader);
        };

        // Rolar até o filme selecionado
        const scrollToSelectedMovie = () => {
            const element = document.querySelector(`[data-movie-id="${selectedMovieId}"]`);
            if (element) {
                // Centralizar o card do filme na tela instantaneamente
                element.scrollIntoView({
                    block: 'center',
                    behavior: 'instant'
                });
                
                // Limpar dados de retorno após a restauração
                DiscoverState.clear();
                return true;
            }
            return false;
        };

        // Carregar páginas progressivamente até encontrar o filme no DOM
        const restorePages = () => {
            if (scrollToSelectedMovie()) {
                initObserver();
                return;
            }

            if (currentPage < totalPages) {
                loadNextPage(() => {
                    restorePages();
                });
            } else {
                // Fim das páginas e não encontrou o filme
                DiscoverState.clear();
                initObserver();
            }
        };

        // Decidir fluxo inicial
        if (isReturning) {
            restorePages();
        } else {
            DiscoverState.clear();
            initObserver();
        }
    } else {
        // Estamos na página principal de descoberta
        const selectedMovieId = DiscoverState.getSelectedMovie();
        const savedCollection = DiscoverState.getSavedCollection();
        
        // Só restaura se não havia coleção ativa cadastrada (significa que veio da index)
        if (selectedMovieId && !savedCollection) {
            const element = document.querySelector(`[data-movie-id="${selectedMovieId}"]`);
            if (element) {
                element.scrollIntoView({
                    block: 'center',
                    behavior: 'instant'
                });
            }
        }
        DiscoverState.clear();
    }
});
