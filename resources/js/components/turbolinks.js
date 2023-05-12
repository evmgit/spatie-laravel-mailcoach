import Turbolinks from 'turbolinks';
import { debounce, listen } from '../util';

Turbolinks.start();

// Preserve scroll

let preservedScrollPosition = null;

function preserveScrollPosition() {
    preservedScrollPosition = window.scrollY;
}

function restoreScrollPosition() {
    if (preservedScrollPosition) {
        window.scrollTo(0, preservedScrollPosition);

        preservedScrollPosition = null;
    }
}

listen('click', '[data-turbolinks-preserve-scroll]', preserveScrollPosition);
document.addEventListener('turbolinks:render', restoreScrollPosition);

// Preserve focus

let preservedFocus = null;

function preserveFocus() {
    if (document.activeElement) {
        preservedFocus = document.activeElement.matches('[data-turbolinks-permanent]') ? document.activeElement : null;
    }
}

function restoreFocus() {
    if (preservedFocus) {
        preservedFocus.focus();

        preservedFocus = null;
    }
}

document.addEventListener('turbolinks:before-visit', preserveFocus);
document.addEventListener('turbolinks:render', restoreFocus);

// Search bar

listen(
    'input',
    '[data-turbolinks-search]',
    debounce(({ target }) => {
        const url = target.value
            ? target.dataset.turbolinksSearchUrl.replace('%search%', target.value)
            : target.dataset.turbolinksSearchClearUrl;

        preserveScrollPosition();

        Turbolinks.visit(url, { action: 'replace' });
    }, 400)
);
