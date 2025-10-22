# Database Tables - Deletion Summary

## ⚠️ BACKUP FIRST! ⚠️
Before deleting any tables, create a full database backup.

---

## IMMEDIATE DELETION CANDIDATES (High Confidence)

### Backup Tables
```sql
DROP TABLE IF EXISTS `theme_options_bkk_14aug2025`;
```

### Travel/Tourism Features (No Models, Likely Unused)
```sql
DROP TABLE IF EXISTS `airports`;
DROP TABLE IF EXISTS `hotels`;
DROP TABLE IF EXISTS `destinations`;
DROP TABLE IF EXISTS `cities`;
```

### E-commerce/Rewards Features (No Models)
```sql
DROP TABLE IF EXISTS `cashbacks`;
DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `client_ratings`;
DROP TABLE IF EXISTS `client_monthly_rewards`;
```

### Education/Mentoring Features (No Models)
```sql
DROP TABLE IF EXISTS `mentorings`;
DROP TABLE IF EXISTS `academic_requirements`;
DROP TABLE IF EXISTS `education`;
DROP TABLE IF EXISTS `subjects`;
DROP TABLE IF EXISTS `subject_areas`;
```

### Old/Replaced Tables
```sql
DROP TABLE IF EXISTS `tbl_paid_appointment_payment`;
DROP TABLE IF EXISTS `promocode_uses`;
```

**Count: 17 tables**

---

## LIKELY SAFE TO DELETE (Marketing/CMS - Verify First)

### Marketing Features
```sql
DROP TABLE IF EXISTS `banners`;
DROP TABLE IF EXISTS `popups`;
DROP TABLE IF EXISTS `offers`;
DROP TABLE IF EXISTS `promotions`;
DROP TABLE IF EXISTS `free_downloads`;
DROP TABLE IF EXISTS `media_images`;
DROP TABLE IF EXISTS `why_chooseuses`;
```

### CMS/Content
```sql
DROP TABLE IF EXISTS `blogs`;
DROP TABLE IF EXISTS `blog_categories`;
DROP TABLE IF EXISTS `cms_pages`;
DROP TABLE IF EXISTS `faqs`;
DROP TABLE IF EXISTS `reviews`;
```

### Navigation
```sql
DROP TABLE IF EXISTS `navmenus`;
```

**Count: 13 tables**

---

## VERIFY BEFORE DELETION (May Have Business Logic)

### Duplicate/Old Systems (Check which is active)
```sql
-- Verify: enquiries vs leads
DROP TABLE IF EXISTS `enquiries`;
DROP TABLE IF EXISTS `enquiry_sources`;

-- Verify: followups vs lead_followups/invoice_followups
DROP TABLE IF EXISTS `followups`;
DROP TABLE IF EXISTS `followup_types`;

-- Verify: services vs our_services
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `service_accounts`;
DROP TABLE IF EXISTS `interested_services`;

-- Verify: contacts vs clients
DROP TABLE IF EXISTS `contacts`;

-- Verify: partners vs agents
DROP TABLE IF EXISTS `partners`;
DROP TABLE IF EXISTS `partner_branches`;
DROP TABLE IF EXISTS `partner_types`;
DROP TABLE IF EXISTS `representing_partners`;
```

### Product Management (May be unused)
```sql
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `product_area_levels`;
DROP TABLE IF EXISTS `product_types`;
DROP TABLE IF EXISTS `check_products`;
DROP TABLE IF EXISTS `check_applications`;
DROP TABLE IF EXISTS `check_partners`;
```

### Other
```sql
DROP TABLE IF EXISTS `attach_files`;  -- vs attachments
DROP TABLE IF EXISTS `api_tokens`;  -- vs personal_access_tokens
DROP TABLE IF EXISTS `checklists`;  -- vs document_checklists
DROP TABLE IF EXISTS `client_married_details`;
DROP TABLE IF EXISTS `client_occupation_lists`;
DROP TABLE IF EXISTS `download_schedule_dates`;
DROP TABLE IF EXISTS `email_uploads`;
DROP TABLE IF EXISTS `message_recipients`;
DROP TABLE IF EXISTS `omrs`;
DROP TABLE IF EXISTS `profiles`;
DROP TABLE IF EXISTS `schedule_items`;
DROP TABLE IF EXISTS `sources`;
DROP TABLE IF EXISTS `to_do_groups`;
DROP TABLE IF EXISTS `verified_numbers`;
DROP TABLE IF EXISTS `visa_types`;
DROP TABLE IF EXISTS `wallets`;
```

**Count: 32 tables**

---

## FINANCIAL TABLES (Consult Accounting Before Deletion)

```sql
DROP TABLE IF EXISTS `quotations`;
DROP TABLE IF EXISTS `quotation_infos`;
DROP TABLE IF EXISTS `markups`;
DROP TABLE IF EXISTS `currencies`;
DROP TABLE IF EXISTS `taxes`;
DROP TABLE IF EXISTS `tax_rates`;
DROP TABLE IF EXISTS `fee_types`;
DROP TABLE IF EXISTS `fee_options`;
DROP TABLE IF EXISTS `fee_option_types`;
DROP TABLE IF EXISTS `income_sharings`;
DROP TABLE IF EXISTS `account_all_invoice_receipts`;
DROP TABLE IF EXISTS `account_client_receipts`;
```

**Count: 12 tables**

---

## TEMPLATE/CMS SYSTEM (Verify if Template Engine is Used)

```sql
DROP TABLE IF EXISTS `templates`;
DROP TABLE IF EXISTS `template_infos`;
DROP TABLE IF EXISTS `theme_options`;
```

**Count: 3 tables**

---

## 🚫 DO NOT DELETE - SYSTEM TABLES

These are essential Laravel/system tables:
- `migrations`
- `cache`
- `cache_locks`
- `failed_jobs`
- `jobs`
- `password_resets`
- `password_reset_links`
- `personal_access_tokens`
- `sessions`

---

## 🚫 DO NOT DELETE - OAUTH TABLES (If using Laravel Passport)

- `oauth_access_tokens`
- `oauth_auth_codes`
- `oauth_clients`
- `oauth_personal_access_clients`
- `oauth_refresh_tokens`

---

## TOTAL SUMMARY

| Category | Count | Confidence |
|----------|-------|------------|
| **Immediate Deletion** | 17 | Very High |
| **Likely Safe** | 13 | High |
| **Verify First** | 32 | Medium |
| **Financial (Verify)** | 12 | Low-Medium |
| **Template System** | 3 | Medium |
| **TOTAL CANDIDATES** | **77** | - |

---

## RECOMMENDED DELETION SEQUENCE

### Phase 1: Obvious Backups (17 tables)
Start with high-confidence deletions. Monitor for 1-2 weeks.

### Phase 2: Marketing/CMS (13 tables)
If website features are confirmed unused.

### Phase 3: Duplicates (32 tables)
After verifying which systems are active.

### Phase 4: Financial (12 tables)
Only after thorough verification with stakeholders.

### Phase 5: Templates (3 tables)
If template engine confirmed unused.

---

## VERIFICATION QUERIES

Before deletion, run these queries to check data presence:

```sql
-- Check if table has data
SELECT COUNT(*) FROM table_name;

-- Check table size
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Size (MB)"
FROM information_schema.TABLES 
WHERE table_schema = 'migration_manager_crm'
    AND table_name IN ('airports', 'hotels', 'banners', 'popups')
ORDER BY (data_length + index_length) DESC;

-- Check last modification (if using timestamps)
SELECT MAX(updated_at) as last_update FROM table_name;
```

---

## SAFETY CHECKLIST

- [ ] Full database backup created
- [ ] Business stakeholders consulted
- [ ] Raw SQL queries checked (grep codebase)
- [ ] External integrations verified
- [ ] Delete in small batches
- [ ] Monitor application logs after each batch
- [ ] Keep backups for 30+ days
- [ ] Document deletion decisions

---

Generated: October 22, 2025
Database: migration_manager_crm
Total Tables Analyzed: 205

