(function () {
    'use strict';

    const wrapper = document.getElementById('verificationVisaTypeEntry');
    if (!wrapper) {
        return;
    }

    const searchInput = wrapper.querySelector('.visa-type-search-input');
    const searchContainer = wrapper.querySelector('.address-search-container');
    const visaTypesRoute = wrapper.dataset.visaTypesRoute || '';
    let visaTypes = [];
    let loaded = false;
    let loadPromise = null;

    function clearSuggestions() {
        wrapper.querySelectorAll('.autocomplete-suggestions, .autocomplete-error, .autocomplete-info').forEach(function (el) {
            el.remove();
        });
    }

    function showMessage(className, text) {
        wrapper.querySelectorAll('.' + className).forEach(function (el) {
            el.remove();
        });
        const message = document.createElement('div');
        message.className = className;
        message.textContent = text;
        if (searchContainer) {
            searchContainer.appendChild(message);
        }
        window.setTimeout(function () {
            message.remove();
        }, 5000);
    }

    function visaLabel(visa) {
        if (visa.label) {
            return visa.label;
        }
        const title = (visa.title || '').trim();
        const nick = (visa.nick_name || '').trim();
        if (!title) {
            return nick;
        }
        return nick ? title + ' (' + nick + ')' : title;
    }

    function matchesQuery(visa, query) {
        if (!query) {
            return true;
        }
        const haystack = [visaLabel(visa), visa.title || '', visa.nick_name || ''].join(' ').toLowerCase();
        return haystack.indexOf(query.toLowerCase()) !== -1;
    }

    function selectVisa(visa) {
        const label = visaLabel(visa);
        wrapper.dataset.selectedLabel = label;
        searchInput.value = label;
        clearSuggestions();
    }

    function renderSuggestions(query) {
        clearSuggestions();
        const matches = visaTypes.filter(function (visa) {
            return matchesQuery(visa, query);
        });
        if (matches.length === 0) {
            showMessage('autocomplete-info', 'No visa types found. Please try a different search.');
            return;
        }

        const list = document.createElement('div');
        list.className = 'autocomplete-suggestions';
        matches.forEach(function (visa) {
            const item = document.createElement('div');
            item.className = 'autocomplete-suggestion';
            item.textContent = visaLabel(visa);
            item.addEventListener('click', function () {
                selectVisa(visa);
            });
            list.appendChild(item);
        });
        searchContainer.appendChild(list);
    }

    function loadVisaTypes() {
        if (loaded) {
            return Promise.resolve(visaTypes);
        }
        if (loadPromise) {
            return loadPromise;
        }
        if (!visaTypesRoute) {
            showMessage('autocomplete-error', 'Visa type list is unavailable. Please refresh the page.');
            return Promise.resolve([]);
        }

        loadPromise = fetch(visaTypesRoute, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        }).then(function (response) {
            if (response.status === 404) {
                showMessage('autocomplete-error', 'This link is no longer valid.');
                return [];
            }
            if (!response.ok) {
                throw new Error('visa-types-failed');
            }
            return response.json();
        }).then(function (data) {
            visaTypes = Array.isArray(data) ? data : [];
            loaded = true;
            return visaTypes;
        }).catch(function () {
            loadPromise = null;
            showMessage('autocomplete-error', 'Unable to load visa types. Please try again.');
            return [];
        });

        return loadPromise;
    }

    window.composeVerificationVisaType = function (field) {
        const panel = field ? field.querySelector('#verificationVisaTypeEntry') : wrapper;
        if (!panel) {
            return '';
        }
        const selected = (panel.dataset.selectedLabel || '').trim();
        if (selected) {
            return selected;
        }

        const typed = ((panel.querySelector('.visa-type-search-input') || {}).value || '').trim();
        if (!typed) {
            return '';
        }

        const exact = visaTypes.filter(function (visa) {
            return visaLabel(visa).toLowerCase() === typed.toLowerCase();
        });
        if (exact.length === 1) {
            panel.dataset.selectedLabel = visaLabel(exact[0]);
            return panel.dataset.selectedLabel;
        }

        return '';
    };

    searchInput.addEventListener('focus', function () {
        loadVisaTypes().then(function () {
            renderSuggestions((searchInput.value || '').trim());
        });
    });

    searchInput.addEventListener('input', function () {
        wrapper.dataset.selectedLabel = '';
        const query = (searchInput.value || '').trim();
        loadVisaTypes().then(function () {
            renderSuggestions(query);
        });
    });

    document.addEventListener('click', function (event) {
        if (!wrapper.contains(event.target)) {
            wrapper.querySelectorAll('.autocomplete-suggestions').forEach(function (el) {
                el.remove();
            });
        }
    });
})();
