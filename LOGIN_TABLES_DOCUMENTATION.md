# Login Tables Documentation

## Overview
This document describes the database tables involved in the login functionality of the Migration Manager application.

## Tables Involved

### 1. `admins` Table
**Purpose**: Stores administrator/staff user accounts for login authentication.

**Key Fields**:
- `id` - Primary key
- `email` - Unique email address (used for login)
- `password` - Hashed password
- `decrypt_password` - Optional decrypted password (for service tokens)
- `first_name`, `last_name` - User's name
- `role` - User role/permission level
- `status` - Account status (1 = active, 0 = inactive)
- `cp_status` - Client portal status
- `service_token` - API service token
- `token_generated_at` - Token generation timestamp
- `remember_token` - Laravel remember token
- `created_at`, `updated_at` - Timestamps

**Model**: `App\Models\Admin`

**Authentication Guard**: `admin` (configured in `config/auth.php`)

**Login Controller**: `App\Http\Controllers\Auth\AdminLoginController`

### 2. `user_logs` Table
**Purpose**: Logs all login attempts (successful and failed) and user activities.

**Key Fields**:
- `id` - Primary key
- `level` - Log level (e.g., 'info', 'critical')
- `user_id` - Foreign key to admins table (nullable for failed login attempts)
- `ip_address` - IP address of the login attempt
- `user_agent` - Browser/user agent string
- `message` - Log message (e.g., "Logged in successfully", "Invalid Email or Password !")
- `created_at`, `updated_at` - Timestamps

**Model**: `App\Models\UserLog`

**Usage**: 
- Successful logins are logged with level 'info'
- Failed logins are logged with level 'critical'

## Database Configuration

### Default Connection
- **Type**: PostgreSQL (configured in `config/database.php`)
- **Connection Name**: `pgsql` (default)

### MySQL Source Connection
- **Type**: MySQL
- **Connection Name**: `mysql_source`
- **Environment Variables**:
  - `MYSQL_SOURCE_HOST`
  - `MYSQL_SOURCE_PORT`
  - `MYSQL_SOURCE_DATABASE`
  - `MYSQL_SOURCE_USERNAME`
  - `MYSQL_SOURCE_PASSWORD`

## Importing Data from MySQL

### Using the Import Command

The application includes a command to import login data from MySQL to PostgreSQL:

```bash
php artisan import:login-data
```

### Command Options

- `--source=mysql_source` - MySQL source connection (default: mysql_source)
- `--target=` - Target connection (default: uses default connection)
- `--table=*` - Specific tables to import (only `admins` or `user_logs` allowed, default: both)
- `--skip-existing` - Skip records that already exist
- `--update-existing` - Update existing records instead of skipping

**Note**: This command only imports login-related tables (`admins` and `user_logs`). Other tables cannot be imported using this command.

### Examples

**Import all login tables:**
```bash
php artisan import:login-data
```

**Import only admins table:**
```bash
php artisan import:login-data --table=admins
```

**Import and update existing records:**
```bash
php artisan import:login-data --update-existing
```

**Import and skip existing records:**
```bash
php artisan import:login-data --skip-existing
```

## Login Flow

1. User submits login form with email and password
2. `AdminLoginController` validates the request (including reCAPTCHA if configured)
3. Authentication uses `Auth::guard('admin')` which queries the `admins` table
4. On successful login:
   - User session is created
   - Log entry is created in `user_logs` with level 'info'
   - Service token is generated (if configured)
5. On failed login:
   - Log entry is created in `user_logs` with level 'critical'
   - Error message is returned to user

## Important Notes

1. **Password Hashing**: Passwords in the `admins` table must be hashed using Laravel's Hash facade
2. **Email Uniqueness**: The `email` field must be unique in the `admins` table
3. **Active Status**: Only admins with `status = 1` should be able to login
4. **PostgreSQL Sequences**: After importing, ensure PostgreSQL sequences are properly set (see migration: `2025_12_14_155025_fix_user_logs_id_sequence.php`)

## Troubleshooting

### Login Fails with "These credentials do not match our records"
- Check if the admin record exists in the `admins` table
- Verify the email address matches exactly (case-sensitive in some databases)
- Ensure the password is correctly hashed
- Check that `status = 1` for the admin account

### Import Errors
- Verify MySQL source connection settings in `.env`
- Ensure target PostgreSQL tables exist (run migrations first)
- Check that column types match between MySQL and PostgreSQL
- Review Laravel logs for detailed error messages
