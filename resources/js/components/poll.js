import morphdom from 'morphdom';
import { $$ } from '../util';

let interval;

document.addEventListener('turbolinks:load', () => {
    clearInterval(interval);

    interval = setInterval(() => {
        const needsPolling = $$('[data-poll]');

        if (needsPolling.length) {
            needsPolling.forEach(node => {
                if (!node.id) {
                    throw new Error("You can't poll an element that doesn't have an ID");
                }
            });

            poll(needsPolling);
        }
    }, 5000);
});

function poll(nodes) {
    fetch('')
        .then(response => response.text())
        .then(html => {
            const newDocument = new DOMParser().parseFromString(html, 'text/html');

            nodes.forEach(node => {
                const newNode = newDocument.getElementById(node.id);

                if (!document.body.contains(node) || !newNode) {
                    return;
                }

                morphdom(node, newNode);
            });
        });
}
