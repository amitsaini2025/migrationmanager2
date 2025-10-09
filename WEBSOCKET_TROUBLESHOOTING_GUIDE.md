# WebSocket Connection Troubleshooting Guide

## 🚨 **Current Issue**
Your WebSocket connection is failing with `connecting -> unavailable` even though you're using the correct port 443. This indicates a **server-side configuration problem**.

## 🔍 **Quick Diagnostics**

### **Step 1: Use the New Diagnostics Button**
1. Open `production-websocket-test.html` on your server
2. Click **"🔍 Run Diagnostics"** button
3. This will test:
   - ✅ Main site accessibility
   - ✅ API endpoint availability  
   - ✅ Broadcasting auth endpoint
   - ✅ WebSocket connection test

### **Step 2: Check Reverb Server Status**
On your Linux server, run:
```bash
# Check if Reverb is running
ps aux | grep reverb
netstat -tlnp | grep 8080

# If not running, start it
cd /path/to/your/project
php artisan reverb:start

# Or check systemd service
sudo systemctl status reverb
```

## 🔧 **Most Likely Issues & Solutions**

### **Issue 1: Reverb Server Not Running** ❌
**Symptoms:** WebSocket fails immediately
**Solution:**
```bash
# Start Reverb server
cd /var/www/html/migration_manager_crm  # or your project path
php artisan reverb:start

# For production, use systemd service
sudo systemctl start reverb
sudo systemctl enable reverb
```

### **Issue 2: Nginx WebSocket Proxy Not Configured** ❌
**Symptoms:** Main site works, but WebSocket fails
**Solution:** Add this to your Nginx configuration:

```nginx
server {
    listen 443 ssl http2;
    server_name migrationmanager.bansalcrm.com;

    # Your existing SSL configuration
    ssl_certificate /etc/pki/tls/certs/migrationmanager.bansalcrm.com.cert;
    ssl_certificate_key /etc/pki/tls/private/migrationmanager.bansalcrm.com.key;

    # Regular Laravel application
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # ✅ ADD THIS: WebSocket proxy for Reverb
    location /app/ {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        
        # Important timeouts for WebSocket
        proxy_connect_timeout 7d;
        proxy_send_timeout 7d;
        proxy_read_timeout 7d;
    }

    # ✅ ADD THIS: Broadcasting authentication
    location /api/broadcasting/auth {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # PHP-FPM configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

**After updating Nginx:**
```bash
sudo nginx -t                    # Test configuration
sudo systemctl reload nginx      # Reload Nginx
```

### **Issue 3: Apache WebSocket Proxy Not Configured** ❌
**Symptoms:** Main site works, but WebSocket fails
**Solution:** Add this to your Apache VirtualHost:

```apache
<VirtualHost *:443>
    ServerName migrationmanager.bansalcrm.com
    
    # Your existing SSL configuration
    SSLEngine on
    SSLCertificateFile /etc/pki/tls/certs/migrationmanager.bansalcrm.com.cert
    SSLCertificateKeyFile /etc/pki/tls/private/migrationmanager.bansalcrm.com.key

    # ✅ Enable required modules
    # sudo a2enmod proxy
    # sudo a2enmod proxy_http
    # sudo a2enmod proxy_wstunnel

    # ✅ ADD THIS: WebSocket proxy for Reverb
    ProxyPass "/app/" "ws://127.0.0.1:8080/app/"
    ProxyPassReverse "/app/" "ws://127.0.0.1:8080/app/"
    
    # ✅ ADD THIS: Broadcasting auth endpoint
    ProxyPass "/api/broadcasting/auth" "http://127.0.0.1:8080/api/broadcasting/auth"
    ProxyPassReverse "/api/broadcasting/auth" "http://127.0.0.1:8080/api/broadcasting/auth"

    # Regular Laravel application
    DocumentRoot /var/www/html/migration_manager_crm/public
    <Directory /var/www/html/migration_manager_crm/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**After updating Apache:**
```bash
sudo apachectl configtest      # Test configuration
sudo systemctl reload httpd    # Reload Apache
```

### **Issue 4: Firewall Blocking Internal Connection** ❌
**Symptoms:** Reverb running but proxy fails
**Solution:**
```bash
# Check if port 8080 is accessible locally
curl -i http://127.0.0.1:8080

# If this fails, check firewall
sudo firewall-cmd --list-ports
sudo firewall-cmd --add-port=8080/tcp --permanent  # Only if needed for testing
sudo firewall-cmd --reload
```

### **Issue 5: Wrong Reverb Configuration** ❌
**Symptoms:** Reverb starts but on wrong port
**Solution:** Check your `.env` file:
```bash
grep REVERB .env
```

Should show:
```env
REVERB_HOST=migrationmanager.bansalcrm.com
REVERB_PORT=443
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
```

## 🔍 **Advanced Debugging**

### **Check Nginx Error Logs:**
```bash
sudo tail -f /var/log/nginx/error.log
```

### **Check Reverb Logs:**
```bash
tail -f storage/logs/laravel.log
```

### **Test WebSocket Manually:**
```bash
# Install wscat for testing
npm install -g wscat

# Test WebSocket connection
wscat -c wss://migrationmanager.bansalcrm.com:443/app/d240d4047a2dab46586c04a65eccc0f8
```

### **Check Network Connectivity:**
```bash
# From your server, test internal connection
curl -i http://127.0.0.1:8080/app/d240d4047a2dab46586c04a65eccc0f8

# Should return WebSocket upgrade headers
```

## 📋 **Complete Verification Checklist**

Run these commands on your server:

```bash
# 1. Check Reverb is running
ps aux | grep reverb
netstat -tlnp | grep 8080

# 2. Check Nginx/Apache configuration
sudo nginx -t  # or sudo apachectl configtest

# 3. Check SSL certificate
openssl s_client -connect migrationmanager.bansalcrm.com:443 -servername migrationmanager.bansalcrm.com

# 4. Test internal WebSocket
curl -i -H "Connection: Upgrade" -H "Upgrade: websocket" \
     -H "Sec-WebSocket-Key: SGVsbG8sIHdvcmxkIQ==" \
     -H "Sec-WebSocket-Version: 13" \
     http://127.0.0.1:8080/app/d240d4047a2dab46586c04a65eccc0f8

# 5. Check Laravel configuration
php artisan config:show reverb
```

## 🚀 **Quick Fix Commands**

If you're using **Nginx**, run these commands:

```bash
# 1. Create Nginx configuration for WebSocket
sudo tee /etc/nginx/sites-available/migrationmanager-websocket << 'EOF'
server {
    listen 443 ssl http2;
    server_name migrationmanager.bansalcrm.com;

    ssl_certificate /etc/pki/tls/certs/migrationmanager.bansalcrm.com.cert;
    ssl_certificate_key /etc/pki/tls/private/migrationmanager.bansalcrm.com.key;

    # WebSocket proxy
    location /app/ {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        proxy_connect_timeout 7d;
        proxy_send_timeout 7d;
        proxy_read_timeout 7d;
    }

    # Broadcasting auth
    location /api/broadcasting/auth {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # Regular Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

# 2. Test and reload
sudo nginx -t
sudo systemctl reload nginx

# 3. Start Reverb
cd /var/www/html/migration_manager_crm
php artisan reverb:start
```

## 🎯 **Expected Results**

After fixing the configuration, you should see:

1. **Diagnostics Button Results:**
   - ✅ Main site accessible: 200
   - ✅ API endpoint accessible (401 = auth required)
   - ✅ Broadcasting auth endpoint accessible
   - ✅ WebSocket connection successful!

2. **Connection Log:**
   - ✅ Using port 443 (HTTPS/WSS)
   - ✅ Connected successfully!
   - ✅ Successfully subscribed to private-user.X

## 📞 **Still Having Issues?**

If the diagnostics still show failures, please run these commands and share the output:

```bash
# System status
ps aux | grep reverb
netstat -tlnp | grep 8080
sudo nginx -t
curl -i http://127.0.0.1:8080

# Configuration files
cat .env | grep REVERB
```

The most common issue is **missing WebSocket proxy configuration** in Nginx/Apache. The diagnostics button will help identify exactly what's failing! 🔍
