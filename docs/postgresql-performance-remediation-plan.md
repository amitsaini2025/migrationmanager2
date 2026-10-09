# PostgreSQL Performance Remediation Plan (URL-wise)

**Date:** 2026-09-25  
**Scope:** PostgreSQL query changes and index changes only (no frontend/JS work in this doc).  
**Status:** Advisory — **nothing in this plan has been applied yet.**  
**Companion:** Architecture background in [crm-postgresql-laravel-performance-analysis.md](crm-postgresql-laravel-performance-analysis.md).

---

## How to use this document

1. **Record a baseline** for each URL before any change (see §1).
2. Apply fixes **one URL (or one phase) at a time**.
3. Re-measure the same URL with the same filters, role, and `per_page`.
4. Tick **Done** only when TTFB and `EXPLAIN (ANALYZE)` meet the target for that page.
5. **Do not** run `migrate:fresh`, `db:wipe`, or `RefreshDatabase` on real data.

**Measure as an allocated staff user**, not only superadmin — allocation `EXISTS` is where most list slowdown appears.

---

## 1. Baseline template (copy per URL)

| Field | Value |
|-------|-------|
| URL | |
| Role tested | e.g. migration agent (non-exempt) |
| Filters used | e.g. name search "smith", per_page=20 |
| Date | |
| HTML TTFB (ms) | Chrome DevTools → Network → document |
| Query count | `CLIENT_DETAIL_QUERY_LOG=true` or Debugbar |
| Slowest query (ms) | From log / `pg_stat_statements` |
| Notes | |

**Targets (p75 on staging with production-like volume):**

| Page type | TTFB target |
|-----------|-------------|
| List / report pages | < 800 ms |
| Client detail shell | < 800 ms |
| Detail tab fragments | < 500 ms |
| Global search AJAX | < 400 ms |

**Staging env (temporary):**

```env
CLIENT_DETAIL_QUERY_LOG=true
LOG_SLOW_QUERIES=true
SLOW_QUERY_THRESHOLD=200
```

**PostgreSQL check after each change:**

```sql
EXPLAIN (ANALYZE, BUFFERS)
-- paste the slowest query captured from logs
```

Look for: `Index Scan` / `Bitmap Index Scan` on GIN trigram or btree — **not** `Seq Scan on admins` with `lower(...) ~~`.

---

## 2. Recommended rollout order

Apply in this order so cross-cutting fixes benefit later pages:

| Order | Phase | What | URLs helped |
|-------|-------|------|-------------|
| 0 | Measurement | Baselines for all URLs in §3 | All |
| 1 | Cross-cutting queries | `LOWER() LIKE` → `ILIKE` | Lists, search, matters |
| 2 | Cross-cutting indexes | New migrations in §4 (staging first) | Lists, search, activities |
| 3 | List pages | §3.1 – §3.6 | Client/matter/lead lists |
| 4 | Global search | §3.7 | Header search |
| 5 | Client detail | §3.8 – §3.17 | Detail + tab fragments |
| 6 | Finance / accounts | §3.18 – §3.21 | Invoices, receipts, analytics |
| 7 | Other slow pages | §3.22 – §3.28 | Signatures, reports, sheets |
| 8 | PostgreSQL ops | §5 | Production monitoring |

---

## 3. URL-wise remediation steps

Each section lists: **baseline → query changes → index changes → verify → done**.

---

### 3.1 Client list

| | |
|---|---|
| **URL** | `GET /clients` |
| **Route name** | `clients.index` |
| **Baseline TTFB** | Not in slow-pages (often slow for allocated staff) |
| **Code** | `ClientCrmFollowups::index()`, `ClientQueries::applyClientFilters()`, `StaffClientVisibility::restrictAdminEloquentQuery()` |
| **Tables** | `admins`, `client_matters`, `client_access_grants` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Record TTFB with name filter + without; test as allocated staff |
| 2 | Query | Remove duplicate `$query->count()` before `paginate()` — use paginator `total()` only |
| 3 | Query | In `ClientQueries::applyClientFilters()`: replace `LOWER(first_name) LIKE` / `LOWER(last_name) LIKE` / `LOWER(email) LIKE` with `first_name ILIKE`, `last_name ILIKE`, `email ILIKE` (keep `%term%` bindings) |
| 4 | Query | Replace concatenated name `LOWER(...) LIKE` with `(first_name \|\| ' ' \|\| last_name) ILIKE ?` or search columns separately |
| 5 | Query | Add explicit `select([...])` on list — only columns the table displays (+ `id`, keys for relations) |
| 6 | Index | Ensure existing `admins_type_is_archived_idx` is used — filter must match `(type, is_archived)` pattern |
| 7 | Index | Optional partial index (§4.2) if list still seq-scans after ILIKE fix |
| 8 | Verify | `EXPLAIN ANALYZE` on filtered list query — expect GIN or btree, not seq scan on name search |
| 9 | Verify | Re-measure TTFB; query count should drop by 1 (no duplicate count) |

**Done:** [ ] Baseline recorded  [ ] Changes applied  [ ] TTFB < 800 ms  [ ] Plan uses index

---

### 3.2 Client matters list

| | |
|---|---|
| **URL** | `GET /clientsmatterslist` |
| **Route name** | `clients.clientsmatterslist` |
| **Baseline TTFB** | — |
| **Code** | `ClientCrmFollowups::clientsmatterslist()` |
| **Tables** | `client_matters`, `admins`, `matters`, `workflow_stages` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Record with name filter + default sort |
| 2 | Query | Replace `LOWER(ad.first_name) LIKE` / `LOWER(ad.last_name) LIKE` with `ILIKE` |
| 3 | Query | Replace `LOWER(TRIM(ws.name)) NOT IN (...)` with pre-normalized stage list or `ws.name NOT ILIKE ANY(ARRAY[...])` without per-row `TRIM` if possible |
| 4 | Index | Existing: `client_matters_cid_sel_*_idx`, dashboard composites — confirm planner uses them for allocation |
| 5 | Index | Optional: btree on `workflow_stages (workflow_id, name)` if stage filter is hot |
| 6 | Verify | `EXPLAIN ANALYZE` on matter list with allocation predicate |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.3 Closed matters list

| | |
|---|---|
| **URL** | `GET /clientsclosedmatterslist` |
| **Route name** | `clients.closedmatterslist` |
| **Code** | Same trait as §3.2 (closed variant) |

**Steps:** Same as §3.2 (ILIKE + stage filter + allocation `EXPLAIN`).

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.4 Lead list

| | |
|---|---|
| **URL** | `GET /leads` |
| **Route name** | `leads.index` |
| **Code** | `LeadController::index()`, shared `ClientQueries` / visibility traits |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Record as allocated staff |
| 2 | Query | Same ILIKE fixes as §3.1 in lead filters |
| 3 | Query | Remove duplicate count if present (mirror client list fix) |
| 4 | Query | Slim `select()` for list columns |
| 5 | Index | Reuse `admins_type_is_archived_idx`, GIN trigram on names (after ILIKE) |
| 6 | Verify | TTFB + `EXPLAIN ANALYZE` |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.5 Dashboard (main)

| | |
|---|---|
| **URL** | `GET /dashboard` |
| **Route name** | `dashboard` |
| **Code** | `DashboardController::index()`, `DashboardService` |
| **Tables** | `client_matters`, `activities_logs`, `email_logs`, `notifications` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Record with `defer_heavy_widgets` on and off |
| 2 | Index | Confirm migrations applied: `client_matters_dashboard_list_idx`, role-scoped `sel_*` composites (`2026_04_22_*`) |
| 3 | Index | Confirm `email_logs_cm_client_conversion_idx` used for unread counts |
| 4 | Query | Ensure matter list fragments use indexed columns in `WHERE` (matter_status, workflow_stage_id, sel_*) |
| 5 | Verify | Fragment endpoints: `/dashboard/matters-fragment`, `/dashboard/cases-fragment` separately |

**Done:** [ ] Baseline  [ ] Indexes confirmed  [ ] TTFB < 800 ms

---

### 3.6 Assigned by me / Action completed

| | |
|---|---|
| **URLs** | `GET /assigned_by_me`, `GET /action_completed` |
| **Route names** | `assignee.assigned_by_me`, `assignee.action_completed` |
| **Baseline TTFB** | 1.8 s (`/assigned_by_me`) |
| **Code** | `AssigneeController` |
| **Tables** | `notes`, `activities_logs`, `admins`, `client_matters` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Record both URLs |
| 2 | Index | Use existing `idx_notes_assigned_type_action_status`, `activities_logs_task_status_*_created_at_idx` |
| 3 | Query | Replace any `LOWER()` LIKE filters with `ILIKE` |
| 4 | Query | Paginate if currently unbounded `get()` |
| 5 | Verify | `EXPLAIN ANALYZE` on assignee queries |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.7 Global search (header)

| | |
|---|---|
| **URL** | `GET /clients/get-allclients?q={term}` |
| **Route name** | `clients.getallclients` |
| **Code** | `ClientCrmFollowups::getallclients()` (~L2943+) |
| **Tables** | `admins`, `client_matters`, `client_contacts`, `client_emails`, `companies` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Search 3–8 char name; record AJAX TTFB |
| 2 | Query | Replace all `LOWER(admins.*) LIKE` with `ILIKE` on raw columns |
| 3 | Query | Same for contacts, emails, company name search branches |
| 4 | Index | Unlock existing GIN: `admins_*_gin_trgm_idx`, `client_contacts_phone_gin_trgm_idx`, `client_emails_email_gin_trgm_idx`, `companies_company_name_gin_trgm_idx` |
| 5 | Index | Add GIN trigram on `client_matters.client_unique_matter_no`, `department_reference`, `other_reference` (§4.3) — MySQL FULLTEXT migration does not run on pgsql |
| 6 | Query | Batch visibility via existing `StaffClientVisibility::globalSearchCanAccessMap()` — avoid per-row access queries |
| 7 | Verify | `EXPLAIN ANALYZE` per search branch; target AJAX < 400 ms |

**Done:** [ ] Baseline  [ ] ILIKE applied  [ ] Matter ref GIN added  [ ] TTFB < 400 ms

---

### 3.8 Client detail shell

| | |
|---|---|
| **URL** | `GET /clients/detail/{client_id}/{matter_ref?}/{tab?}` |
| **Route name** | `clients.detail` |
| **Code** | `ClientCrmFollowups::detail()`, `buildClientDetailMatterContext()` |
| **Tables** | `admins`, `client_matters`, `matters`, `workflow_stages`, company relations |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Open non-company client; record shell TTFB (tab fragments excluded) |
| 2 | Query | Load company graph (`Admin::with(company...)`) only when `is_company = true` |
| 3 | Query | Collapse overlapping matter queries in `buildClientDetailMatterContext()` (count + list + latest id) |
| 4 | Query | Batch `canAccessClientOrLead()` for nominations — reuse `globalSearchCanAccessMap` pattern |
| 5 | Query | Replace EOI matter join `LIKE` with `ILIKE` where used |
| 6 | Index | `client_matters` indexes on `client_id`, matter refs for matter context lookups |
| 7 | Verify | Query count on shell should drop; TTFB < 800 ms |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.9 Activity feed (AJAX)

| | |
|---|---|
| **URL** | `GET /get-activities?client_id=…&page=…` |
| **Route name** | `clients.activities` |
| **Code** | `ClientCrmFollowups::activities()` |
| **Tables** | `activities_logs`, `admins` (staff filter) |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Page 1 and page 5; with/without keyword |
| 2 | Index | Confirm `activities_logs_client_id_created_at_index` used for default feed |
| 3 | Query | Replace keyword `description LIKE '%x%'` / `subject LIKE '%x%'` with `ILIKE` **and** add GIN trigram (§4.4) if keyword search stays |
| 4 | Query | Replace OFFSET pagination with keyset: `WHERE (created_at, id) < (?, ?) ORDER BY created_at DESC, id DESC LIMIT n` |
| 5 | Index | Add composite `(client_id, created_at DESC, id DESC)` for keyset (§4.5) |
| 6 | Query | Staff name filter: `ILIKE` instead of `whereHas` + `LOWER(first_name) LIKE` |
| 7 | Verify | Page 5 should not degrade linearly; keyword search uses GIN in plan |

**Done:** [ ] Baseline  [ ] Keyset pagination  [ ] Keyword GIN  [ ] TTFB < 500 ms

---

### 3.10 Personal details tab

| | |
|---|---|
| **URL** | `GET /clients/detail-personaldetails-tab/{client_id}/…` |
| **Route name** | `clients.detail.personaldetails-tab` |
| **Tables** | `admins`, `client_contacts`, `client_emails`, family/nomination tables |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Fragment TTFB only |
| 2 | Query | Move Blade `Admin::where(...)` verification lookups into controller — single query |
| 3 | Query | `select()` only needed columns per relation query |
| 4 | Index | FK indexes on `client_contacts.client_id`, `client_emails.client_id` (confirm exist) |
| 5 | Verify | Fragment TTFB < 500 ms; fewer queries than baseline |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 500 ms

---

### 3.11 Notes tab

| | |
|---|---|
| **URL** | `GET /clients/detail-noteterm-tab/{client_id}/…` |
| **Route name** | `clients.detail.noteterm-tab` |
| **Tables** | `notes` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Client with 100+ notes — record fragment size + TTFB |
| 2 | Query | Replace `Note::…->get()` with `paginate(40)` or reuse existing `/get-notes` API |
| 3 | Query | Filter by `client_id` + matter in SQL, not client-side only |
| 4 | Index | Existing `idx_notes_type_client_id_action_status` — ensure `WHERE client_id = ?` matches index prefix |
| 5 | Verify | First page only in HTML; TTFB < 500 ms |

**Done:** [ ] Baseline  [ ] Paginated  [ ] TTFB < 500 ms

---

### 3.12 Personal documents tab

| | |
|---|---|
| **URL** | `GET /clients/detail-personaldocuments-tab/{client_id}/…` |
| **Route name** | `clients.detail.personaldocuments-tab` |
| **Tables** | `documents`, `personal_document_types` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Document-heavy client |
| 2 | Query | Replace `personalDocumentsByFolder()` full `get()` with paginated query per folder |
| 3 | Query | `select()` metadata columns only — exclude large blob/text (`myfile`) from list query |
| 4 | Index | Confirm `documents_client_folder_type_updated_at_idx` (`client_id`, folder, type, updated_at) |
| 5 | Verify | First folder/page only; TTFB < 500 ms |

**Done:** [ ] Baseline  [ ] Paginated  [ ] TTFB < 500 ms

---

### 3.13 Visa documents tab

| | |
|---|---|
| **URL** | `GET /clients/detail-visadocuments-tab/{client_id}/…` |
| **Route name** | `clients.detail.visadocuments-tab` |
| **Tables** | `documents`, signers relation |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Visa-doc-heavy client |
| 2 | Query | Same as §3.12 — paginate by folder; lazy-load `signers` for selected doc only |
| 3 | Index | Same `documents_client_folder_type_updated_at_idx` |
| 4 | Verify | TTFB < 500 ms |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 500 ms

---

### 3.14 Account tab

| | |
|---|---|
| **URL** | `GET /clients/detail-account-tab/{client_id}/…` |
| **Route name** | `clients.detail.account-tab` |
| **Tables** | `account_client_receipts`, `documents`, `document_checklists` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Client with long ledger |
| 2 | Query | Replace PHP balance loop with SQL `SUM` using filter on `void_fee_transfer` |
| 3 | Query | Paginate ledger rows — do not load all into PHP |
| 4 | Query | Keep PostgreSQL `DISTINCT ON (receipt_id)` but add `LIMIT` + slimmer `select()` |
| 5 | Index | Confirm `account_client_receipts_filter_sum_idx` (`client_id, client_matter_id, receipt_type, invoice_status INCLUDE (balance_amount)`) |
| 6 | Verify | Fewer full-table scans; TTFB < 500 ms |

**Done:** [ ] Baseline  [ ] SUM + paginate  [ ] TTFB < 500 ms

---

### 3.15 Checklists tab

| | |
|---|---|
| **URL** | `GET /clients/detail-checklists-tab/{client_id}/…` |
| **Route name** | `clients.detail.checklists-tab` |
| **Tables** | `cost_assignment_forms`, `admins`, `client_matters`, `branches` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Client with many matters/forms |
| 2 | Query | Paginate or filter forms by active matter in SQL |
| 3 | Query | Cache staff/matter dropdowns in Redis (short TTL) — stop loading all active staff/matters per request |
| 4 | Index | Btree on `cost_assignment_forms (client_id, client_matter_id)` if missing |
| 5 | Verify | TTFB < 500 ms |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 500 ms

---

### 3.16 Emails tab

| | |
|---|---|
| **URL** | `GET /clients/detail-emails-tab/…` + `POST /clients/filter-emails` |
| **Route names** | `clients.detail.emails-tab`, filter route in clients.php |
| **Tables** | `email_logs`, attachments |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | First POST `/clients/filter-emails` TTFB |
| 2 | Index | Confirm `email_logs_cm_client_conversion_idx`; `email_log_attachments_email_log_id_index` |
| 3 | Query | Ensure filter uses indexed columns (`client_id`, `client_matter_id`, date range) before `ILIKE` on subject |
| 4 | Query | Subject search: `ILIKE` + optional GIN on `email_logs.subject` if slow |
| 5 | Verify | Paginated list TTFB < 500 ms |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 500 ms

---

### 3.17 Client portal tab

| | |
|---|---|
| **URL** | `GET /clients/detail-client-portal-tab/{client_id}/…` |
| **Route name** | `clients.detail.client-portal-tab` |
| **Tables** | Many client sub-tables (contacts, passports, qualifications, etc.) |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Fragment TTFB (often largest tab) |
| 2 | Query | Split 10+ queries — load sub-sections via separate endpoints or lazy fragments |
| 3 | Index | FK indexes on `client_id` for each portal table (audit missing) |
| 4 | Verify | TTFB improves per sub-section load |

**Done:** [ ] Baseline  [ ] Query split  [ ] TTFB improved

---

### 3.18 Invoice list

| | |
|---|---|
| **URL** | `GET /clients/invoicelist` |
| **Route name** | `clients.invoicelist` |
| **Baseline TTFB** | 1.5 s |
| **Code** | `ClientAccountsController::invoicelist()` |
| **Tables** | `account_client_receipts`, `admins`, `client_matters` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Default filters + date range |
| 2 | Query | Slim `select()`; avoid `SELECT *` on receipts |
| 3 | Query | Replace `LOWER()` search filters with `ILIKE` |
| 4 | Index | Btree on `(invoice_status, created_at)` or extend existing receipt indexes for list filters |
| 5 | Query | Ensure single count + paginate (no duplicate count) |
| 6 | Verify | TTFB < 800 ms |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.19 Client / office receipt lists

| | |
|---|---|
| **URLs** | `GET /clients/clientreceiptlist`, `GET /clients/officereceiptlist` |
| **Baseline TTFB** | 1.4 s / 1.1 s |
| **Tables** | `account_client_receipts` |

**Steps:** Same pattern as §3.18 (slim select, ILIKE, pagination, receipt indexes).

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.20 Accounts analytics dashboard

| | |
|---|---|
| **URL** | `GET /clients/analytics-dashboard` |
| **Route name** | `clients.analytics-dashboard` |
| **Baseline TTFB** | 2.4 s |
| **Tables** | `account_client_receipts`, aggregates |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Default date range |
| 2 | Query | Move aggregations to SQL (`SUM`, `COUNT`, `GROUP BY`) — avoid PHP over full `get()` |
| 3 | Index | `account_client_receipts_filter_sum_idx` + date column btree if filters use `created_at` |
| 4 | Query | Consider materialized summary table (nightly refresh) if still > 800 ms after SQL aggregates |
| 5 | Verify | TTFB < 800 ms |

**Done:** [ ] Baseline  [ ] SQL aggregates  [ ] TTFB < 800 ms

---

### 3.21 Admin — Emails feature

| | |
|---|---|
| **URL** | `GET /adminconsole/features/emails` |
| **Baseline TTFB** | 2.0 s |
| **Tables** | `email_logs`, related |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Default list |
| 2 | Index | `email_logs` — btree on `(created_at DESC)` or list filter columns |
| 3 | Query | Paginate + slim select; ILIKE for search |
| 4 | Verify | TTFB < 800 ms |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.22 Signature dashboard

| | |
|---|---|
| **URLs** | `GET /signatures`, `GET /signatures/create` |
| **Baseline TTFB** | 2.4 s / 0.8 s |
| **Code** | `SignatureDashboardController::index()`, `create()` |
| **Tables** | `documents`, `admins` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | `/signatures` document list page |
| 2 | Query | **Do not** load all clients/leads for attach modal — use typeahead (`/clients/get-allclients` or dedicated API) |
| 3 | Query | `create()`: remove `Admin::whereIn('type', ['client','lead'])->get()` — replace with search-on-type |
| 4 | Query | Keep `Document::with([...])->paginate(20)` — that part is fine |
| 5 | Index | `documents` list filters — confirm index on status/created_at if filtered |
| 6 | Verify | `/signatures` TTFB < 800 ms |

**Done:** [ ] Baseline  [ ] Attach typeahead  [ ] TTFB < 800 ms

---

### 3.23 EOI/ROI sheet

| | |
|---|---|
| **URL** | `GET /clients/sheets/eoi-roi` |
| **Baseline TTFB** | 1.7 s |
| **Tables** | `admins`, EOI fields, `client_matters` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Default sheet load |
| 2 | Query | ILIKE for any name/search filters; paginate sheet rows |
| 3 | Index | Sheet filter columns — btree on commonly filtered EOI columns if seq scan in EXPLAIN |
| 4 | Verify | TTFB < 800 ms |

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.24 Visa expiry report

| | |
|---|---|
| **URL** | `GET /reports/visaexpires` |
| **Route name** | `reports.visaexpires` |
| **Baseline TTFB** | 1.7 s |
| **Tables** | `admins`, visa-related columns |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Default report |
| 2 | Index | Partial index on visa expiry date: `admins (visa_expiry_date) WHERE type IN ('client','lead') AND is_deleted IS NULL` |
| 3 | Query | Filter in SQL with indexed date range — avoid loading all clients |
| 4 | Verify | TTFB < 800 ms |

**Done:** [ ] Baseline  [ ] Partial index  [ ] TTFB < 800 ms

---

### 3.25 Admin matter list

| | |
|---|---|
| **URL** | `GET /adminconsole/features/matter` |
| **Baseline TTFB** | 1.0 s |

**Steps:** Paginate + ILIKE + confirm matter table indexes.

**Done:** [ ] Baseline  [ ] Applied  [ ] TTFB < 800 ms

---

### 3.26 Activity search (admin)

| | |
|---|---|
| **URL** | `GET /adminconsole/activity-search` |
| **Tables** | `activities_logs` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Keyword + date filter |
| 2 | Index | GIN trigram on `description`, `subject` (§4.4) |
| 3 | Query | ILIKE + paginate; avoid OFFSET on deep pages |
| 4 | Verify | TTFB < 800 ms |

**Done:** [ ] Baseline  [ ] GIN + ILIKE  [ ] TTFB < 800 ms

---

### 3.27 Merge / contact search APIs

| | |
|---|---|
| **URLs** | `GET /merge_records/search`, `GET /api/search-contact-person` |
| **Tables** | `admins`, contacts |

**Steps:** Apply ILIKE pattern (some paths already use driver-aware ILIKE — align all branches).

**Done:** [ ] Baseline  [ ] ILIKE aligned  [ ] TTFB < 400 ms

---

### 3.28 CRMUtility recipient search

| | |
|---|---|
| **URL** | Uses `likevalue` param in `CRMUtilityController` (~L1887) |
| **Tables** | `admins` |

**Steps**

| Step | Type | Action |
|------|------|--------|
| 1 | Baseline | Trigger from compose/recipient UI |
| 2 | Query | Replace `LOWER(email/first_name/last_name/phone) LIKE` with `ILIKE` |
| 3 | Index | Existing GIN trigram on admins |
| 4 | Verify | Uses GIN in EXPLAIN |

**Done:** [ ] Baseline  [ ] ILIKE  [ ] Index used

---

## 4. Index migrations (staging first)

Run each migration on **staging** only. Validate with `EXPLAIN ANALYZE` before production.

### 4.1 Already deployed (confirm present)

| Index | Table | Migration |
|-------|-------|-----------|
| `admins_type_is_archived_idx` | admins | `2026_04_24_*` |
| `admins_*_gin_trgm_idx` (name, email, phone) | admins | `2026_04_24_*` |
| `client_matters_cid_sel_ma/pr/pa_idx` | client_matters | `2026_04_23_*` |
| Dashboard composites | client_matters | `2026_04_22_*` |
| `activities_logs_client_id_created_at_index` | activities_logs | `2026_04_22_*` |
| `documents_client_folder_type_updated_at_idx` | documents | `2026_04_24_*` |
| `account_client_receipts_filter_sum_idx` | account_client_receipts | `2026_04_23_*` |
| Notes indexes | notes | `2026_04_24_*` |

**Action:** On staging, run `\di+ indexname` in psql and confirm all exist.

### 4.2 New — partial index for active client/lead lists (optional)

**When:** After ILIKE fix, if client list still seq-scans.

```sql
CREATE INDEX CONCURRENTLY admins_active_clients_list_idx
ON admins (type, is_archived, id DESC)
WHERE is_deleted IS NULL
  AND type IN ('client', 'lead')
  AND is_archived = 0;
```

**URLs to re-test:** §3.1, §3.4

### 4.3 New — global search matter references (GIN trigram)

**When:** §3.7 matter/ref search is slow after ILIKE on admins.

```sql
CREATE EXTENSION IF NOT EXISTS pg_trgm;

CREATE INDEX CONCURRENTLY client_matters_unique_matter_no_gin_idx
ON client_matters USING gin (client_unique_matter_no gin_trgm_ops);

CREATE INDEX CONCURRENTLY client_matters_dept_ref_gin_idx
ON client_matters USING gin (department_reference gin_trgm_ops);

CREATE INDEX CONCURRENTLY client_matters_other_ref_gin_idx
ON client_matters USING gin (other_reference gin_trgm_ops);
```

**URLs to re-test:** §3.7

### 4.4 New — activity keyword search (GIN trigram)

**When:** §3.9 or §3.26 keyword filter is required.

```sql
CREATE INDEX CONCURRENTLY activities_logs_description_gin_idx
ON activities_logs USING gin (description gin_trgm_ops);

CREATE INDEX CONCURRENTLY activities_logs_subject_gin_idx
ON activities_logs USING gin (subject gin_trgm_ops);
```

**Warning:** `activities_logs` is high-churn — measure write impact.

**URLs to re-test:** §3.9, §3.26

### 4.5 New — activity feed keyset pagination

**When:** §3.9 page 5+ still slow after ILIKE.

```sql
CREATE INDEX CONCURRENTLY activities_logs_client_keyset_idx
ON activities_logs (client_id, created_at DESC, id DESC);
```

**URLs to re-test:** §3.9

### 4.6 New — visa expiry report (partial btree)

**When:** §3.24.

```sql
CREATE INDEX CONCURRENTLY admins_visa_expiry_active_clients_idx
ON admins (visa_expiry_date)
WHERE type IN ('client', 'lead')
  AND is_deleted IS NULL;
```

**URLs to re-test:** §3.24

### 4.7 Alternative to ILIKE — expression GIN (only if ILIKE rewrite blocked)

Prefer **ILIKE** over new expression indexes. If you must keep `LOWER(col)`:

```sql
CREATE INDEX CONCURRENTLY admins_lower_first_name_gin_idx
ON admins USING gin (LOWER(first_name) gin_trgm_ops);
-- repeat for last_name, email as needed
```

---

## 5. Query change cheat sheet (files)

| Change | Primary files |
|--------|----------------|
| Client/lead list filters | `app/Traits/ClientQueries.php` |
| Client list duplicate count | `app/Traits/ClientCrmFollowups.php` → `index()` |
| Matter list name search | `app/Traits/ClientCrmFollowups.php` → `clientsmatterslist()` |
| Global search | `app/Traits/ClientCrmFollowups.php` → `getallclients()` |
| Recipient search | `app/Http/Controllers/CRM/CRMUtilityController.php` |
| Activity feed | `app/Traits/ClientCrmFollowups.php` → `activities()` |
| Detail shell | `app/Traits/ClientCrmFollowups.php` → `detail()`, `buildClientDetailMatterContext()` |
| Account tab | `app/Support/ClientDetailAccountTab.php` |
| Documents tab | `app/Support/ClientDetailDocumentsTab.php` |
| Notes tab | `app/Traits/ClientCrmFollowups.php` → `notesTab()` |
| Checklists tab | `app/Support/ClientDetailChecklistsTab.php` |
| Signatures | `app/Http/Controllers/CRM/SignatureDashboardController.php` |
| Visibility / EXISTS | `app/Support/StaffClientVisibility.php` |

**Pattern:**

```php
// Before (index not used)
->whereRaw('LOWER(first_name) LIKE ?', ['%'.$term.'%'])

// After (uses existing GIN trigram on pgsql)
->whereRaw('first_name ILIKE ?', ['%'.$term.'%'])
```

---

## 6. PostgreSQL server ops (production)

| Step | Action | Helps |
|------|--------|-------|
| 1 | Enable `pg_stat_statements` | Find real top queries |
| 2 | Deploy **PgBouncer** (transaction mode) | Connection pooling |
| 3 | Review **autovacuum** on `activities_logs`, `notes`, `email_logs`, `notifications` | Reduce bloat |
| 4 | Set `random_page_cost = 1.1` (SSD) | Planner prefers indexes |
| 5 | Tune `work_mem` for large sorts (matter lists, analytics) | Avoid disk sorts |
| 6 | Schedule `VACUUM (ANALYZE)` on high-churn tables after bulk imports | Fresh statistics |

---

## 7. Regression checklist (after each URL fix)

- [ ] Baseline row updated in §1 spreadsheet
- [ ] `EXPLAIN (ANALYZE, BUFFERS)` saved for main query
- [ ] TTFB meets target for that URL
- [ ] No new N+1 (optional: `Model::preventLazyLoading()` in local)
- [ ] Write path smoke-tested (create note, activity, receipt) if index added on that table
- [ ] Allocated staff role re-tested (not only superadmin)

---

## 8. Related docs

| Doc | Purpose |
|-----|---------|
| [crm-postgresql-laravel-performance-analysis.md](crm-postgresql-laravel-performance-analysis.md) | Full technical analysis |
| [client-detail-tab-performance-review.md](client-detail-tab-performance-review.md) | Frontend / CWV per tab |
| [slow-pages.md](../slow-pages.md) | Aug 2026 TTFB baselines |
| [ADMINS_TABLE_COLUMNS.md](ADMINS_TABLE_COLUMNS.md) | Why `SELECT *` on admins is costly |

---

*Advisory plan only. Implement on staging, measure per URL, then promote to production one phase at a time.*
