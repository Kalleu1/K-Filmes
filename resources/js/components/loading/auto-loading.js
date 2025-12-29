import { showLoading } from './loading-overlay';

document.addEventListener('DOMContentLoaded', () => {
    const loadingId = 'page-loading';

    // Form submit
    document.querySelectorAll('form[data-loading]').forEach(form => {
        form.addEventListener('submit', () => {
            showLoading(loadingId);
        });
    });

    // Link click
    document.querySelectorAll('a[data-loading]').forEach(link => {
        link.addEventListener('click', () => {
            showLoading(loadingId);
        });
    });
});
