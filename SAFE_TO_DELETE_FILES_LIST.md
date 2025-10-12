# Safe to Delete Files - Migration Manager

This document lists files that can be safely deleted from the project root. These are categorized by type.

## 1. One-Time Migration/Fix Scripts (ROOT DIRECTORY)
These were used for one-time fixes and migrations and are no longer needed:

- `check_travel_data.php` - One-time data check script
- `check_view_filtering.php` - One-time view filtering check
- `complete_database_restore.php` - Database restoration script
- `fix_admins_table_structure.php` - One-time database fix
- `fix_notes_client_relationships.php` - One-time relationship fix
- `fix_orphaned_notes.php` - One-time orphaned notes fix
- `find_test_clients_for_documents.php` - Testing utility
- `import_anzsco_data.php` - One-time ANZSCO import (if already completed)
- `remove_duplicate_note_methods.php` - One-time cleanup script
- `verify_anzsco_import.php` - Verification script (if import verified)

## 2. PowerShell Migration Scripts (ROOT DIRECTORY)
One-time migration scripts that are no longer needed:

- `cleanup_remaining_public_files.ps1`
- `fix_routes_simple.ps1`
- `fix-adminconsole-route-names.ps1`
- `migrate-adminconsole-urls.ps1`
- `update_admin_routes.ps1`

## 3. Migration Scripts Folder (ENTIRE DIRECTORY)
If migrations are complete, this entire folder can be removed:

- `migration-scripts/` - Entire directory including:
  - `backup-system.php`
  - `controller-import-mapper.php`
  - `migrate-routes.php`
  - `README.md`
  - `route-architecture-design.md`
  - `route-converter.php`
  - `test-migration-scripts.php`
  - `test-report.txt`

## 4. Test Files (ROOT DIRECTORY)
Testing and debugging files no longer needed in production:

- `test_direct_access.php`
- `test_email_sync_system.php`
- `test_messaging_api.php`
- `test_messaging.html`
- `test_regional_code_browser.html`
- `test_simple_textbox.html`

## 5. Backup Files
Old backup files from previous edits:

- `app/Http/Controllers/Admin/ClientsController.php.backup`
- `resources/views/Admin/clients/edit.blade.php.backup`

## 6. Default/Sample Files (ROOT DIRECTORY)
Files that came with server installations:

- `index.html` - Default Apache/web server page
- `index.nginx-debian.html` - Default Nginx page
- `default.php` - Sample/default PHP file
- `info.php` - PHP info page (security risk in production)

## 7. Network Test Results
Old network diagnostic files:

- `network_test_imap_zoho_com_20250909_155256.json`
- `network_test_imap_zoho_com_20250909_155336.json`

## 8. Installation Executables
Third-party installers that should be downloaded separately:

- `pdftk_server_installer.exe` - PDFtk installer
- `pdftk_test.txt` - Test file for PDFtk

## 9. Text Files (ROOT DIRECTORY)
Reference/temporary text files:

- `form956_fields.txt` - Form fields reference (if documented elsewhere)
- `robust_email_sync_env.txt` - Environment config notes
- `migrationmanager` - Empty or temporary file

## 10. Completed Migration Documentation (OPTIONAL - ARCHIVE THESE FIRST)
If migrations are complete and documented in main README:

### Migration Status Documents:
- `ADMINCONSOLE_MIGRATION_STATUS.md`
- `ADMINCONSOLE_VERIFICATION_REPORT.md`
- `CLIENT_NOTES_CONTROLLER_MIGRATION_COMPLETE.md`
- `CLIENTNOTES_DEEP_CHECK_REPORT.md`
- `DOCUMENT_DOWNLOAD_PREVIEW_FIX_COMPLETE.md`
- `DUPLICATE_METHODS_CLEANUP_COMPLETE.md`
- `IMPLEMENTATION_COMPLETE.md`
- `MODAL_CONTROLLER_MAPPING.md`
- `MODAL_REFACTORING_SUMMARY.md`

### ANZSCO Implementation Documents (if completed):
- `ANZSCO_DATABASE_GUIDE.md`
- `ANZSCO_FINAL_CHECKLIST.md`
- `ANZSCO_IMPLEMENTATION_SUMMARY.md`
- `ANZSCO_IMPORT_GUIDE.md`
- `ANZSCO_OVERLAPPING_FIX.md`
- `ANZSCO_QUICK_START.md`

### Troubleshooting Guides (if issues resolved):
- `CLIENT_PORTAL_API_TROUBLESHOOTING_GUIDE.md`
- `CRITICAL_ISSUE_PASSWORD_AND_N1_ANALYSIS.md`
- `DEEP_ERROR_ANALYSIS.md`
- `EMAIL_CONNECTION_TROUBLESHOOTING.md`
- `GOOGLE_AUTOCOMPLETE_ISSUES.md`
- `N1_QUERY_FIX_PROPOSAL.md`
- `PYTHON_VENV_TROUBLESHOOTING.md`
- `QUALIFICATION_SECTION_FIXES.md`
- `WEBSOCKET_TROUBLESHOOTING_GUIDE.md`

### Setup/Configuration Guides (if already set up):
- `HUBDOC_EMAIL_SETUP.md`
- `LISTING_PAGES_CSS_GUIDE.md`
- `MESSAGING_SETUP_GUIDE.md`
- `PRODUCTION_WEBSOCKET_CONFIG.md`
- `PUBLIC_FOLDER_CLEANUP_LIST.md`
- `UTF8_FIX_DOCUMENTATION.md`
- `VERIFICATION_PROMPT.md`

### Deployment Guides (archive after deployment):
- `QUICK_LINUX_DEPLOYMENT.md`
- `QUICK_TEST_GUIDE.md`
- `ROBUST_DEPLOYMENT_GUIDE.md`
- `WINDOWS_TO_LINUX_MIGRATION_GUIDE.md`

## 11. Postman Collection (MOVE TO DOCS)
API testing collection that should be in a docs folder:

- `Client_Portal_Postman_Collection.json`

## 12. Optional: Python Folders (if not in use)
**VERIFY BEFORE DELETING** - Check if these are used by the application:

- `python/` - Standalone Python scripts
- `python_outlook_web/` - Python Outlook integration

## 13. Optional: Shell Scripts (if migrations complete)
- `setup_reverb_production.sh` - If Reverb is already set up

## 14. Websocket Documentation Folder (OPTIONAL - MOVE TO DOCS)
- `websocket_reverb_laravel/` - Documentation that could be moved to a docs folder

## Recommended Action Steps:

### Step 1: Immediate Safe Deletion
Delete categories 1-9 immediately - these are temporary, test, and backup files.

### Step 2: Archive Then Delete
For category 10 (documentation):
1. Create a `docs/archive/` folder
2. Move all completed migration/troubleshooting docs there
3. Update main README with links to important archived docs
4. Later, delete archived docs if not needed

### Step 3: Verify Before Deletion
For categories 11-14:
- Check if Python folders are imported/used anywhere in PHP code
- Verify Postman collection isn't being auto-updated
- Confirm shell scripts aren't in cron jobs

### Step 4: Keep These Files
**DO NOT DELETE:**
- `README.md` - Main project documentation
- `CRM_SYSTEM_DOCUMENTATION.md` - System documentation
- `REFACTORING_RECOMMENDATIONS.md` - Future reference
- `Client_portal_README.md` - Active documentation
- `composer.json`, `composer.lock`, `composer.phar` - PHP dependencies
- `package.json`, `package-lock.json`, `yarn.lock` - Node dependencies
- `artisan` - Laravel CLI tool
- `server.php` - Laravel server entry point
- All config, routes, app, resources, database folders
- `email-template/` - Active templates

## Estimated Space Savings:
- Scripts & Tests: ~500KB
- Documentation: ~2-5MB
- Executables: ~50-100MB (pdftk_server_installer.exe)
- Total: ~50-105MB

## Notes:
- Always backup before bulk deletion
- Run `composer dump-autoload` after deleting PHP files
- Clear Laravel cache: `php artisan cache:clear`
- Test application after deletion

