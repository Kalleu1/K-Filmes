/**
 * Módulo Descobrir
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log('Módulo Descobrir inicializado com sucesso!');
    
    // Configurar carrosséis
    const carousels = document.querySelectorAll('.collection-carousel-wrapper');
    carousels.forEach(wrapper => {
        const carousel = wrapper.querySelector('.collection-carousel');
        const prevBtn = wrapper.querySelector('.carousel-control.prev');
        const nextBtn = wrapper.querySelector('.carousel-control.next');
        
        if (carousel && prevBtn && nextBtn) {
            // Distância aproximada para deslizar ~3 cards
            const scrollAmount = 480; 
            
            prevBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            
            nextBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
            
            // Gerenciar visibilidade das setas conforme posição do scroll
            const toggleButtons = () => {
                const scrollLeft = carousel.scrollLeft;
                const maxScrollLeft = carousel.scrollWidth - carousel.clientWidth;
                
                // Exibe se houver scroll para a esquerda (> 5px de tolerância)
                prevBtn.style.opacity = scrollLeft > 5 ? '1' : '0';
                prevBtn.style.pointerEvents = scrollLeft > 5 ? 'auto' : 'none';
                
                // Exibe se houver scroll para a direita
                nextBtn.style.opacity = scrollLeft < maxScrollLeft - 5 ? '1' : '0';
                nextBtn.style.pointerEvents = scrollLeft < maxScrollLeft - 5 ? 'auto' : 'none';
            };
            
            carousel.addEventListener('scroll', toggleButtons);
            window.addEventListener('resize', toggleButtons);
            
            // Executa no carregamento após breve atraso para renderização correta
            setTimeout(toggleButtons, 150);
        }
    });
});
