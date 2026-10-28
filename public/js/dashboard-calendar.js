/**
 * Dashboard appointment calendar (migrationmanager2).
 * Appointments only. Requires window.FullCalendar + window.FullCalendarPlugins from Vite app.js.
 */
(function () {
    'use strict';

    const CALENDAR_EL_ID = 'staffDashboardCalendar';
    const DEFAULT_TYPE = 'paid';
    const EVENTS_API = window.dashboardRoutes?.calendarEvents || '/dashboard/calendar-events';

    function calendarEl() {
        return document.getElementById(CALENDAR_EL_ID);
    }

    function calendarTz() {
        var el = calendarEl();
        return (el && el.getAttribute('data-timezone')) || 'Australia/Melbourne';
    }

    function selectedType() {
        var el = calendarEl();
        var type = el && el.getAttribute('data-calendar-type');
        return type || DEFAULT_TYPE;
    }

    function calendarTypes() {
        var el = calendarEl();
        var raw = el && el.getAttribute('data-calendar-types');
        if (!raw) {
            return [];
        }
        try {
            var parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    function typeLabel(type) {
        var match = calendarTypes().find(function (item) {
            return item && item.key === type;
        });
        return match && match.label ? match.label : type;
    }

    function setSelectedType(type) {
        var el = calendarEl();
        if (el) {
            el.setAttribute('data-calendar-type', type);
        }
        renderTypeSwitcher(type);
    }

    function closeTypeMenu() {
        var menu = document.getElementById('dashboardCalMoreMenu');
        var btn = document.getElementById('dashboardCalMoreBtn');
        if (menu) {
            menu.hidden = true;
        }
        if (btn) {
            btn.setAttribute('aria-expanded', 'false');
        }
    }

    function renderTypeSwitcher(type) {
        var primary = document.getElementById('dashboardCalPrimary');
        var menu = document.getElementById('dashboardCalMoreMenu');
        if (primary) {
            primary.setAttribute('data-calendar-type', type);
            primary.textContent = typeLabel(type);
            if (type === 'tourist') {
                primary.setAttribute('title', 'Vijay(Tourist Visa)');
            } else {
                primary.removeAttribute('title');
            }
        }
        if (!menu) {
            return;
        }
        menu.innerHTML = '';
        calendarTypes().forEach(function (item) {
            if (!item || item.key === type) {
                return;
            }
            var option = document.createElement('button');
            option.type = 'button';
            option.className = 'dashboard-cal-type-option';
            option.setAttribute('data-calendar-type', item.key);
            option.setAttribute('role', 'option');
            option.textContent = item.label || item.key;
            if (item.key === 'tourist') {
                option.setAttribute('title', 'Vijay(Tourist Visa)');
            }
            menu.appendChild(option);
        });
    }

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function waitForFullCalendar(callback, maxAttempts) {
        maxAttempts = maxAttempts || 100;
        function ready() {
            return typeof FullCalendar !== 'undefined' && FullCalendar.Calendar &&
                typeof FullCalendarPlugins !== 'undefined';
        }
        if (ready()) {
            callback();
            return;
        }
        var attempts = 0;
        var interval = setInterval(function () {
            attempts++;
            if (ready()) {
                clearInterval(interval);
                callback();
            } else if (attempts >= maxAttempts) {
                clearInterval(interval);
                var el = calendarEl();
                if (el) {
                    el.innerHTML = '<div class="alert alert-warning mb-0">Calendar could not load. Please refresh the page.</div>';
                }
            }
        }, 100);
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function todayDateStr(tz) {
        try {
            return new Date().toLocaleDateString('en-CA', { timeZone: tz || calendarTz() });
        } catch (e) {
            return new Date().toISOString().slice(0, 10);
        }
    }

    function formatEventTime(iso, tz) {
        if (!iso) return '';
        var date = new Date(iso);
        if (isNaN(date.getTime())) return '';
        return date.toLocaleString('en-AU', {
            timeZone: tz || calendarTz(),
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
        });
    }

    function formatDayGroupLabel(dateStr, tz) {
        var today = todayDateStr(tz);
        if (dateStr === today) return 'Today';
        var date = new Date(dateStr + 'T12:00:00');
        if (isNaN(date.getTime())) return dateStr;
        return date.toLocaleDateString('en-AU', {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    }

    function eventDateKey(event, tz) {
        var start = event.start || event.extendedProps?.starts_at || event.extendedProps?.appointment_datetime;
        if (!start) return '';
        try {
            return new Date(start).toLocaleDateString('en-CA', { timeZone: tz || calendarTz() });
        } catch (e) {
            return String(start).slice(0, 10);
        }
    }

    function statusClass(status) {
        var key = String(status || 'pending').toLowerCase();
        if (['pending', 'awaiting_confirmation', 'paid', 'confirmed', 'completed'].indexOf(key) !== -1) {
            return key;
        }
        return 'other';
    }

    function updateStats(stats) {
        if (!stats) return;
        var today = document.getElementById('calStatToday');
        var week = document.getElementById('calStatWeek');
        var upcoming = document.getElementById('calStatUpcoming');
        if (today) today.textContent = String(stats.today ?? 0);
        if (week) week.textContent = String(stats.this_week ?? 0);
        if (upcoming) upcoming.textContent = String(stats.upcoming ?? 0);
    }

    function updateUpcomingCount(count) {
        var el = document.getElementById('dashboardUpcomingCount');
        if (el) el.textContent = String(count);
    }

    function fetchAppointments(params) {
        var query = new URLSearchParams(params);
        query.set('type', selectedType());
        return fetch(EVENTS_API + '?' + query.toString(), {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Could not load appointments');
            }
            return response.json();
        });
    }

    function renderUpcomingList(events, tz) {
        var listEl = document.getElementById('dashboardUpcomingList');
        if (!listEl) return;

        if (!events || !events.length) {
            listEl.innerHTML = '<div class="dashboard-upcoming-empty">No upcoming appointments for this calendar.</div>';
            updateUpcomingCount(0);
            return;
        }

        var grouped = {};
        var order = [];
        events.forEach(function (event) {
            var key = eventDateKey(event, tz);
            if (!key) return;
            if (!grouped[key]) {
                grouped[key] = [];
                order.push(key);
            }
            grouped[key].push(event);
        });

        var html = '<div class="dashboard-upcoming-agenda">';
        order.forEach(function (dateKey) {
            var items = grouped[dateKey];
            html += '<div class="dashboard-upcoming-day" data-date="' + escapeHtml(dateKey) + '">';
            html += '<div class="dashboard-upcoming-day-header">';
            html += '<span>' + escapeHtml(formatDayGroupLabel(dateKey, tz)) + '</span>';
            html += '<span class="dashboard-upcoming-day-count">' + items.length + '</span>';
            html += '</div><ul class="dashboard-upcoming-day-items">';
            items.forEach(function (event) {
                var props = event.extendedProps || {};
                var status = statusClass(props.status);
                var url = props.detail_url || event.url || '';
                var payload = {
                    id: event.id,
                    title: event.title,
                    start: event.start || props.starts_at,
                    end: event.end || props.ends_at,
                    extendedProps: props,
                };
                html += '<li class="dashboard-upcoming-item" data-url="' + escapeHtml(url) +
                    '" data-event="' + encodeURIComponent(JSON.stringify(payload)) + '">';
                html += '<div class="dashboard-upcoming-item-time">' + escapeHtml(formatEventTime(event.start || props.starts_at, tz)) + '</div>';
                html += '<div class="dashboard-upcoming-item-body">';
                html += '<div class="dashboard-upcoming-item-main">';
                html += '<div class="dashboard-upcoming-title">' + escapeHtml(event.title || props.client_name || 'Appointment') + '</div>';
                html += '<span class="dashboard-upcoming-type dashboard-upcoming-type--' + escapeHtml(status) + '">' + escapeHtml(props.status_label || props.status || 'Appointment') + '</span>';
                html += '</div>';
                if (props.location) {
                    html += '<div class="dashboard-upcoming-sub">' + escapeHtml(props.location) + '</div>';
                }
                html += '</div></li>';
            });
            html += '</ul></div>';
        });
        html += '</div>';

        listEl.innerHTML = html;
        updateUpcomingCount(events.length);

        listEl.querySelectorAll('.dashboard-upcoming-item').forEach(function (row) {
            row.addEventListener('click', function () {
                var eventJson = row.getAttribute('data-event');
                if (eventJson && typeof window.openBookingAppointmentModal === 'function') {
                    try {
                        window.openBookingAppointmentModal({ event: JSON.parse(decodeURIComponent(eventJson)) });
                        return;
                    } catch (e) {
                        // Fall through to URL.
                    }
                }
                var url = row.getAttribute('data-url');
                if (url) {
                    window.open(url, '_blank', 'noopener');
                }
            });
        });
    }

    function loadUpcoming() {
        var listEl = document.getElementById('dashboardUpcomingList');
        if (listEl) {
            listEl.innerHTML = '<div class="dashboard-upcoming-empty">Loading appointments…</div>';
        }
        fetchAppointments({ upcoming: '1' })
            .then(function (payload) {
                if (payload && payload.stats) {
                    updateStats(payload.stats);
                }
                renderUpcomingList(payload && payload.data ? payload.data : [], calendarTz());
            })
            .catch(function () {
                if (listEl) {
                    listEl.innerHTML = '<div class="dashboard-upcoming-empty">Could not load appointments.</div>';
                }
            });
    }

    function focusUpcomingDate(dateStr) {
        var listEl = document.getElementById('dashboardUpcomingList');
        if (!listEl || !dateStr) return;
        var day = listEl.querySelector('.dashboard-upcoming-day[data-date="' + dateStr + '"]');
        listEl.querySelectorAll('.dashboard-upcoming-day.is-focused').forEach(function (el) {
            el.classList.remove('is-focused');
        });
        if (day) {
            day.classList.add('is-focused');
            day.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }

    function bindTypeSwitcher(calendar) {
        var moreWrap = document.getElementById('dashboardCalMore');
        var moreBtn = document.getElementById('dashboardCalMoreBtn');
        var menu = document.getElementById('dashboardCalMoreMenu');
        var typesEl = document.querySelector('.dashboard-calendar-types');

        function selectType(type) {
            if (!type || type === selectedType()) {
                closeTypeMenu();
                return;
            }
            setSelectedType(type);
            closeTypeMenu();
            calendar.refetchEvents();
            loadUpcoming();
        }

        if (typesEl) {
            typesEl.addEventListener('click', function (event) {
                var option = event.target.closest('.dashboard-cal-type-option');
                if (option) {
                    selectType(option.getAttribute('data-calendar-type'));
                }
            });
        }

        if (moreBtn && menu) {
            moreBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                var willOpen = menu.hidden;
                menu.hidden = !willOpen;
                moreBtn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            });
        }

        document.addEventListener('click', function (event) {
            if (moreWrap && !moreWrap.contains(event.target)) {
                closeTypeMenu();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeTypeMenu();
            }
        });
    }

    function placeMorePopover(pop, left, top) {
        var box = pop.getBoundingClientRect();
        var nextLeft = left;
        var nextTop = top;
        if (box.right > window.innerWidth - 8) {
            nextLeft = Math.max(8, window.innerWidth - box.width - 8);
        }
        if (box.bottom > window.innerHeight - 8) {
            nextTop = Math.max(8, window.innerHeight - box.height - 8);
        }
        if (nextLeft < 8) {
            nextLeft = 8;
        }
        if (nextTop < 8) {
            nextTop = 8;
        }
        pop.style.left = nextLeft + 'px';
        pop.style.top = nextTop + 'px';
    }

    function liftMorePopover(pop) {
        if (!pop || pop.dataset.lifted === '1') {
            return;
        }
        pop.dataset.lifted = '1';
        pop.classList.add('dashboard-cal-more-popover');
        window.requestAnimationFrame(function () {
            if (!pop.isConnected) {
                return;
            }
            var rect = pop.getBoundingClientRect();
            document.body.appendChild(pop);
            pop.style.position = 'fixed';
            pop.style.zIndex = '4000';
            pop.style.margin = '0';
            pop.style.width = Math.max(rect.width, 240) + 'px';
            pop.style.left = rect.left + 'px';
            pop.style.top = rect.top + 'px';
            placeMorePopover(pop, rect.left, rect.top);
            window.requestAnimationFrame(function () {
                if (pop.isConnected) {
                    placeMorePopover(pop, pop.getBoundingClientRect().left, pop.getBoundingClientRect().top);
                }
            });
        });
    }

    function watchMorePopover(root) {
        if (!root || typeof MutationObserver === 'undefined') {
            return;
        }
        var observer = new MutationObserver(function () {
            var pop = root.querySelector('.fc-more-popover');
            if (pop) {
                liftMorePopover(pop);
            }
        });
        observer.observe(root, { childList: true, subtree: true });
    }

    function initDashboardCalendar() {
        var el = calendarEl();
        if (!el) return;

        var tz = calendarTz();

        waitForFullCalendar(function () {
            var calendar = new FullCalendar.Calendar(el, {
                plugins: [
                    FullCalendarPlugins.dayGridPlugin,
                    FullCalendarPlugins.timeGridPlugin,
                    FullCalendarPlugins.interactionPlugin,
                ],
                initialView: 'dayGridMonth',
                timeZone: tz,
                firstDay: 1,
                height: '100%',
                // Only the weeks that belong to the month. A blank sixth week
                // made the grid taller than the card and turned on the scroller.
                fixedWeekCount: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay',
                },
                navLinks: true,
                nowIndicator: true,
                // Fit events to the day cell. Extras open in the +more popover
                // instead of stretching the row and scrolling the month.
                dayMaxEventRows: true,
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short',
                },
                events: function (fetchInfo, successCallback, failureCallback) {
                    fetchAppointments({
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr,
                    })
                        .then(function (payload) {
                            if (!payload || payload.success === false) {
                                failureCallback('Failed to load appointments');
                                return;
                            }
                            if (payload.stats) {
                                updateStats(payload.stats);
                            }
                            successCallback(payload.data || []);
                        })
                        .catch(function (err) {
                            failureCallback(err);
                        });
                },
                dateClick: function (info) {
                    focusUpcomingDate(info.dateStr);
                },
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    if (typeof window.openBookingAppointmentModal === 'function') {
                        window.openBookingAppointmentModal(info);
                        return;
                    }
                    var url = info.event.url || info.event.extendedProps?.detail_url;
                    if (url) {
                        window.open(url, '_blank', 'noopener');
                    }
                },
            });

            calendar.render();
            watchMorePopover(el);
            window.bookingFcCalendar = calendar;
            window.onBookingAppointmentChanged = function () {
                calendar.refetchEvents();
                loadUpcoming();
            };
            bindTypeSwitcher(calendar);
            loadUpcoming();

            window.addEventListener('resize', function () {
                calendar.updateSize();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardCalendar);
    } else {
        initDashboardCalendar();
    }
})();
