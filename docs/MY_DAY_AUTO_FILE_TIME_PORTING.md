# My Day + Automatic Time-on-File — Porting Guide

**Purpose:** implement the same “My Day” dashboard pattern (especially **silent automatic time on client/lead/company files**) on another education CRM.  
**Source of truth in this repo:** shipped 15 Sep 2026 (Phases A–D). Product plan: `docs/PLAN_MY_DAY.md`. Prototype: `docs/prototypes/my-day-proposed.html`.

This document is written so you can rebuild the feature without depending on migration-law domain names. Map terms once, then follow the architecture and rules below.

---

## 1. What you are building

Staff open a **student / enquiry / institution file** in the CRM. While that browser tab is focused, the system quietly accrues time. When they leave (or the tab dies), minutes are attributed to that file and explained by the CRM writes they already made (notes, emails, uploads, stage moves, bookings, SMS).

On the personal dashboard, **My Day** shows:

| Block | Source | Staff action |
|--------|--------|--------------|
| Hours in CRM | Login / session presence | None (header only) |
| Already in CRM | Union of today’s CRM writes by this staff | Read-only |
| Time on files (auto) | Focused-tab sessions on detail pages | Edit minutes / delete only if not posted |
| Files opened | Sessions still `accessed` (opened, no promote yet) | Read-only |
| Manual logs | Off-CRM work staff choose to log | Modal: matter or Admin + minutes + kind |
| End-of-day summary | Server-built plain text | Copy (and optionally save for managers) |

**Workload KPI cards stay untouched.** Auto/manual minutes must never inflate “Completed / Pending / Call completed” style metrics.

---

## 2. Domain mapping (migration CRM → education CRM)

| This repo | Typical education CRM | Role in the design |
|-----------|----------------------|--------------------|
| `staff` (`auth:admin`) | Counsellor / admissions officer / agent user | Writer of time and CRM events |
| `admins` / client / lead / company | Student / enquiry / partner / institution record | The **person or org file** being viewed |
| `client_matters` (visa/matter file) | Application / enrolment / course case / program file | Optional **case** under a person; unit of timed work when selected |
| Matter ref (`client_unique_matter_no`) | Application ref / student ID + program code | Human-readable label in UI + feed |
| Call / In-person file notes | Call / campus visit / counselling notes | CRM events that **promote** a session |
| Immi / mailbox presets (manual only) | PRISMS / provider portal / Outlook skim | Manual overlay kinds only — **not** auto |
| `activities_logs` feed | Student activity timeline | Receives one `file_time` row per recorded session |

**Rule that ports cleanly:** time attaches to the **case** when one is selected; otherwise to the **person/org record**. One student with two applications = two auto sessions if both are opened.

---

## 3. Architecture (keep layers separate)

```
Detail page (student / enquiry / institution)
  └── file-time-session.js
        POST heartbeat / blur / idle-cut
              ↓
        StaffMatterSessionService
              ↓ promote via StaffDayCrmEventsService::forStaffOnRecord()
              ↓ close via scheduled my-day:close-stale-sessions
              ↓ post activities_logs activity_type = file_time

Dashboard My Day
  ├── StaffDayHoursService          → “Hours in CRM”
  ├── StaffDayCrmEventsService      → “Already in CRM” (read-only)
  ├── StaffMatterSessionService     → auto + opened
  └── StaffFileTimeService          → manual overlay + copy summary text
```

**Two tables, never merged:**

| Table | Lifecycle | Has kind/title? | One-running timer? |
|-------|-----------|-----------------|--------------------|
| `staff_matter_sessions` | Silent focus sessions | No | No (many open files OK) |
| `staff_file_time_entries` | Manual / logged overlay | Yes | Yes (at most one `is_running` per staff) |

Auto sessions **must not** pause manual timers and **must not** write into the overlay table.

---

## 4. Non-negotiable product rules

1. **No double-logging for in-CRM work.** If the staff wrote a note/email/upload while the file was open, auto time covers it. Manual presets must not duplicate “sent email”, “created note”, etc.
2. **Credit the writer only.** Sessions and events use the logged-in staff id. Never credit a colleague for someone else’s tab.
3. **Focused time only.** Count only while `document.hasFocus() && visibilityState === 'visible'`. Blur / tab switch / close stops counting immediately (no grace). Gap is not credited; refocus continues the same day session.
4. **Idle cut.** 15 min no input while focused → “Still on {ref}?”. No answer in 2 min → cut seconds back to idle start.
5. **Promotion, not button.** Session starts `accessed`. Becomes `recorded` when any CRM write by this staff on this record lands in `[started_at, last_heartbeat_at]`, **or** after ≥ 2 minutes focused with no write (“reviewed file”).
6. **Under 2 min + no write stays `accessed`.** Shows under “Files opened”, no feed post.
7. **Feed only for `recorded` (then closed).** One timeline row per session; never for pure `accessed`.
8. **Personal view only.** No “view as colleague” on the dashboard. Manager reports (if any) use a stored end-of-day snapshot, not live peeking.
9. **Workload cards CRM-only.** `file_time` feed rows use `task_status = 0` and must be excluded from completed-action queries.

---

## 5. Automatic time-on-file (deep dive)

This is the core of the feature and what you should port first if you only ship one layer.

### 5.1 Session identity

One row per:

```
staff_id × client_id × matter_key × session_date
```

- `session_date` = business timezone date (this repo: `Australia/Melbourne` via `dayBounds()`).
- `matter_key` = `COALESCE(client_matter_id, 0)` so nullable matter is unique-safe across Postgres and SQLite.
- Reopening the same file later the same day **continues** the same row (focused seconds only ever increase via `max(existing, incoming)`).
- Switching matter on a multi-case student detail page = **new record**: flush blur for old matter, reset local accumulator, heartbeat for new matter.

### 5.2 Status machine

```
accessed ──write in window──► recorded ──stale/close──► closed (+ feed if minutes ≥ 1)
    │                              ▲
    └── ≥120s focused, no write ───┘   (is_reviewed_only = true)
```

Same-day heartbeat after close **reopens** to `recorded` or `accessed` (clears `ended_at`). Overnight dead tabs are handled by stale heartbeat, not a midnight job.

### 5.3 Heartbeat contract

| Constant | Value | Meaning |
|----------|-------|---------|
| Heartbeat interval | 60s | While focused |
| Stale threshold | 180s | No heartbeat → ended at `last_heartbeat_at` |
| Reviewed-only | 120s | Focused, no write → still `recorded` |
| Idle warning | 15 min | No mouse/keyboard/scroll/click/touch |
| Idle grace | 2 min | Then `idle-cut` |

**Client** maintains a local `focusedSeconds` accumulator (`requestAnimationFrame` while focused). Each heartbeat sends the **total for that session today**, not a delta. Server stores `max(db, client)`.

**Blur / `pagehide`:** prefer `navigator.sendBeacon` with `FormData` including `_token` (CSRF). Fallback to `fetch` + keepalive.

**Multi-tab:** `BroadcastChannel('my-day-session')` — when another tab gains focus on a *different* record, stop local tick (server still authoritative via last heartbeat).

### 5.4 Promotion (server-side only)

On heartbeat (and again just before close):

1. Query CRM events for this staff + record + window `[started_at, last_heartbeat_at]`.
2. If any event → `recorded`, `is_reviewed_only = false`.
3. Else if `focused_seconds >= 120` → `recorded`, `is_reviewed_only = true`.
4. Else stay `accessed`.

**Do not** hang promotion off every write controller. Detect on heartbeat/close by reusing the same event union the dashboard already lists.

**Attribution rule for notes without a case id:** if the session has a selected matter, events with `client_matter_id` null on that student still count for the open matter. Events with a *different* matter id do not.

### 5.5 Closing and feed post

Scheduled every 5 minutes:

```
my-day:close-stale-sessions
→ StaffMatterSessionService::closeStale(now())
```

For each non-closed row with `last_heartbeat_at < now − 180s`:

1. Run `promoteIfWritten` again (note saved seconds before tab death must count).
2. `ended_at = last_heartbeat_at` (not `now()`).
3. `confirmed_minutes = round(focused_seconds / 60)` if unset.
4. If was `recorded` and minutes ≥ 1 → post/update feed row.
5. Status → `closed`.

Feed subject shapes:

- With activities: `logged {N}m on {ref} · {count} activities`
- Reviewed only: `logged {N}m on {ref} · reviewed file`

`activity_type = file_time`, `created_by = staff_id`, `task_status = 0`, `pin = 0`. Matter sessions set `use_for = matter` when that column exists. Editing minutes after post **updates** the same feed row.

### 5.6 Minutes split across CRM events (display only)

At **read time** for the board / copy summary:

- Load events for the session window.
- Sort stably by `sort_at`, then event key.
- Split `confirmed_minutes` evenly: `base = intdiv(N, count)`, remainder goes to the **first** event so the sum is exact.
- Store nothing per-event in the DB.

These chips also decorate “Already in CRM” rows when event keys match.

### 5.7 Detail-page wiring

Emit once on every record detail view (student, enquiry, institution):

```js
window.MyDaySession = {
  clientId: <record id>,
  clientMatterId: <case id or null>,
  ref: '<human label>',
  csrf: '<token>',
  routes: {
    heartbeat: '.../sessions/heartbeat',
    blur: '.../sessions/blur',
    idleCutBase: '.../sessions'  // append /{id}/idle-cut
  }
};
```

Load a **standalone** script (no dependency on your main detail bundle). Tracking must never throw into the page UI — swallow network errors.

**Access control:** heartbeat/blur must re-check the staff can open that record (same gate as the detail page). Ownership checks on update/delete/idle-cut by `session.staff_id`.

---

## 6. Manual overlay (second layer)

Use only for work the CRM **cannot** see: drafting offline, government portals, mailbox skim *on this file*, internal discussion, Admin/no-file admin work.

| Field | Notes |
|-------|--------|
| `kind` | Domain-specific presets (draft / docs / mailbox / internal / other…). Education: PRISMS, provider portal, etc. |
| `title` | Required short text |
| `client_matter_id` | Nullable; null + admin flag = Admin row |
| `status` | `doing` / `parked` / `done` (or skip live timer and only “log completed minutes”) |
| `confirmed_minutes` | 1–480 at Done |
| `is_running` | Partial unique index: one true per staff |

On Done with a linked case → post `file_time` to that case’s feed. Admin rows stay on My Day only (no feed).

This repo also supports a **log modal** that creates a completed entry in one step (no running clock) — preferred UX for end-of-day catch-up.

---

## 7. “Already in CRM” event union

Read-only service. Cap list (e.g. 50) with “and N more”.

Typical sources (writer = this staff, today in business TZ):

| Kind | Source examples |
|------|-----------------|
| Email | Sent / attributed mail logs (exclude system-generated / fetched inbound) |
| Document | Uploads created by staff |
| Booking | Appointments created by staff |
| SMS | Outbound SMS by staff |
| Stage / feed | Activity feed rows staff wrote (stage moves, EOI, etc.) |
| Contact note | Call / visit / counselling notes (not assigned tasks) |

Each event row shape used downstream:

```
key, kind, title, time, sort_at, client_id, client_matter_id?, ref?
```

`forStaffOnRecord(...)` filters the same union by record (+ matter-null attribution rule). Keep `Schema::hasTable` guards so unit tests can omit unused tables.

---

## 8. Schema to copy

### 8.1 `staff_matter_sessions` (auto)

| Column | Type | Purpose |
|--------|------|---------|
| `id` | PK | |
| `staff_id` | FK staff, cascade | Writer |
| `client_id` | unsigned int | Person/org record |
| `client_matter_id` | nullable FK case | Selected case |
| `matter_key` | unsigned int default 0 | `COALESCE(matter, 0)` for uniqueness |
| `session_date` | date | Business day |
| `status` | string(16) | `accessed` \| `recorded` \| `closed` |
| `focused_seconds` | unsigned int | Accrued focus |
| `idle_cut_seconds` | unsigned int | Audit of idle removals |
| `confirmed_minutes` | smallint nullable | Set at close; editable |
| `event_count` | smallint nullable | Snapshot for feed subject |
| `is_reviewed_only` | bool | Rule: ≥2m no write |
| `started_at` / `last_heartbeat_at` / `ended_at` | timestamps | |
| `activities_log_id` | nullable unique FK | Feed row |
| timestamps | | |

Indexes:

- **Unique** `(staff_id, client_id, matter_key, session_date)`
- `(staff_id, session_date)`
- `(last_heartbeat_at)` for closer
- Unique `activities_log_id`

Create/find under `lockForUpdate()` so concurrent heartbeats do not duplicate.

### 8.2 `staff_file_time_entries` (manual)

| Column | Purpose |
|--------|---------|
| `staff_id`, `client_matter_id?`, `client_id?` | Writer + target |
| `kind`, `title` | Preset + text |
| `status`, `is_running` | Timer state |
| `clock_seconds`, `confirmed_minutes` | Helper clock vs published minutes |
| `started_at`, `completed_at` | |
| `activities_log_id` | Feed when posted |

Partial unique: `(staff_id) WHERE is_running = true` (Postgres/SQLite).

### 8.3 Optional `staff_day_summaries`

Store the copied end-of-day text for manager/admin workload views. Snapshot nightly if you need historical Teams dumps without recompute.

---

## 9. HTTP API surface

All authenticated as CRM staff. JSON.

### Auto sessions

| Method | Path | Body / notes |
|--------|------|--------------|
| POST | `/dashboard/my-day/sessions/heartbeat` | `client_id`, `client_matter_id?`, `focused_seconds` (0–86400) |
| POST | `/dashboard/my-day/sessions/blur` | Same (beacon-friendly) |
| POST | `/dashboard/my-day/sessions/{id}/idle-cut` | `idle_started_at` ISO |
| PATCH | `/dashboard/my-day/sessions/{id}` | `confirmed_minutes` 1–480 |
| DELETE | `/dashboard/my-day/sessions/{id}` | Only if `activities_log_id` null |

### Manual overlay

| Method | Path | Notes |
|--------|------|-------|
| GET | `/dashboard/my-day` | Board + sessions + events payload |
| POST | `/dashboard/my-day/file-time` | Start timer |
| POST | `/dashboard/my-day/file-time/log` | One-shot completed log |
| POST | `.../pause|park|resume|done|reopen` | Lifecycle |
| PATCH / DELETE | `.../file-time/{entry}` | Open only for delete |

### Summary

| Method | Path | Notes |
|--------|------|-------|
| GET | `/dashboard/my-day/copy-summary` | Plain text + structured sections |
| POST | `/dashboard/my-day/copy-summary` | Optional save snapshot |
| GET | `/dashboard/my-day/matter-search?q=` | Typeahead; respect access rules |

---

## 10. Dashboard UI composition

Place **My Day below** existing workload metrics, **before or after** the personal calendar (this repo: after workload strip).

Recommended sections (order):

1. Header: title + Hours in CRM + “+” manual log
2. Already in CRM (list + optional minutes chips)
3. Split: Time on files (auto) | Files opened
4. Collapsible End-of-day: manual logs list + `<pre>` summary + Copy

CSS: keep tokens aligned with the dashboard (dedicated stylesheet). Distinct classes for auto vs manual vs opened (e.g. `.my-day-auto`, `.my-day-opened`).

JS on dashboard: one script owning state from `data-initial-*` attributes; PATCH minutes on blur of inline inputs; never re-render workload cards on heartbeat.

---

## 11. Copy-summary text contract

Server builds the string so clipboard matches storage:

```
{Staff name} — {date}
Hours in CRM: …

— Already in CRM —
{ref} · {kind} · {title} · {time} · {Nm?}

— Manual logs —
{ref} · {kind} · {title} · {Nm}

— Admin / no file —
…

— Time on files (auto) —
{ref} · {Nm} · {N activities|reviewed file}

— Files opened —
{ref} · {Nm} (open)

— Still open —
{ref} · {kind} · {title} · {status}
```

Minutes on CRM event lines prefer auto-session splits; otherwise share remaining record minutes across that file’s events (deterministic sort).

---

## 12. Scheduler / ops

| Command | Schedule | Job |
|---------|----------|-----|
| `my-day:close-stale-sessions` | every 5 minutes, withoutOverlapping | Close dead heartbeats + feed post |
| `my-day:snapshot-summaries` (optional) | nightly | Persist copy text for managers |

Ensure the app scheduler actually runs in the target environment (cron / supervisor). Without the closer, sessions stay open forever and never post.

---

## 13. Suggested build order (green at each step)

1. **Schema + models + factories** for sessions (and overlay if shipping both).
2. **CRM events service** (`forStaff` + `forStaffOnRecord`) + unit tests.
3. **Session service** (heartbeat, blur, idleCut, promote, closeStale, board, feed) + unit tests — no UI yet.
4. **Closer command + schedule**.
5. **Routes + Form Requests + controller** + feature auth tests.
6. **Detail script + Blade config** on student / enquiry / institution pages — sessions start accruing silently.
7. **Dashboard Blade/CSS/JS** — staff see auto + opened + copy.
8. **Manual overlay** (if needed) — modal + board + feed on Done.
9. **Docs / activity-feed icon** for `file_time`; confirm workload queries exclude it.

Ship 1–4 without UI if you want a safe merge: sessions simply do not get created until step 6.

---

## 14. Porting checklist for another education CRM

### Must map

- [ ] Staff auth guard and `staff_id` column
- [ ] Record primary key used on detail pages (`client_id` analogue)
- [ ] Optional case/application id when a sub-file is selected
- [ ] Business timezone + day bounds helper (one place)
- [ ] Activity / timeline table and how to insert without marking “task complete”
- [ ] Record access gate reused on heartbeat
- [ ] List of write sources that count as “CRM work” for promotion

### Must decide

- [ ] Preset kinds for **manual** logs (education-specific)
- [ ] Whether leads/enquiries and partners share one `client_id` table or need polymorphic `record_type`
- [ ] Manager visibility: personal-only vs stored day summaries
- [ ] Live timer UI for manual vs log-minutes-only modal
- [ ] Idle thresholds (15m / 2m) — keep unless staff culture differs

### Must not do

- [ ] Do not store auto time inside notes/actions tables
- [ ] Do not let `file_time` inflate KPI / workload cards
- [ ] Do not require a Start button for in-CRM file time
- [ ] Do not count background tabs or blurred windows
- [ ] Do not post feed rows for `accessed`-only opens

### Education-specific gotchas

- Multiple applications per student → treat like multiple matters; matter switcher must flush sessions.
- Shared “family / agent” records → still credit the **logged-in** staff only.
- Bulk list pages and search → **no** session (only detail pages).
- Embedded iframes / external portals opened from a file → still only count while the CRM tab is focused (portal in another tab is manual overlay).

---

## 15. Edge cases learned from this implementation

1. **Matter id vs matter ref.** Detail pages often expose a human ref string; heartbeats need the **numeric case id**. Resolve from the matter `<select>` value, not from the label.
2. **`sendBeacon` and CSRF.** Beacons cannot set custom headers; put `_token` in the form body.
3. **Focused seconds never decrease** except via explicit idle-cut. Clients that send lower totals after refresh must not wipe accrued time — use `max()`.
4. **Stale close uses last heartbeat, not now.** Otherwise laptop-sleep over-credits.
5. **Promote again at close.** Last write can land after the final heartbeat.
6. **Reviewed-only feed** still needs a synthetic event when splitting minutes for display.
7. **Partial unique `is_running`** needs DB support (Postgres/SQLite); enforce in service with `pauseAllRunning` either way.
8. **Company/partner detail** may reuse client config objects — confirm `clientId` is the partner’s primary key before wiring the script.
9. **Silent failures.** Tracking JS must not break counselling workflows; fail closed (no toast on network error).
10. **Tests without `RefreshDatabase`.** Build minimal sqlite `:memory:` schema in `setUp` (staff, records, cases, notes/docs/activities, sessions). Freeze time with `Carbon::setTestNow`.

---

## 16. Test matrix (minimum)

### Auto sessions

- Heartbeat creates one row per staff/record/day; second heartbeat updates, does not duplicate.
- Stale close ends at `last_heartbeat_at`.
- Idle cut reduces `focused_seconds` and increments `idle_cut_seconds`.
- Note/document inside window → `recorded`; outside → stays `accessed`.
- ≥ 120s no write → `recorded` + `is_reviewed_only`; &lt; 120s → `accessed`.
- Close `recorded` posts `file_time`; close `accessed` does not.
- Even split of minutes across N events sums to `confirmed_minutes`.
- Guest cannot hit routes; staff A cannot mutate staff B’s session.
- Auto never sets `is_running` on manual table; never changes workload tallies.

### Overlay / dashboard

- Manual Done with case → feed; Admin Done → no feed.
- Second start pauses previous running timer.
- Copy summary includes CRM + auto + manual + opened sections.
- Markup: workload strip still present; My Day mounts.

---

## 17. Source map in this repository

| Concern | Path |
|---------|------|
| Auto session service | `app/Services/StaffMatterSessionService.php` |
| CRM event union | `app/Services/StaffDayCrmEventsService.php` |
| Manual overlay + copy text | `app/Services/StaffFileTimeService.php` |
| Hours header | `app/Services/StaffDayHoursService.php` |
| Session HTTP | `app/Http/Controllers/CRM/StaffMatterSessionController.php` |
| Overlay HTTP | `app/Http/Controllers/CRM/DashboardMyDayController.php` |
| Routes | `app/Support/RegisterWebRoutes.php` (`/dashboard/my-day/*`) |
| Models | `app/Models/StaffMatterSession.php`, `StaffFileTimeEntry.php` |
| Migrations | `database/migrations/2026_09_15_*staff_matter_sessions*`, `*staff_file_time_entries*` |
| Detail tracker | `public/js/crm/clients/file-time-session.js` |
| Detail bootstrap | `resources/views/partials/my-day-session-script.blade.php` |
| Dashboard UI | `resources/views/components/dashboard/my-day.blade.php` (+ `file-time-auto`, `files-opened`, `crm-events`) |
| Dashboard JS/CSS | `public/js/dashboard-my-day.js`, `public/css/dashboard-my-day.css` |
| Closer | `app/Console/Commands/CloseStaleMatterSessions.php` |
| Schedule | `app/Console/Kernel.php` |
| Unit tests | `tests/Unit/Services/StaffMatterSessionServiceTest.php`, `StaffDayCrmEventsServiceTest.php`, `StaffFileTimeServiceTest.php` |
| Feature tests | `tests/Feature/StaffMatterSessionRoutesTest.php`, `DashboardMyDayRoutesTest.php`, `DashboardWorkloadMarkupTest.php` |
| Product plan | `docs/PLAN_MY_DAY.md` |

---

## 18. One-paragraph mental model

**My Day** is a personal diary layered on top of existing CRM writes: a read-only “what I already did” list, silent focused-tab time on open files that promotes when those writes happen (or after a short review), optional manual minutes for off-CRM work, and a copy-paste summary for Teams — without ever asking staff to re-log in-CRM work or letting timers rewrite managerial workload KPIs.

When porting to an education CRM, keep that mental model, rename the entities, and implement **auto sessions first**; the dashboard and manual overlay are presentation and catch-up around the same core.
