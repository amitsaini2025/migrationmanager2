# 500 Server Error - Debugging Guide

## Error Details
- **URL**: `POST /adminconsole/system/users/44007`
- **Status**: 500 Server Error
- **Content-Type**: `multipart/form-data`
- **Server**: Nginx/1.14.1
- **Framework**: Laravel

## Root Cause Analysis

The error occurs in the `UserController@update` method. Since it works locally but fails on production, this suggests an **environment-specific issue**.

## Most Likely Causes (in order of probability)

### 1. **Database Connection/Query Issues** ⚠️ HIGH PRIORITY
- **Symptoms**: Works locally, fails on production
- **Possible causes**:
  - Database connection timeout
  - Missing database credentials in `.env`
  - Database server not accessible
  - Different database schema between local and production
  - Constraint violations (foreign keys, unique constraints)
  
**How to check:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### 2. **Missing Environment Variables** ⚠️ HIGH PRIORITY
- **Symptoms**: Works locally, fails on production
- **Possible causes**:
  - Missing `APP_KEY` in production `.env`
  - Missing database credentials
  - Missing `GOOGLE_MAPS_API_KEY` (if used)
  - Different `APP_ENV` or `APP_DEBUG` settings

**How to check:**
```bash
# Compare .env files
# Ensure all required variables are set in production
php artisan config:clear
php artisan cache:clear
```

### 3. **File Permission Issues** ⚠️ MEDIUM PRIORITY
- **Symptoms**: Works locally, fails on production
- **Possible causes**:
  - `storage/` directory not writable
  - `bootstrap/cache/` directory not writable
  - `public/img/profile_imgs/` directory doesn't exist or not writable (if file upload is involved)

**How to fix:**
```bash
# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Create profile_imgs directory if missing
mkdir -p public/img/profile_imgs
chmod 775 public/img/profile_imgs
```

### 4. **PHP Configuration Differences** ⚠️ MEDIUM PRIORITY
- **Symptoms**: Works locally, fails on production
- **Possible causes**:
  - `memory_limit` too low
  - `post_max_size` too small (request is 3112 bytes, but check if files are involved)
  - `upload_max_filesize` too small
  - `max_execution_time` too low
  - Different PHP version

**How to check:**
```bash
# Check PHP configuration
php -i | grep -E "memory_limit|post_max_size|upload_max_filesize|max_execution_time"

# Or create a phpinfo.php file temporarily
```

### 5. **Nginx Configuration Issues** ⚠️ MEDIUM PRIORITY
- **Symptoms**: Works locally, fails on production
- **Possible causes**:
  - `client_max_body_size` too small in Nginx config
  - Missing PHP-FPM configuration
  - Incorrect `fastcgi_pass` settings

**How to check:**
```nginx
# In nginx.conf or site config
client_max_body_size 10M;  # Ensure this is set appropriately
```

### 6. **Authorization Check Failure** ⚠️ LOW PRIORITY
- **Symptoms**: Works locally, fails on production
- **Possible causes**:
  - `checkAuthorizationAction` method returning unexpected value
  - Missing `UserRole` record in production database
  - Invalid JSON in `module_access` field

**How to check:**
```sql
-- Check if UserRole exists for the user's role
SELECT * FROM user_roles WHERE usertype = [user_role_id];

-- Check module_access JSON
SELECT usertype, module_access FROM user_roles;
```

### 7. **Missing Dependencies or Different Versions** ⚠️ LOW PRIORITY
- **Symptoms**: Works locally, fails on production
- **Possible causes**:
  - Missing Composer dependencies
  - Different PHP extensions installed
  - Different Laravel version

**How to check:**
```bash
# Ensure dependencies are installed
composer install --no-dev --optimize-autoloader

# Check PHP extensions
php -m
```

## Immediate Actions Taken

✅ **Added Error Handling**: Wrapped the `update` method in try-catch block
✅ **Added Logging**: Errors are now logged to `storage/logs/laravel.log` with full details
✅ **Better Error Messages**: Users see friendly error messages instead of generic 500

## How to Debug Further

### Step 1: Check Laravel Logs
```bash
# On production server
tail -f storage/logs/laravel.log

# Or check the latest log file
tail -n 100 storage/logs/laravel-$(date +%Y-%m-%d).log
```

The error handling I added will now log:
- The exact error message
- The user ID being updated
- The request data (excluding passwords)
- File and line number where error occurred
- Full stack trace

### Step 2: Enable Debug Mode (Temporarily)
**⚠️ WARNING: Only for debugging, disable after!**

In `.env`:
```
APP_DEBUG=true
APP_ENV=local
```

This will show the actual error on the page instead of generic 500.

### Step 3: Check Server Error Logs
```bash
# Nginx error log
tail -f /var/log/nginx/error.log

# PHP-FPM error log
tail -f /var/log/php-fpm/error.log
# or
tail -f /var/log/php7.4-fpm.log  # Adjust version as needed
```

### Step 4: Test Database Connection
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> Admin::find(44007);
```

### Step 5: Check File Permissions
```bash
ls -la storage/
ls -la bootstrap/cache/
ls -la public/img/profile_imgs/  # If exists
```

## Common Production vs Local Differences

| Aspect | Local | Production | Check |
|--------|-------|------------|-------|
| PHP Version | ? | ? | `php -v` |
| Database | Local | Remote | `.env` DB_* |
| File Permissions | User owned | www-data | `ls -la` |
| Error Display | On | Off | `APP_DEBUG` |
| Logging | Enabled | ? | `storage/logs/` |
| Environment | local | production | `APP_ENV` |

## Next Steps

1. **Deploy the updated code** with error handling
2. **Check the Laravel logs** after the next error occurs
3. **Compare environment configurations** between local and production
4. **Test database connectivity** on production
5. **Verify file permissions** on production

## Prevention

To prevent similar issues in the future:
- ✅ Always wrap database operations in try-catch
- ✅ Log errors with context
- ✅ Use environment-specific configurations
- ✅ Test in staging environment that matches production
- ✅ Monitor error logs regularly
