import { $ } from '../util';

document.addEventListener('turbolinks:load', () => {
    const container = $('[data-segments]');

    if (!container) {
        return;
    }

    const emailLists = JSON.parse(container.dataset.segments);
    const emailListSelect = $('[data-segments-email-list]', container);
    const entireListCheck = $('[name="segment"][value="entire_list"]', container);
    const segmentCheck = $('[name="segment"][value="segment"]', container);
    const segmentsCreate = $('[data-segments-create]', container);
    const segmentsCreateLink = $('a', segmentsCreate);
    const segmentsChoose = $('[data-segments-choose]', container);
    const segmentsChooseSelect = $('select', segmentsChoose);

    function renderSegments({ reset = false, selectedSegmentId = null } = {}) {
        if (reset) {
            entireListCheck.checked = true;
        }

        const selectedEmailList = emailLists.find(emailList => {
            return emailList.id == emailListSelect.value;
        });

        const hasSegments = selectedEmailList.segments.length > 0;

        segmentCheck.disabled = !hasSegments || segmentCheck.readOnly;

        segmentsCreate.classList.toggle('hidden', hasSegments);

        segmentsCreateLink.href = selectedEmailList.createSegmentUrl;

        segmentsChoose.classList.toggle('hidden', !hasSegments);

        segmentsChooseSelect.parentNode.classList.toggle('hidden', entireListCheck.checked);

        segmentsChooseSelect.innerHTML = selectedEmailList.segments
            .map((segment, index) => {
                const selected = selectedSegmentId ? segment.id == selectedSegmentId : index === 0;

                return `
                    <option value="${segment.id}" ${selected ? 'selected' : ''}>
                        ${segment.name}
                    </option>
                `;
            })
            .join('');
    }

    renderSegments({ selectedSegmentId: container.dataset.segmentsSelected });

    emailListSelect.addEventListener('input', () => {
        renderSegments({ reset: true });
    });

    entireListCheck.addEventListener('input', () => renderSegments());
    segmentCheck.addEventListener('input', () => renderSegments());
});
