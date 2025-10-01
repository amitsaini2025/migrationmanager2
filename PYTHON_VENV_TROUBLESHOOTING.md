# Python Virtual Environment Troubleshooting Guide

## Problem
Error: `Python executable not found at: /home/migratio/public_html/python_outlook_web/venv/bin/python. Please ensure the virtual environment is set up correctly.`

## Root Cause
The system is running on Linux but has a Windows virtual environment structure (`venv/Scripts/` instead of `venv/bin/`).

## Quick Fix (Run on Linux Server)

### Option 1: Automated Fix
```bash
# Run from project root directory
chmod +x fix_python_venv_linux.sh
./fix_python_venv_linux.sh
```

### Option 2: Manual Fix
```bash
# Navigate to python_outlook_web directory
cd python_outlook_web

# Remove existing Windows virtual environment
rm -rf venv

# Create Linux virtual environment
python3 -m venv venv

# Activate and install dependencies
source venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt

# Set proper permissions
chmod +x venv/bin/python*
chmod +x run_python.sh
```

## Complete Setup (For Fresh Deployments)

### Option 1: Automated Setup
```bash
# Run from project root directory
chmod +x setup_linux_python_environment.sh
./setup_linux_python_environment.sh
```

### Option 2: Manual Setup
```bash
# Navigate to python_outlook_web directory
cd python_outlook_web

# Run the existing setup script
chmod +x setup_linux_env.sh
./setup_linux_env.sh
```

## Configuration Updates Made

### 1. Enhanced mail_sync.php
- Added auto-detection for Python executables
- Added cross-platform path detection
- Added fallback mechanisms

### 2. Updated EmailController.php
- Added `autoDetectPythonExecutable()` method
- Enhanced error handling and logging
- Added support for multiple Python versions

### 3. Environment Configuration
- Added `MAIL_AUTO_DETECT_PYTHON=true` to .env
- Added `MAIL_PYTHON_EXECUTABLE` path configuration

## Verification Steps

### 1. Check Python Executable
```bash
cd python_outlook_web
ls -la venv/bin/python*
# Should show: venv/bin/python, venv/bin/python3, etc.
```

### 2. Test Python Execution
```bash
cd python_outlook_web
./venv/bin/python -c "import sys; print('Python version:', sys.version)"
```

### 3. Test Email Modules
```bash
cd python_outlook_web
./run_python.sh test_network.py imap.zoho.com 993
```

### 4. Test Email Sync
Visit: `https://migrationmanager.bansalcrm.com/email_users/emails/sync/2?folder=Inbox&limit=50`

## Troubleshooting

### If Python is not found:
```bash
# Install Python 3
sudo apt update
sudo apt install python3 python3-pip python3-venv

# Or on CentOS/RHEL:
sudo yum install python3 python3-pip
```

### If permissions are wrong:
```bash
cd python_outlook_web
chmod +x venv/bin/python*
chmod +x run_python.sh
chmod -R 755 venv/
```

### If dependencies are missing:
```bash
cd python_outlook_web
source venv/bin/activate
pip install -r requirements.txt
```

### If virtual environment is corrupted:
```bash
cd python_outlook_web
rm -rf venv
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
```

## Environment Variables

Add to your `.env` file:
```env
# Python Email System Configuration
MAIL_PYTHON_EXECUTABLE=/path/to/your/project/python_outlook_web/venv/bin/python
MAIL_AUTO_DETECT_PYTHON=true
```

## File Structure After Fix

```
python_outlook_web/
├── venv/
│   ├── bin/
│   │   ├── python          # ← This should exist
│   │   ├── python3
│   │   └── activate
│   ├── lib/
│   └── pyvenv.cfg
├── requirements.txt
├── run_python.sh
├── sync_emails.py
└── send_mail.py
```

## Support

If issues persist after following this guide:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Verify file permissions and ownership
4. Ensure Python 3.6+ is installed
5. Verify all dependencies are installed correctly
