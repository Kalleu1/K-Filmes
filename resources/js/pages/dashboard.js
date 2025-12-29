export default function initDashboard() {
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

    // SIDEBAR
    // O toggle pode estar fora do .dashboard (ex: no header), por isso procurar no document
    const sidebar = document.getElementById('dashboard-sidebar') || document.querySelector('#dashboard-sidebar');
    const toggle  = document.querySelector('.sidebar-toggle') || page.querySelector('.sidebar-toggle');
    const overlay = page.querySelector('[data-sidebar-overlay]') || document.querySelector('[data-sidebar-overlay]');

    if (!sidebar || !toggle || !overlay) return;

    function openSidebar() {
        sidebar.classList.add('is-open');
        overlay.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-active');
        document.body.style.overflow = '';
    }

    toggle.addEventListener('click', (e) => {
        e.preventDefault();
        // toggle comportamento (abrir/fechar)
        if (sidebar.classList.contains('is-open')) closeSidebar();
        else openSidebar();
    });

    overlay.addEventListener('click', closeSidebar);
}
