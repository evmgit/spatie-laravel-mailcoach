import { $$, listen } from '../util';

function toggleConditionals(controller) {
    const value = getValue(controller)
        .replace(/[\\"']/g, '\\$&')
        .replace(/\u0000/g, '\\0'); // addslashes

    $$(`[data-conditional-${controller.dataset.conditional}]`).forEach(element => {
        const isVisible = element.matches(`[data-conditional-${controller.dataset.conditional}="${value}"]`);

        element.classList.toggle('hidden', !isVisible);
    });

    $$(`[data-conditional-unless-${controller.dataset.conditional}]`).forEach(element => {
        const isVisible = !element.matches(`[data-conditional-unless-${controller.dataset.conditional}="${value}"]`);

        element.classList.toggle('hidden', !isVisible);
    });
}

function getValue(controller) {
    if (controller.matches('[type="checkbox"]')) {
        return String(controller.checked);
    }

    if (controller.matches('[type="radio"]')) {
        const checkedInput = $$(`[name="${controller.name}"]`).find(radio => radio.checked);

        return checkedInput ? checkedInput.value : 'null';
    }

    return controller.value;
}

window.addEventListener('turbolinks:load', () => {
    $$('[data-conditional]').forEach(toggleConditionals);
});

listen('change', '[data-conditional]', ({ target }) => {
    toggleConditionals(target);
});
