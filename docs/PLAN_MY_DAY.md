# Plan: My day (CRM events + time-on-file overlay)

**Status:** implemented (Phases A+B).  
**Prototype:** `docs/prototypes/my-day-proposed.html` (copy in Downloads: `my-day-proposed.html`).  
**Related:** `docs/STAFF_WORKLOAD_EFFICIENCY.md`, `docs/CRM_ACTIVITY_FEED.md`, live `StaffWorkloadService` cards.

This plan is the product in that HTML, not the earlier sticky-notes prototype that **added** note counts onto Completed / Updated / Pending / Call completed.

---

## 1. Problem

Staff already write a daily Teams dump (mailbox windows, 3CX, WhatsApp, Immi, Excel, internal chats, minutes per file). The dashboard **My Workload — Today** strip only counts assigned **actions** and Call/In-person **file notes**. A busy processing day can show Completed 0 / Call completed 1 while the Teams post is a page long.

Two failures to avoid:

1. Asking staff to **double-log** work the CRM already stored (uploads, sends, bookings, completed actions).
2. Letting a personal timer **rewrite** the four workload cards so managers no longer know what the numbers mean.

---

## 2. Product rules (non-negotiable)

1. **Workload cards stay CRM-only.** `StaffWorkloadService` metrics do not include overlay time, overlay “done” counts, or timer pauses. Clicking a card still opens the existing drill-down, not the time board.
2. **Hours in CRM are a header**, not a score. Reuse session / `staff_login_logs` / `TrackStaffCrmActivity` presence. Do not score efficiency %.
3. **Credit the writer.** Overlay rows and auto events use `created_by` / `staff_id` of the logged-in staff (`auth:admin`). Ghost credit for MA/PR/PA is out.
4. **Matter is the unit of file time.** Time attaches to `client_matters.id` (reference like `JARN2504926-…`), not to the person. One client with 491 and 482 is two rows.
5. **Admin / no file is allowed.** Mailbox skim, 3CX list, Teams chats, Excel not on a file. That time **must not** post to a client activity feed.
6. **Manual overlay is only for work CRM cannot see:** drafting, Immi/portal, off-CRM doc check, mailbox *on this file*, internal discussion, other (Excel, 3CX on a file, etc.). Do **not** use presets that duplicate CRM creation, email send, or Call/In-person file notes. Time *inside* the CRM on a file is the separate **auto session** layer (§12), never a manual preset.
7. **Confirmed minutes at Done are stored.** A live clock is a helper. Staff edit approx minutes (Rajan’s habit). No silent overnight accumulation. Auto sessions (§12) store focused minutes as-is, editable on the board; idle and dead-tab time is cut.
8. **One running timer per staff** for the manual overlay. Starting or resuming one pauses every other. Auto sessions are outside this rule and never pause a manual block.
9. **Post overlay to the matter feed automatically on Done** when a matter is linked. No batch “post all” button. Admin rows stay on My day only.
10. **Personal view only.** No team table, no ranking, no `staff_id` query to view a colleague. Same as the efficiency spec.

When this ships, amend `docs/STAFF_WORKLOAD_EFFICIENCY.md`: duration is **out** for Call/In-person **file notes**; duration is **in** only as this overlay layer, and it is **not** Layer A contact.

---

## 3. What ships (user-visible)

On the existing dashboard (`crm/dashboard-optimized.blade.php`), **below** the current workload strip (and without replacing the calendar):

| Block | Behaviour |
|--------|-----------|
| Hours in CRM | Header chip for today (Melbourne). |
| My Workload — Today | Unchanged four cards + legend. |
| Already in CRM | Read-only list of today’s events this staff wrote (email, document, booking, SMS, stage, completed actions, Call/In-person notes). |
| Time on a file | Matter search **or** Admin / no file → presets → Doing / Parked / Done. Done opens minutes confirm. |
| Time by matter | Overlay minutes only, filter the board by matter or Admin. |
| Overlay tally | Confirmed minutes, files timed, still open, Admin minutes. Bars by overlay kind. |
| Copy summary | Plain text: hours + CRM events + overlay minutes + Admin + still open. Replaces the Teams paste. |

Not in v1: 3CX CDR ingest, WhatsApp Business, Immi Account API, live second-sync of the whole page, fifth “In-person meet” workload card.

---

## 4. Architecture

Keep overlay storage **separate** from `notes` (actions/file notes) so timers cannot be mistaken for contact or assigned work.

```
DashboardController@index
  StaffWorkloadService          → cards (unchanged)
  StaffDayCrmEventsService      → read-only union of existing tables
  StaffDayHoursService          → header (thin wrap of login/session)
  StaffFileTimeService          → overlay CRUD + one-running + Done → feed

New table: staff_file_time_entries
```

Follow current CRM patterns: service + Form Request + `auth:admin` routes next to other `/dashboard/*` names in `RegisterWebRoutes.php`. No new top-level app folder. Use `LogsClientActivity` (or a dedicated writer that **always sets `activity_type`**) so overlay feed rows do not fall into the DB default `'note'`.

### 4.1 Table `staff_file_time_entries`

| Column | Purpose |
|--------|---------|
| `id` | PK |
| `staff_id` | FK `staff.id`, writer |
| `client_matter_id` | FK `client_matters.id`, **nullable** (null = Admin) |
| `client_id` | `admins.id`, denormalised when matter is set (feed `client_id`) |
| `kind` | `draft` \| `immi` \| `docs` \| `mailbox` \| `internal` \| `other` |
| `title` | Required short text |
| `status` | `doing` \| `parked` \| `done` |
| `is_running` | boolean; at most one `true` per `staff_id` (PostgreSQL **partial unique** index) |
| `clock_seconds` | Helper elapsed; not the published figure |
| `confirmed_minutes` | Nullable until Done; integer 1–480 |
| `started_at` / `completed_at` | Timestamps |
| `activities_log_id` | Nullable FK after feed post; unique when set |
| `created_at` / `updated_at` | |

Indexes: `(staff_id, created_at)`, `(staff_id, status)`, `(client_matter_id, staff_id)`, partial unique `(staff_id) WHERE is_running = true`.

Model: normal Eloquent `StaffFileTimeEntry` (do not extend `Authenticatable`). Factory for tests. Soft deletes **optional**; prefer hard delete only while `status !== done`, and block delete of posted Done rows (or reopen clears feed row — pick one and test it). **Recommendation:** Done + posted cannot delete; Reopen sets `status = doing`, `confirmed_minutes = null`, `is_running = true`, and **does not** delete the feed row (append “reopened” or leave history). Simpler v1: Reopen allowed only if feed post is same day; otherwise leave Done.

### 4.2 Activity feed write (Done + matter)

New `activity_type`: **`file_time`** (≤64 chars). Document it in `docs/CRM_ACTIVITY_FEED.md` (icon map + filter). Subject example: `logged 11m drafting on JARN2504926-485_1` — **no client display name** in subject (feed prepends staff). Description: title + kind + confirmed minutes. `created_by` = staff. `client_id` = matter’s client. `use_for` = `matter`. `task_status` = 0, `pin` = 0.

Admin / no file: **no** `activities_logs` row.

**Do not** set `task_status = 1`. Overlay Done must not appear in `StaffWorkloadService` completed-action queries (`subject LIKE 'completed action for%'`).

### 4.3 Auto CRM events (read-only)

`StaffDayCrmEventsService` for **this staff**, today, `config('app.timezone')` (Australia/Melbourne), using the same day-bounds helper as `StaffWorkloadService::dayBounds()`.

Sources (writer column = this staff):

| Kind shown | Source | Notes |
|------------|--------|--------|
| Email in / Email | `email_logs` + feed `email` | Exclude `conversion_type = system_generated` and fetched inbound per efficiency spec |
| Email out | CRM sent / `email_logs` outbound this staff | Align with existing sent-archive if that is how sends are attributed |
| Document | `documents` (`created_by` / `user_id`) and/or feed `document` | Prefer not double-listing the same upload |
| Booking | `booking_appointments` created today by this staff | Join matter/client when present |
| SMS | `sms_logs.sender_id` | |
| Stage | `activities_logs` `activity_type = stage`, `created_by` | |
| Action completed | existing completed-action feed rows | Same definition as the Call / Completed cards, listed as events not added twice into cards |
| Call / In-person note | `notes` file notes `is_action = 0`, `task_group` Call \| In-Person | |
| EOI | feed `eoi_*` if `created_by` is staff | |

Cap the list (e.g. 50) with “and N more”. Grouping identical Admin mailbox uploads is a display nicety, not v1-required.

**Does not write.** Does not change workload cache keys except that overlay Done must **not** call `StaffWorkloadService::forgetForStaff` unless we later prove a card query accidentally includes `file_time` (it must not).

### 4.4 Matter search

JSON endpoint, `auth:admin`, query `q`, limit ~8:

- `client_matters.client_unique_matter_no`
- client name (`admins`)
- matter title / nick (`matters`)
- current stage label

Respect existing client/matter **access** rules (same as the rest of the CRM for that staff — do not invent a wider search than they can open). Empty result copy: create the matter on the client file first.

Reuse patterns from `BookingAppointmentsController` / visa sheet search; do not add a second global-search stack.

### 4.5 Overlay API (JSON, same-day UI)

All scoped to `Auth::guard('admin')->id()`.

| Action | Behaviour |
|--------|-----------|
| `GET` today’s entries | Board + tally |
| `POST` start | Require `kind`, `title`, and either `client_matter_id` or `admin=true`. Pause other running. `status=doing`, `is_running=true` |
| `POST` pause / resume / park | Resume pauses others |
| `POST` done | `confirmed_minutes` required. Stop clock. If matter: write `file_time` feed row, store `activities_log_id` |
| `POST` reopen | Same-day only in v1 |
| `PATCH` title / link matter while not done | Linking Admin → matter later allowed; then Done can post |
| `DELETE` | Only `doing`/`parked` never posted |

Heartbeat: optional `POST` tick every N minutes **only while tab is open** to persist `clock_seconds`, or persist clock only on pause/park/done (preferred: **no 1s server tick**; compute clock from `started_at` minus parked intervals on the client, persist on those transitions). Avoid re-rendering the workload strip every second.

### 4.6 Copy summary

Server-built text (so copy matches what we would store):

```
{Staff name} — {date}
Hours in CRM: …

— Already in CRM —
{ref or —} · {kind} · {title} · {time}

— Time on files (overlay) —
{matter no} · {kind} · {title} · {Nm}

— Admin / no file —
…

— Still open —
…
```

---

## 5. UI placement

- New Blade partials under `resources/views/components/dashboard/` (e.g. `my-day.blade.php`, `crm-events.blade.php`, `file-time-board.blade.php`).
- CSS: dedicated `public/css/dashboard-my-day.css` (or extend `dashboard-calendar.css` only if it stays small). Match dashboard tokens already used by the workload strip; do not import the prototype’s Poppins CDN as a new brand font unless the dashboard already uses it.
- JS: `public/js/dashboard-my-day.js` — matter typeahead, board, Done modal, copy. No Vite package change unless the project already bundles this path the same way as `dashboard-calendar.js`.
- Markup test: extend `DashboardWorkloadMarkupTest` so the strip **remains** and My day **is present**, without bringing back legacy KPI cards.

Keep the staff calendar as it is. My day sits between the strip and the calendar **or** after the calendar — **recommendation:** after the strip, before the calendar, matching the prototype’s “queue then diary” reading order. Confirm with you before build if calendar-first is preferred.

---

## 6. Spec / docs to update when building (not now)

- `docs/STAFF_WORKLOAD_EFFICIENCY.md` — overlay layer vs “no timer on notes”.
- `docs/CRM_ACTIVITY_FEED.md` — `file_time` type, writers, do not count as action complete.
- After code: `graphify update .`

Optional Boost `record-rule`: overlay must never increment `StaffWorkloadService` card totals.

---

## 7. Tests (PHPUnit, no `RefreshDatabase`)

Use factories + `DatabaseTransactions` only if the suite already does for similar dashboard tests; otherwise unit-test services with sqlite `:memory:` as in `phpunit.xml`.

Cover:

- Overlay Done with matter creates `activities_logs.activity_type = file_time` and does **not** change completed/updated/call_completed tallies for that staff/day.
- Overlay Done Admin creates **no** feed row.
- Second start/resume sets previous `is_running = false` (unique index).
- CRM events service returns only this staff’s rows for today; excludes system-generated email.
- Matter search 403/empty for inaccessible files (match existing auth).
- Guest cannot hit overlay routes.
- Copy payload includes both layers.
- Markup: strip unchanged; My day mounted.

---

## 8. Phased delivery

| Phase | Scope | Why ship separately |
|-------|--------|---------------------|
| **A** | Hours header + Already in CRM list + Copy of CRM events only | Replaces most of the Teams paste with **zero** new logging habit |
| **B** | Overlay table, matter/Admin capture, board, minutes at Done, feed post, time-by-matter, copy includes overlay | The prototype’s new behaviour |
| **C** (later) | Optional 3CX count without duration, WhatsApp, grouping mailbox uploads, week-vs-week | Integrations, not the diary |
| **D** | Auto time on open matter (§12): focused-tab sessions, promote on CRM write, minutes split across events, `file_time` row at close | Removes the manual start habit for in-CRM work |

Do not start C in the first build. D follows A+B and does not depend on C.

---

## 9. Explicitly out of scope

- Changing Completed / Updated / Pending / Call completed definitions.
- Live 3CX CDR or call duration as KPI.
- Separate Call / In-person start-stop timer on `notes`. Call time is covered by the auto session on the open matter (§12).
- Manager “view as”, Admin Console columns for overlay (can follow later; not v1).
- Replacing assigned actions (`notes.is_action = 1`) with the Kanban.
- Client portal.

---

## 10. Suggested implementation order (when approved)

1. Migration + model + factory + partial unique index.  
2. `StaffFileTimeService` + Form Requests + routes + tests for start/pause/done/admin.  
3. Feed writer `file_time` + activity-feed docs/icon.  
4. `StaffDayCrmEventsService` + hours header + tests.  
5. Matter search endpoint.  
6. Blade/CSS/JS on dashboard + markup test.  
7. Copy summary endpoint.  
8. Pint on dirty PHP; `graphify update .`; run the new tests only.

---

## 11. Open points (confirm before coding)

1. **Placement:** My day immediately under the workload strip vs under the calendar.  
2. **Phase A first** (CRM list only) vs A+B in one PR. Recommendation: **A then B** so processing staff get value without a timer.  
3. **Reopen after Done:** allowed same day, or never after feed post.  
4. **Access for matter search:** exact reuse of which existing client/matter gate.  
5. **`file_time` on the client activity filter bar:** show as its own chip vs lump under Activity.

---

## 12. Phase D — Auto time on open matter (agreed 15 Sep 2026)

**Status:** implemented.

### 12.1 Idea

Opening a client, lead, or company detail page starts a silent session for that record (matter when one is selected). Time counts only while that browser tab is focused. The CRM decides what the time was for from the writes the staff already made (notes, uploads, stage moves, emails, completed actions, bookings, SMS). No start button, no title, no preset, no AI, no separate call / in-person timer.

### 12.2 Rules

| # | Rule |
|---|------|
| 1 | **Unit:** one session per staff × record × Melbourne day. Record = `client_matters.id` when a matter is selected; otherwise `admins.id` (lead / company / client with no matter). Reopening the same record later that day continues the same session. |
| 2 | **Counting:** seconds accrue only while the tab is focused (`document.hasFocus()` + `visibilityState = visible`). Blur, tab switch, or close stops counting immediately. No grace window. The gap is not credited; refocus continues the session. |
| 3 | **Multiple tabs:** the browser tells us. The tab that gains focus posts a heartbeat for its record. Same record in two tabs = same session. Two matters of one client = two sessions. Non-record pages (dashboard, lists) start nothing. |
| 4 | **Heartbeat:** `POST` every 60s while focused, carrying `focused_seconds`. Server sets `last_heartbeat_at`. A session whose heartbeat is older than 3 min is treated as ended at the last heartbeat (crash, sleep, closed laptop). |
| 5 | **Idle while focused:** no mouse / keyboard for 15 min → modal in that tab “Still on {ref}?”. No answer within 2 min → session is cut back to the moment idle started. Answer → counting continues. |
| 6 | **Promotion:** session starts `accessed`. It becomes `recorded` when any CRM write by this staff on this record lands inside the session window (same sources as `StaffDayCrmEventsService`). Detection is server-side on heartbeat / close, not per-endpoint JS. Under 2 min with no write stays `accessed`. Any write → `recorded` regardless of length. |
| 7 | **Reviewed file:** ≥ 2 min focused with **no** write → `recorded` with a single synthetic event “reviewed file”. |
| 8 | **Minutes:** focused minutes stored as-is. Editable inline on the board (minutes only). Delete allowed until the feed row exists. Not confirmed via modal. |
| 9 | **Split:** minutes are divided evenly across the session’s events at read time; nothing per-event is stored. |
| 10 | **Feed:** one `activities_logs` row per `recorded` session at close, `activity_type = file_time`, subject `logged {N}m on {ref} · {events} activities`, `created_by` staff, `task_status = 0`. Updated when minutes are edited or the session reopens and closes again. **Never** for `accessed`. Lead / company sessions post to that record’s feed. |
| 11 | **Notes with `matter_id` null** on a client with several matters are attributed to the matter whose tab is open. |
| 12 | **Layering:** auto sessions are outside rule 8 (one running per staff). They never pause a manual block and never touch `StaffWorkloadService`. |
| 13 | **Close:** scheduled command every 5 min closes sessions with `last_heartbeat_at` older than 3 min (status `closed`, post feed row if `recorded`). Same-day refocus reopens the session. No midnight special case; a dead heartbeat covers overnight tabs. |
| 14 | **Visibility:** personal view only, same as the rest of My day. On for every `auth:admin` staff; no opt-in. |

### 12.3 Storage

New table `staff_matter_sessions` (separate from `staff_file_time_entries`: different lifecycle, no kind / title, not one-running).

| Column | Purpose |
|--------|---------|
| `id` | PK |
| `staff_id` | FK `staff.id` |
| `client_matter_id` | FK `client_matters.id`, nullable |
| `client_id` | `admins.id`, always set (lead / company / client) |
| `session_date` | Melbourne date |
| `status` | `accessed` \| `recorded` \| `closed` |
| `focused_seconds` | Accrued focused time |
| `idle_cut_seconds` | Seconds removed by idle cut (audit) |
| `confirmed_minutes` | Nullable; set at close = round(focused / 60), editable |
| `event_count` | Snapshot at close for the feed subject |
| `is_reviewed_only` | boolean, rule 7 |
| `started_at` / `last_heartbeat_at` / `ended_at` | Timestamps |
| `activities_log_id` | Nullable FK, unique when set |
| `created_at` / `updated_at` | |

Indexes: unique `(staff_id, client_id, client_matter_id, session_date)` (nulls-distinct handled by coalescing `client_matter_id` to 0 in a generated column or by two partial indexes), `(staff_id, session_date)`, `(last_heartbeat_at)` for the closer.

### 12.4 API (`auth:admin`, JSON)

| Route | Behaviour |
|-------|-----------|
| `POST dashboard/my-day/sessions/heartbeat` | `client_id`, `client_matter_id?`, `focused_seconds`. Upserts today’s session, sets `last_heartbeat_at`, runs promotion check. Returns status + idle-warning flag. |
| `POST dashboard/my-day/sessions/{id}/idle-cut` | `idle_started_at`. Cuts focused seconds back to that instant. |
| `POST dashboard/my-day/sessions/{id}/blur` | `focused_seconds` via `sendBeacon` on blur / `pagehide`. |
| `PATCH dashboard/my-day/sessions/{id}` | `confirmed_minutes` |
| `DELETE dashboard/my-day/sessions/{id}` | Only while `activities_log_id` is null |

`GET dashboard/my-day` (existing) gains `sessions` (recorded, with split events) and `accessed` (list of refs).

### 12.5 Services / code

- `StaffMatterSessionService`: `heartbeat()`, `blur()`, `idleCut()`, `promoteIfWritten()`, `closeStale()`, `eventsForSession()` (thin wrapper over `StaffDayCrmEventsService` scoped to record + window), `postToFeed()`.
- `App\Console\Commands\CloseStaleMatterSessions`, scheduled `everyFiveMinutes()`.
- `public/js/crm/clients/file-time-session.js`: standalone script on client / lead / company detail. Reads record ids from the page, owns focus / visibility / idle detection, heartbeat, `sendBeacon` on blur. Does **not** live inside `detail-main.js`.
- `dashboard-my-day.js`: render auto blocks (distinct style), “Files opened” list, minutes chip on Already-in-CRM rows, inline minutes edit.
- Copy summary: new sections “Time on files (auto)” and “Files opened”.

### 12.6 Tests (PHPUnit, no `RefreshDatabase`)

- Heartbeat creates one session per staff / record / day; second heartbeat same day updates, does not duplicate.
- Stale heartbeat closes at `last_heartbeat_at`, not `now()`.
- Idle cut reduces `focused_seconds` to the idle start.
- Promotion: note / document / stage inside window → `recorded`; outside window → stays `accessed`.
- ≥ 120s no write → `recorded`, `is_reviewed_only = true`; < 120s no write → `accessed`.
- Close of `recorded` writes `file_time` row; close of `accessed` writes none; lead session writes to lead feed.
- Even split across N events sums back to `confirmed_minutes` (rounding).
- Auto session never changes `StaffWorkloadService` tallies and never sets `is_running` on `staff_file_time_entries`.
- Guest cannot hit session routes; staff cannot touch another staff’s session.

### 12.7 Docs to amend when built

- `docs/STAFF_WORKLOAD_EFFICIENCY.md` line 15: duration is in via manual overlay **and** auto sessions; still not Layer A.
- `docs/CRM_ACTIVITY_FEED.md`: auto `file_time` subject format; note that a `file_time` row can be updated after posting.
- `graphify update .` after code.

### 12.8 Build plan (ordered; each step lands green before the next)

Ground rules for the build chat: run `graphify query` before exploring; read `.ai/rules/index.md` if present; no `RefreshDatabase`; tests follow `tests/Unit/Services/StaffFileTimeServiceTest.php` (hand-built sqlite `:memory:` schema in `setUp`, `Carbon::setTestNow`, `#[Test]` attributes); `vendor/bin/pint --dirty --format agent` on every PHP change; `php artisan make:*` for new files.

Existing anchors to reuse, not duplicate:

| Need | Reuse |
|------|-------|
| Day bounds (Melbourne) | `StaffWorkloadService::dayBounds()` |
| Event sources | `StaffDayCrmEventsService::{emailEvents,documentEvents,bookingEvents,smsEvents,feedEvents,contactNoteEvents}` (currently `protected`, staff + window only) |
| Feed write shape | `StaffFileTimeService::postToMatterFeed()` |
| Record access | `EnsuresCrmRecordAccess::ensureCrmRecordAccess()`, `StaffClientVisibility::canAccessClientOrLead()` |
| Staff resolve | `DashboardMyDayController::staffOrAbort()` |
| Route block | `RegisterWebRoutes.php` L190–200, `/dashboard/my-day/*` |
| Detail page config | `window.ClientDetailConfig` in `crm/clients/detail.blade.php` L798 (`matterId` there is the **ref no**, not `client_matters.id`; numeric id is `$latestClientMatterId` / selected option of `#sel_matter_id_client_detail`) and `crm/companies/detail.blade.php` L1342; `crm/leads/detail.blade.php` has none yet |
| Scheduler | `app/Console/Kernel.php::schedule()` (Laravel Kernel, not `routes/console.php`) |

---

**Step 1 — Migration + model + factory**

- `php artisan make:migration create_staff_matter_sessions_table --no-interaction`; columns per §12.3. FKs: `staff_id → staff` cascade, `client_matter_id → client_matters` nullOnDelete, `activities_log_id → activities_logs` nullOnDelete, unique `activities_log_id`.
- Uniqueness with nullable matter: add stored generated column `matter_key` = `COALESCE(client_matter_id, 0)` and unique `(staff_id, client_id, matter_key, session_date)`. If the generated column is awkward across Postgres and sqlite, fall back to enforcing in the service with `lockForUpdate()` on the lookup and a plain index.
- `php artisan make:model StaffMatterSession --factory --no-interaction`. Constants `STATUS_ACCESSED|RECORDED|CLOSED`, `ACTIVITY_TYPE = 'file_time'` (same as manual), casts for timestamps/booleans, relations `staff`, `clientMatter`, `client`, `activitiesLog`. Factory states `accessed()`, `recorded()`, `closed()`, `reviewedOnly()`.
- Run `php artisan migrate --no-interaction` on the dev DB (forward only).

**Step 2 — Expose scoped events from `StaffDayCrmEventsService`**

- Add `public function forStaffOnRecord(int $staffId, int $clientId, ?int $clientMatterId, Carbon $start, Carbon $end): Collection` that runs the six sources with the same window and then filters by `client_id` and, when a matter is given, by `client_matter_id` when the row has one **or** null (rule 11). Each source row already carries `client_id`; confirm each also carries `client_matter_id` where the table has it and add it where missing (`notes.matter_id`, `documents`, `booking_appointments`, `activities_logs`).
- Do **not** change `forStaff()` output shape; `crm-events.blade.php` and the copy summary depend on it.
- Extend `tests/Unit/Services/StaffDayCrmEventsServiceTest.php`: record scoping, matter-null note attributed to open matter, other-client rows excluded.

**Step 3 — `StaffMatterSessionService` (core, no HTTP)**

`php artisan make:class Services/StaffMatterSessionService --no-interaction`. Constructor: `StaffWorkloadService`, `StaffDayCrmEventsService`.

| Method | Behaviour |
|--------|-----------|
| `heartbeat(int $staffId, int $clientId, ?int $matterId, int $focusedSeconds): StaffMatterSession` | Find-or-create today’s row (rule 1). `focused_seconds = max(existing, $focusedSeconds)` when same session, never lower. `last_heartbeat_at = now()`. If `status = closed` and same day → reopen to prior status (rule 13). Then `promoteIfWritten()`. |
| `blur(...)` | Same as heartbeat but no promotion re-check needed beyond the standard one; exists so `sendBeacon` has a cheap target. |
| `idleCut(int $staffId, StaffMatterSession $s, Carbon $idleStartedAt): StaffMatterSession` | `cut = focused_seconds − seconds between idleStartedAt and last_heartbeat_at` (floor 0); `idle_cut_seconds += removed`. |
| `promoteIfWritten(StaffMatterSession $s): StaffMatterSession` | If `accessed`: events in `[started_at, last_heartbeat_at]` via Step 2 → `recorded`. Else if `focused_seconds ≥ 120` → `recorded`, `is_reviewed_only = true` (rule 7). |
| `closeStale(Carbon $now): int` | Rows with `status != closed` and `last_heartbeat_at < now − 3 min`: `ended_at = last_heartbeat_at`, `status = closed`, `confirmed_minutes = confirmed_minutes ?? round(focused/60)`, `event_count` snapshot, feed row if `recorded` and `confirmed_minutes ≥ 1`. Returns count. Also promotes before closing (a note saved seconds before the tab closed must count). |
| `updateMinutes(int $staffId, StaffMatterSession $s, int $minutes)` | 1–480; update feed row if exists. |
| `delete(int $staffId, StaffMatterSession $s)` | Only when `activities_log_id` null. |
| `sessionsForBoard(int $staffId, ?Carbon $day)` | `recorded`/`closed`-recorded rows with `events` (Step 2, split minutes: `intdiv` + remainder to the first event so the sum equals `confirmed_minutes`) and `accessed` list of refs. |
| `postToFeed(StaffMatterSession $s)` | Subject `logged {N}m on {ref} · {events} activities` (or `· reviewed file`); `client_id` from session; matter → `use_for = matter`, lead/company → `use_for` matching what `LogsClientActivity` uses for that record type; `task_status = 0`, `pin = 0`; update existing row when `activities_log_id` set. |

Tests, `tests/Unit/Services/StaffMatterSessionServiceTest.php`, schema built in `setUp` like the sibling test (needs `staff`, `admins`, `client_matters`, `notes`, `documents`, `activities_logs`, `staff_matter_sessions`): every bullet in §12.6.

**Step 4 — Closer command + schedule**

- `php artisan make:command CloseStaleMatterSessions --no-interaction`, signature `my-day:close-stale-sessions`, calls `closeStale(now())`, prints count.
- `Kernel.php`: `->everyFiveMinutes()->withoutOverlapping()->runInBackground()` next to `booking:sync-appointments`.
- Test: command closes a stale row and leaves a fresh one.

**Step 5 — Form Requests + controller + routes**

- `php artisan make:request StaffMatterSession/HeartbeatStaffMatterSessionRequest` (`client_id` required int, `client_matter_id` nullable int, `focused_seconds` required int 0..86400), `IdleCutStaffMatterSessionRequest` (`idle_started_at` required ISO date), `UpdateStaffMatterSessionRequest` (`confirmed_minutes` 1..480). Authorize via `auth:admin` guard as siblings do.
- `php artisan make:controller CRM/StaffMatterSessionController --no-interaction` with `heartbeat`, `blur`, `idleCut`, `update`, `destroy`. `ensureCrmRecordAccess($clientId)` on heartbeat/blur; ownership check inside service for the rest. Do **not** grow `DashboardMyDayController` further; it already has 12 actions.
- Routes in `RegisterWebRoutes.php` after L200: `POST /dashboard/my-day/sessions/heartbeat`, `POST /dashboard/my-day/sessions/blur`, `POST /dashboard/my-day/sessions/{session}/idle-cut`, `PATCH /dashboard/my-day/sessions/{session}`, `DELETE /dashboard/my-day/sessions/{session}`, names `dashboard.my-day.sessions.*`.
- `DashboardMyDayController@index` and the initial payload in `DashboardController@index` gain `sessions` from `sessionsForBoard()`.
- Feature test `tests/Feature/StaffMatterSessionRoutesTest.php`: guest 401/redirect, staff A cannot touch staff B’s session (404/403), heartbeat validates.

**Step 6 — Detail-page script `public/js/crm/clients/file-time-session.js`**

Standalone IIFE, no jQuery dependency, loaded with `defer` from the three detail blades. Reads `window.MyDaySession = { clientId, clientMatterId|null, ref, routes: {heartbeat, blur, idleCut}, csrf }`, which each blade emits (client detail: numeric id from `$latestClientMatterId`, updated when `#sel_matter_id_client_detail` changes → treat as a new record: blur old, heartbeat new).

Behaviour:

- `focused = document.hasFocus() && document.visibilityState === 'visible'`; listeners on `focus`, `blur`, `visibilitychange`, `pagehide`.
- Local accumulator ticks 1s only while focused. Heartbeat every 60s while focused (`fetch`, keepalive). On losing focus or `pagehide`: `navigator.sendBeacon(blur, FormData)` with current seconds; a `fetch` fallback when `sendBeacon` is unavailable.
- First heartbeat fires immediately on load when focused (creates the `accessed` row).
- Idle: `mousemove|keydown|scroll|click|touchstart` reset `lastInputAt`. If focused and idle ≥ 15 min → show a minimal modal (`bootstrap.Modal` if present, else inline `<dialog>`) “Still on {ref}?” with one button. No click within 2 min → `POST idle-cut { idle_started_at }`, stop counting until next input. Click → dismiss, continue.
- 15-min idle warning also fires an `iziToast` if available.
- `BroadcastChannel('my-day-session')`: on gaining focus, post `{type:'focus', clientId, matterId}` so other tabs stop their local tick without waiting for their own blur event (server already stops counting; this is UI only).
- No console noise; every network error swallowed silently (tracking must never break the page).

Blades: add the config + `<script … defer>` inside the existing `@push('scripts')` in `crm/clients/detail.blade.php`, `crm/companies/detail.blade.php`, `crm/leads/detail.blade.php`. Cache-bust with the same `filemtime` pattern used on L1180.

**Step 7 — Dashboard rendering**

- `resources/views/components/dashboard/`: new `file-time-auto.blade.php` (auto blocks, distinct style, inline minutes edit, delete when unposted) and `files-opened.blade.php` (accessed refs, count badge). Include both in `my-day.blade.php` under the manual board.
- `crm-events.blade.php` / `dashboard-my-day.js`: minutes chip on Already-in-CRM rows when an event belongs to a session (match by event id + kind returned from `sessionsForBoard()`).
- `dashboard-my-day.js`: render from `board.sessions`; `PATCH` minutes on blur of the inline input; `DELETE` with confirm. Extend `state`, not a second script.
- `public/css/dashboard-my-day.css`: `.my-day-auto`, `.my-day-opened`, `.my-day-mins-chip`.
- Copy summary (`StaffFileTimeService::copySummary()`): add sections `— Time on files (auto) —` (`{ref} · {N}m · {events} activities`) and `— Files opened —` (refs only). Pass the session service in; extend the existing return shape (`auto`, `opened`).
- Markup test: extend `DashboardWorkloadMarkupTest` so the strip remains and the two new partials mount.

**Step 8 — Docs, graph, formatting**

- Amend `docs/STAFF_WORKLOAD_EFFICIENCY.md` L15 and `docs/CRM_ACTIVITY_FEED.md` per §12.7; set §12 status to implemented.
- `vendor/bin/pint --dirty --format agent`; `graphify update .`.
- Run only the new/changed tests: `php artisan test --compact --filter=StaffMatterSession`, `--filter=StaffDayCrmEventsService`, `--filter=DashboardWorkloadMarkup`. Offer the full suite to the user, do not run it unasked.

**Ship order / PRs**

1. Steps 1–4 (schema, services, closer) — no UI, safe to merge; sessions simply never get created until Step 6 ships.
2. Steps 5–6 (routes + detail script) — sessions start accruing silently.
3. Steps 7–8 (dashboard + docs) — staff see the blocks.

**Known risks to watch during build**

- `StaffDayCrmEventsService` sources guard with `Schema::hasTable`; the record-scoped variant must keep those guards so the sqlite test schema can omit tables it does not need.
- `client_matters.id` is `unsignedInteger` in the manual table; match that type for the FK.
- Company detail reuses `ClientDetailConfig`; confirm `clientId` there is the company `admins.id` before wiring.
- `sendBeacon` sends no CSRF header; accept `_token` in the `FormData` body (Laravel’s `VerifyCsrfToken` reads `_token`).
- Splitting minutes across events must be deterministic so the chip values do not jitter between refreshes (sort events by `sort_at`, id before splitting).
