import { listen, $ } from '../util';

function updateHtmlPreview() {
    const source = $('[data-html-preview-source]');
    const target = $('[data-html-preview-target]');

    if (!source || !target) {
        return;
    }

    target.src = `data:text/html;base64,${btoa(unescape(encodeURIComponent(source.value)))}`;
}

listen('input', '[data-html-preview-source]', updateHtmlPreview);
