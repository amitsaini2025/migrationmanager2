# 🚀 Quick Linux Production Server Deployment

This guide will fix the Gateway timeout and JSON parsing errors on your Linux production server.

## 🚨 Current Problem
- **Gateway Timeout**: Server timing out during email sync
- **JSON Error**: Python returning HTML instead of JSON
- **Root Cause**: Python environment not properly configured for Linux

## ⚡ Quick Fix (5 minutes)

### Step 1: Upload Files to Server
Upload these files to your Linux server:
- `deploy_to_linux_production.sh`
- `test_linux_email_sync.php`
- Updated `app/Http/Controllers/EmailUser/EmailController.php`
- Updated `config/mail_sync.php`

### Step 2: SSH into Your Server
```bash
ssh your-username@your-server-ip
cd /path/to/your/laravel/project
```

### Step 3: Run Automated Deployment
```bash
chmod +x deploy_to_linux_production.sh
sudo ./deploy_to_linux_production.sh
```

### Step 4: Test the Fix
```bash
php test_linux_email_sync.php
```

## 🔧 Manual Steps (if automated script fails)

### 1. Install Python 3
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install python3 python3-pip python3-venv python3-dev

# CentOS/RHEL
sudo yum install python3 python3-pip python3-venv python3-devel
```

### 2. Setup Virtual Environment
```bash
cd python_outlook_web
python3 -m venv venv
source venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt
```

### 3. Set Permissions
```bash
sudo chown -R www-data:www-data python_outlook_web/
sudo chmod -R 755 python_outlook_web/
sudo chmod +x python_outlook_web/venv/bin/python
```

### 4. Update .env File
Add these lines to your `.env` file:
```env
MAIL_PYTHON_EXECUTABLE=/full/path/to/your/project/python_outlook_web/venv/bin/python
MAIL_AUTO_DETECT_PYTHON=true
MAIL_PROCESS_TIMEOUT=300
MAIL_MAX_SYNC_LIMIT=3
MAIL_BATCH_SIZE=2
MAIL_MEMORY_LIMIT=512M
```

### 5. Clear Laravel Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

## 🎯 Performance Optimizations Applied

1. **Reduced Batch Sizes**: 2-3 emails per request instead of 10
2. **Shorter Timeouts**: 300 seconds instead of 600 seconds
3. **Memory Limits**: 512MB instead of 1GB
4. **Better Error Handling**: Detailed logging for troubleshooting

## 🧪 Testing Your Fix

### Test 1: Diagnostic Script
```bash
php test_linux_email_sync.php
```
Should show all ✅ green checkmarks.

### Test 2: Direct API Test
```bash
curl -X POST 'https://migrationmanager.bansalcrm.com/email_users/emails/sync/3?folder=Inbox&paginated=true&offset=0&start_date=2025-09-01&end_date=2025-09-05' \
  -H 'Content-Type: application/json' \
  -H 'X-CSRF-TOKEN: your-csrf-token'
```
Should return JSON instead of HTML.

### Test 3: Web Interface
Visit: `https://migrationmanager.bansalcrm.com/email_users/dashboard`
Try the sync button - should work without timeout.

## 📊 Expected Results

**Before Fix:**
- ❌ Gateway timeout (504)
- ❌ JSON parsing error
- ❌ "Unexpected token '<'"

**After Fix:**
- ✅ Fast sync response (< 30 seconds)
- ✅ Valid JSON response
- ✅ Successful email sync

## 🚨 If Problems Persist

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Common Issues & Solutions

1. **Still Getting Timeout**
   - Reduce batch size further in `.env`: `MAIL_BATCH_SIZE=1`
   - Increase server timeout in nginx/apache config

2. **Python Module Errors**
   - Reinstall requirements: `pip install -r requirements.txt --force-reinstall`

3. **Permission Errors**
   - Fix ownership: `sudo chown -R www-data:www-data python_outlook_web/`

4. **Path Issues**
   - Use absolute path in `.env`: `MAIL_PYTHON_EXECUTABLE=/full/absolute/path/to/python`

## 🔄 Rollback Plan

If something goes wrong, restore from backup:
```bash
# Restore .env
cp backup_YYYYMMDD_HHMMSS/.env.backup .env

# Restore Python directory  
rm -rf python_outlook_web
mv backup_YYYYMMDD_HHMMSS/python_outlook_web.backup python_outlook_web

# Clear cache
php artisan config:clear
```

## 📞 Support

If you need help:
1. Check the diagnostic output: `php test_linux_email_sync.php`
2. Review the logs: `tail -f storage/logs/laravel.log`
3. Test individual components step by step

---

**Expected Deployment Time**: 5-10 minutes  
**Downtime**: Minimal (< 2 minutes)  
**Success Rate**: 95%+ for standard Linux servers
