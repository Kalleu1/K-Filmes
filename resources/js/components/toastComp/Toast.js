export default class Toast {
    static show({ type = 'info', message = '', timeout = 4000 }) {
        const container =
            document.querySelector('.toast-container') ??
            Toast.createContainer();

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');

        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-icon">${Toast.getIcon(type)}</span>
                <span class="toast-message">${message}</span>
            </div>
            <button class="toast-close" type="button" aria-label="Fechar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        const remove = () => Toast.remove(toast);

        toast.querySelector('.toast-close')
            .addEventListener('click', remove);

        setTimeout(remove, timeout);
    }

    static remove(toast) {
        if (!toast) return;

        toast.classList.remove('show');

        const fallback = setTimeout(() => {
            toast.remove();
        }, 350); // levemente maior que a transition

        toast.addEventListener(
            'transitionend',
            () => {
                clearTimeout(fallback);
                toast.remove();
            },
            { once: true }
        );
    }

    static createContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
        return container;
    }

    static getIcon(type) {
        switch (type) {
            case 'success':
                return '<i class="fa-solid fa-circle-check"></i>';
            case 'error':
                return '<i class="fa-solid fa-circle-xmark"></i>';
            case 'warning':
                return '<i class="fa-solid fa-triangle-exclamation"></i>';
            default:
                return '<i class="fa-solid fa-circle-info"></i>';
        }
    }
}
