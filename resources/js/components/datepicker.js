import flatpickr from 'flatpickr';

function initDatepickers() {
    document.querySelectorAll('[data-datepicker]').forEach(node => {
        flatpickr(node, { dateFormat: 'Y-m-d', minDate: 'today', position: 'above' });
    });
}

document.addEventListener('turbolinks:load', initDatepickers);
