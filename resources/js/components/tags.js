import Choices from 'choices.js';
import { $, $$ } from '../util';

export function createTagsInput(node, { tags, selectedTags, canCreateNewTags }) {
    function createChoicesFromTags() {
        return tags.map(tag => {
            return {
                value: tag,
                label: tag,
                selected: selectedTags.includes(tag),
                customProperties: {
                    isCurrentSearch: false,
                    exists: true,
                },
            };
        });
    }

    let currentChoices = createChoicesFromTags();

    const tagsInput = new Choices(node, {
        removeItemButton: true,
        noResultsText: canCreateNewTags ? __('Type to add tags') : __('No tags found'),
        noChoicesText: canCreateNewTags ? __('Type to add tags') : __('No tags to choose from'),
        itemSelectText: canCreateNewTags ? __('Press to add') : __('Press to select'),
        shouldSortItems: false,
        choices: currentChoices,
    });

    // Choices.js doesn't have an option that combines selecting multiple tags
    // and creating new tags at once. Below is a hack to update the choices
    // when a a user searches for a tag that doesn't exist yet.

    function updateChoices(search) {
        const hasCurrentSearchCoice = Boolean(
            tagsInput._currentState.choices.find(choice => choice.customProperties.isCurrentSearch)
        );

        if (!hasCurrentSearchCoice && !search) {
            return;
        }

        if (!hasCurrentSearchCoice) {
            addCurrentSearchChoice(search);
            return;
        }

        if (!search) {
            removeCurrentSearchChoice();
            return;
        }

        updateCurrentSearchChoice(search);
    }

    function addCurrentSearchChoice(search) {
        if (!hasExistingTag(search)) {
            tagsInput.setChoices([
                {
                    value: search,
                    label: search,
                    customProperties: {
                        isCurrentSearch: true,
                        exists: false,
                    },
                },
            ]);
        }
    }

    function updateCurrentSearchChoice(search) {
        if (hasExistingTag(search)) {
            removeCurrentSearchChoice();
        } else {
            tagsInput._currentState.choices.forEach(choice => {
                if (choice.customProperties.isCurrentSearch) {
                    choice.value = search;
                    choice.label = search;
                }
            });
        }
    }

    function removeCurrentSearchChoice() {
        const currentSearchChoiceIndex = tagsInput._currentState.choices.findIndex(
            choice => choice.customProperties.isCurrentSearch
        );

        if (currentSearchChoiceIndex !== -1) {
            tagsInput._currentState.choices.splice(currentSearchChoiceIndex, 1);
        }
    }

    function hasExistingTag(value) {
        return (
            tagsInput._currentState.choices.findIndex(choice => {
                if (choice.customProperties.isCurrentSearch) {
                    return false;
                }

                return choice.value.toLowerCase() === value.toLowerCase();
            }) !== -1
        );
    }

    node.addEventListener('addItem', () => {
        tagsInput._currentState.choices.forEach(choice => {
            delete choice.customProperties.isCurrentSearch;
        });
    });

    if (canCreateNewTags) {
        $('input.choices__input', node.parentNode).addEventListener('input', event => {
            updateChoices(event.target.value);
        });
    }

    return tagsInput;
}

document.addEventListener('turbolinks:load', () => {
    $$('[data-tags]').forEach(node => {
        window.tagsInput = createTagsInput(node, {
            tags: JSON.parse(node.dataset.tags),
            selectedTags: JSON.parse(node.dataset.tagsSelected),
            canCreateNewTags: 'tagsAllowCreate' in node.dataset,
        });
    });
});
