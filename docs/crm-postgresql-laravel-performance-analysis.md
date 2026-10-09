# CRM Performance Degradation Analysis — PostgreSQL & Laravel

**Date:** 2026-09-22  
**Stack:** Laravel 13 / PHP 8.3 / PostgreSQL (primary; `config/database.php` default `pgsql`)  
**Scope:** Why CRM pages get slower as data grows, and which Laravel query shapes plus PostgreSQL planner behaviours cause it.  
**Method:** Static architecture review of list/search/detail query paths, existing index migrations, and prior TTFB measurements. No application code was changed for this document.  
**Companion:** Frontend / Core Web Vitals for the client detail shell is covered in [client-detail-tab-performance-review.md](client-detail-tab-performance-review.md). This document is the **database and Eloquent** half.

---

## 1. Executive summary

CRM slowdown is not a single missing index. It is the product of **row growth** on a few wide tables (`admins`, `client_matters`, `activities_logs`, `notes`, `documents`, `account_client_receipts`, `email_logs`) multiplied by **query shapes that PostgreSQL cannot index well**, then amplified by **Laravel loading more rows and columns than the screen needs**.

The same patterns repeat across the CRM:

| Pattern | What happens as data grows | Typical symptom |
|---------|----------------------------|-----------------|
| Leading-wildcard `LIKE` / `LOWER(col) LIKE '%x%'` | Sequential or bitmap scans; GIN `pg_trgm` indexes are often **not used** because the SQL wraps the column | Search, client list, global search, activity keyword filter |
| Correlated `EXISTS` on `client_matters` (allocation) | Planner re-evaluates assignment OR-conditions per candidate row | Client list, matter list, lead list for non-exempt staff |
| Unbounded `->get()` | Memory + HTML size grow linearly with the client’s history | Notes, personal/visa documents, account ledger, checklists |
| `SELECT *` on `admins` | Every list page hydrates a very wide client/staff table | Client index, signature attach dropdowns, global search |
| Extra round-trips (count + paginate, PHP loops over receipts) | TTFB grows even when indexes exist | Client list, Account tab, invoice/receipt lists |
| OFFSET pagination | Later pages re-scan discarded rows | Activities feed, large DataTables-style lists |

**Already in place (good):** dashboard/list composite indexes, `pg_trgm` GIN on some name/email/phone columns, covering `INCLUDE` index on ledger sums, Redis cache/session/queue defaults, lazy client-detail **tab fragments**, dashboard heavy-widget deferral.

**The gap:** several hot queries still wrap columns in `LOWER()`, still load full collections, and still run allocation `EXISTS` plus a second `COUNT(*)` on the same filtered set. Indexes exist; the SQL often cannot use them.

---

## 2. How degradation appears in this app

### 2.1 Growth curve (qualitative)

Early CRM data (hundreds of clients) hides these costs: sequential scans finish in tens of milliseconds. After tens of thousands of `admins` rows, hundreds of thousands of `activities_logs` / `notes` / `documents`, and a large `client_matters` assignment graph, the **same code** starts to miss Google’s HTML TTFB target (**&lt; 800 ms** “good”; **&gt; 2.5 s** poor for LCP-adjacent TTFB).

Local TTFB baselines from [slow-pages.md](../slow-pages.md) (25–26 Aug 2026, `php artisan serve`, logged-in superadmin):

| Surface | Path | TTFB |
|---------|------|------|
| Signature dashboard | `/signatures` | 2.4 s |
| Accounts analytics | `/clients/analytics-dashboard` | 2.4 s |
| Admin emails | `/adminconsole/features/emails` | 2.0 s |
| Assigned by me | `/assigned_by_me` | 1.8 s |
| EOI/ROI sheet | `/clients/sheets/eoi-roi` | 1.7 s |
| Visa expiry report | `/reports/visaexpires` | 1.7 s |
| Invoice / receipt lists | `/clients/invoicelist`, `clientreceiptlist` | 1.4–1.5 s |

Those numbers are **server HTML only**. Browser load (CSS/JS) is extra. `php artisan serve` is single-threaded, so overlapping AJAX (activity feed, tab fragments, SendGrid senders) queues behind the first request.

### 2.2 Two layers of latency

1. **PostgreSQL:** plan time + scan + join + sort. Dominated by sequential scans, nested-loop `EXISTS`, and sorts that spill to disk (`work_mem`).
2. **Laravel:** PDO fetch of wide rows, Eloquent hydration, PHP aggregation loops, Blade rendering of unbounded collections.

Fixing only indexes without shrinking result sets (or vice versa) leaves half the problem.

---

## 3. PostgreSQL-specific issues

### 3.1 `LOWER(column) LIKE '%term%'` defeats `pg_trgm`

Migration `2026_04_24_120000_add_performance_indexes_search_documents_notes_activities.php` creates GIN trigram indexes **on the raw columns**:

- `admins_first_name_gin_trgm_idx`, `admins_last_name_gin_trgm_idx`, `admins_email_gin_trgm_idx`, `admins_phone_gin_trgm_idx`
- Similar on `client_contacts.phone`, `client_emails.email`, `companies.company_name`

PostgreSQL can use `gin_trgm_ops` for `col ILIKE '%term%'` or `col LIKE '%term%'`. It **cannot** use an index on `first_name` for:

```sql
LOWER(first_name) LIKE '%smith%'
```

That expression needs a matching **expression index** (`CREATE INDEX … ON admins (LOWER(first_name) gin_trgm_ops)`), or the query must drop `LOWER()` and use `ILIKE`.

**Where this happens today**

| Location | Pattern |
|----------|---------|
| `ClientQueries::applyClientFilters()` | `LOWER(first_name/last_name) LIKE ?` and concatenated full name |
| `ClientQueries::applyArchivedListFilters()` | Same + `LOWER(email)` |
| `ClientCrmFollowups::clientsmatterslist()` | `LOWER(ad.first_name/last_name)` |
| Global search (~`ClientCrmFollowups` 3000–3185) | Widespread `LOWER(admins.*) LIKE '%…%'` plus `LOWER()` on contacts/emails/company |
| `activities()` keyword filter | `description LIKE '%keyword%'` / `subject LIKE '%keyword%'` — **no trgm index** on those columns |
| Staff search on activities | `LOWER(first_name) LIKE` via `whereHas('staff')` |

Some other paths already use driver-aware `ILIKE` (merge search, contact-person search). Client list and global search did not follow that pattern.

**Planner consequence:** as `admins` grows, name/email/phone search becomes a sequential scan (or a filter after a type/archive btree), even though GIN indexes were added specifically for search.

### 3.2 MySQL `FULLTEXT` never runs on PostgreSQL

`2026_04_22_140000_add_global_search_fulltext_indexes.php` **returns immediately** unless the driver is MySQL. Global search therefore:

- On MySQL: `MATCH … AGAINST` plus LIKE fallback.
- On PostgreSQL: the `else` branch of `LOWER(...) LIKE '%term%'` / column `LIKE '%term%'` — no `to_tsvector` / GIN FTS, and the `pg_trgm` indexes are still bypassed by `LOWER()`.

Production CRM on PostgreSQL is on the slower path by design of that migration.

### 3.3 Allocation `EXISTS` + OR on three staff columns

`StaffClientVisibility::restrictAdminEloquentQuery()` (and lead/matter variants) adds, for non-exempt staff:

```sql
WHERE EXISTS (
  SELECT 1 FROM client_matters
  WHERE client_matters.client_id = admins.id
    AND (sel_migration_agent = ? OR sel_person_responsible = ? OR sel_person_assisting = ?)
)
OR admins.user_id = ?
OR EXISTS ( … active client_access_grants … )
```

Helpful indexes exist:

- `client_matters_cid_sel_ma_idx` / `_pr_` / `_pa_` (`client_id` + each `sel_*`)
- Dashboard role-scoped composites on `(sel_*, matter_status, workflow_stage_id, updated_at)`

**Remaining cost**

- `OR` across three assignment columns often becomes a **BitmapOr** or three index probes **per outer row**.
- The subquery is **correlated** on `admins.id`. Nested loop × growing `admins` × growing `client_matters` is the classic CRM “it got slow after we hired more staff / opened more files” curve.
- Exempt roles (superadmin, configured roles) skip the predicate — **superadmin list pages can look fine while allocated staff pages degrade**. That is expected, not a coincidence.

`canAccessClientOrLead()` issues a **separate** `Admin::first()` even when the caller already loaded the row (`detail()`, `activities()`). That is a small constant, multiplied by every detail/AJAX hit.

### 3.4 Wide `admins` table + `SELECT *`

`admins` stores staff **and** CRM clients/leads, plus EOI, verification, GST, SMTP leftovers, and portal fields ([ADMINS_TABLE_COLUMNS.md](ADMINS_TABLE_COLUMNS.md)). List queries typically hydrate the full model:

```php
$query->sortable(['id' => 'desc'])->paginate($perPage);
```

PostgreSQL then reads many heap pages per row (toast for long text). Index-only scans are almost never possible. Pagination of 20–200 rows still pulls unnecessary columns; DataTables-style `per_page=200` makes it obvious.

### 3.5 OFFSET pagination on activity-like tables

`activities()` uses:

```php
->orderBy('created_at', 'DESC')
->skip(($page - 1) * $perPage)
->take($perPage + 1)
```

Index `activities_logs_client_id_created_at_index` helps **page 1**. Later pages still walk and discard offset rows. Combined with client-side “fill until scrollable” extra pages (see companion frontend doc), the feed can issue several OFFSET queries in a burst.

Keyword `LIKE '%…%'` on `description`/`subject` ignores that btree entirely.

### 3.6 `DISTINCT ON` vs PHP aggregation

`ClientDetailAccountTab::latestInvoices()` uses PostgreSQL `DISTINCT ON (receipt_id)` — the right tool for “latest row per invoice”. It still:

- Selects `*`
- Has no `LIMIT`
- Is preceded by **three other** scans of `account_client_receipts` (balance loop, ledger list, outstanding `SUM`)

Covering index `account_client_receipts_filter_sum_idx` (`client_id, client_matter_id, receipt_type, invoice_status INCLUDE (balance_amount)`) can serve the `SUM`. It does **not** remove the PHP walk of every ledger row to compute `calculated_balance`.

### 3.7 Connection and server-side settings (ops)

`config/database.php` pgsql options:

- `PDO::ATTR_PERSISTENT` default **false** (`DB_PERSISTENT`) — correct for `php artisan serve` / PHP-FPM without a pooler; production should use **PgBouncer** (transaction mode) rather than PHP persistent PDO.
- `ATTR_EMULATE_PREPARES => false` — good (native prepares).
- `charset => utf8` — fine.

Not in application code, but required as data grows:

| Setting | Why CRM cares |
|---------|----------------|
| `shared_buffers` / `effective_cache_size` | Heap of `admins` + `activities_logs` must stay cached |
| `work_mem` | Sorts for matter lists, analytics, `DISTINCT ON` |
| `random_page_cost` (SSD ~1.1) | Planner prefers indexes if set realistically |
| Autovacuum on high-churn tables (`activities_logs`, `notes`, `email_logs`, `notifications`) | Bloat → sequential scans look cheaper than indexes |
| `pg_stat_statements` | Identify real slow SQL in staging/production |

`Schema::defaultStringLength(191)` in `AppServiceProvider` is a MySQL utf8mb4 leftover; it does not help PostgreSQL.

---

## 4. Laravel / Eloquent-specific issues

### 4.1 N+1 and over-eager graphs

`Model::preventLazyLoading()` is **not** enabled anywhere under `app/`. N+1 can ship to production silently.

**Over-eager (too much `with()`):** `detail()` always loads the company graph (`company.directors`, `nominations`, `sponsorships`, `financials`, `companyNominationsAsNominee.company`) for **every** individual client, then mostly unused unless `is_company`. That is extra joins/queries on the hottest CRM URL.

**Under-eager (queries in Blade):** Personal Details fragment still has `Admin::where` lookups inside `personal_details.blade.php` (verification flags, related clients). Companion tab review lists these as P2.

**Correct eager load, wrong cardinality:** Signature dashboard `Document::with(['creator', 'signers', 'client', 'lead'])->paginate(20)` is fine for 20 rows; `index()` also loads **all** attach-modal clients/leads (`signatureAttachClients()` / `signatureAttachLeads()`). `create()` loads `Admin::whereIn('type', ['client', 'lead'])->get([...])` with no pagination. TTFB 2.4 s on `/signatures` is consistent with that.

### 4.2 Unbounded `get()` on detail fragments

These scale with the **client’s** history, not with page size:

| Method | Query | Risk |
|--------|--------|------|
| `notesTab()` | `Note::…->get()` all unassigned client notes | Hundreds of cards in one HTML response |
| `ClientDetailDocumentsTab::personalDocumentsByFolder()` | All personal docs + `staff` | Large blob/text `myfile` columns in memory |
| `visaDocumentsByFolder()` | All visa docs + `staff` + `signers` | Worse (signers collection) |
| `ClientDetailAccountTab::build()` | All ledger rows into PHP, then again for display | CPU + memory |
| `ClientDetailChecklistsTab::build()` | All cost-assignment forms with deep `with()`, plus **full** staff dropdowns and **all** active matters | Tab TTFB independent of which matter is open |

Laravel docs for v13 recommend `chunk` / `cursor` / pagination for large sets. Fragments currently choose completeness over bounds.

### 4.3 Double work: `count()` then `paginate()`

`ClientCrmFollowups::index()`:

```php
$query = $this->getBaseClientQuery();
$totalData = $query->count();           // COUNT(*)
$lists = $query->…->paginate($perPage); // COUNT(*) again + SELECT
```

Two identical filtered counts per list request. `paginate()` already provides `total()`. Allocation `EXISTS` runs twice.

### 4.4 Query-in-request instrumentation

When `APP_DEBUG` is true (typical local), `AppServiceProvider` registers `DB::listen` and on slow queries (`SLOW_QUERY_THRESHOLD`, default 1000 ms) runs `debug_backtrace()`. That does not slow fast queries much, but:

- Default threshold **1 s** is too coarse for CRM TTFB budgets (a 400 ms query is already a problem).
- `CLIENT_DETAIL_QUERY_LOG` is a better per-route profiler; it is off by default.

No slow-query hits were present in today’s local log file; that does not mean queries are fast — it means they are under 1000 ms or logging is off.

### 4.5 Hydration vs aggregates

Account balance is computed in PHP:

```php
foreach ($ledgerEntries as $entry) {
    $calculatedBalance += deposit - withdraw;
}
```

PostgreSQL can `SUM` with a filter on `void_fee_transfer` using the covering index. Laravel currently pays for full model hydration to add numbers.

Same class of issue: checklist and document grouping in PHP after `get()`.

### 4.6 Cache is configured, rarely used for CRM lists

Defaults: `CACHE_DRIVER=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`. Dashboard already defers heavy widgets. Client/matter **lists**, **global search**, **staff dropdowns** on checklists, and **signature attach** catalogs are still live SQL on every request. Redis is available; list endpoints mostly ignore it.

---

## 5. Hot surfaces (mapped to code)

### 5.1 Client list — `GET /clients`

- `getBaseClientQuery()`: `admins` filtered by `type`, `is_archived`, `is_deleted` + visibility `EXISTS`.
- Name/phone filters wrap `LOWER()` (index miss).
- Duplicate `COUNT(*)`.
- Full `Admin` models paginated.

**Degradation driver:** table size × allocation predicate × `SELECT *`.

### 5.2 Client matters list — `clientsmatterslist()`

Join `client_matters` × `admins` × `matters` × `workflow_stages`, closed-stage `LOWER(TRIM(ws.name)) NOT IN (…)` (function on column), name `LOWER LIKE`, plus `restrictMatterListToAllocatedClients`.

**Degradation driver:** join width + non-sargable stage filter + allocation.

### 5.3 Client detail shell — `detail()`

Even with tab payloads deferred:

1. Access check query + duplicate `exists()` + heavy `Admin::with(company…)->find()`.
2. EOI matter `ILIKE`/`LIKE` join on `client_matters`/`matters`.
3. Per-nomination `canAccessClientOrLead()` (N extra access queries).
4. `buildClientDetailMatterContext()` — multiple overlapping matter queries (count, list, URL matter, latest id, workflow stage, refs).
5. Lead path: **all** active `Staff` for assignable dropdown.

See companion doc for JS/HTML on top of this TTFB.

### 5.4 Activity feed — `activities()`

Indexed `(client_id, created_at)` for the default path. Keyword and staff filters are unindexed `LIKE` / `whereHas`. OFFSET + extra pages from the browser.

### 5.5 Global search

On PostgreSQL this is a **union of several leading-wildcard searches** (matters, admins, contacts, emails, companies) with `LOWER()` wrappers, then visibility mapping. Latency grows with `admins` + `client_matters` + contact/email tables. MySQL FULLTEXT shortcut is unused.

### 5.6 Account / invoices / receipts

Repeated scans of `account_client_receipts`; PHP balance; `DISTINCT ON *`; document URL attachment (batched — good). List pages (`invoicelist`, `clientreceiptlist`, `officereceiptlist`) already measured 1.1–1.5 s TTFB locally.

### 5.7 Signature dashboard — `/signatures`

Eager-loaded page of 20 documents **plus** full client/lead catalogs for the attach UI. Matches the worst measured TTFB in `slow-pages.md`.

### 5.8 Dashboard

`defer_heavy_widgets` is the right pattern. Remaining cost is `DashboardService` client-matter lists (indexes were added for this) plus workload. Superadmin vs allocated staff will still diverge because of `EXISTS`.

---

## 6. Indexes inventory (what we already bought)

| Migration | Purpose | Used by current SQL? |
|-----------|---------|----------------------|
| `admins_type_is_archived_idx` | Client/lead list base filter | Yes (btree prefix) |
| `admins_*_gin_trgm_idx` | Substring name/email/phone | **Often no** — queries use `LOWER(col)` |
| `client_matters_cid_sel_*_idx` | Allocation EXISTS | Yes, if planner picks them |
| `client_matters_dashboard_list_idx` + dash `sel_*` composites | Dashboard matter lists | Yes for those queries |
| `activities_logs_client_id_created_at_index` | Feed page 1 | Yes without keyword |
| `activities_logs_task_status_*_created_at_idx` | Task dashboards | Task filters only |
| `idx_notes_type_client_id_action_status` (+ assigned/cover) | Notes/actions | List/count of notes; **not** unbounded detail `get()` |
| `documents_client_folder_type_updated_at_idx` | Folder lookups | Partial; detail still loads all folders |
| `account_client_receipts_filter_sum_idx` | SUM outstanding | SUM yes; PHP ledger loop no |
| `email_logs_cm_client_conversion_idx` | Unread counts | Dashboard/email |
| MySQL `admins_global_search_ft` | Global search | **No on pgsql** |

**Missing / weak (candidates, not applied here)**

- Expression GIN on `LOWER(first_name)` **or** rewrite to `ILIKE` so existing GIN is used.
- `pg_trgm` (or FTS) on `activities_logs.description` / `subject` if keyword search stays.
- `pg_trgm` on `client_matters.client_unique_matter_no`, `department_reference`, `other_reference` (global search).
- Partial indexes e.g. `WHERE type IN ('client','lead') AND is_deleted IS NULL AND is_archived = 0` on `admins` for list pages.
- Keyset pagination support (`WHERE (created_at, id) < (?, ?)` ) for activities.

Do not add indexes blindly: each extra GIN slows writes (notes, activities, documents are high-churn). Prove with `EXPLAIN (ANALYZE, BUFFERS)` on staging.

---

## 7. Why it feels like “PostgreSQL is slower than MySQL”

After a MySQL → PostgreSQL move, teams often blame the engine. In this codebase the more accurate statement is:

1. MySQL `FULLTEXT` indexes were **never ported** (migration explicitly no-ops on pgsql).
2. Queries were written with `LOWER(col) LIKE` for “case-insensitive” behaviour. MySQL non-binary collations made that cheap; PostgreSQL `utf8` is case-sensitive and needs `ILIKE` or `citext` / FTS.
3. `OR` + correlated `EXISTS` is planned more conservatively on PostgreSQL once statistics show large `client_matters`.
4. Eloquent still emits `SELECT *` and unbounded `get()`; PostgreSQL then returns more toast data per row.

PostgreSQL is a good fit (covering `INCLUDE`, `DISTINCT ON`, `pg_trgm`, partial indexes). The SQL has not been rewritten to those features except in a few places (ledger `DISTINCT ON`, some `INCLUDE` indexes, some `ILIKE`).

---

## 8. Recommended measurement (before changing code)

Use staging with production-like volumes. Do **not** run `migrate:fresh` / `RefreshDatabase` against real data.

### 8.1 Application

```bash
# Temporarily in .env (local/staging only)
CLIENT_DETAIL_QUERY_LOG=true
LOG_SLOW_QUERIES=true
SLOW_QUERY_THRESHOLD=200
# LOG_ALL_QUERIES=true   # very noisy; short window only
```

Hit: client list (with name filter), matter list, client detail default tab, Account tab, Notes, Documents, `/signatures`, global search.

### 8.2 PostgreSQL

```sql
-- After enabling the extension on staging
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;

SELECT mean_exec_time, calls, query
FROM pg_stat_statements
ORDER BY mean_exec_time * calls DESC
LIMIT 30;

EXPLAIN (ANALYZE, BUFFERS)
-- paste a captured client-list / global-search query
```

Confirm whether `admins_first_name_gin_trgm_idx` is used when searching by name. If the plan shows `Seq Scan on admins` with `Filter: (lower(first_name) ~~ …)`, that validates §3.1.

### 8.3 Success metrics

| Metric | Target |
|--------|--------|
| HTML TTFB (list pages, allocated staff) | &lt; 800 ms p75 |
| Client detail shell TTFB (no tab payload) | &lt; 800 ms p75 |
| Fragment TTFB (notes/docs/account) first page | &lt; 500 ms p75 |
| Queries per client-list request | 1 filtered select + 1 count (or window count), not 3+ |
| Global search p75 | &lt; 400 ms for typical 3–8 character names |

Re-measure the [slow-pages.md](../slow-pages.md) table after each P0 fix.

---

## 9. Prioritized remediation (advisory)

No items below were implemented as part of this write-up.

### P0 — Unlock indexes you already have

1. Replace `LOWER(col) LIKE ?` with `col ILIKE ?` (or `ILIKE` + `CONCAT`) on client filters, matter list name search, and PostgreSQL global search. Keep bindings parameterized.
2. Stop wrapping concatenated names in `LOWER()`; use `ILIKE` on the concatenated expression **or** search first/last separately (GIN can help each column).
3. Remove the extra `$query->count()` on client index; use paginator `total()` (or a slim cached count for the unfiltered badge).

### P0 — Bound the unbounded

4. Paginate notes fragment (or reuse `/get-notes`); first paint ≤ 20–40 rows.
5. Load documents by folder + page, not all personal/visa docs.
6. Replace PHP ledger walk with `SUM` SQL; paginate ledger HTML.
7. Signature dashboard: do not load every client/lead for the attach modal; use typeahead/API.

### P1 — Shrink Eloquent payloads

8. `detail()`: load company graph only when `is_company` (cheap `select is_company` first).
9. Client list `select()` only columns the table needs (`id`, `client_id`, names, email, phone, type, dates). Always include keys used by relations.
10. Batch `canAccessClientOrLead` for nomination/company lists (`globalSearchCanAccessMap` already exists — reuse).
11. Collapse `buildClientDetailMatterContext()` overlapping queries.

### P1 — Allocation at scale

12. `EXPLAIN` allocation `EXISTS` for a non-exempt role on staging. If nested loop dominates, consider a materialized `staff_id × admin_id` visibility table maintained on matter/grant write (bigger change; only if indexes are not enough).
13. Enable `Model::preventLazyLoading(! app()->isProduction())` so new N+1s fail in local/CI.

### P2 — Search & activities

14. PostgreSQL global search: `ILIKE` + existing GIN, or `to_tsvector` GIN if product wants stemming. Do not copy MySQL `MATCH` syntax.
15. Activity keyword: `pg_trgm` on `description`/`subject` **or** drop substring search in favour of prefix / FTS.
16. Keyset pagination for the activity feed (`created_at, id`).

### P2 — PostgreSQL ops

17. Production: PgBouncer, `pg_stat_statements`, autovacuum review on `activities_logs` / `notes` / `email_logs`.
18. Lower `SLOW_QUERY_THRESHOLD` to 200–300 ms in staging.
19. Checklist tab: cache staff/matter dropdowns (Redis, short TTL) instead of querying all active staff/matters per fragment.

### P3

20. Split `admins` read models (list DTO vs full detail) or eventually split staff vs CRM person tables (already discussed in staff-table plans — do not block P0 on that).
21. Frontend code-split of `detail-main.js` — see companion CWV review; it does not replace the SQL work above.

---

## 10. What not to do

- Do **not** `migrate:fresh` / `db:wipe` / `RefreshDatabase` on the project database to “test” performance.
- Do **not** add more GIN indexes on every text column without `EXPLAIN ANALYZE` — write amplification on `activities_logs` will hurt ingestion.
- Do **not** enable `LOG_ALL_QUERIES` in production.
- Do **not** set `DB_PERSISTENT=true` without a pooler; PHP-FPM × persistent connections exhaust `max_connections`.
- Do **not** treat superadmin TTFB as the user experience; measure **allocated** roles (strict allocation on).

---

## 11. Files and docs referenced

| Layer | Paths |
|-------|--------|
| List / search / detail | `app/Traits/ClientQueries.php`, `app/Traits/ClientCrmFollowups.php` |
| Visibility | `app/Support/StaffClientVisibility.php` |
| Detail tabs | `app/Support/ClientDetailAccountTab.php`, `ClientDetailDocumentsTab.php`, `ClientDetailChecklistsTab.php` |
| Signatures | `app/Http/Controllers/CRM/SignatureDashboardController.php` |
| DB config / query log | `config/database.php`, `app/Providers/AppServiceProvider.php` |
| Index migrations | `database/migrations/2026_04_22_*`, `2026_04_23_*`, `2026_04_24_*` |
| Prior measurements | `slow-pages.md` |
| Frontend companion | `docs/client-detail-tab-performance-review.md` |
| `admins` width | `docs/ADMINS_TABLE_COLUMNS.md` |

---

*This analysis is advisory. Confirm with `EXPLAIN (ANALYZE, BUFFERS)` and `CLIENT_DETAIL_QUERY_LOG` on staging before implementing P0 items.*
