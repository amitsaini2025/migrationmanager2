# Client Portal API Troubleshooting Guide

## Common Issues and Solutions

### 1. CORS Errors

**Problem:** Browser shows CORS errors when making API requests
```
Access to fetch at 'http://localhost/api/login' from origin 'http://localhost:3000' has been blocked by CORS policy
```

**Solutions:**
1. **Check CORS Configuration:**
   ```bash
   # Verify config/cors.php exists and is properly configured
   cat config/cors.php
   ```

2. **Update CORS Settings:**
   ```php
   // config/cors.php
   'allowed_origins' => ['*'], // For development
   'allowed_origins' => ['https://yourdomain.com'], // For production
   ```

3. **Clear Configuration Cache:**
   ```bash
   php artisan config:cache
   php artisan config:clear
   ```

### 2. Token Issues

**Problem:** Authentication fails with "Unauthenticated" error
```
{"message": "Unauthenticated."}
```

**Solutions:**
1. **Check Sanctum Configuration:**
   ```bash
   # Verify Sanctum is properly installed
   composer show laravel/sanctum
   ```

2. **Verify Token Format:**
   ```javascript
   // Correct format
   headers: {
       'Authorization': 'Bearer 1|abcdef123456...'
   }
   ```

3. **Check Token Expiration:**
   ```php
   // config/sanctum.php
   'expiration' => 60 * 24 * 7, // 7 days
   ```

4. **Verify Database Table:**
   ```sql
   -- Check if personal_access_tokens table exists
   SHOW TABLES LIKE 'personal_access_tokens';
   ```

### 3. Email Not Sending

**Problem:** Verification codes are not being sent via email

**Solutions:**
1. **Check Mail Configuration:**
   ```bash
   # Test mail configuration
   php artisan tinker
   Mail::raw('Test email', function($message) {
       $message->to('test@example.com')->subject('Test');
   });
   ```

2. **Verify .env Mail Settings:**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your-email@gmail.com
   MAIL_FROM_NAME="${APP_NAME}"
   ```

3. **Check Mail Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Test with Mailtrap (Development):**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your-mailtrap-username
   MAIL_PASSWORD=your-mailtrap-password
   ```

### 4. Database Errors

**Problem:** Database connection or query errors

**Solutions:**
1. **Check Database Connection:**
   ```bash
   php artisan migrate:status
   ```

2. **Verify Admins Table Structure:**
   ```sql
   DESCRIBE admins;
   ```

3. **Check Required Fields:**
   ```sql
   -- Ensure these fields exist in admins table
   SELECT COLUMN_NAME 
   FROM INFORMATION_SCHEMA.COLUMNS 
   WHERE TABLE_NAME = 'admins' 
   AND COLUMN_NAME IN ('email', 'password', 'role', 'cp_status', 'cp_random_code', 'cp_code_verify');
   ```

4. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

### 5. Authentication Fails

**Problem:** Login fails even with correct credentials

**Solutions:**
1. **Check User Status:**
   ```sql
   SELECT email, role, cp_status, cp_code_verify 
   FROM admins 
   WHERE email = 'user@example.com';
   ```

2. **Verify Required Values:**
   - `role` should be `7` (Client type)
   - `cp_status` should be `1` (Active)
   - `cp_code_verify` should be `1` (Verified)

3. **Check Password Hashing:**
   ```php
   // Verify password is properly hashed
   Hash::check('password123', $admin->password);
   ```

### 6. Route Not Found (404)

**Problem:** API endpoints return 404 Not Found

**Solutions:**
1. **Check Route Registration:**
   ```bash
   php artisan route:list --path=api
   ```

2. **Verify Route File:**
   ```bash
   # Check if routes/api.php contains our routes
   grep -n "ClientPortalController" routes/api.php
   ```

3. **Clear Route Cache:**
   ```bash
   php artisan route:cache
   php artisan route:clear
   ```

4. **Check Web Server Configuration:**
   ```apache
   # .htaccess should redirect to public/index.php
   RewriteEngine On
   RewriteRule ^(.*)$ public/$1 [L]
   ```

### 7. Rate Limiting Issues

**Problem:** Too many requests error (429)

**Solutions:**
1. **Check Rate Limiting Configuration:**
   ```php
   // routes/api.php
   Route::middleware(['throttle:5,1'])->group(function () {
       Route::post('/send-verification', [ClientPortalController::class, 'sendVerification']);
   });
   ```

2. **Adjust Rate Limits:**
   ```php
   // For development, you can increase limits
   Route::middleware(['throttle:60,1'])->group(function () {
       Route::post('/send-verification', [ClientPortalController::class, 'sendVerification']);
   });
   ```

### 8. Validation Errors

**Problem:** API returns validation errors

**Solutions:**
1. **Check Request Format:**
   ```javascript
   // Ensure proper JSON format
   {
       "email": "user@example.com",
       "password": "password123",
       "password_confirmation": "password123"
   }
   ```

2. **Verify Required Fields:**
   - Email must be valid format
   - Password must be at least 8 characters
   - Password confirmation must match

3. **Check Content-Type Header:**
   ```javascript
   headers: {
       'Content-Type': 'application/json'
   }
   ```

## Debug Commands

### Laravel Debug Commands
```bash
# Check application status
php artisan about

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check configuration
php artisan config:show

# List all routes
php artisan route:list

# Check database connection
php artisan migrate:status

# Test mail configuration
php artisan tinker
```

### Database Debug Commands
```sql
-- Check if user exists
SELECT * FROM admins WHERE email = 'user@example.com';

-- Check verification status
SELECT email, cp_status, cp_code_verify, cp_random_code 
FROM admins 
WHERE email = 'user@example.com';

-- Check tokens
SELECT * FROM personal_access_tokens 
WHERE tokenable_type = 'App\\Admin' 
AND tokenable_id = 1;
```

### Network Debug Commands
```bash
# Test API endpoint
curl -X POST http://localhost/api/send-verification \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com"}'

# Check server response
curl -I http://localhost/api/send-verification

# Test with verbose output
curl -v -X POST http://localhost/api/send-verification \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com"}'
```

## Log Files

### Laravel Logs
```bash
# View recent logs
tail -f storage/logs/laravel.log

# Search for specific errors
grep -i "error" storage/logs/laravel.log

# Search for API requests
grep -i "api" storage/logs/laravel.log
```

### Web Server Logs
```bash
# Apache error logs
tail -f /var/log/apache2/error.log

# Nginx error logs
tail -f /var/log/nginx/error.log
```

## Performance Issues

### Slow API Responses
1. **Check Database Queries:**
   ```php
   // Enable query logging
   DB::enableQueryLog();
   // Your API call
   dd(DB::getQueryLog());
   ```

2. **Optimize Database:**
   ```sql
   -- Add indexes for frequently queried fields
   CREATE INDEX idx_admins_email ON admins(email);
   CREATE INDEX idx_admins_role ON admins(role);
   CREATE INDEX idx_admins_cp_status ON admins(cp_status);
   ```

3. **Check Server Resources:**
   ```bash
   # Check memory usage
   free -h
   
   # Check CPU usage
   top
   
   # Check disk space
   df -h
   ```

## Security Issues

### Token Security
1. **Check Token Storage:**
   ```sql
   -- Verify tokens are properly stored
   SELECT id, name, token, last_used_at, expires_at 
   FROM personal_access_tokens;
   ```

2. **Implement Token Cleanup:**
   ```php
   // Add to a scheduled command
   PersonalAccessToken::where('expires_at', '<', now())->delete();
   ```

### Input Validation
1. **Test SQL Injection:**
   ```javascript
   // This should be rejected
   {
       "email": "'; DROP TABLE admins; --"
   }
   ```

2. **Test XSS Prevention:**
   ```javascript
   // This should be sanitized
   {
       "email": "<script>alert('xss')</script>@example.com"
   }
   ```

## Environment-Specific Issues

### Development Environment
- Use `APP_DEBUG=true` in .env
- Enable detailed error messages
- Use Mailtrap for email testing
- Allow all CORS origins

### Production Environment
- Set `APP_DEBUG=false` in .env
- Use proper SSL certificates
- Configure specific CORS origins
- Enable rate limiting
- Monitor error logs

## Getting Help

### Before Asking for Help
1. Check this troubleshooting guide
2. Review Laravel logs
3. Test with simple curl commands
4. Verify database structure
5. Check configuration files

### Information to Provide
When reporting issues, include:
- Laravel version
- PHP version
- Error messages (full stack trace)
- Request/response examples
- Configuration files (sanitized)
- Database structure

### Contact Information
- Check the main project documentation
- Review the API integration guide
- Contact the development team with detailed information
