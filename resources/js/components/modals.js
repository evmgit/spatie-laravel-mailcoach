import Alpine from 'alpinejs';

document.addEventListener('alpine:init', () => {
    Alpine.store('modals', {
        openModals: [],
        onConfirm: null,
        init() {
            if (window.location.hash) {
                this.openModals.push(window.location.hash.replace('#', ''));
            }
        },
        isOpen(id) {
            return this.openModals.includes(id);
        },
        open(id) {
            this.openModals.push(id);
            window.location.hash = id;
            Alpine.nextTick(() => {
                const input = document.querySelector(`#modal-${id} input:not([type=hidden])`);
                if (input) {
                    input.focus();
                    return;
                }

                const button = document.querySelector(`#modal-${id} [data-confirm]`);
                if (button) button.focus();
            });
        },
        close(id) {
            this.openModals = this.openModals.filter(modal => modal !== id);
            history.pushState('', document.title, window.location.pathname + window.location.search);
        },
    });
});
