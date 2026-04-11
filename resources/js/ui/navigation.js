const UIState = {
  sidebarOpen: false,
};

let sidebarInitialized = false;
let bottomNavInitialized = false;

const MOBILE_BREAKPOINT = 768;
const TOP_VISIBILITY_THRESHOLD = 50;
const SCROLL_DELTA_THRESHOLD = 2;
const NAV_TOGGLE_DISTANCE = 24;

function initBottomNavAutoHideOnScroll() {
  if (bottomNavInitialized) return;

  const bottomNav = document.querySelector('.bottom-nav');
  if (!bottomNav) return;

  bottomNavInitialized = true;

  let lastScrollY = window.scrollY || 0;
  let lastToggleScrollY = lastScrollY;
  let ticking = false;
  let isHidden = false;

  const setHiddenState = (hidden) => {
    if (hidden === isHidden) return;

    isHidden = hidden;
    bottomNav.classList.toggle('is-hidden', hidden);
  };

  const updateBottomNavVisibility = () => {
    const currentScrollY = window.scrollY || 0;
    const delta = currentScrollY - lastScrollY;

    if (window.innerWidth > MOBILE_BREAKPOINT || currentScrollY < TOP_VISIBILITY_THRESHOLD) {
      setHiddenState(false);
      lastScrollY = currentScrollY;
      lastToggleScrollY = currentScrollY;
      ticking = false;
      return;
    }

    if (Math.abs(delta) < SCROLL_DELTA_THRESHOLD) {
      lastScrollY = currentScrollY;
      ticking = false;
      return;
    }

    if (!isHidden && delta > 0 && currentScrollY - lastToggleScrollY >= NAV_TOGGLE_DISTANCE) {
      setHiddenState(true);
      lastToggleScrollY = currentScrollY;
    } else if (isHidden && delta < 0 && lastToggleScrollY - currentScrollY >= NAV_TOGGLE_DISTANCE) {
      setHiddenState(false);
      lastToggleScrollY = currentScrollY;
    }

    lastScrollY = currentScrollY;
    ticking = false;
  };

  const onScroll = () => {
    if (!ticking) {
      ticking = true;
      window.requestAnimationFrame(updateBottomNavVisibility);
    }
  };

  const onResize = () => {
    if (window.innerWidth > MOBILE_BREAKPOINT) {
      setHiddenState(false);
    }
  };

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onResize, { passive: true });
}

export function initMobileNavigation() {
  initBottomNavAutoHideOnScroll();

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
