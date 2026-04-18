const UIState = {
  sidebarOpen: false,
};

let sidebarInitialized = false;

export function initMobileNavigation() {
  if (sidebarInitialized) return;

  const sidebar = document.getElementById('dashboard-sidebar');
  const toggle  = document.querySelector('.sidebar-toggle');
  const overlay = document.querySelector('[data-sidebar-overlay]');

  if (!sidebar || !toggle || !overlay) return;
  sidebarInitialized = true;

  function openSidebar() {
    if (UIState.sidebarOpen) return;

    UIState.sidebarOpen = true;

    sidebar.classList.add('is-open');
    overlay.classList.add('is-active');
    document.body.style.overflow = 'hidden';

    history.pushState({ sidebar: true }, '');
  }

  function closeSidebar(fromPopState = false) {
    if (!UIState.sidebarOpen) return;

    UIState.sidebarOpen = false;

    sidebar.classList.remove('is-open');
    overlay.classList.remove('is-active');
    document.body.style.overflow = '';

    // se não veio do botão voltar, volta o histórico
    if (!fromPopState) {
      history.back();
    }
  }

  toggle.addEventListener('click', (e) => {
    e.preventDefault();
    UIState.sidebarOpen ? closeSidebar() : openSidebar();
  });

  overlay.addEventListener('click', closeSidebar);

  window.addEventListener('popstate', () => {
    if (UIState.sidebarOpen) {
      closeSidebar(true);
    }
  });

  // swipe
  let startX = 0;
  sidebar.addEventListener('touchstart', e => {
    startX = e.touches[0].clientX;
  });

  sidebar.addEventListener('touchend', e => {
    const endX = e.changedTouches[0].clientX;
    if (startX - endX > 60) {
      closeSidebar();
    }
  });
}
