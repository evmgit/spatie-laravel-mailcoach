import { listen, $$, leave } from '../util';

listen('click', '[data-dismiss]', ({ target }) => {
    target.remove();
});

document.addEventListener('turbolinks:load', () => {
    $$('[data-dismiss]').forEach(node => {
        const timeoutMs = Math.min(Math.max(node.textContent.trim().length * 60, 5000), 15000);

        setTimeout(() => {
            leave(node, 'fade').then(() => {
                node.remove();
            });
        }, timeoutMs);
    });
});
