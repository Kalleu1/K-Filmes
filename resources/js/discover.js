/**
 * Módulo Descobrir
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log('Módulo Descobrir inicializado com sucesso!');
    
    // 1. Configurar carrosséis e setas
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

    // 2. Monitoramento de carregamento de imagens para fade-in suave
    const moviePosters = document.querySelectorAll('.discover-page .filme-poster');
    moviePosters.forEach(initImageFadeIn);

    // 3. Paginação progressiva na página de Coleção (Infinite Scroll)
    const gridContainer = document.querySelector('.discover-movies-grid');
    const collectionPage = document.querySelector('.discover-collection-page');
    
    if (gridContainer && collectionPage) {
        const collectionId = collectionPage.dataset.collectionId;
        let currentPage = parseInt(collectionPage.dataset.currentPage) || 1;
        const totalPages = parseInt(collectionPage.dataset.totalPages) || 1;
        
        let isLoading = false;
        
        // Criar indicador de carregamento (Spinner)
        const loader = document.createElement('div');
        loader.className = 'discover-loader';
        loader.innerHTML = '<div class="spinner"></div>';
        gridContainer.parentNode.appendChild(loader);
        
        // Se já começou na última página, oculta o loader
        if (currentPage >= totalPages) {
            loader.style.display = 'none';
        } else {
            // Configurar IntersectionObserver
            const observerOptions = {
                root: null, // viewport
                rootMargin: '200px', // Carregar preventivamente um pouco antes de atingir o fundo
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
            
            function loadNextPage() {
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
                        // Anexar novos itens
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data.html;
                        
                        while (tempDiv.firstElementChild) {
                            const child = tempDiv.firstElementChild;
                            gridContainer.appendChild(child);
                            
                            // Adicionar efeito de fade-in para a nova imagem renderizada
                            const poster = child.querySelector('.filme-poster');
                            if (poster) {
                                initImageFadeIn(poster);
                            }
                        }
                        
                        currentPage = data.page;
                        collectionPage.dataset.currentPage = currentPage;
                        
                        // Encerrar observação caso chegue à última página
                        if (currentPage >= totalPages) {
                            observer.disconnect();
                            loader.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro na paginação progressiva:', error);
                })
                .finally(() => {
                    isLoading = false;
                    loader.classList.remove('loading');
                });
            }
        }
    }
});
