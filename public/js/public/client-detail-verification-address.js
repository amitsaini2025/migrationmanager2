(function () {
    'use strict';

    const wrapper = document.getElementById('verificationAddressEntry');
    if (!wrapper) {
        return;
    }

    const searchInput = wrapper.querySelector('.address-search-input');
    const searchRoute = wrapper.dataset.searchRoute || '';
    const detailsRoute = wrapper.dataset.detailsRoute || '';
    const csrfToken = wrapper.dataset.csrfToken || '';
    let searchTimer = null;
    let searchAbort = null;

    function csrfHeaders() {
        return {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
        };
    }

    function field(name) {
        return wrapper.querySelector('[data-addr="' + name + '"]');
    }

    function setField(name, value) {
        const input = field(name);
        if (input) {
            input.value = value || '';
        }
    }

    function clearMessages() {
        wrapper.querySelectorAll('.autocomplete-error, .autocomplete-info, .autocomplete-warning, .autocomplete-suggestions').forEach(function (el) {
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
        const container = wrapper.querySelector('.address-search-container');
        if (container) {
            container.appendChild(message);
        }
        window.setTimeout(function () {
            message.remove();
        }, 5000);
    }

    window.composeVerificationAddress = function (addressField) {
        const panel = addressField ? addressField.querySelector('#verificationAddressEntry') : wrapper;
        if (!panel) {
            return '';
        }

        const required = ['line1', 'suburb', 'state', 'zip', 'country'];
        const missing = required.some(function (name) {
            const input = panel.querySelector('[data-addr="' + name + '"]');
            return !input || !(input.value || '').trim();
        });
        if (missing) {
            return '';
        }

        const parts = ['line1', 'line2', 'suburb', 'state', 'country', 'zip']
            .map(function (name) {
                const input = panel.querySelector('[data-addr="' + name + '"]');
                return input ? (input.value || '').trim() : '';
            })
            .filter(function (part) {
                return part !== '';
            });

        return parts.join(', ');
    };

    function populateAddressFields(result) {
        const components = result.address_components || [];
        let addressLine1 = '';
        let suburb = '';
        let state = '';
        let postcode = '';
        let country = 'Australia';
        let unitNumber = '';
        let streetNumber = '';
        let streetName = '';

        components.forEach(function (component) {
            const types = component.types || [];
            if (types.includes('subpremise')) {
                unitNumber = component.long_name;
            }
            if (types.includes('street_number')) {
                streetNumber = component.long_name;
            }
            if (types.includes('route')) {
                streetName = component.long_name;
            }
            if (types.includes('establishment') || types.includes('point_of_interest') || types.includes('airport')) {
                addressLine1 = component.long_name;
            }
            if (types.includes('locality') || types.includes('sublocality')) {
                suburb = component.long_name;
            }
            if (types.includes('administrative_area_level_1')) {
                state = component.short_name || component.long_name;
            }
            if (types.includes('postal_code')) {
                postcode = component.long_name;
            }
            if (!postcode && types.includes('postal_code_prefix')) {
                postcode = component.long_name;
            }
            if (types.includes('country')) {
                country = component.long_name;
            }
        });

        if (unitNumber && streetNumber && streetName) {
            addressLine1 = unitNumber + '/' + streetNumber + ' ' + streetName;
        } else if (streetNumber && streetName) {
            addressLine1 = streetNumber + ' ' + streetName;
        } else if (unitNumber && streetName) {
            addressLine1 = unitNumber + '/' + streetName;
        } else if (streetName) {
            addressLine1 = streetName;
        }

        if (!addressLine1.trim() && result.formatted_address) {
            addressLine1 = result.formatted_address.split(',')[0].trim();
        }

        if (!suburb && result.formatted_address) {
            const addressParts = result.formatted_address.split(',');
            for (let i = 1; i < addressParts.length && i < 4; i++) {
                const part = addressParts[i].trim();
                if (!part.match(/^\d{4}$/) && !/(NSW|VIC|QLD|SA|WA|TAS|NT|ACT)/.test(part)) {
                    suburb = part;
                    break;
                }
            }
        }

        if (!postcode && result.formatted_address) {
            const afterState = result.formatted_address.match(/\b(NSW|VIC|QLD|SA|WA|TAS|NT|ACT)\s+(\d{4})\b/i);
            const anyPostcode = result.formatted_address.match(/\b(\d{4})\b/);
            if (afterState && afterState[2]) {
                postcode = afterState[2];
            } else if (anyPostcode) {
                postcode = anyPostcode[1];
            }
        }

        setField('line1', addressLine1.trim());
        setField('suburb', suburb);
        setField('state', state);
        setField('zip', postcode);
        setField('country', country);

        if (!postcode) {
            showMessage('autocomplete-warning', 'Address populated but postcode is missing. Please enter manually.');
        }
    }

    function renderSuggestions(predictions) {
        clearMessages();
        const list = document.createElement('div');
        list.className = 'autocomplete-suggestions';
        predictions.forEach(function (prediction) {
            const item = document.createElement('div');
            item.className = 'autocomplete-suggestion';
            item.dataset.placeId = prediction.place_id || '';
            item.textContent = prediction.description || '';
            item.addEventListener('click', function () {
                searchInput.value = item.textContent;
                clearMessages();
                fetchPlaceDetails(item.dataset.placeId, item.textContent);
            });
            list.appendChild(item);
        });
        wrapper.querySelector('.address-search-container').appendChild(list);
    }

    function fetchPlaceDetails(placeId, description) {
        if (!detailsRoute || !placeId) {
            showMessage('autocomplete-error', 'Unable to fetch address details. Please enter manually.');
            return;
        }

        const body = new URLSearchParams({
            place_id: placeId,
            description: description || '',
            _token: csrfToken,
        });

        fetch(detailsRoute, {
            method: 'POST',
            headers: csrfHeaders(),
            body: body,
            credentials: 'same-origin',
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('details-failed');
            }
            return response.json();
        }).then(function (data) {
            if (data.result && data.result.address_components) {
                populateAddressFields(data.result);
            } else {
                showMessage('autocomplete-info', 'Please fill in address fields manually');
            }
        }).catch(function () {
            showMessage('autocomplete-error', 'Unable to fetch address details. Please enter manually.');
        });
    }

    function searchAddress(query) {
        if (!searchRoute) {
            showMessage('autocomplete-error', 'Address search unavailable. Please enter the address manually.');
            return;
        }

        if (searchAbort) {
            searchAbort.abort();
        }
        searchAbort = new AbortController();

        const body = new URLSearchParams({
            query: query,
            _token: csrfToken,
        });

        fetch(searchRoute, {
            method: 'POST',
            headers: csrfHeaders(),
            body: body,
            credentials: 'same-origin',
            signal: searchAbort.signal,
        }).then(function (response) {
            if (response.status === 404) {
                showMessage('autocomplete-error', 'This link is no longer valid.');
                return null;
            }
            if (!response.ok) {
                throw new Error('search-failed');
            }
            return response.json();
        }).then(function (data) {
            if (!data) {
                return;
            }
            if (data.status === 'ERROR' && data.error_message) {
                showMessage('autocomplete-error', data.error_message);
                return;
            }
            if (data.predictions && data.predictions.length > 0) {
                renderSuggestions(data.predictions);
                return;
            }
            showMessage('autocomplete-info', 'No addresses found. Please enter the address manually.');
        }).catch(function (error) {
            if (error.name === 'AbortError') {
                return;
            }
            showMessage('autocomplete-error', 'Address search failed. Please try again or enter manually.');
        });
    }

    searchInput.addEventListener('input', function () {
        const query = (searchInput.value || '').trim();
        clearMessages();
        if (query.length < 3) {
            return;
        }
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(function () {
            searchAddress(query);
        }, 250);
    });

    document.addEventListener('click', function (event) {
        if (!wrapper.contains(event.target)) {
            wrapper.querySelectorAll('.autocomplete-suggestions').forEach(function (el) {
                el.remove();
            });
        }
    });
})();
