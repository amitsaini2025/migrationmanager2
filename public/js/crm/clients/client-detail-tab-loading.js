/**
 * Client Detail — shared in-tab loading markup (matches x-client-detail-tab-loading).
 */
(function (global) {
    'use strict';

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function spinnerHtml() {
        if (typeof global.crmIconAny === 'function') {
            return global.crmIconAny('fas fa-spinner fa-spin');
        }
        if (typeof global.crmI === 'function') {
            return global.crmI('fas fa-spinner fa-spin');
        }

        return '<i class="fas fa-spinner fa-spin client-detail-tab-loading__icon icon-spin" aria-hidden="true"></i>';
    }

    global.clientDetailTabLoadingHtml = function (label, placeholderAttr) {
        var attr = placeholderAttr ? ' ' + placeholderAttr : '';

        return '<div class="workflow-v2-empty client-detail-tab-loading"' + attr + '>' +
            spinnerHtml() +
            '<p class="client-detail-tab-loading__label mb-0">' + escapeHtml(label) + '</p>' +
            '</div>';
    };
})(typeof window !== 'undefined' ? window : this);
