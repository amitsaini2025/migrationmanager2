# HTTP polling & Australia performance — migrationmanager2

**Purpose:** Inventory where this CRM uses HTTP polling (and related timers), summarize production Apache error-log findings for `migrationmanager.bansalcrm.com`, explain why staff in **Australia** can perceive slowness while **India** feels fine, and document a **safe** improvement order that does not break office visits, My Day file time, broadcasts, or client detail.

**Status:** Review / planning only — no changes applied from this document alone.  
**Sibling doc (bansalcrm2):** `bansalcrm2/docs/HTTP_POLLING_AND_AU_PERFORMANCE.md`  
**Related:** `docs/REVERB_PRODUCTION_NGINX.md`, `docs/client-detail-tab-performance-review.md`, `docs/crm-postgresql-laravel-performance-analysis.md`  
**Last reviewed:** 2026-09-26

---

## 1. Executive summary

| Finding | Detail |
|--------|--------|
| Main AU slowness driver | **Geographic latency** to origin server (likely India/Asia) × **many small HTTP requests** per page (polling + AJAX tabs) |
| Real-time stack | **Laravel Echo + Reverb** is implemented; polling is fallback — but office-visit poll still runs **always** every 10 s |
| Production error log (Sep 2025, ~1 h sample) | ~177k lines, **99.99% ModSecurity warnings**; **0** PHP/Laravel errors; **6** brief PHP-FPM drops |
| Safest fix order | Keep Echo working for AU → reduce redundant polls → infrastructure (region, PHP-FPM, ModSecurity) → DB/query work per `postgresql-performance-remediation-plan.md` |

**One-line diagnosis for AU staff:**

> Same app and server as India, but every background poll and AJAX call costs ~200–350 ms round-trip from Australia vs ~30–80 ms from India — and several endpoints poll continuously while staff work in client detail and dashboard.

---

## 2. Production Apache error log review (Sep 2025 sample)

**Source:** `migrationmanager.bansalcrm.com.error.log.part1` (~174 MB, ~177,575 lines, **09:05–10:05** on 25 Sep 2026).

| Category | Count | Notes |
|----------|-------|-------|
| ModSecurity warnings | ~177,569 | OWASP CRS 3.3.2 — not application errors |
| PHP Fatal / Warning / Notice | **0** | No Laravel crash evidence in this log |
| SQL / database errors | **0** | |
| Requests blocked by WAF | **0** | Warnings only; traffic still passed |
| PHP-FPM / FastCGI errors | **6** | ~09:38 and ~09:54 — brief backend disconnects |

### 2.1 ModSecurity noise (false positives)

Typical warnings on **normal** staff traffic (via Cloudflare):

- `cf_clearance` cookie flagged as SQL injection (`942421`)
- `CF-Visitor`, `sec-ch-ua`, `sec-ch-ua-mobile` flagged as invalid headers (`920274`)
- Normal `HTTP/1.1` and `GET` flagged as “not allowed by policy” (`920430`, `911100`) — suggests CRS variable / config issue

**Impact:** Huge log volume; small per-request overhead. **Not** the primary AU slowness cause.

### 2.2 Busiest URIs in that log (polling-heavy)

| Approx. hits | URI |
|-------------|-----|
| ~40k | `/fetch-office-visit-notifications` |
| ~25k | `/dashboard/my-day/sessions/blur` |
| ~21k | `/action/list` |
| ~20k | `/fetch-notification` |
| ~14k | `/dashboard/my-day/sessions/heartbeat` |
| ~12k | `/notifications/broadcasts/unread` |

These align with the browser polling inventory in §3.

### 2.3 Real outage signal

```
AH01067: Failed to read FastCGI header
AH01075: Connection reset by peer
AH02454: FCGI socket migratio.sock failed
```

Affected referrers: client detail (`/account`, `/nominationdocuments`), `/action`. **Brief, all regions** — not AU-specific.

---

## 3. HTTP polling inventory (browser → server)

### 3.1 Global — logged-in CRM pages (highest impact)

**File:** `resources/js/app.js` (Vite bundle on CRM layouts)

| What | Endpoint | Interval | Echo behaviour |
|------|----------|----------|----------------|
| Office visit notifications | `GET /fetch-office-visit-notifications` | **10 s** | Poll runs **always** as safety net; Echo also listens for `.OfficeVisitNotificationCreated` |
| Notification bell count | `GET /fetch-notification` | **30 s** | **Skipped** when `isCrmEchoConnected()`; runs when Echo down |

**UI handler:** `showTeamsNotification` in `resources/views/layouts/crm_client_detail.blade.php` and `crm_client_detail_dashboard.blade.php`.

**Echo setup:** Reverb via `VITE_REVERB_*` in same file; private channel `user.{id}`.

**Backend:** `app/Http/Controllers/CRM/CRMUtilityController.php` (`fetchOfficeVisitNotifications`, `fetchnotification`).

**Routes:** `app/Support/RegisterWebRoutes.php` — `/fetch-office-visit-notifications`, `/fetch-notification`.

---

### 3.2 Broadcast messages — CRM layouts

**Files:** `public/js/broadcasts.js`, loaded via `resources/js/layouts/crm-echo-broadcasts.js`

| What | Endpoint | Interval |
|------|----------|----------|
| Unread broadcasts (fallback) | `GET /notifications/broadcasts/unread` | **60 s** when Echo unavailable |
| On Echo event | Same endpoint | One-off fetch on `.BroadcastNotificationCreated` |

When Reverb connects, interval polling is **cleared** (`startRealtimeListener`).

**Backend:** `app/Http/Controllers/API/BroadcastNotificationController.php` (`unread` — “polling fallback”).

---

### 3.3 My Day — automatic file time

**Files:** `public/js/crm/clients/file-time-session.js`, `resources/views/partials/my-day-session-script.blade.php`

**Included on:**

- `resources/views/crm/clients/detail.blade.php`
- `resources/views/crm/companies/detail.blade.php`
- `resources/views/crm/leads/detail.blade.php`

| What | Endpoint | Interval |
|------|----------|----------|
| Focus heartbeat | `POST /dashboard/my-day/sessions/heartbeat` | **60 s** (focused tab) |
| Tab blur / leave | `POST /dashboard/my-day/sessions/blur` | On blur / visibility |

**Controller:** `app/Http/Controllers/CRM/StaffMatterSessionController.php`  
**Docs:** `docs/MY_DAY_AUTO_FILE_TIME_PORTING.md`, `docs/PLAN_MY_DAY.md`

---

### 3.4 Office visit popup — while notification is open

**Files:** `crm_client_detail.blade.php`, `crm_client_detail_dashboard.blade.php`

| What | Endpoint | Interval |
|------|----------|----------|
| Check-in status | `GET /check-checkin-status` | **5 s** per open popup |

Stops when status is no longer “waiting”.

---

### 3.5 Client detail — visa expiry alerts

**File:** `public/js/crm/clients/modules/visa-expiry.js`  
**Config:** `resources/views/crm/clients/detail.blade.php` → `fetchVisaExpiryMessages`

| What | Endpoint | Interval |
|------|----------|----------|
| Visa expiry toast/sound | `GET /fetch-visa_expiry_messages` | **15 min** |

---

### 3.6 Client edit — email verification

**File:** `public/js/clients/edit-client.js`

| What | Interval |
|------|----------|
| Email verify status check | **5 s**, max **2 min** per email |

Only while email section is open and address is unverified.

---

### 3.7 Booking / admin — full page refresh

| Page | File | Interval |
|------|------|----------|
| Booking sync dashboard | `resources/views/crm/booking/sync/dashboard.blade.php` | **Reload every 30 s** |
| Appointments list | `resources/views/crm/booking/appointments/index.blade.php` | **Reload every 5 min** |

---

### 3.8 Server-side scheduled jobs (not browser polling)

**File:** `app/Console/Kernel.php`

| Command | Schedule |
|---------|----------|
| `booking:sync-appointments --minutes=20` | Every **15 min** (Australia/Melbourne) |
| `my-day:close-stale-sessions` | Every **5 min** |
| `my-day:snapshot-summaries` | Daily 23:55 |
| `smart-email-import:clean` | Daily 03:00 |

---

### 3.9 High-traffic AJAX (not timer polling, but AU-sensitive)

Client detail and dashboard load many endpoints on demand (see error log and `docs/client-detail-tab-performance-review.md`):

- `/get-activities`, `/action/list`, `/clients/save-section`
- `/get-checkin-option-lists`, `/get-compose-option-lists`
- Lazy tab modules in `public/js/crm/clients/`

Each extra request × high AU latency adds perceived slowness beyond polling.

---

### 3.10 Timers that are NOT server polling

| File | Purpose |
|------|---------|
| `public/js/dashboard-my-day.js` | On-screen timer display |
| `public/js/scripts.js` | Scrollbar resize |
| `public/js/crm/clients/modules/notes.js` | Wait for dropdown DOM |
| `public/js/dashboard-calendar.js` | Wait for FullCalendar |
| `resources/views/crm/officevisits/index.blade.php` | Waiting-time clock |
| `edit-client.js` | OTP countdown UI |

---

## 4. Comparison with bansalcrm2

| Area | migrationmanager2 | bansalcrm2 |
|------|-------------------|------------|
| Office visit poll | **10 s** + Echo | **4 s**, Echo **not initialized** |
| Notification bell poll | **30 s** when Echo down | **Disabled** (commented interval) |
| Broadcast poll | **60 s** fallback | Not present |
| My Day heartbeat | Client / company / lead detail | Client + partner detail |
| Real-time | Reverb + Echo **active** | `BROADCAST_DRIVER` null; Echo commented out |
| Email inbox | Different stack | Elite inbox **10 s** poll |

**Implication:** migrationmanager2 is in a **better** position to reduce HTTP load once AU Echo/WebSocket connectivity is verified; bansalcrm2 depends more on raw polling today.

---

## 5. Why India is fine and Australia feels slow

### 5.1 Latency model

```
Staff browser  →  Cloudflare  →  Origin (PHP-FPM + PostgreSQL)
```

| Region | Typical round-trip to India-hosted origin |
|--------|-------------------------------------------|
| India | ~30–80 ms |
| Australia | ~200–350+ ms |

**Example — one admin minute with client detail open:**

| Source | Requests/min (approx.) | AU wait (250 ms each) |
|--------|------------------------|------------------------|
| Office visit poll | 6 | ~1.5 s |
| My Day heartbeat | 1 | ~0.25 s |
| Broadcast poll (if Echo down) | 1 | ~0.25 s |
| Notification poll (if Echo down) | 2 | ~0.5 s |
| Tab AJAX / lazy loads | 10–30+ | **2.5–7.5+ s** |

Same usage in India: roughly **4–5× faster** perceived response.

### 5.2 AU-specific amplifiers in this project

1. **Office visit poll always on** — even when Echo works (safety net never disabled).
2. **Echo/WebSocket from AU** — if Reverb connection fails or is slow, fallbacks add `fetch-notification` every 30 s and broadcast poll every 60 s. See `docs/REVERB_PRODUCTION_NGINX.md`.
3. **Client detail weight** — many sequential AJAX calls; see `docs/client-detail-tab-performance-review.md`.
4. **PostgreSQL query time** — affects all users but stacks with latency; see `docs/postgresql-performance-remediation-plan.md`.

### 5.3 What it is not

- Not ModSecurity blocking AU users (sample log: 0 blocks).
- Not evidence of Laravel/PHP errors in the sampled Apache log.
- Not a separate “Australia-only” application bug.

---

## 6. Safe improvement plan (do not skip steps)

Apply in order so **existing features keep working**.

### Phase A — Measure (no behaviour change)

- [ ] Confirm origin server region and AU vs IN latency (`curl -w`, traceroute, or hosting panel).
- [ ] Browser DevTools → Network: requests/min on dashboard, client detail, with Echo connected vs disconnected.
- [ ] Verify production `VITE_REVERB_*`, Nginx/WebSocket proxy (`docs/REVERB_PRODUCTION_NGINX.md`).
- [ ] Console: `window.Echo`, connection state from AU office.
- [ ] PostgreSQL slow queries during peak (see performance docs).

### Phase B — Real-time first (AU staff)

| Action | Why safe |
|--------|----------|
| Fix Reverb/WebSocket path for AU (Nginx, TLS, firewall) | Reduces fallback polling |
| Keep office-visit HTTP poll as fallback | Reception still gets alerts if WS drops |
| Rely on `showTeamsNotification` dedupe by `id` | No double popups when Echo + poll both fire |

**Do not** remove office-visit polling until Echo is stable for AU for several days.

### Phase C — Reduce redundant polls (after Phase B)

| Current | Suggested | Feature preserved |
|---------|-----------|-------------------|
| Office visits **10 s always** | Poll only when `!isCrmEchoConnected()` OR increase to **20–30 s** as safety net | Echo primary; poll backup |
| Notification **30 s** when Echo down | Keep; ensure Echo up in AU | Bell count + toasts |
| Broadcast **60 s** fallback | Keep; stops when Echo connected | Broadcast popups |
| Check-in popup **5 s** | **8–10 s** acceptable | Popup auto-close |
| Booking sync page **30 s** reload | Manual refresh + longer interval | Sync dashboard still updates |
| My Day heartbeat **60 s** | Keep initially | File time accuracy |

### Phase D — Infrastructure & WAF

- [ ] Server or edge closer to AU users (largest win for all requests).
- [ ] PHP-FPM: workers, memory, slow log — address Sep 2025 FastCGI drops.
- [ ] ModSecurity: Cloudflare header/cookie exclusions; log blocks only; fix `tx.allowed_*` CRS setup.
- [ ] Cloudflare: cache static assets; dynamic CRM routes still hit origin.

### Phase E — Application / database (see other docs)

- [ ] Client detail tab lazy-load and query batching (`client-detail-tab-performance-review.md`).
- [ ] PostgreSQL indexes and N+1 fixes (`postgresql-performance-remediation-plan.md`).

### Phase F — What not to change without regression testing

| Area | Risk |
|------|------|
| `attend_session` / office visit status flow | Reception workflow breaks |
| `mark-notification-seen` | Stuck or duplicate popups |
| My Day `file-time-session.js` idle / heartbeat | Incorrect focus minutes |
| Broadcast Echo + unread fetch chain | Missing broadcast popups |
| `booking:sync-appointments` schedule | Missed website appointments |

---

## 7. AU regression test checklist (before/after any change)

Run from an Australian network (or VPN) and from India.

| # | Scenario | Pass criteria |
|---|----------|---------------|
| 1 | Reception: new office visit | Popup within acceptable delay; Echo + poll deduped |
| 2 | Assignee: attend / check-in actions | Status updates; popup closes |
| 3 | Notification bell | Count updates live via Echo; fallback if WS disabled |
| 4 | Broadcast message sent | Popup on AU client; no duplicate |
| 5 | Client detail: 3+ min on tab | My Day heartbeat + blur behave correctly |
| 6 | Client detail: open heavy tabs | Acceptable load time; no new errors |
| 7 | Echo disconnected (simulate) | Office visit + bell fallbacks still work |
| 8 | Booking sync dashboard | Still refreshes data |

---

## 8. File reference index

| Topic | Path |
|-------|------|
| Office visit + bell polling + Echo | `resources/js/app.js` |
| Broadcast polling / Echo | `public/js/broadcasts.js`, `resources/js/layouts/crm-echo-broadcasts.js` |
| Office visit UI + 5 s check-in | `resources/views/layouts/crm_client_detail.blade.php`, `crm_client_detail_dashboard.blade.php` |
| CRM utility API | `app/Http/Controllers/CRM/CRMUtilityController.php` |
| Routes | `app/Support/RegisterWebRoutes.php` |
| My Day session JS | `public/js/crm/clients/file-time-session.js` |
| My Day partial | `resources/views/partials/my-day-session-script.blade.php` |
| Visa expiry poll | `public/js/crm/clients/modules/visa-expiry.js` |
| Email verify poll | `public/js/clients/edit-client.js` |
| Reverb production | `docs/REVERB_PRODUCTION_NGINX.md` |
| Client detail performance | `docs/client-detail-tab-performance-review.md` |
| PostgreSQL performance | `docs/postgresql-performance-remediation-plan.md` |
| bansalcrm2 counterpart | `../bansalcrm2/docs/HTTP_POLLING_AND_AU_PERFORMANCE.md` |

---

## 9. Priority summary

| Priority | Item | Effort | AU impact |
|----------|------|--------|-----------|
| 1 | Reverb/WebSocket reliability from AU | Medium | High — cuts fallback polls |
| 2 | Origin closer to AU / CDN for static | Infra | Highest — every request |
| 3 | Office visit poll only when Echo down (or slower safety net) | Low | Medium–high |
| 4 | PHP-FPM + ModSecurity tuning | Infra | Medium (stability + logs) |
| 5 | Client detail + PostgreSQL optimizations | Medium–high | High on detail pages |
| 6 | Booking page reload intervals | Low | Low (admin-only views) |

---

*This document is for planning and handoff. Implement changes in small PRs with the checklist in §7.*
