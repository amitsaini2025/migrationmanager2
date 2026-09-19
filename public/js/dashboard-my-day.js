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
        sessions: safeJson(root.getAttribute('data-initial-sessions'), { auto: [], opened: [], event_minutes: {} }),
        activityCounts: safeJson(root.getAttribute('data-initial-activity-counts'), {
            checklists: 0,
            documents: 0,
            actions: 0,
            sms: 0
        }),
        selected: null,
        selectedKind: null,
        filterKey: null,
        pendingDoneId: null,
        tickTimer: null,
        crmLimit: null,
        eodCrmLimit: null
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
                return res.text().then(function (text) {
                    var data = {};
                    if (text) {
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            throw new Error(
                                res.status
                                    ? ('Request failed (' + res.status + ').')
                                    : 'Invalid response from server.'
                            );
                        }
                    }
                    if (!res.ok) {
                        var msg = (data && (data.message || (data.errors && Object.values(data.errors)[0]))) || ('Request failed (' + res.status + ').');
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
        if (board.sessions) {
            state.sessions = board.sessions;
        }
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
            btn.className = 'my-day-preset' + (state.selectedKind === p.id ? ' active' : '');
            btn.disabled = !state.selected;
            btn.innerHTML = '<span class="swatch" style="background:' + p.fill + '"></span>' + p.label;
            btn.addEventListener('click', function () {
                state.selectedKind = p.id;
                renderPresets();
                updateLogSaveEnabled();
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
            updateLogSaveEnabled();
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
            state.selectedKind = null;
            renderChosen();
            renderPresets();
        });
        updateLogSaveEnabled();
    }

    function updateLogSaveEnabled() {
        var btn = document.getElementById('myDayLogSave');
        if (!btn) {
            return;
        }
        var title = (document.getElementById('myDayTitleInput')?.value || '').trim();
        var mins = parseInt(document.getElementById('myDayLogMins')?.value || '0', 10);
        btn.disabled = !(state.selected && state.selectedKind && title && mins >= 1 && mins <= 480);
    }

    function renderManualList() {
        var list = document.getElementById('myDayManualList');
        var badge = document.getElementById('myDayManualCount');
        var manual = (state.entries || []).filter(function (e) {
            return e.status === 'done';
        });
        if (badge) {
            badge.textContent = String(manual.length);
        }
        if (!list) {
            return;
        }
        if (!manual.length) {
            list.innerHTML = '<p class="my-day-empty">No manual logs yet today.</p>';
            return;
        }
        list.innerHTML = manual.map(function (entry) {
            var p = preset(entry.kind);
            return '<div class="my-day-manual-row">' +
                '<span class="my-day-manual-kind" style="background:' + p.bg + ';color:' + p.ink + '">' + escapeHtml(p.label) + '</span>' +
                '<div class="my-day-manual-body">' +
                '<div class="my-day-manual-title">' + escapeHtml(entry.title) + '</div>' +
                '<div class="my-day-manual-meta">' + escapeHtml(entry.matter_no || 'Admin / no file') +
                (entry.posted ? ' · posted' : '') + '</div></div>' +
                '<span class="my-day-manual-mins">' + escapeHtml(String(entry.confirmed_minutes || 0)) + 'm</span></div>';
        }).join('');
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
        var row = (state.byMatter || []).find(function (r) {
            return String(r.key) === String(state.filterKey);
        });
        var label = row && row.matter_no
            ? row.matter_no
            : (state.filterKey === 'admin' ? 'Admin / no file' : String(state.filterKey));
        bar.hidden = false;
        bar.innerHTML = 'Filtered to <b>' + escapeHtml(label) + '</b> <button type="button" id="myDayClearFilter">Clear</button>';
        document.getElementById('myDayClearFilter')?.addEventListener('click', function () {
            state.filterKey = null;
            renderFilterbar();
            renderBoard();
            renderMatterRows();
        });
    }

    function autoEventCount(row) {
        if (Array.isArray(row.events) && row.events.length) {
            return row.events.length;
        }
        return parseInt(row.event_count, 10) || 0;
    }

    function autoTotalMinutes(row) {
        var total = parseInt(row.confirmed_minutes, 10);
        if (isNaN(total) || total < 0) {
            return 0;
        }
        return total;
    }

    function autoShowsAverage(row) {
        return !row.is_reviewed_only && autoEventCount(row) > 1;
    }

    function autoDisplayMinutes(row) {
        var total = autoTotalMinutes(row);
        var count = autoEventCount(row);
        if (!autoShowsAverage(row) || total < 1) {
            return Math.max(1, total);
        }
        return Math.max(1, Math.round(total / count));
    }

    function autoConfirmedFromInput(row, entered) {
        var value = parseInt(entered, 10);
        if (isNaN(value) || value < 1) {
            return null;
        }
        if (!autoShowsAverage(row)) {
            return Math.min(480, value);
        }
        return Math.min(480, Math.max(1, value * autoEventCount(row)));
    }

    function renderAutoSessions() {
        var list = document.getElementById('myDayAutoList');
        var badge = document.getElementById('myDayAutoCount');
        var auto = (state.sessions && state.sessions.auto) || [];
        if (badge) {
            badge.textContent = String(auto.length);
        }
        if (!list) {
            return;
        }
        if (!auto.length) {
            list.innerHTML = '<p class="my-day-empty">No auto file time recorded yet today.</p>';
            return;
        }
        list.innerHTML = auto.map(function (row) {
            var count = autoEventCount(row);
            var total = autoTotalMinutes(row);
            var showsAvg = autoShowsAverage(row) && total > 0;
            var display = autoDisplayMinutes(row);
            var meta;
            if (row.is_reviewed_only) {
                meta = 'reviewed file';
            } else if (count > 0) {
                meta = '<button type="button" class="my-day-auto-events-btn" data-session-id="' +
                    escapeAttr(String(row.id)) + '">' +
                    escapeHtml(String(count)) + ' activities</button>';
            } else {
                meta = '0 activities';
            }
            if (row.posted) {
                meta += ' · posted';
            }
            var del = row.posted
                ? ''
                : '<button type="button" class="my-day-auto-delete" data-session-id="' + row.id + '">Delete</button>';
            var minsTitle = showsAvg
                ? ('Average per activity (session total ' + total + 'm). Editing sets average; total is average × activities.')
                : 'Session total minutes';
            return '<div class="my-day-auto-row" data-session-id="' + escapeAttr(String(row.id)) +
                '" data-total-minutes="' + escapeAttr(String(total)) +
                '" data-event-count="' + escapeAttr(String(count)) +
                '" data-shows-avg="' + (showsAvg ? '1' : '0') + '">' +
                '<div class="my-day-auto-ref">' +
                (row.url
                    ? '<a href="' + escapeAttr(row.url) + '">' + escapeHtml(row.ref || '—') + '</a>'
                    : escapeHtml(row.ref || '—')) +
                '</div>' +
                '<label class="my-day-auto-mins"><input type="number" class="my-day-auto-mins-input" min="1" max="480" value="' +
                escapeAttr(String(display)) + '" aria-label="' +
                (showsAvg ? 'Average minutes per activity' : 'Minutes') +
                '" title="' + escapeAttr(minsTitle) + '"><span>m</span></label>' +
                '<div class="my-day-auto-meta">' + meta + '</div>' + del + '</div>';
        }).join('');
        list.querySelectorAll('.my-day-auto-mins-input').forEach(function (input) {
            input.addEventListener('change', function () {
                var rowEl = input.closest('.my-day-auto-row');
                var id = rowEl && rowEl.getAttribute('data-session-id');
                var auto = (state.sessions && state.sessions.auto) || [];
                var row = auto.find(function (item) {
                    return String(item.id) === String(id);
                });
                if (!id || !row) {
                    return;
                }
                var mins = autoConfirmedFromInput(row, input.value);
                if (!mins) {
                    return;
                }
                api(routes.sessionsBase + '/' + id + '/minutes', { method: 'POST', body: { confirmed_minutes: mins } })
                    .then(refreshFromIndex)
                    .catch(showError);
            });
        });
        list.querySelectorAll('.my-day-auto-events-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openAutoEventsModal(btn.getAttribute('data-session-id'));
            });
        });
        list.querySelectorAll('.my-day-auto-delete').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-session-id');
                if (!id || !window.confirm('Delete this auto session?')) {
                    return;
                }
                api(routes.sessionsBase + '/' + id, { method: 'DELETE' })
                    .then(refreshFromIndex)
                    .catch(showError);
            });
        });
    }

    function openAutoEventsModal(sessionId) {
        if (!sessionId) {
            return;
        }
        var auto = (state.sessions && state.sessions.auto) || [];
        var row = auto.find(function (item) {
            return String(item.id) === String(sessionId);
        });
        if (!row) {
            return;
        }

        var titleEl = document.getElementById('myDayAutoEventsModalLabel');
        var refEl = document.getElementById('myDayAutoEventsRef');
        var listEl = document.getElementById('myDayAutoEventsList');
        var modalEl = document.getElementById('myDayAutoEventsModal');
        if (!listEl || !modalEl) {
            return;
        }

        var total = autoTotalMinutes(row);
        var count = autoEventCount(row);
        if (titleEl) {
            titleEl.textContent = 'Activities on this file';
        }
        if (refEl) {
            var summary = count > 1
                ? (' · total ' + total + 'm · avg ' + autoDisplayMinutes(row) + 'm')
                : (' · ' + total + 'm');
            if (row.url && row.ref) {
                refEl.innerHTML = '<a href="' + escapeAttr(row.url) + '">' + escapeHtml(row.ref) + '</a>' +
                    escapeHtml(summary);
            } else {
                refEl.textContent = (row.ref || '—') + summary;
            }
        }

        var events = Array.isArray(row.events) ? row.events : [];
        if (!events.length) {
            listEl.innerHTML = '<p class="my-day-empty">No CRM activities found for this session.</p>';
        } else {
            listEl.innerHTML = events.map(function (event) {
                var title = event.title || event.kind || 'Activity';
                var linkUrl = event.url || row.url || '';
                var titleHtml = linkUrl
                    ? '<a href="' + escapeAttr(linkUrl) + '">' + escapeHtml(title) + '</a>'
                    : escapeHtml(title);
                var eventMins = parseInt(event.minutes, 10);
                if (isNaN(eventMins) || eventMins < 0) {
                    eventMins = 0;
                }
                var metaParts = [];
                if (event.time) {
                    metaParts.push(escapeHtml(String(event.time)));
                }
                metaParts.push(escapeHtml(eventMins + 'm'));
                if (event.ref) {
                    metaParts.push(
                        linkUrl
                            ? '<a href="' + escapeAttr(linkUrl) + '">' + escapeHtml(String(event.ref)) + '</a>'
                            : escapeHtml(String(event.ref))
                    );
                }
                return '<div class="my-day-auto-event">' +
                    '<div class="my-day-auto-event-kind">' + escapeHtml(event.kind || 'Activity') + '</div>' +
                    '<div class="my-day-auto-event-title">' + titleHtml + '</div>' +
                    '<div class="my-day-auto-event-meta">' + metaParts.join(' · ') + '</div>' +
                    '</div>';
            }).join('');
        }

        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else if (window.jQuery) {
            jQuery(modalEl).modal('show');
        }
    }

    function renderOpenedFiles() {
        var list = document.getElementById('myDayOpenedList');
        var badge = document.getElementById('myDayOpenedCount');
        var opened = (state.sessions && state.sessions.opened) || [];
        if (badge) {
            badge.textContent = String(opened.length);
        }
        if (!list) {
            return;
        }
        if (!opened.length) {
            list.innerHTML = '<li class="my-day-empty">No files opened without recorded time.</li>';
            return;
        }
        list.innerHTML = opened.map(function (row) {
            var mins = row.minutes ? '<span class="my-day-opened-mins">' + escapeHtml(String(row.minutes)) + 'm</span>' : '';
            return '<li>' +
                (row.url
                    ? '<a href="' + escapeAttr(row.url) + '">' + escapeHtml(row.ref || '—') + '</a>'
                    : escapeHtml(row.ref || '—')) +
                mins + '</li>';
        }).join('');
    }

    function crmEventMinutes(item) {
        var fromItem = parseInt(item && item.minutes, 10);
        if (fromItem > 0) {
            return fromItem;
        }
        var key = item && item.key ? String(item.key) : '';
        var map = (state.sessions && state.sessions.event_minutes) || {};
        var fromMap = key ? parseInt(map[key], 10) : 0;
        return fromMap > 0 ? fromMap : null;
    }

    function renderCrmMinuteChips() {
        var map = (state.sessions && state.sessions.event_minutes) || {};
        document.querySelectorAll('.my-day-crm-item[data-event-key]').forEach(function (el) {
            var key = el.getAttribute('data-event-key');
            var fromMap = key ? parseInt(map[key], 10) : 0;
            var meta = el.querySelector('.my-day-crm-meta');
            if (!meta) {
                meta = document.createElement('div');
                meta.className = 'my-day-crm-meta';
                var tag = el.querySelector('.my-day-tag');
                if (tag) {
                    tag.replaceWith(meta);
                    meta.appendChild(tag);
                } else {
                    el.appendChild(meta);
                }
            }
            var existing = meta.querySelector('.my-day-mins-chip');
            var existingMins = existing ? parseInt(existing.textContent, 10) : 0;
            if (existing) {
                existing.remove();
            }
            // Prefer auto session splits; keep SSR/API minutes when map has none.
            var mins = fromMap > 0 ? fromMap : (existingMins > 0 ? existingMins : 0);
            if (!mins) {
                return;
            }
            var chip = document.createElement('span');
            chip.className = 'my-day-mins-chip';
            chip.textContent = mins + 'm';
            meta.appendChild(chip);
        });
    }

    function refreshFromIndex() {
        return api(myDayIndexUrl(), { method: 'GET' }).then(function (data) {
            if (data.board) {
                applyBoard(data.board);
            }
            if (data.crm_events) {
                renderCrmList(data.crm_events);
            }
            if (data.activity_counts) {
                applyActivityCounts(data.activity_counts);
            }
        });
    }

    function applyActivityCounts(counts) {
        if (!counts) {
            return;
        }
        state.activityCounts = {
            checklists: parseInt(counts.checklists, 10) || 0,
            documents: parseInt(counts.documents, 10) || 0,
            actions: parseInt(counts.actions, 10) || 0,
            sms: parseInt(counts.sms, 10) || 0
        };
        renderActivityCounts();
    }

    function renderActivityCounts() {
        var counts = state.activityCounts || {};
        setText('myDayCountChecklists', String(counts.checklists || 0));
        setText('myDayCountDocuments', String(counts.documents || 0));
        setText('myDayCountActions', String(counts.actions || 0));
        setText('myDayCountSms', String(counts.sms || 0));
    }

    function renderCrmList(payload) {
        var wrap = document.getElementById('myDayCrmList');
        if (!wrap || !payload) {
            return;
        }
        var items = payload.items || [];
        var more = payload.more || 0;
        var total = payload.total || (items.length + more);
        if (!items.length) {
            wrap.innerHTML = '<p class="my-day-empty">No CRM events logged by you today yet.</p>';
            return;
        }
        var html = items.map(function (item) {
            var mins = crmEventMinutes(item);
            var minsHtml = mins
                ? '<span class="my-day-mins-chip">' + escapeHtml(String(mins)) + 'm</span>'
                : '';
            var bodyHtml = '';
            if (item.body) {
                bodyHtml = '<div class="my-day-crm-body is-collapsed">' +
                    '<div class="my-day-crm-body-text">' + escapeHtml(item.body) + '</div>' +
                    '<button type="button" class="my-day-crm-show-more" aria-expanded="false">Show more</button>' +
                    '</div>';
            }
            var isNote = String(item.key || '').indexOf('note:') === 0;
            var titleHtml = (isNote && item.url)
                ? '<a href="' + escapeAttr(item.url) + '">' + escapeHtml(item.title || '') + '</a>'
                : escapeHtml(item.title || '');
            return '<div class="my-day-crm-item" data-event-key="' + escapeAttr(item.key || '') + '">' +
                '<div class="my-day-crm-kind">' + escapeHtml(item.kind || '') + '</div>' +
                '<div><div class="my-day-crm-title">' + titleHtml + '</div>' +
                (item.ref
                    ? '<div class="my-day-crm-ref">' +
                        (item.url
                            ? '<a href="' + escapeAttr(item.url) + '">' + escapeHtml(item.ref) + '</a>'
                            : escapeHtml(item.ref)) +
                      '</div>'
                    : '') +
                bodyHtml +
                '</div><div class="my-day-crm-meta"><span class="my-day-tag">' +
                escapeHtml(item.time || '') + '</span>' + minsHtml + '</div></div>';
        }).join('');
        if (more > 0) {
            html += '<button type="button" class="my-day-more my-day-more-btn" data-total="' +
                escapeAttr(String(total)) + '">… and ' + more + ' more</button>';
        }
        wrap.innerHTML = html;
    }

    function setupCrmBodyToggle() {
        var wrap = document.getElementById('myDayCrmList');
        if (!wrap || wrap.dataset.bodyToggleBound === '1') {
            return;
        }
        wrap.dataset.bodyToggleBound = '1';
        wrap.addEventListener('click', function (event) {
            var moreBtn = event.target.closest('.my-day-more-btn');
            if (moreBtn && wrap.contains(moreBtn)) {
                event.preventDefault();
                if (moreBtn.disabled) {
                    return;
                }
                var total = parseInt(moreBtn.getAttribute('data-total'), 10) || 0;
                if (total < 1) {
                    return;
                }
                moreBtn.disabled = true;
                moreBtn.textContent = 'Loading…';
                state.crmLimit = total;
                api(myDayIndexUrl(), { method: 'GET' })
                    .then(function (data) {
                        if (data.crm_events) {
                            renderCrmList(data.crm_events);
                        }
                    })
                    .catch(function () {
                        moreBtn.disabled = false;
                        var remaining = parseInt(moreBtn.getAttribute('data-total'), 10) || total;
                        var shown = wrap.querySelectorAll('.my-day-crm-item').length;
                        var left = Math.max(0, remaining - shown);
                        moreBtn.textContent = '… and ' + left + ' more';
                        state.crmLimit = null;
                    });
                return;
            }

            var btn = event.target.closest('.my-day-crm-show-more');
            if (!btn || !wrap.contains(btn)) {
                return;
            }
            var body = btn.closest('.my-day-crm-body');
            if (!body) {
                return;
            }
            var expanding = body.classList.contains('is-collapsed');
            body.classList.toggle('is-collapsed', !expanding);
            btn.setAttribute('aria-expanded', expanding ? 'true' : 'false');
            btn.textContent = expanding ? 'Show less' : 'Show more';
        });
    }

    function renderAll() {
        renderPresets();
        renderChosen();
        renderBoard();
        renderManualList();
        renderTally();
        renderMatterRows();
        renderFilterbar();
        renderAutoSessions();
        renderOpenedFiles();
        renderActivityCounts();
        renderCrmMinuteChips();
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

    function openLogModal() {
        state.selected = null;
        state.selectedKind = null;
        var title = document.getElementById('myDayTitleInput');
        var mins = document.getElementById('myDayLogMins');
        var search = document.getElementById('myDayMatterSearch');
        var results = document.getElementById('myDayResults');
        if (title) {
            title.value = '';
        }
        if (mins) {
            mins.value = '15';
        }
        if (search) {
            search.value = '';
        }
        if (results) {
            results.hidden = true;
            results.innerHTML = '';
        }
        renderChosen();
        renderPresets();
        updateLogSaveEnabled();
        var modalEl = document.getElementById('myDayLogModal');
        if (!modalEl) {
            return;
        }
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else if (window.jQuery) {
            jQuery(modalEl).modal('show');
        }
    }

    function hideLogModal() {
        var modalEl = document.getElementById('myDayLogModal');
        if (!modalEl) {
            return;
        }
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        } else if (window.jQuery) {
            jQuery(modalEl).modal('hide');
        }
    }

    function submitLog() {
        if (!state.selected || !state.selectedKind) {
            return;
        }
        var title = (document.getElementById('myDayTitleInput')?.value || '').trim();
        var mins = parseInt(document.getElementById('myDayLogMins')?.value || '0', 10);
        if (!title) {
            showError(new Error('Add a short title for what you did.'));
            return;
        }
        if (mins < 1 || mins > 480) {
            showError(new Error('Minutes must be between 1 and 480.'));
            return;
        }
        var body = {
            kind: state.selectedKind,
            title: title,
            confirmed_minutes: mins
        };
        if (state.selected.admin) {
            body.admin = true;
        } else {
            body.client_matter_id = state.selected.id;
        }
        var saveBtn = document.getElementById('myDayLogSave');
        if (saveBtn) {
            saveBtn.disabled = true;
        }
        api(routes.log, { method: 'POST', body: body }).then(function (data) {
            hideLogModal();
            applyBoard(data.board);
            expandEod();
            if (window.iziToast) {
                iziToast.success({ title: 'My day', message: 'Time logged', position: 'topRight' });
            }
        }).catch(function (err) {
            updateLogSaveEnabled();
            showError(err);
        });
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

    function markSaved(iso) {
        var el = document.getElementById('myDayEodSaved');
        if (!el) {
            return;
        }
        if (!iso) {
            el.hidden = true;
            el.textContent = '';
            return;
        }
        var when = new Date(iso);
        var label = Number.isNaN(when.getTime()) ? iso : when.toLocaleString();
        el.textContent = 'Saved ' + label;
        el.hidden = false;
    }

    function defaultCrmListCap() {
        var cap = parseInt(routes.crmListCap, 10);
        return cap > 0 ? cap : 50;
    }

    function withCrmLimit(url, limit) {
        var sep = url.indexOf('?') >= 0 ? '&' : '?';
        return url + sep + 'crm_limit=' + encodeURIComponent(limit);
    }

    function myDayIndexUrl() {
        return withCrmLimit(routes.index, state.crmLimit || defaultCrmListCap());
    }

    function eodSummaryUrl() {
        return withCrmLimit(routes.copySummary, state.eodCrmLimit || defaultCrmListCap());
    }

    function estimateCrmTotalFromText(beforeText, more) {
        var marker = '— Already in CRM —';
        var idx = beforeText.indexOf(marker);
        var chunk = idx >= 0 ? beforeText.slice(idx + marker.length) : beforeText;
        var shown = chunk.split('\n').filter(function (line) {
            var t = line.trim();
            return t !== '' && t !== '(none)';
        }).length;
        return shown + more;
    }

    function renderEodSummary(summary) {
        var el = document.getElementById('myDayEod');
        if (!el || !summary) {
            return;
        }
        var text = String(summary.text || '');
        var more = parseInt(summary.crm_more, 10) || 0;
        var total = parseInt(summary.crm_total, 10) || 0;
        var lineRe = /\n(?:…|\.\.\.) and (\d+) more\n/;
        var lineMatch = text.match(lineRe);
        if (lineMatch && !more) {
            more = parseInt(lineMatch[1], 10) || 0;
        }

        if (more > 0) {
            var parts = text.split(/\n(?:…|\.\.\.) and \d+ more\n/);
            var before = parts[0] || text.replace(/\n(?:…|\.\.\.) and \d+ more(?=\n|$)/g, '');
            var after = parts.length > 1 ? parts.slice(1).join('\n') : '';
            if (!total) {
                total = estimateCrmTotalFromText(before, more);
            }
            el.innerHTML = escapeHtml(before) +
                '\n<button type="button" class="my-day-more my-day-more-btn my-day-eod-more-inline" data-total="' +
                escapeAttr(String(total)) + '" data-more="' + escapeAttr(String(more)) +
                '">… and ' + more + ' more</button>' +
                (after ? '\n' + escapeHtml(after) : '');
            return;
        }

        el.textContent = text.replace(/\n(?:…|\.\.\.) and \d+ more(?=\n|$)/g, '');
    }

    function expandEodCrmMore(btn) {
        if (!btn || btn.disabled) {
            return;
        }
        var total = parseInt(btn.getAttribute('data-total'), 10) || 0;
        var more = parseInt(btn.getAttribute('data-more'), 10) || 0;
        if (total < 1) {
            return;
        }
        btn.disabled = true;
        btn.textContent = 'Loading…';
        state.eodCrmLimit = total;
        refreshSummary({ rethrow: true }).catch(function () {
            state.eodCrmLimit = null;
            btn.disabled = false;
            btn.textContent = '… and ' + (more || total) + ' more';
        });
    }

    function refreshSummary(options) {
        options = options || {};
        return api(eodSummaryUrl(), { method: 'GET' }).then(function (data) {
            if (data.summary) {
                renderEodSummary(data.summary);
            }
            renderStillOpenLinks(data.summary && data.summary.still_open);
        }).catch(function (err) {
            if (options.rethrow) {
                throw err;
            }
        });
    }

    function renderStillOpenLinks(items) {
        var list = document.getElementById('myDayStillOpenList');
        var count = document.getElementById('myDayStillOpenCount');
        var rows = Array.isArray(items) ? items : [];
        if (count) {
            count.textContent = String(rows.length);
        }
        if (!list) {
            return;
        }
        if (!rows.length) {
            list.innerHTML = '<li class="my-day-empty">No files still open.</li>';
            return;
        }
        list.innerHTML = rows.map(function (row) {
            var ref = escapeHtml(row.ref || '—');
            var label = row.url
                ? '<a href="' + escapeAttr(row.url) + '">' + ref + '</a>'
                : ref;
            var meta = '';
            if (row.kind && row.kind !== 'opened') {
                meta = '<span class="my-day-still-open-meta">' +
                    escapeHtml(String(row.kind)) +
                    (row.status ? ' · ' + escapeHtml(String(row.status)) : '') +
                    '</span>';
            }
            return '<li>' + label + meta + '</li>';
        }).join('');
    }

    function setupCopy() {
        document.getElementById('myDayEod')?.addEventListener('click', function (event) {
            var btn = event.target.closest('.my-day-eod-more-inline');
            if (!btn || !document.getElementById('myDayEod').contains(btn)) {
                return;
            }
            event.preventDefault();
            expandEodCrmMore(btn);
        });

        document.getElementById('myDayCopyBtn')?.addEventListener('click', function () {
            if (!routes.saveSummary) {
                showError(new Error('Save summary is not available on this page.'));
                return;
            }
            var saveUrl = withCrmLimit(routes.saveSummary, state.eodCrmLimit || defaultCrmListCap());
            api(saveUrl, { method: 'POST', body: {} }).then(function (data) {
                var text = data.summary?.text || '';
                markSaved(data.summary?.saved_at);
                var copied = Promise.resolve();
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    copied = navigator.clipboard.writeText(text).catch(function () {
                        copyViaTextarea(text);
                    });
                } else {
                    copyViaTextarea(text);
                }
                return copied.then(function () {
                    return refreshSummary();
                });
            }).then(function () {
                var btn = document.getElementById('myDayCopyBtn');
                if (btn) {
                    var old = btn.textContent;
                    btn.textContent = 'Copied & saved';
                    setTimeout(function () { btn.textContent = old; }, 1500);
                }
            }).catch(showError);
        });
    }

    function copyViaTextarea(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        ta.remove();
    }

    function setupEodCollapse() {
        var section = document.getElementById('myDayEodSection');
        var toggle = document.getElementById('myDayEodToggle');
        var body = document.getElementById('myDayEodBody');
        if (!section || !toggle || !body) {
            return;
        }
        toggle.addEventListener('click', function () {
            var collapsed = section.classList.toggle('is-collapsed');
            body.hidden = collapsed;
            toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            if (collapsed) {
                // Next open uses default list cap again (so “… and N more” can reappear).
                state.eodCrmLimit = null;
            } else {
                refreshSummary();
            }
        });
    }

    function expandEod() {
        var section = document.getElementById('myDayEodSection');
        var toggle = document.getElementById('myDayEodToggle');
        var body = document.getElementById('myDayEodBody');
        if (!section || !toggle || !body) {
            return;
        }
        section.classList.remove('is-collapsed');
        body.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
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
    document.querySelectorAll('.my-day-add-btn').forEach(function (btn) {
        btn.addEventListener('click', openLogModal);
    });
    document.getElementById('myDayLogSave')?.addEventListener('click', submitLog);
    document.getElementById('myDayTitleInput')?.addEventListener('input', updateLogSaveEnabled);
    document.getElementById('myDayLogMins')?.addEventListener('input', updateLogSaveEnabled);

    setupEodCollapse();
    setupMatterSearch();
    setupCopy();
    setupCrmBodyToggle();
    renderAll();
    refreshSummary();
})();
