export function showLoading(id = 'loading-overlay') {
    const el = document.getElementById(id);
    if (!el) return;

    el.classList.remove('hidden');
    el.setAttribute('aria-hidden', 'false');
}

export function hideLoading(id = 'loading-overlay') {
    const el = document.getElementById(id);
    if (!el) return;

    el.classList.add('hidden');
    el.setAttribute('aria-hidden', 'true');
}
