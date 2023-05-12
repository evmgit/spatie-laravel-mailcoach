import Turbolinks from 'turbolinks';
import { listen, $ } from '../util';
import { showModal } from './modal';

listen('input', '[data-dirty-check]', ({ target }) => {
    target.dirty = true;
});

listen('click', '[data-dirty-warn]', () => {
    if (!$('[data-dirty-check]') || !$('[data-dirty-check]').dirty) {
        return;
    }

    function handleBeforeVisit(event) {
        event.preventDefault();

        showModal('dirty-warning', {
            onConfirm() {
                Turbolinks.visit(event.data.url);
            },
        });
    }

    document.addEventListener('turbolinks:before-visit', handleBeforeVisit, { once: true });
});
