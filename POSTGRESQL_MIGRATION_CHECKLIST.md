# PostgreSQL Migration Verification Checklist

This document outlines critical areas to verify after migrating from MySQL to PostgreSQL to ensure everything is working correctly.

## 1. Database Connection & Configuration

### ✅ Check Database Configuration
- [ ] Verify `.env` file has correct PostgreSQL credentials:
  ```
  DB_CONNECTION=pgsql
  DB_HOST=127.0.0.1
  DB_PORT=5432
  DB_DATABASE=your_database_name
  DB_USERNAME=your_username
  DB_PASSWORD=your_password
  ```
- [ ] Verify `config/database.php` default connection is set to `pgsql`
- [ ] Test database connection: `php artisan tinker` → `DB::connection()->getPdo()`

### ✅ Connection Testing
```bash
# Test connection
php artisan migrate:status

# Check if you can query the database
php artisan tinker
>>> DB::table('migrations')->count()
```

## 2. Data Integrity Verification

### ✅ Record Counts
- [ ] Compare record counts between MySQL source and PostgreSQL target for all major tables:
  ```sql
  -- PostgreSQL
  SELECT 
    schemaname,
    tablename,
    n_live_tup as row_count
  FROM pg_stat_user_tables
  ORDER BY tablename;
  ```
- [ ] Verify critical tables have expected data:
  - `admins` (clients/users)
  - `client_visa_countries`
  - `client_addresses`
  - `client_travel_informations`
  - `account_client_receipts`
  - Other business-critical tables

### ✅ Data Validation
- [ ] Check for NULL values in critical fields (should match MySQL)
- [ ] Verify foreign key relationships are intact
- [ ] Check date fields are properly migrated (no `0000-00-00` dates)
- [ ] Verify text encoding (UTF-8) is correct

## 3. PostgreSQL Sequences

### ✅ Auto-Increment Sequences

**Quick Verification:**
```bash
# Run the dedicated sequence verification script
php verify_postgresql_sequences.php
```

This script will:
- Find all tables with ID columns
- Check if sequences exist for each table
- Verify sequence values are synchronized with table data
- Identify missing sequences
- Generate fix SQL if needed

**Manual Verification:**
- [ ] Verify all tables with auto-increment IDs have proper sequences:
  ```sql
  -- Check sequences
  SELECT 
    schemaname,
    sequencename,
    last_value
  FROM pg_sequences
  WHERE schemaname = 'public'
  ORDER BY sequencename;
  ```
- [ ] Check sequences are synchronized (sequence value should be >= max ID):
  ```sql
  -- Compare max ID with sequence value
  SELECT 
    t.tablename,
    (SELECT MAX(id) FROM table_name) as max_id,
    s.last_value as sequence_value,
    CASE 
      WHEN s.last_value < (SELECT MAX(id) FROM table_name) 
      THEN 'NEEDS FIX' 
      ELSE 'OK' 
    END as status
  FROM pg_tables t
  JOIN pg_sequences s ON s.sequencename = t.tablename || '_id_seq'
  WHERE t.schemaname = 'public';
  ```
- [ ] Verify column defaults are set to use sequences:
  ```sql
  -- Check if ID columns use sequences as default
  SELECT 
    table_name,
    column_name,
    column_default
  FROM information_schema.columns
  WHERE table_schema = 'public'
    AND column_name = 'id'
    AND column_default LIKE '%nextval%';
  ```

**Testing Sequences:**
- [ ] Test inserting new records to ensure sequences work:
  ```php
  // In tinker
  $test = new \App\Models\Admin();
  $test->first_name = 'Test';
  $test->save();
  // Verify ID is generated correctly and is > max existing ID
  ```
- [ ] Test with a table that has existing data:
  ```php
  // Get current max ID
  $maxId = DB::table('admins')->max('id');
  
  // Insert new record
  $new = DB::table('admins')->insert(['first_name' => 'Test']);
  $newId = DB::getPdo()->lastInsertId();
  
  // Verify new ID > max ID
  assert($newId > $maxId, "Sequence not working correctly!");
  ```

**Fixing Sequence Issues:**
If sequences are missing or out of sync, you can:

1. **Use the auto-fix script (recommended):**
   ```bash
   # Dry run first to see what will be fixed
   php fix_all_sequences.php --dry-run
   
   # Apply fixes
   php fix_all_sequences.php
   ```

2. **Manual fix for a specific table:**
   ```sql
   -- Fix sequence for a specific table
   SELECT setval('table_name_id_seq', (SELECT MAX(id) FROM table_name) + 1, false);
   ```

3. **Create missing sequence:**
   ```sql
   -- If sequence doesn't exist
   CREATE SEQUENCE table_name_id_seq START WITH 1;
   ALTER TABLE table_name ALTER COLUMN id SET DEFAULT nextval('table_name_id_seq'::regclass);
   ALTER SEQUENCE table_name_id_seq OWNED BY table_name.id;
   ```

**Common Sequence Issues:**
- ❌ **Sequence missing**: Table has no sequence → Create sequence
- ❌ **Sequence out of sync**: `last_value < MAX(id)` → Run `setval()`
- ❌ **No default set**: Column doesn't use sequence → Set `ALTER COLUMN ... SET DEFAULT`
- ❌ **Sequence not owned**: Sequence not linked to column → Run `ALTER SEQUENCE ... OWNED BY`

## 4. SQL Syntax Compatibility

### ✅ MySQL-Specific Functions (Already Converted)
The following have been identified and converted:
- ✅ `TO_DATE()` - Used in `FinancialStatsService.php` (PostgreSQL syntax)
- ✅ `STRING_AGG()` - Used in `ClientsController.php` (PostgreSQL syntax)
- ✅ `COALESCE()` with `||` - Used for string concatenation (PostgreSQL syntax)

### ⚠️ Check for Remaining MySQL-Specific Syntax
Search for and verify these patterns are PostgreSQL-compatible:

- [ ] **GROUP_CONCAT** → Should be `STRING_AGG(DISTINCT column, ', ' ORDER BY column)`
- [ ] **IFNULL** → Should be `COALESCE`
- [ ] **CONCAT()** → Should use `||` operator or `CONCAT()` (PostgreSQL supports both)
- [ ] **DATE_FORMAT** → Should use `TO_CHAR(date_column, 'format')`
- [ ] **STR_TO_DATE** → Should use `TO_DATE(string, 'format')`
- [ ] **UNIX_TIMESTAMP** → Should use `EXTRACT(EPOCH FROM timestamp)`
- [ ] **FROM_UNIXTIME** → Should use `TO_TIMESTAMP(unix_timestamp)`

### ✅ Raw SQL Queries
- [ ] Review all `DB::raw()` and `whereRaw()` calls in:
  - `app/Services/FinancialStatsService.php` ✅ (Already using PostgreSQL syntax)
  - `app/Http/Controllers/CRM/ClientsController.php` ✅ (Already using STRING_AGG)
  - Any other service or controller files

## 5. Data Types & Constraints

### ✅ Data Type Compatibility
- [ ] Verify `TEXT` fields work correctly (PostgreSQL handles TEXT differently)
- [ ] Check `BOOLEAN` fields (PostgreSQL uses true/false, not 0/1)
- [ ] Verify `DATETIME` vs `TIMESTAMP` usage
- [ ] Check `ENUM` types (PostgreSQL doesn't have ENUM, uses CHECK constraints or separate tables)

### ✅ Constraints
- [ ] Verify foreign keys are working
- [ ] Check unique constraints
- [ ] Verify NOT NULL constraints
- [ ] Test CHECK constraints if any

## 6. Query Performance

### ✅ Indexes
- [ ] Verify all indexes were migrated:
  ```sql
  SELECT 
    tablename,
    indexname,
    indexdef
  FROM pg_indexes
  WHERE schemaname = 'public'
  ORDER BY tablename, indexname;
  ```
- [ ] Compare index count with MySQL source
- [ ] Check composite indexes are present

### ✅ Query Performance Testing
- [ ] Test slow queries from application logs
- [ ] Use `EXPLAIN ANALYZE` on critical queries:
  ```sql
  EXPLAIN ANALYZE SELECT ...;
  ```
- [ ] Monitor query execution times
- [ ] Check for missing indexes on frequently queried columns

## 7. Application Functionality Testing

### ✅ Critical Features
Test these core functionalities:

- [ ] **User Authentication & Login**
  - Login works
  - Session management
  - Password reset

- [ ] **Client Management**
  - Create new client
  - Edit client details
  - Search clients
  - View client details

- [ ] **Financial Operations**
  - Create receipts
  - Generate invoices
  - View financial reports
  - Date-based queries (using TO_DATE)

- [ ] **Data Entry Forms**
  - Client personal details
  - Visa information
  - Address information
  - Travel information

- [ ] **Reports & Analytics**
  - Dashboard statistics
  - Financial reports
  - Date range filters
  - Aggregation queries

### ✅ API Endpoints
- [ ] Test all API endpoints in `routes/api.php`
- [ ] Verify JSON responses are correct
- [ ] Check date formatting in API responses
- [ ] Test pagination

## 8. Date & Time Handling

### ✅ Date Format Issues
- [ ] Verify dates stored as VARCHAR (dd/mm/yyyy) are handled correctly
- [ ] Check `TO_DATE()` conversions work in queries
- [ ] Test date range queries
- [ ] Verify timezone handling (PostgreSQL uses TIMESTAMP WITH TIME ZONE)

### ✅ Date Functions
Test these date operations:
```php
// In tinker
DB::table('account_client_receipts')
  ->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') >= TO_DATE(?, 'DD/MM/YYYY')", ['01/01/2024'])
  ->count();
```

## 9. String Operations

### ✅ String Concatenation
- [ ] Verify `||` operator works (PostgreSQL)
- [ ] Check `CONCAT()` function if used
- [ ] Test `STRING_AGG()` for grouped concatenation

### ✅ String Functions
- [ ] `LIKE` queries work correctly
- [ ] Case-sensitive/insensitive searches
- [ ] Pattern matching with `ILIKE` (PostgreSQL-specific)

## 10. Transactions & Locking

### ✅ Transaction Behavior
- [ ] Test transaction rollbacks
- [ ] Verify nested transactions (if used)
- [ ] Check deadlock handling

### ✅ Locking
- [ ] Test row-level locking
- [ ] Verify SELECT FOR UPDATE works
- [ ] Check advisory locks if used

## 11. Error Handling

### ✅ Database Errors
- [ ] Check application logs for PostgreSQL-specific errors
- [ ] Verify error messages are user-friendly
- [ ] Test constraint violation handling
- [ ] Check unique constraint violations

### ✅ Exception Handling
- [ ] Verify `PDOException` handling
- [ ] Check `QueryException` messages
- [ ] Test connection timeout handling

## 12. Migration Status

### ✅ Migration Files
- [ ] All migrations have run successfully:
  ```bash
  php artisan migrate:status
  ```
- [ ] Verify new migration files executed:
  - `2025_12_15_000000_fix_and_migrate_client_visa_countries.php`
  - `2025_12_15_000001_fix_and_migrate_client_addresses.php`
  - `2025_12_15_000002_fix_and_migrate_client_travel_informations.php`

### ✅ Migration Logs
- [ ] Check `storage/logs/laravel.log` for migration errors
- [ ] Verify no errors during data migration
- [ ] Check sequence fix logs

## 13. Backup & Recovery

### ✅ Backup Strategy
- [ ] Create a full PostgreSQL backup:
  ```bash
  pg_dump -U username -d database_name > backup.sql
  ```
- [ ] Test backup restoration
- [ ] Document backup procedures

## 14. Environment-Specific Checks

### ✅ Development Environment
- [ ] All tests pass
- [ ] Development data is accessible
- [ ] Debugging tools work

### ✅ Staging/Production (When Ready)
- [ ] Production data migrated correctly
- [ ] Performance is acceptable
- [ ] Monitoring is in place
- [ ] Rollback plan is ready

## 15. Code Review Checklist

### ✅ Search for Potential Issues
Run these searches to find MySQL-specific code:

```bash
# Search for MySQL-specific functions
grep -r "GROUP_CONCAT" app/
grep -r "IFNULL" app/
grep -r "DATE_FORMAT" app/
grep -r "STR_TO_DATE" app/
grep -r "UNIX_TIMESTAMP" app/
grep -r "FROM_UNIXTIME" app/

# Search for raw SQL that might be MySQL-specific
grep -r "DB::raw" app/ | grep -i "mysql"
grep -r "whereRaw" app/
```

## 16. Performance Monitoring

### ✅ Query Monitoring
- [ ] Enable PostgreSQL slow query log
- [ ] Monitor connection pool usage
- [ ] Check for connection leaks
- [ ] Monitor database size growth

### ✅ Application Monitoring
- [ ] Monitor response times
- [ ] Check error rates
- [ ] Monitor memory usage
- [ ] Track database connection pool

## Quick Verification Commands

```bash
# 1. Test database connection
php artisan tinker
>>> DB::connection()->getPdo()

# 2. Check migration status
php artisan migrate:status

# 3. Count records in key tables
php artisan tinker
>>> DB::table('admins')->count()
>>> DB::table('client_visa_countries')->count()
>>> DB::table('client_addresses')->count()

# 4. Test a complex query
php artisan tinker
>>> DB::table('account_client_receipts')
     ->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') >= TO_DATE(?, 'DD/MM/YYYY')", ['01/01/2024'])
     ->count()

# 5. Check sequences
psql -U username -d database_name
>>> \ds
>>> SELECT last_value FROM client_visa_countries_id_seq;
```

## Common Issues & Solutions

### Issue: Sequence not working
**Solution:** Run the migration files that fix sequences, or manually:
```sql
SELECT setval('table_name_id_seq', (SELECT MAX(id) FROM table_name));
```

### Issue: Date format errors
**Solution:** Ensure all date comparisons use `TO_DATE()` for VARCHAR date fields:
```php
->whereRaw("TO_DATE(trans_date, 'DD/MM/YYYY') BETWEEN ...")
```

### Issue: String concatenation errors
**Solution:** Use PostgreSQL `||` operator or `CONCAT()`:
```php
DB::raw("COALESCE(first_name, '') || ' ' || COALESCE(last_name, '')")
```

### Issue: GROUP_CONCAT not found
**Solution:** Use PostgreSQL `STRING_AGG()`:
```php
DB::raw("STRING_AGG(DISTINCT column, ', ' ORDER BY column)")
```

## Next Steps

1. ✅ Complete all checklist items above
2. ✅ Document any issues found and their resolutions
3. ✅ Update code comments where MySQL→PostgreSQL conversions were made
4. ✅ Consider adding database-agnostic query builders where possible
5. ✅ Set up monitoring and alerting for production

---

**Last Updated:** After PostgreSQL migration
**Status:** In Progress - Complete all items before production deployment
