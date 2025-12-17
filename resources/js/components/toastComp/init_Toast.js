import Toast from './Toast.js';

export default function initToast() {
    if (!window.toasts || !Array.isArray(window.toasts)) return;

    window.toasts.forEach(toast => {
        Toast.show(toast);
    });
}
