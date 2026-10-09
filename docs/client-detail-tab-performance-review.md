# Client Detail Page — Tab Performance Review

**Date:** 2026-09-22  
**Scope:** Individual client detail (`/clients/detail/{id}/…`) — tabs requested by product  
**Method:** Static architecture and code review (routes, controllers, Blade, JS bundles). No code changes were applied.  
**Measurement note:** This document maps issues to [Google Core Web Vitals](https://web.dev/vitals/) and typical Lighthouse categories. Run field/Lab measurement (Chrome DevTools → Lighthouse, or `web-vitals` in production) per tab URL to validate TTFB, LCP, INP, and CLS on real data.

---

## Google performance targets (reference)

| Metric | Good | Needs improvement | Poor |
|--------|------|-------------------|------|
| **LCP** (Largest Contentful Paint) | ≤ 2.5s | 2.5–4.0s | > 4.0s |
| **INP** (Interaction to Next Paint) | ≤ 200ms | 200–500ms | > 500ms |
| **CLS** (Cumulative Layout Shift) | ≤ 0.1 | 0.1–0.25 | > 0.25 |
| **TBT** (Total Blocking Time, Lab) | ≤ 200ms | 200–600ms | > 600ms |

CRM pages with large synchronous JS, wide HTML fragments, and multi-request waterfalls commonly miss **LCP** and **INP** unless tab-specific assets are deferred and payloads are paginated.

---

## Shared shell (affects every tab)

These costs apply before any tab-specific work runs.

### Server — initial `detail()` request

| Area | Finding | CWV impact |
|------|---------|------------|
| Client load | `Admin::with([...])` eager-loads company graph (`company.contactPerson`, directors, nominations, sponsorships, financials, etc.) for **every** individual client open, even when the record is not a company. | ↑ TTFB, ↑ HTML size |
| Matter context | `buildClientDetailMatterContext()` issues many separate queries (count, list join, URL matter count, latest matter id, matter info, workflow stage join, ref fields) with overlapping logic. | ↑ TTFB |
| EOI flag | Additional `client_matters` ↔ `matters` join(s) on most loads. | ↑ TTFB |
| Extra collections | `primaryContactCompaniesForClient`, `visibleNomineeNominations`, lead `assignableStaff`, Google review modal flags. | ↑ TTFB / memory |
| Positive | Account, checklists, notes, documents, workflow, and portal **payloads are deferred** to fragment routes (see `ClientDetailTabs::detailDeferredViewKeys()`). | ↓ initial HTML vs legacy |

**Relevant code:** `app/Traits/ClientCrmFollowups.php` (`detail()`, `buildClientDetailMatterContext()`).

### Layout — `layouts/crm_client_detail.blade.php`

| Area | Finding | CWV impact |
|------|---------|------------|
| CSS | Multiple global stylesheets (`app.min.css`, `style.css`, `components.css`, `custom.css`, Vite vendor CSS). | ↑ LCP (render-blocking) |
| JS in `<head>` | jQuery 3.7.1 loaded synchronously before body content. | ↑ TBT, delays hydration |
| Footer JS | `app.min.js` (~350 KB), `custom-form-validation.js` (~147 KB), Bootstrap, `scripts.js`, `custom.js`, mentions component, etc. | ↑ TBT, ↑ INP on first interaction |
| TinyMCE | Loader present globally; email/notes paths pull editor weight. | ↑ TBT when initialized |

### Page — `resources/views/crm/clients/detail.blade.php`

| Area | Finding | CWV impact |
|------|---------|------------|
| Inline config | Large `window.ClientDetailConfig` (dozens of URLs, lazy modal packs, `actionScripts` array). | ↑ parse time, ↑ memory |
| Synchronous modules | For non-`activityfeed` tabs, **~12 feature modules** load before `detail-main.js` (~352 KB). | ↑ TBT (often **> 600ms** in Lab on mid devices) |
| `detail-main.js` | Monolithic ~10k-line bundle; most tab handlers live here regardless of active tab. | ↑ TBT, ↑ INP |
| Activity path | `activityfeed` defers `detail-main` via `detail-actions-loader.js` until after `/get-activities` — good pattern, but other tabs do not get equivalent deferral. | Activity tab only |
| Extra defer scripts | `shared.js`, `detail.js`, `tabs/client_portal.js` loaded on every client detail page. | ↑ network + parse |
| Tab stubs | All tab panes (lazy placeholders) ship in first HTML; DOM size grows with stub markup. | ↑ style/layout cost |

### Right-rail activity feed (Personal Details + Activity tabs)

| Area | Finding | CWV impact |
|------|---------|------------|
| Auto-fetch | `sidebar-tabs.js` calls `ensureActivityFeedLoaded()` → `/get-activities` when Personal Details or Activity is active. | Extra API + main-thread HTML build on default landing tab |
| Pagination behavior | `activity-feed.js` may chain additional `/get-activities` requests via `fillFeedIfNotScrollable()` until the sidebar scrolls. | ↑ server load, ↑ main-thread work |
| Client rendering | `detail-feed-loader.js` builds large HTML strings in jQuery for 40 items per page. | ↑ INP when appending |

---

## Tab-by-tab review

### 1. Personal Detail (`personaldetails`)

**Load path**

1. Full page shell (above).
2. Lazy fragment: `GET /clients/detail-personaldetails-tab/{client}` (`personalDetailsTab()`).
3. Parallel: activity feed `/get-activities` (default tab shows feed).
4. `personaldetails-tab.js` + eventually all preloaded `detail-main.js` modules.

**Server (fragment)**

- Multiple queries: contacts, emails, family relationships, nominations, companies, verification statuses, lead staff list.
- Does **not** re-use the heavy `Admin::with()` graph from `detail()` (loads `Admin` with `first()` only) — good, but fragment still does many round-trips.

**HTML / DOM**

- `personal_details.blade.php` ~87 KB source; full form sections rendered server-side.
- Blade **N+1-style queries**: e.g. `Admin::where('id', …)->whereNotNull('dob_verified_date')->first()` inside the DOB block; similar patterns for visa verification; per-row `Admin::where` for related clients; tag lookups.

**Client**

- Default landing tab = worst-case **LCP** (shell + fragment + feed + JS).
- Verification UI, toggles, and large field groups increase layout/paint cost.

**Likely CWV posture:** LCP **poor** on typical clients; INP **needs improvement** once `detail-main` finishes parsing.

**Recommendations (not applied)**

- Stop auto-loading activity feed until user expands feed or switches to Activity (or idle `requestIdleCallback`).
- Move verification flags into `personalDetailsTab()` single query / `$detailVerificationStatuses` only (remove duplicate `Admin::where` in Blade).
- Split personal detail into smaller fragments or JSON sections (header card vs immigration vs tags).
- Code-split `detail-main.js` so Personal Details does not download account/invoice/document modules up front.

---

### 2. Activity (`activityfeed`)

**Load path**

1. Shell without synchronous `detail-main.js` (uses `detail-actions-loader.js` instead) — **best deferral story** on this page.
2. `/get-activities` drives first paint for main column.
3. After feed completes, `ensureClientDetailActions()` loads **entire** `actionScripts` chain (including `detail-main.js` + all modules).

**Server**

- `activities()`: paginated `ActivitiesLog` with `staff` eager load; `LIKE` filters on description/subject when keyword query present (index-sensitive).
- Up to 40 rows per request; client may request multiple pages immediately.

**Client**

- Main column hidden until feed path runs; large shell DOM still parsed.
- Chained page fetches for short viewports inflate time-to-interactive for hash deep links (`#activity_{id}`).

**Likely CWV posture:** LCP **needs improvement** (feed JSON + string HTML); INP **needs improvement** after deferred scripts load.

**Recommendations (not applied)**

- Cap auto-prefetch pages (e.g. max 2 chained loads unless user scrolls).
- Return slimmer JSON (pre-sanitized HTML snippets server-side, or smaller fields).
- Load only feed-related JS first; defer `detail-main` until user clicks an action that needs it (not only after first feed).

---

### 3. Notes (`noteterm`)

**Load path**

1. Shell + global `modules/notes.js` already loaded.
2. Fragment: `GET /clients/detail-noteterm-tab/{client}`.
3. `notesTab()` loads **all** client notes: `Note::where(...)->with('user')->orderBy(...)->get()` — no pagination.

**HTML / DOM**

- `notes.blade.php` renders every note card in HTML.
- Matter filtering is client-side (`filterNotesByMatter`) after full DOM exists.

**Likely CWV posture:** LCP **poor** for clients with hundreds of notes; INP **poor** when filtering/reflowing many cards.

**Recommendations (not applied)**

- Paginated API (`/get-notes` already in config) as primary load path; fragment = skeleton only.
- Virtualized list or “load more” for note cards.
- Do not eager-load note modals until `extra` modal pack opens.

---

### 4. Personal Documents (`personaldocuments`)

**Load path**

1. Fragment: `GET /clients/detail-personaldocuments-tab/{client}`.
2. `personaldocuments-tab.js` injects HTML and re-binds handlers.

**Server**

- `ClientDetailDocumentsTab::personalDocumentsByFolder()`: one query loads **all** personal documents (+ `staff`), grouped in PHP.
- Category list query on `PersonalDocumentType`.

**HTML / DOM**

- `personal_documents.blade.php` ~149 KB; extremely large tab markup (categories, grids, preview containers).
- Matter scoping is mostly **client-side** `filterDocumentListRowsByMatter` after full HTML is present.

**Likely CWV posture:** LCP **poor** for document-heavy clients; INP **poor** (thousands of nodes); CLS risk when previews load.

**Recommendations (not applied)**

- Per-category lazy load via API (folder id + page).
- Thumbnails/metadata first; preview on demand.
- Server-side matter filter in query, not jQuery show/hide.

---

### 5. Visa Documents (`visadocuments`)

**Load path**

Same pattern as personal documents; fragment `detail-visadocuments-tab`.

**Server**

- `visaDocumentsByFolder()`: all visa docs + `staff` + `signers` in one query.

**HTML / DOM**

- `visa_documents.blade.php` ~150 KB; signing / Form 956 UI increases DOM and script work.

**Likely CWV posture:** Same as personal documents — **poor** at scale.

**Recommendations (not applied)**

- Paginate by category/matter; lazy-load signers for selected document only.
- Split “signing actions” JS from general `detail-main` bundle.

---

### 6. Account (`account`)

**Load path**

1. Fragment: `GET /clients/detail-account-tab/{client}`.
2. `ClientDetailAccountTab::build()` runs ledger/invoice/office/DIBP queries.
3. `account-tab.js` + `dibp-receipts-tab.js` on deep link.

**Server (`ClientDetailAccountTab::build`)**

- Multiple passes over `account_client_receipts` (balance loop, ledger list, outstanding sum, office receipts).
- PostgreSQL `DISTINCT ON` invoice selection or grouped fallback.
- DIBP document queries + `DocumentChecklist::activeDibpReceipts()`.
- `attachDocumentUrls()` batch-loads documents and matters — good batching, but still heavy.

**HTML / DOM**

- `account.blade.php` ~166 KB — large tables for ledger, invoices, receipts.

**Client**

- Additional AJAX typical after inject (`listOfInvoice`, `clientLedgerBalance`, drag-drop modules already in global bundle).

**Likely CWV posture:** LCP **poor**; INP **needs improvement** on drag/drop and ledger edits.

**Recommendations (not applied)**

- Single aggregated query (or materialized balance) instead of PHP loop over all ledger rows.
- Tab shell + paginated ledger API; render first page only.
- Load `ledger-dragdrop.js` / `invoices.js` only when Account tab is first opened.

---

### 7. Email (`emails`)

**Load path**

1. Fragment: `GET /clients/detail-emails-tab/{client}` (light shell).
2. `emails-tab.js` loads `emails.js` (~160 KB) if needed.
3. `loadEmails()` → `POST /clients/filter-emails` (or lead/sent variants) with pagination — **good secondary fetch**.

**Client**

- Two-step load is correct architecturally; still pays **global** `detail-main` + modules cost before tab feels ready.
- Matter required for clients without `client_matter_id` — empty state after fragment load.

**Likely CWV posture:** LCP **needs improvement** (fragment + first email POST); INP **needs improvement** when switching folders/search (large `emails.js`).

**Recommendations (not applied)**

- Route-level code split: email tab entry without full `detail-main`.
- HTTP caching headers on static `emails.js`; ensure single load (already partially handled in `emails-tab.js`).
- Prefetch email list only after tab click, not on hover (unless intentional).

---

### 8. Checklist (`checklists`)

**Load path**

1. Fragment: `GET /clients/detail-checklists-tab/{client}`.
2. `ClientDetailChecklistsTab::build()`.

**Server**

- Loads **all** `CostAssignmentForm` rows for client with deep `with([...])` (matter, agent, office, etc.).
- Loads **full** staff dropdown sets (migration agents, responsible, assisting) and **all** active matters + branches on every tab open.

**HTML / DOM**

- `checklists.blade.php` ~82 KB with many forms/agreements.

**Likely CWV posture:** LCP **needs improvement** to **poor** depending on form count.

**Recommendations (not applied)**

- Lazy-load dropdown option lists via shared cached endpoints (`get-compose-option-lists` pattern).
- Paginate or collapse inactive matters’ checklists.
- Defer agreement document attachments to expander clicks.

---

### 9. Workflow (`workflow`)

**Load path**

1. Fragment: `GET /clients/detail-workflow-tab/{client}`.
2. `workflow-tab.js` (~87 KB) handles inject and stage actions.

**Server (`workflow-tab-body.blade.php`)**

- Matter resolve query.
- **Risk:** if `workflow_id` is null, loads **all** rows from `workflow_stages` (`orderByRaw` on full table).
- `WorkflowStageChecklistSync::ensureSeededForMatter()` on each fragment render — potential write/sync latency on every open.

**Assets**

- `workflow-tab.css` uses `?v={{ time() }}` — **disables browser caching** every second.

**Likely CWV posture:** LCP **needs improvement**; TTFB spikes when seed/sync runs.

**Recommendations (not applied)**

- Never load global `workflow_stages`; scope by `workflow_id` with empty state.
- Run checklist seed on matter change events, not on every tab fragment.
- Static cache-bust via `filemtime` like other client-detail assets.

---

### 10. Client Portal (`client_portal`)

**Load path**

1. Fragment: `GET /clients/detail-client-portal-tab/{client}` via `ensureClientPortalTabLoaded()` in `workflow-tab.js`.
2. `clientPortalTab()` runs **10+ Eloquent queries** (contacts, emails, addresses, passports, visa countries, travel, qualifications, experiences, occupations, test scores).

**HTML / DOM**

- `client_portal.blade.php` ~254 KB — **largest tab template**; multiple sub-tabs (Activities, Documents, Messages, Details) in one response.
- Injects `client-portal.css` and inline scripts on fragment parse.

**Client**

- `tabs/client_portal.js` also loaded on every detail page (defer).
- Deep coupling with `workflow-tab.js` (shared loader, stage icons).

**Likely CWV posture:** LCP **poor**; INP **poor** when switching portal sub-tabs.

**Recommendations (not applied)**

- Split portal sub-tabs into separate fragment endpoints or JSON APIs.
- Load portal CSS once in layout with proper cache version (not per-fetch link tags).
- Move portal-specific JS out of global page footers; load on first portal visit only.

---

## Cross-tab comparison

| Tab | Fragment lazy? | Typical bottleneck | Worst metric |
|-----|----------------|-------------------|--------------|
| Personal Detail | Yes | Shell + fragment + activity feed + `detail-main` | LCP |
| Activity | N/A (AJAX feed) | Chained `/get-activities` + deferred script chain | LCP / TBT |
| Notes | Yes | Unbounded notes query + DOM | LCP / INP |
| Personal Documents | Yes | All docs in one HTML + query | LCP / INP |
| Visa Documents | Yes | All visa docs + signers in one HTML | LCP / INP |
| Account | Yes | Many ledger queries + 166 KB HTML | LCP |
| Email | Yes (+ API) | `emails.js` size + POST list | TBT |
| Checklist | Yes | Full forms + staff/matter lists | TTFB |
| Workflow | Yes | Stage seed/sync + uncached CSS | TTFB / LCP |
| Client Portal | Yes | 254 KB HTML + 10 queries | LCP |

---

## Suggested measurement plan (validation)

For each tab, on a **staging** client with realistic data volume:

1. Open deep link: `/clients/detail/{encodedId}/{matterRef}/{tab}`.
2. Chrome DevTools → Performance: record cold load (disable cache).
3. Lighthouse (Desktop + Mobile): note LCP, TBT, CLS.
4. Network: document **HTML TTFB**, fragment size, `/get-activities` count, JS transferred/parsed.
5. Backend: enable `CLIENT_DETAIL_QUERY_LOG=true` temporarily for fragment routes (not `migrate:fresh`) to count queries per tab.

Record baselines in a spreadsheet before implementing fixes.

---

## Prioritized remediation backlog (documentation only)

| Priority | Item | Tabs helped |
|----------|------|-------------|
| P0 | Code-split / defer `detail-main.js` and module graph per tab (not only Activity) | All |
| P0 | Paginate or API-load notes, documents, account ledger | Notes, Docs, Account |
| P1 | Remove duplicate matter/EOI queries; slim `detail()` eager loads for non-company clients | Shell |
| P1 | Stop unconditional activity feed on Personal Details default | Personal, Activity |
| P1 | Fix workflow CSS cache bust (`time()` → `filemtime`) | Workflow |
| P1 | Scope `workflow_stages` query; move checklist seed off fragment hot path | Workflow |
| P2 | Shrink `ClientDetailConfig` (group URLs, lazy fetch on demand) | Shell |
| P2 | Portal sub-tab lazy fragments | Client Portal |
| P2 | Blade N+1 cleanup in `personal_details.blade.php` | Personal Detail |
| P3 | Virtualize long lists (feed, documents, ledger) | Activity, Docs, Account |

---

## Files reviewed (primary)

| Layer | Paths |
|-------|--------|
| Routes | `routes/clients.php` (detail + `detail-*-tab` fragments) |
| Controller | `app/Traits/ClientCrmFollowups.php` |
| Support | `app/Support/ClientDetailTabs.php`, `ClientDetailAccountTab.php`, `ClientDetailChecklistsTab.php`, `ClientDetailDocumentsTab.php` |
| Views | `resources/views/crm/clients/detail.blade.php`, `resources/views/layouts/crm_client_detail.blade.php`, `resources/views/crm/clients/tabs/*` |
| JS | `public/js/crm/clients/detail-main.js`, `sidebar-tabs.js`, `detail-feed-loader.js`, `detail-actions-loader.js`, `*-tab.js`, `public/js/emails.js` |

---

*This review is advisory. No application changes were made as part of this document.*
