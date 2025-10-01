# Windows to Linux Migration Guide for Email System

## Key Differences Summary

| Aspect | Windows (Local) | Linux (Production) |
|--------|----------------|-------------------|
| **Python Execution** | `run_python.bat` | `run_python.sh` |
| **Virtual Environment** | `venv\Scripts\activate.bat` | `source venv/bin/activate` |
| **File Paths** | `C:\xampp\htdocs\...` | `/var/www/html/...` or `/home/user/...` |
| **Web Server User** | `IUSR` or `IIS_IUSRS` | `www-data`, `nginx`, or `apache` |
| **SSL Context** | Permissive (Windows compatibility) | Strict (Production security) |
| **Process Execution** | Windows-specific environment | Linux environment variables |
| **File Permissions** | Windows ACLs | Unix permissions (755, 644) |

## Critical Changes Made

### 1. Python Script Execution

**Windows (Before):**
```batch
@echo off
cd /d "%~dp0"
call venv\Scripts\activate.bat
python %*
```

**Linux (After):**
```bash
#!/bin/bash
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"
source venv/bin/activate
python "$@"
```

### 2. SSL Configuration

**Windows (Before):**
```python
# For Windows compatibility, be more permissive with SSL
context.check_hostname = False
context.verify_mode = ssl.CERT_NONE
```

**Linux (After):**
```python
# For production Linux servers, use secure SSL settings
context.check_hostname = True
context.verify_mode = ssl.CERT_REQUIRED
```

### 3. Laravel Process Execution

**Windows (Before):**
```php
$env = [
    'PATH' => getenv('PATH'),
    'SYSTEMROOT' => getenv('SYSTEMROOT'),
    'WINDIR' => getenv('WINDIR'),
    'TEMP' => getenv('TEMP'),
    'TMP' => getenv('TMP'),
    'PYTHONPATH' => getenv('PYTHONPATH'),
    'PYTHONIOENCODING' => 'utf-8',
];
```

**Linux (After):**
```php
$env = [
    'PATH' => env('PATH', '/usr/local/bin:/usr/bin:/bin'),
    'PYTHONPATH' => env('PYTHONPATH', ''),
    'PYTHONIOENCODING' => 'utf-8',
    'LANG' => env('LANG', 'en_US.UTF-8'),
    'LC_ALL' => env('LC_ALL', 'en_US.UTF-8'),
    'HOME' => env('HOME', '/tmp'),
    'TMPDIR' => env('TMPDIR', '/tmp'),
];
```

### 4. File Permissions

**Windows:**
- Uses Windows ACLs
- Permissions managed through Windows Security tab
- Web server runs with specific Windows user accounts

**Linux:**
```bash
# Set ownership
sudo chown -R www-data:www-data /path/to/project

# Set permissions
sudo chmod -R 755 /path/to/project
sudo chmod +x python_outlook_web/run_python.sh
```

## Migration Steps

### 1. Upload Files to Linux Server
```bash
# Upload your project to Linux server
scp -r /path/to/local/project user@server:/path/to/destination/
```

### 2. Run Linux Setup Script
```bash
# Make scripts executable
chmod +x deploy_linux_email_system.sh
chmod +x python_outlook_web/setup_linux_env.sh
chmod +x python_outlook_web/run_python.sh

# Run deployment
./deploy_linux_email_system.sh
```

### 3. Update Configuration
- Update `.env` file with Linux-specific paths
- Configure database connection for Linux MySQL
- Set up web server virtual host

### 4. Test Everything
```bash
# Test Python environment
cd python_outlook_web
./run_python.sh test_network.py imap.zoho.com 993

# Test Laravel application
php artisan serve --host=0.0.0.0 --port=8000
```

## Common Issues and Solutions

### Issue 1: Python Script Not Executing
**Problem:** `Permission denied` when running Python scripts
**Solution:**
```bash
chmod +x python_outlook_web/run_python.sh
chmod +x python_outlook_web/setup_linux_env.sh
```

### Issue 2: File Permission Errors
**Problem:** Web server cannot write to storage directories
**Solution:**
```bash
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/
```

### Issue 3: Python Dependencies Missing
**Problem:** Import errors when running Python scripts
**Solution:**
```bash
cd python_outlook_web
source venv/bin/activate
pip install -r requirements.txt
```

### Issue 4: SSL Certificate Errors
**Problem:** SSL verification failures
**Solution:**
- Update system certificates: `sudo apt update && sudo apt install ca-certificates`
- Check if system date/time is correct
- Verify firewall allows HTTPS connections

### Issue 5: Database Connection Issues
**Problem:** Laravel cannot connect to MySQL
**Solution:**
- Verify MySQL service is running: `sudo systemctl status mysql`
- Check database credentials in `.env`
- Ensure MySQL user has proper permissions

## Performance Considerations

### 1. PHP-FPM Configuration
```ini
; /etc/php/7.4/fpm/pool.d/www.conf
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 1000
```

### 2. MySQL Configuration
```ini
; /etc/mysql/mysql.conf.d/mysqld.cnf
innodb_buffer_pool_size = 1G
max_connections = 200
query_cache_size = 64M
```

### 3. Nginx Configuration
```nginx
# Enable gzip compression
gzip on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

# Set appropriate timeouts
proxy_read_timeout 300;
proxy_connect_timeout 300;
proxy_send_timeout 300;
```

## Security Enhancements

### 1. File Permissions
```bash
# Restrict access to sensitive files
chmod 600 .env
chmod 600 config/database.php
chmod 700 storage/logs/
```

### 2. Firewall Configuration
```bash
# Allow only necessary ports
sudo ufw allow 22    # SSH
sudo ufw allow 80    # HTTP
sudo ufw allow 443   # HTTPS
sudo ufw enable
```

### 3. SSL/TLS Configuration
```nginx
# Strong SSL configuration
ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
ssl_prefer_server_ciphers off;
```

## Monitoring and Logging

### 1. Log Files to Monitor
```bash
# Application logs
tail -f storage/logs/laravel.log
tail -f storage/logs/email-sync.log

# System logs
tail -f /var/log/nginx/error.log
tail -f /var/log/mysql/error.log
tail -f /var/log/syslog
```

### 2. Health Check Commands
```bash
# Check services
sudo systemctl status nginx
sudo systemctl status mysql
sudo systemctl status php7.4-fpm

# Check disk space
df -h

# Check memory usage
free -h

# Check running processes
ps aux | grep -E "(nginx|mysql|php-fpm|python)"
```

## Backup and Recovery

### 1. Database Backup
```bash
# Create database backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore database
mysql -u username -p database_name < backup_file.sql
```

### 2. File System Backup
```bash
# Create file backup
tar -czf project_backup_$(date +%Y%m%d_%H%M%S).tar.gz /path/to/project

# Restore files
tar -xzf project_backup_file.tar.gz -C /path/to/restore/
```

This migration guide should help you successfully deploy your email user system from Windows to Linux production server. The key is to follow the deployment checklist and test each component thoroughly.
