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

    // 2. Monitoramento de carregamento de imagens para fade-in suave (removendo shimmer)
    const moviePosters = document.querySelectorAll('.discover-page .filme-poster');
    moviePosters.forEach(img => {
        // Se a imagem já estiver no cache e carregada
        if (img.complete) {
            img.classList.add('loaded');
        } else {
            // Caso contrário, adiciona o escutador de load
            img.addEventListener('load', () => {
                img.classList.add('loaded');
            });
            // Em caso de erro, também exibe para não ficar eternamente em shimmer
            img.addEventListener('error', () => {
                img.classList.add('loaded');
            });
        }
    });
});
