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
6. **Overlay is only for work CRM cannot see:** drafting, Immi/portal, off-CRM doc check, mailbox *on this file*, internal discussion, other (Excel, 3CX on a file, etc.). Do **not** use presets that duplicate CRM creation, email send, or Call/In-person file notes.
7. **Confirmed minutes at Done are stored.** A live clock is a helper. Staff edit approx minutes (Rajan’s habit). No silent overnight accumulation.
8. **One running timer per staff.** Starting or resuming one pauses every other.
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

Do not start C in the first build.

---

## 9. Explicitly out of scope

- Changing Completed / Updated / Pending / Call completed definitions.
- Live 3CX CDR or call duration as KPI.
- Timer on `notes` Call / In-person.
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
