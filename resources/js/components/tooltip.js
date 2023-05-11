import tippy from 'tippy.js';

document.addEventListener('alpine:init', () => {
    // Magic: $tooltip
    Alpine.magic('tooltip', el => message => {
        let instance = tippy(el, {
            content: message,
            trigger: 'manual',
            allowHTML: true,
            interactive: true,
        });

        instance.show();

        setTimeout(() => {
            instance.hide();

            setTimeout(() => instance.destroy(), 150);
        }, 2000);
    });

    // Directive: x-tooltip
    Alpine.directive('tooltip', (el, { expression }) => {
        tippy(el, {
            content: expression,
            allowHTML: true,
            interactive: true,
        });
    });
});
