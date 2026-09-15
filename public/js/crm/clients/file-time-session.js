(function () {
    'use strict';

    var cfg = window.MyDaySession;
    if (!cfg || !cfg.routes || !cfg.clientId) {
        return;
    }

    var IDLE_MS = 15 * 60 * 1000;
    var IDLE_GRACE_MS = 2 * 60 * 1000;
    var HEARTBEAT_MS = 60 * 1000;

    var focusedSeconds = 0;
    var lastTickAt = null;
    var heartbeatTimer = null;
    var idleTimer = null;
    var idleGraceTimer = null;
    var idleStartedAt = null;
    var channel = typeof BroadcastChannel !== 'undefined' ? new BroadcastChannel('my-day-session') : null;
    var localTick = false;
    var sessionId = null;

    function isFocused() {
        return document.hasFocus() && document.visibilityState === 'visible';
    }

    function currentRecord() {
        return {
            clientId: parseInt(cfg.clientId, 10),
            matterId: cfg.clientMatterId ? parseInt(cfg.clientMatterId, 10) : null,
            ref: cfg.ref || 'file'
        };
    }

    function postJson(url, body) {
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': cfg.csrf || ''
            },
            body: JSON.stringify(body),
            keepalive: true
        })
            .then(function (res) {
                return res.json().catch(function () {
                    return {};
                });
            })
            .catch(function () {
                return {};
            });
    }

    function sendBlur() {
        var rec = currentRecord();
        var payload = {
            client_id: rec.clientId,
            client_matter_id: rec.matterId,
            focused_seconds: focusedSeconds,
            _token: cfg.csrf || ''
        };
        var url = cfg.routes.blur;
        if (navigator.sendBeacon) {
            var fd = new FormData();
            Object.keys(payload).forEach(function (key) {
                if (payload[key] !== null && payload[key] !== undefined) {
                    fd.append(key, payload[key]);
                }
            });
            if (!navigator.sendBeacon(url, fd)) {
                postJson(url, payload);
            }
        } else {
            postJson(url, payload);
        }
    }

    function heartbeat() {
        if (!isFocused() || !localTick) {
            return;
        }
        var rec = currentRecord();
        postJson(cfg.routes.heartbeat, {
            client_id: rec.clientId,
            client_matter_id: rec.matterId,
            focused_seconds: focusedSeconds
        }).then(function (res) {
            if (res && res.session && res.session.id) {
                sessionId = res.session.id;
            }
        });
    }

    function resetIdleTimers() {
        idleStartedAt = null;
        if (idleTimer) {
            clearTimeout(idleTimer);
            idleTimer = null;
        }
        if (idleGraceTimer) {
            clearTimeout(idleGraceTimer);
            idleGraceTimer = null;
        }
        hideIdleModal();
        scheduleIdleCheck();
    }

    function scheduleIdleCheck() {
        if (idleTimer) {
            clearTimeout(idleTimer);
        }
        if (!isFocused() || !localTick) {
            return;
        }
        idleTimer = setTimeout(onIdleWarning, IDLE_MS);
    }

    function onIdleWarning() {
        if (!isFocused() || !localTick) {
            return;
        }
        idleStartedAt = new Date().toISOString();
        showIdleModal();
        if (window.iziToast) {
            iziToast.info({
                title: 'Still working?',
                message: 'Still on ' + (cfg.ref || 'this file') + '?',
                position: 'topRight',
                timeout: 8000
            });
        }
        idleGraceTimer = setTimeout(applyIdleCut, IDLE_GRACE_MS);
    }

    function applyIdleCut() {
        if (!idleStartedAt || !sessionId || !cfg.routes.idleCutBase) {
            return;
        }
        postJson(cfg.routes.idleCutBase + '/' + sessionId + '/idle-cut', { idle_started_at: idleStartedAt });
        localTick = false;
        idleStartedAt = null;
        hideIdleModal();
    }

    function showIdleModal() {
        var el = document.getElementById('myDaySessionIdleDialog');
        if (!el) {
            el = document.createElement('dialog');
            el.id = 'myDaySessionIdleDialog';
            el.innerHTML = '<p>Still on <strong id="myDaySessionIdleRef"></strong>?</p>' +
                '<button type="button" id="myDaySessionIdleOk">Yes, still here</button>';
            document.body.appendChild(el);
            el.querySelector('#myDaySessionIdleOk').addEventListener('click', function () {
                resetIdleTimers();
            });
        }
        var refEl = el.querySelector('#myDaySessionIdleRef');
        if (refEl) {
            refEl.textContent = cfg.ref || 'this file';
        }
        if (typeof el.showModal === 'function') {
            el.showModal();
        } else {
            el.setAttribute('open', 'open');
        }
    }

    function hideIdleModal() {
        var el = document.getElementById('myDaySessionIdleDialog');
        if (!el) {
            return;
        }
        if (typeof el.close === 'function') {
            el.close();
        } else {
            el.removeAttribute('open');
        }
    }

    function startHeartbeatLoop() {
        if (heartbeatTimer) {
            clearInterval(heartbeatTimer);
        }
        heartbeatTimer = setInterval(heartbeat, HEARTBEAT_MS);
        heartbeat();
    }

    function stopHeartbeatLoop() {
        if (heartbeatTimer) {
            clearInterval(heartbeatTimer);
            heartbeatTimer = null;
        }
    }

    function onFocusGain() {
        localTick = true;
        lastTickAt = Date.now();
        startHeartbeatLoop();
        resetIdleTimers();
        if (channel) {
            channel.postMessage({
                type: 'focus',
                clientId: cfg.clientId,
                matterId: cfg.clientMatterId || null
            });
        }
    }

    function onFocusLoss() {
        tickAccumulator();
        localTick = false;
        stopHeartbeatLoop();
        resetIdleTimers();
        sendBlur();
    }

    function tickAccumulator() {
        if (!localTick || lastTickAt === null) {
            return;
        }
        var now = Date.now();
        focusedSeconds += Math.max(0, Math.floor((now - lastTickAt) / 1000));
        lastTickAt = now;
    }

    function tickLoop() {
        if (localTick && isFocused()) {
            tickAccumulator();
        }
        requestAnimationFrame(tickLoop);
    }

    function bindInputReset() {
        ['mousemove', 'keydown', 'scroll', 'click', 'touchstart'].forEach(function (ev) {
            document.addEventListener(ev, resetIdleTimers, { passive: true });
        });
    }

    function bindMatterChange() {
        var sel = document.getElementById('sel_matter_id_client_detail');
        if (!sel) {
            return;
        }
        sel.addEventListener('change', function () {
            onFocusLoss();
            var opt = sel.options[sel.selectedIndex];
            cfg.clientMatterId = sel.value ? parseInt(sel.value, 10) : null;
            cfg.ref = opt ? (opt.getAttribute('data-clientuniquematterno') || opt.textContent.trim()) : cfg.ref;
            if (isFocused()) {
                onFocusGain();
            }
        });
    }

    if (channel) {
        channel.addEventListener('message', function (ev) {
            var data = ev.data || {};
            if (data.type !== 'focus') {
                return;
            }
            if (String(data.clientId) === String(cfg.clientId) &&
                String(data.matterId || '') === String(cfg.clientMatterId || '')) {
                return;
            }
            localTick = false;
        });
    }

    window.addEventListener('focus', function () {
        if (isFocused()) {
            onFocusGain();
        }
    });
    window.addEventListener('blur', onFocusLoss);
    document.addEventListener('visibilitychange', function () {
        if (isFocused()) {
            onFocusGain();
        } else {
            onFocusLoss();
        }
    });
    window.addEventListener('pagehide', onFocusLoss);

    bindInputReset();
    bindMatterChange();
    requestAnimationFrame(tickLoop);

    if (isFocused()) {
        onFocusGain();
    }
})();
