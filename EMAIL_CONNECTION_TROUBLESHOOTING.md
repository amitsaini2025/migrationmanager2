# Email Connection Troubleshooting Guide

## Problem Description
Your Zoho email account (Visitor@bansalimmigration.com.au) is failing all connection tests:
- Internet test failed
- DNS test failed  
- Socket test failed
- SSL test failed
- IMAP test failed

## Root Cause Analysis
Based on the network test results, you're experiencing Windows networking issues:

1. **DNS Resolution Failed (Error 11003)**: `getaddrinfo failed` - Windows can't resolve `imap.zoho.com`
2. **Service Provider Error (Error 10106)**: `The requested service provider could not be loaded or initialized` - Windows networking stack issue

## Quick Fix Solutions

### Option 1: Automated Fix (Recommended)
Run the automated fix script as Administrator:

**For Windows:**
```cmd
# Right-click Command Prompt and select "Run as Administrator"
fix_network_issues.bat
```

**For PowerShell:**
```powershell
# Right-click PowerShell and select "Run as Administrator"
.\fix_network_issues.ps1
```

### Option 2: Manual Fix Commands
If you prefer to run commands manually, open Command Prompt as Administrator and run:

```cmd
# Flush DNS cache
ipconfig /flushdns

# Reset Winsock catalog
netsh winsock reset

# Reset TCP/IP stack
netsh int ip reset

# Reset network adapters
netsh int ip reset all

# Reset Windows Firewall
netsh advfirewall reset

# Register DNS client
netsh winsock reset catalog
```

**IMPORTANT:** After running these commands, you MUST restart your computer.

### Option 3: Enhanced Network Test
The system now uses an enhanced network test that automatically attempts fixes:

```bash
# The enhanced test will run automatically when you click "Test" in the UI
# It includes automatic Windows network fixes and better error reporting
```

## Additional Troubleshooting Steps

### If Basic Fixes Don't Work:

1. **Check Windows Firewall:**
   - Temporarily disable Windows Firewall
   - Test email connection
   - Re-enable firewall and add exceptions if needed

2. **Check Antivirus Software:**
   - Temporarily disable real-time protection
   - Test email connection
   - Add email application to antivirus exceptions

3. **Try Different Network:**
   - Connect to mobile hotspot
   - Test email connection
   - This helps identify if it's a network-specific issue

4. **Update Network Drivers:**
   - Go to Device Manager
   - Find Network Adapters
   - Right-click and select "Update driver"

5. **Check ISP Restrictions:**
   - Some ISPs block email ports (25, 465, 587, 993, 995)
   - Contact your ISP about email port restrictions
   - Try using a VPN service

### Zoho-Specific Solutions:

1. **Alternative Zoho Servers:**
   - Try different Zoho IMAP/SMTP servers
   - Some regions have different server addresses

2. **Port Configuration:**
   - IMAP: 993 (SSL) or 143 (TLS)
   - SMTP: 465 (SSL) or 587 (TLS)

3. **Authentication:**
   - Ensure you're using an App Password, not your regular password
   - Enable 2FA and generate an App Password in Zoho settings

## Testing Your Fix

After applying fixes and restarting:

1. Go to your email account settings
2. Click the "Test" button
3. Check if all tests now pass:
   - ✓ Internet test
   - ✓ DNS test
   - ✓ Socket test
   - ✓ SSL test
   - ✓ IMAP test

## Prevention

To prevent future issues:

1. **Regular Maintenance:**
   - Run `ipconfig /flushdns` monthly
   - Keep Windows and network drivers updated

2. **Network Monitoring:**
   - Monitor for Windows updates that might affect networking
   - Check antivirus software updates

3. **Backup Solutions:**
   - Consider using a VPN for reliable email access
   - Keep alternative email access methods available

## Getting Help

If issues persist after trying all solutions:

1. **Check System Logs:**
   - Windows Event Viewer → Windows Logs → System
   - Look for network-related errors

2. **Contact Support:**
   - Provide the enhanced network test results
   - Include Windows version and network configuration details

3. **Alternative Access:**
   - Use Zoho webmail as temporary solution
   - Consider using a different email client

## Technical Details

The enhanced network test now includes:
- Multiple DNS resolution methods
- Automatic Windows network fixes
- Better error reporting and recommendations
- Fallback connection methods
- Detailed diagnostic information

This should resolve the connection issues you're experiencing with your Zoho email account.
