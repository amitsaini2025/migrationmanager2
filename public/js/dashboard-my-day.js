(function () {
    'use strict';

    var root = document.getElementById('myDay');
    if (!root || typeof window.dashboardMyDayRoutes === 'undefined') {
        return;
    }

    var routes = window.dashboardMyDayRoutes;
    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    var PRESETS = [
        { id: 'draft', label: 'Drafting', bg: '#fdf1c9', ink: '#6b5310', fill: '#dfae2e' },
        { id: 'immi', label: 'Immi / portal', bg: '#e6e2fa', ink: '#3b3072', fill: '#7a6ad0' },
        { id: 'docs', label: 'Doc check (off CRM)', bg: '#dce9fb', ink: '#123f75', fill: '#3d7fd4' },
        { id: 'mailbox', label: 'Mailbox on this file', bg: '#d8f2e4', ink: '#0f5a41', fill: '#2fa47c' },
        { id: 'internal', label: 'Internal / discussion', bg: '#fde5d1', ink: '#7d4210', fill: '#e07d2a' },
        { id: 'other', label: 'Other (Excel, 3CX…)', bg: '#fbdfe6', ink: '#7a2540', fill: '#d1587c' }
    ];

    var state = {
        entries: safeJson(root.getAttribute('data-initial-entries'), []),
        tally: safeJson(root.getAttribute('data-initial-tally'), {}),
        byMatter: safeJson(root.getAttribute('data-initial-by-matter'), []),
        selected: null,
        filterKey: null,
        pendingDoneId: null,
        tickTimer: null
    };

    function safeJson(raw, fallback) {
        try {
            return raw ? JSON.parse(raw) : fallback;
        } catch (e) {
            return fallback;
        }
    }

    function preset(kind) {
        return PRESETS.find(function (p) { return p.id === kind; }) || PRESETS[0];
    }

    function formatClock(seconds) {
        var s = Math.max(0, parseInt(seconds, 10) || 0);
        var m = Math.floor(s / 60);
        var r = s % 60;
        return m + ':' + String(r).padStart(2, '0');
    }

    function liveSeconds(entry) {
        var base = parseInt(entry.clock_seconds, 10) || 0;
        if (!entry.is_running || !entry.started_at) {
            return base;
        }
        var started = Date.parse(entry.started_at);
        if (!started) {
            return base;
        }
        return base + Math.max(0, Math.floor((Date.now() - started) / 1000));
    }

    function api(url, options) {
        options = options || {};
        var headers = Object.assign({
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf
        }, options.headers || {});
        if (options.body && !(options.body instanceof FormData)) {
            headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(options.body);
        }
        return fetch(url, Object.assign({}, options, { headers: headers, credentials: 'same-origin' }))
            .then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok) {
                        var msg = (data && (data.message || (data.errors && Object.values(data.errors)[0]))) || 'Request failed';
                        if (Array.isArray(msg)) {
                            msg = msg[0];
                        }
                        throw new Error(msg);
                    }
                    return data;
                });
            });
    }

    function applyBoard(board) {
        if (!board) {
            return;
        }
        state.entries = board.entries || [];
        state.tally = board.tally || {};
        state.byMatter = board.by_matter || [];
        renderAll();
        refreshSummary();
    }

    function renderPresets() {
        var wrap = document.getElementById('myDayPresets');
        if (!wrap) {
            return;
        }
        wrap.innerHTML = '';
        PRESETS.forEach(function (p) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'my-day-preset';
            btn.disabled = !state.selected;
            btn.innerHTML = '<span class="swatch" style="background:' + p.fill + '"></span>' + p.label;
            btn.addEventListener('click', function () {
                startEntry(p.id);
            });
            wrap.appendChild(btn);
        });
    }

    function renderChosen() {
        var wrap = document.getElementById('myDayChosenWrap');
        if (!wrap) {
            return;
        }
        if (!state.selected) {
            wrap.innerHTML = '';
            return;
        }
        var admin = !!state.selected.admin;
        wrap.innerHTML =
            '<div class="my-day-chosen' + (admin ? ' admin' : '') + '">' +
            '<span class="ref">' + escapeHtml(state.selected.ref || 'Admin / no file') + '</span>' +
            '<span class="text-muted small">' + escapeHtml(state.selected.label || '') + '</span>' +
            '<button type="button" class="btn btn-sm btn-outline-secondary ms-auto" id="myDayClearChosen">Clear</button>' +
            '</div>';
        document.getElementById('myDayClearChosen')?.addEventListener('click', function () {
            state.selected = null;
            renderChosen();
            renderPresets();
        });
    }

    function renderBoard() {
        var cols = {
            doing: document.getElementById('myDayColDoing'),
            parked: document.getElementById('myDayColParked'),
            done: document.getElementById('myDayColDone')
        };
        Object.keys(cols).forEach(function (k) {
            if (cols[k]) {
                cols[k].innerHTML = '';
            }
        });
        var counts = { doing: 0, parked: 0, done: 0 };
        var filtered = state.entries.filter(function (e) {
            if (!state.filterKey) {
                return true;
            }
            if (state.filterKey === 'admin') {
                return !!e.is_admin;
            }
            return String(e.client_matter_id) === String(state.filterKey);
        });

        filtered.forEach(function (entry) {
            var status = entry.status === 'parked' ? 'parked' : (entry.status === 'done' ? 'done' : 'doing');
            counts[status]++;
            var col = cols[status];
            if (!col) {
                return;
            }
            var p = preset(entry.kind);
            var note = document.createElement('div');
            note.className = 'my-day-note' + (status === 'done' ? ' done' : '');
            note.style.background = p.bg;
            note.style.color = p.ink;
            var secs = liveSeconds(entry);
            var timerClass = 'my-day-timer' + (entry.is_running ? ' live' : '');
            var timeLabel = status === 'done' && entry.confirmed_minutes
                ? (entry.confirmed_minutes + 'm')
                : formatClock(secs);
            var acts = '';
            if (status !== 'done') {
                if (entry.is_running) {
                    acts += '<button type="button" data-act="pause">Pause</button>';
                } else {
                    acts += '<button type="button" data-act="resume">Resume</button>';
                }
                acts += '<button type="button" data-act="park">Park</button>';
                acts += '<button type="button" data-act="done">Done</button>';
                acts += '<button type="button" data-act="delete">Delete</button>';
            } else {
                acts += '<button type="button" data-act="reopen">Reopen</button>';
            }
            note.innerHTML =
                '<div class="type">' + escapeHtml(p.label) + '</div>' +
                '<div class="title">' + escapeHtml(entry.title) + '</div>' +
                '<span class="ref-chip">' + escapeHtml(entry.matter_no || 'Admin / no file') + '</span>' +
                '<div class="foot"><span class="' + timerClass + '" data-entry-id="' + entry.id + '">' + timeLabel + '</span>' +
                '<div class="my-day-acts" data-entry-id="' + entry.id + '">' + acts + '</div></div>' +
                (entry.posted ? '<div class="my-day-posted">Posted to matter feed</div>' : '');
            col.appendChild(note);
        });

        Object.keys(counts).forEach(function (k) {
            var el = document.querySelector('.my-day-count[data-count="' + k + '"]');
            if (el) {
                el.textContent = String(counts[k]);
            }
        });

        document.querySelectorAll('.my-day-acts button').forEach(function (btn) {
            btn.addEventListener('click', onActClick);
        });
    }

    function renderTally() {
        var t = state.tally || {};
        setText('myDayKTime', (t.confirmed_minutes || 0) + 'm');
        setText('myDayKMatters', String(t.files_timed || 0));
        setText('myDayKOpen', String(t.still_open || 0));
        setText('myDayKAdmin', (t.admin_minutes || 0) + 'm');
        var bars = document.getElementById('myDayBars');
        if (!bars) {
            return;
        }
        var byKind = t.by_kind || {};
        var max = Math.max.apply(null, Object.values(byKind).concat([1]));
        bars.innerHTML = '';
        PRESETS.forEach(function (p) {
            var mins = byKind[p.id] || 0;
            if (!mins) {
                return;
            }
            var row = document.createElement('div');
            row.className = 'my-day-bar-row';
            row.innerHTML =
                '<span>' + escapeHtml(p.label) + '</span><span>' + mins + 'm</span>' +
                '<div class="my-day-track"><div class="my-day-fill" style="width:' + Math.round((mins / max) * 100) + '%;background:' + p.fill + '"></div></div>';
            bars.appendChild(row);
        });
    }

    function renderMatterRows() {
        var body = document.getElementById('myDayMatterRows');
        if (!body) {
            return;
        }
        if (!state.byMatter.length) {
            body.innerHTML = '<tr><td colspan="3" class="my-day-empty">No confirmed overlay time yet.</td></tr>';
            return;
        }
        body.innerHTML = state.byMatter.map(function (row) {
            var active = state.filterKey && String(state.filterKey) === String(row.key) ? ' active' : '';
            return '<tr class="my-day-matter-row' + active + '" data-filter-key="' + escapeAttr(row.key) + '">' +
                '<td class="my-day-refcell">' + escapeHtml(row.matter_no || '—') + '</td>' +
                '<td class="text-end">' + (row.blocks || 0) + '</td>' +
                '<td class="text-end">' + (row.minutes || 0) + 'm</td></tr>';
        }).join('');
        body.querySelectorAll('.my-day-matter-row').forEach(function (tr) {
            tr.addEventListener('click', function () {
                var key = tr.getAttribute('data-filter-key');
                state.filterKey = state.filterKey === key ? null : key;
                renderFilterbar();
                renderBoard();
                renderMatterRows();
            });
        });
    }

    function renderFilterbar() {
        var bar = document.getElementById('myDayFilterbar');
        if (!bar) {
            return;
        }
        if (!state.filterKey) {
            bar.hidden = true;
            bar.innerHTML = '';
            return;
        }
        bar.hidden = false;
        bar.innerHTML = 'Filtered to <b>' + escapeHtml(String(state.filterKey === 'admin' ? 'Admin / no file' : state.filterKey)) + '</b> <button type="button" id="myDayClearFilter">Clear</button>';
        document.getElementById('myDayClearFilter')?.addEventListener('click', function () {
            state.filterKey = null;
            renderFilterbar();
            renderBoard();
            renderMatterRows();
        });
    }

    function renderAll() {
        renderPresets();
        renderChosen();
        renderBoard();
        renderTally();
        renderMatterRows();
        renderFilterbar();
        ensureTick();
    }

    function ensureTick() {
        if (state.tickTimer) {
            clearInterval(state.tickTimer);
            state.tickTimer = null;
        }
        var anyRunning = state.entries.some(function (e) { return e.is_running; });
        if (!anyRunning) {
            return;
        }
        state.tickTimer = setInterval(function () {
            state.entries.forEach(function (entry) {
                if (!entry.is_running) {
                    return;
                }
                var el = document.querySelector('.my-day-timer[data-entry-id="' + entry.id + '"]');
                if (el) {
                    el.textContent = formatClock(liveSeconds(entry));
                }
            });
        }, 1000);
    }

    function onActClick(ev) {
        var btn = ev.currentTarget;
        var act = btn.getAttribute('data-act');
        var id = btn.parentElement.getAttribute('data-entry-id');
        var entry = state.entries.find(function (e) { return String(e.id) === String(id); });
        if (!entry) {
            return;
        }
        var urlBase = routes.fileTimeBase.replace(/\/$/, '') + '/' + entry.id;
        if (act === 'done') {
            openDoneModal(entry);
            return;
        }
        if (act === 'delete') {
            if (!window.confirm('Delete this open timer?')) {
                return;
            }
            api(urlBase, { method: 'DELETE' }).then(function (data) {
                applyBoard(data.board);
            }).catch(showError);
            return;
        }
        var path = act;
        var body = null;
        if (act === 'pause' || act === 'park') {
            body = { clock_seconds: liveSeconds(entry) };
        }
        api(urlBase + '/' + path, { method: 'POST', body: body }).then(function (data) {
            applyBoard(data.board);
        }).catch(showError);
    }

    function openDoneModal(entry) {
        state.pendingDoneId = entry.id;
        var suggested = Math.max(1, Math.round(liveSeconds(entry) / 60));
        var input = document.getElementById('myDayMinsInput');
        if (input) {
            input.value = String(Math.min(480, suggested));
        }
        var blurb = document.getElementById('myDayDoneBlurb');
        if (blurb) {
            blurb.textContent = entry.is_admin
                ? 'Admin time stays on My day only (no matter feed post).'
                : 'This will post to the matter feed automatically.';
        }
        var modalEl = document.getElementById('myDayDoneModal');
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else if (window.jQuery) {
            jQuery(modalEl).modal('show');
        }
    }

    function confirmDone() {
        var entry = state.entries.find(function (e) { return e.id === state.pendingDoneId; });
        if (!entry) {
            return;
        }
        var mins = parseInt(document.getElementById('myDayMinsInput')?.value || '0', 10);
        api(routes.fileTimeBase.replace(/\/$/, '') + '/' + entry.id + '/done', {
            method: 'POST',
            body: { confirmed_minutes: mins, clock_seconds: liveSeconds(entry) }
        }).then(function (data) {
            var modalEl = document.getElementById('myDayDoneModal');
            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).hide();
            } else if (window.jQuery) {
                jQuery(modalEl).modal('hide');
            }
            applyBoard(data.board);
        }).catch(showError);
    }

    function startEntry(kind) {
        if (!state.selected) {
            return;
        }
        var title = (document.getElementById('myDayTitleInput')?.value || '').trim();
        if (!title) {
            showError(new Error('Add a short title for what you are doing.'));
            return;
        }
        var body = { kind: kind, title: title };
        if (state.selected.admin) {
            body.admin = true;
        } else {
            body.client_matter_id = state.selected.id;
        }
        api(routes.start, { method: 'POST', body: body }).then(function (data) {
            document.getElementById('myDayTitleInput').value = '';
            applyBoard(data.board);
        }).catch(showError);
    }

    function setupMatterSearch() {
        var input = document.getElementById('myDayMatterSearch');
        var results = document.getElementById('myDayResults');
        if (!input || !results) {
            return;
        }
        var timer = null;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            var q = input.value.trim();
            if (q.length < 2) {
                results.hidden = true;
                results.innerHTML = '';
                return;
            }
            timer = setTimeout(function () {
                api(routes.matterSearch + '?q=' + encodeURIComponent(q), { method: 'GET' }).then(function (data) {
                    var list = data.results || [];
                    if (!list.length) {
                        results.innerHTML = '<div class="my-day-res text-muted">' + escapeHtml(data.empty_hint || 'No matters found') + '</div>';
                        results.hidden = false;
                        return;
                    }
                    results.innerHTML = list.map(function (row) {
                        return '<div class="my-day-res" data-id="' + row.id + '" data-ref="' + escapeAttr(row.ref || '') + '" data-label="' + escapeAttr((row.client || '') + ' · ' + (row.matter || '')) + '">' +
                            '<div class="rref">' + escapeHtml(row.ref || '') + '</div>' +
                            '<div class="rmat">' + escapeHtml(row.client || '') + (row.matter ? ' — ' + escapeHtml(row.matter) : '') + '</div>' +
                            (row.stage ? '<div class="rstage">' + escapeHtml(row.stage) + '</div>' : '') +
                            '</div>';
                    }).join('');
                    results.hidden = false;
                    results.querySelectorAll('.my-day-res[data-id]').forEach(function (el) {
                        el.addEventListener('click', function () {
                            state.selected = {
                                id: parseInt(el.getAttribute('data-id'), 10),
                                ref: el.getAttribute('data-ref'),
                                label: el.getAttribute('data-label'),
                                admin: false
                            };
                            input.value = '';
                            results.hidden = true;
                            renderChosen();
                            renderPresets();
                        });
                    });
                }).catch(showError);
            }, 220);
        });
    }

    function refreshSummary() {
        api(routes.copySummary, { method: 'GET' }).then(function (data) {
            var pre = document.getElementById('myDayEod');
            if (pre && data.summary) {
                pre.textContent = data.summary.text || '';
            }
        }).catch(function () { /* ignore summary errors on load */ });
    }

    function setupCopy() {
        document.getElementById('myDayCopyBtn')?.addEventListener('click', function () {
            api(routes.copySummary, { method: 'GET' }).then(function (data) {
                var text = data.summary?.text || '';
                var pre = document.getElementById('myDayEod');
                if (pre) {
                    pre.textContent = text;
                }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    return navigator.clipboard.writeText(text);
                }
                var ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                ta.remove();
            }).then(function () {
                var btn = document.getElementById('myDayCopyBtn');
                if (btn) {
                    var old = btn.textContent;
                    btn.textContent = 'Copied';
                    setTimeout(function () { btn.textContent = old; }, 1500);
                }
            }).catch(showError);
        });
    }

    function setText(id, value) {
        var el = document.getElementById(id);
        if (el) {
            el.textContent = value;
        }
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function escapeAttr(str) {
        return escapeHtml(str).replace(/'/g, '&#39;');
    }

    function showError(err) {
        var msg = err && err.message ? err.message : 'Something went wrong';
        if (window.iziToast) {
            iziToast.error({ title: 'My day', message: msg, position: 'topRight' });
        } else {
            window.alert(msg);
        }
    }

    document.getElementById('myDayAdminBtn')?.addEventListener('click', function () {
        state.selected = { admin: true, ref: 'Admin / no file', label: 'Mailbox, 3CX list, internal, Excel not on a file' };
        renderChosen();
        renderPresets();
    });
    document.getElementById('myDayDoneOk')?.addEventListener('click', confirmDone);

    setupMatterSearch();
    setupCopy();
    renderAll();
    refreshSummary();
})();
