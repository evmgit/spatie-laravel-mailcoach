import { listen, $ } from '../util';

listen('click', '[data-navigation-trigger]', ({ event }) => {
    
    if(window.innerWidth > 1024){
        return true;
    }

    event.preventDefault();
    const nav = $('[data-navigation]');
    nav.classList.toggle('navigation-shown');
});
