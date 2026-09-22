/**
 * Activity tab: load detail-main.js and the feature modules after the feed
 * request, or on the first click that needs them, then replay that click.
 */
(function() {
    'use strict';

    var loading = null;
    var replaying = false;

    function scriptList() {
        return (window.ClientDetailConfig && window.ClientDetailConfig.actionScripts) || [];
    }

    function detailMainReady() {
        var nodes = document.querySelectorAll('script[src*="detail-main.js"]');
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].getAttribute('data-loading') !== '1') {
                return true;
            }
        }
        return false;
    }

    function loadSrc(src) {
        return new Promise(function(resolve, reject) {
            if (!src) {
                resolve();
                return;
            }
            var path = src.split('?')[0];
            var nodes = document.querySelectorAll('script[src]');
            for (var i = 0; i < nodes.length; i++) {
                if ((nodes[i].src || '').indexOf(path) !== -1 && nodes[i].getAttribute('data-loading') !== '1') {
                    resolve();
                    return;
                }
            }
            var script = document.createElement('script');
            script.src = src;
            script.async = false;
            script.setAttribute('data-loading', '1');
            script.onload = function() {
                script.removeAttribute('data-loading');
                resolve();
            };
            script.onerror = function() {
                reject(new Error('Failed to load ' + src));
            };
            document.body.appendChild(script);
        });
    }

    window.ensureClientDetailActions = function() {
        if (detailMainReady()) {
            return Promise.resolve();
        }
        if (loading) {
            return loading;
        }
        loading = scriptList().reduce(function(chain, src) {
            return chain.then(function() {
                return loadSrc(src);
            });
        }, Promise.resolve()).catch(function(err) {
            loading = null;
            console.error('[Client detail] Failed to load page actions', err);
            throw err;
        });
        return loading;
    };

    function feedControl(target) {
        return target.closest('.client-nav-button, .activity-filter-btn, #activity-feed-refresh, #activity-feed-load-more, .feed-item-show-more, #sidebar-toggle, #collapsed-toggle');
    }

    document.addEventListener('click', function(event) {
        if (replaying || event.button || detailMainReady()) {
            return;
        }
        var target = event.target && event.target.closest ? event.target : (event.target && event.target.parentElement);
        if (!target || feedControl(target)) {
            return;
        }
        event.preventDefault();
        event.stopImmediatePropagation();
        window.ensureClientDetailActions().then(function() {
            replaying = true;
            try {
                var clickable = target.closest('a, button, [data-bs-toggle], [onclick], [role="button"]') || target;
                clickable.dispatchEvent(new MouseEvent('click', {
                    bubbles: true,
                    cancelable: true,
                    composed: true,
                    view: window
                }));
            } finally {
                replaying = false;
            }
        }).catch(function() {
            if (typeof iziToast !== 'undefined') {
                iziToast.error({
                    title: 'Error',
                    message: 'Could not load this action. Please refresh the page.',
                    position: 'topRight'
                });
            }
        });
    }, true);

    document.addEventListener('change', function(event) {
        if (replaying || detailMainReady()) {
            return;
        }
        var target = event.target && event.target.closest ? event.target : (event.target && event.target.parentElement);
        if (!target || !target.closest('.crm-container')) {
            return;
        }
        window.ensureClientDetailActions().then(function() {
            replaying = true;
            try {
                target.dispatchEvent(new Event('change', { bubbles: true }));
            } finally {
                replaying = false;
            }
        });
    }, true);
})();
