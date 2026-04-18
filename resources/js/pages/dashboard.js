export default function initDashboard() {
    let sidebarOpen = false;

    const page = document.querySelector('.dashboard');
    if (!page) return;

    // HERO SLIDER
    const slides = page.querySelectorAll('.hero-slide');
    let currentIndex = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => slide.classList.toggle('active', i === index));
    }

    function nextSlide() {
        if (!slides.length) return;
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
    }

    if (slides.length) {
        // iniciar troca automática a cada 30s
        setInterval(nextSlide, 30000);
    }

    // SORTEAR FILME BUTTON
    const navSortear = document.getElementById('navSortear');
    if (navSortear) {
        navSortear.addEventListener('click', () => {
            const filmeCard = document.querySelector('.filme-card');
            if (filmeCard) {
                filmeCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
}
