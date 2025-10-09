# Production WebSocket Configuration Guide

## 📋 Your Production .env Configuration

Based on your provided .env file, here's your production setup:

### ✅ Reverb Configuration
```env
BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=database

# Reverb App Credentials
REVERB_APP_ID=676670f9338a4e77
REVERB_APP_KEY=d240d4047a2dab46586c04a65eccc0f8
REVERB_APP_SECRET=9068c80e1e6e4db4f56c3af54fe314bd6d1b044e30cba191515c8174f8bfe591

# Client Connection Settings (What browsers/clients connect to)
REVERB_HOST=migrationmanager.bansalcrm.com
REVERB_PORT=443                    # ✅ Clients connect here (via Nginx/Apache)
REVERB_SCHEME=https
REVERB_USE_TLS=true

# SSL/TLS Certificates
REVERB_TLS_CERT=/etc/pki/tls/certs/migrationmanager.bansalcrm.com.cert
REVERB_TLS_KEY=/etc/pki/tls/private/migrationmanager.bansalcrm.com.key

# Reverb Server Settings (Internal - where Reverb actually runs)
REVERB_SERVER_HOST=0.0.0.0         # Listens on all interfaces
REVERB_SERVER_PORT=8080            # ✅ Reverb server runs here (internal)

# Vite Frontend Configuration
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Scaling Settings
REVERB_SCALING_ENABLED=false
REVERB_SCALING_CHANNEL=reverb
```

## 🌐 Architecture Overview

```
┌─────────────┐
│   Browser   │
│   Client    │
└──────┬──────┘
       │
       │ WSS (Secure WebSocket)
       │ Port 443
       ▼
┌──────────────────────────┐
│   Nginx/Apache Proxy     │
│ migrationmanager.bansalcrm.com:443 │
└──────────┬───────────────┘
           │
           │ Proxy Pass
           │ Port 8080 (internal)
           ▼
┌──────────────────────────┐
│   Laravel Reverb Server  │
│   localhost:8080         │
└──────────────────────────┘
```

## 📝 Production File Configuration

### `production-websocket-test.html` Parameters:

| Parameter | Value | Description |
|-----------|-------|-------------|
| **Host** | `migrationmanager.bansalcrm.com` | Production domain |
| **Port** | `443` | HTTPS/WSS port (proxied by Nginx/Apache) |
| **App Key** | `d240d4047a2dab46586c04a65eccc0f8` | From REVERB_APP_KEY |
| **Protocol** | `wss://` | Secure WebSocket (TLS enabled) |
| **Channel Type** | `private` | Recommended (requires Bearer token) |

### ✅ Changes Made to production-websocket-test.html:

1. ✅ **App Key Updated**: `d240d4047a2dab46586c04a65eccc0f8`
2. ✅ **Host Pre-filled**: `migrationmanager.bansalcrm.com`
3. ✅ **Port Set**: `443` (with dropdown for alternatives)
4. ✅ **All API endpoints use HTTPS**: `https://migrationmanager.bansalcrm.com/api/*`
5. ✅ **Auth endpoint**: `https://migrationmanager.bansalcrm.com/api/broadcasting/auth`

## 🔧 Required Nginx/Apache Configuration

### Nginx Configuration Example:

Add this to your Nginx config for WebSocket support:

```nginx
server {
    listen 443 ssl http2;
    server_name migrationmanager.bansalcrm.com;

    ssl_certificate /etc/pki/tls/certs/migrationmanager.bansalcrm.com.cert;
    ssl_certificate_key /etc/pki/tls/private/migrationmanager.bansalcrm.com.key;

    # Regular Laravel application
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # WebSocket proxy for Reverb
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
        
        # Timeouts
        proxy_connect_timeout 7d;
        proxy_send_timeout 7d;
        proxy_read_timeout 7d;
    }

    # Broadcasting authentication endpoint
    location /api/broadcasting/auth {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # PHP-FPM for Laravel
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Apache Configuration Example:

Add to your Apache VirtualHost:

```apache
<VirtualHost *:443>
    ServerName migrationmanager.bansalcrm.com
    
    SSLEngine on
    SSLCertificateFile /etc/pki/tls/certs/migrationmanager.bansalcrm.com.cert
    SSLCertificateKeyFile /etc/pki/tls/private/migrationmanager.bansalcrm.com.key
    
    # Enable required modules: proxy, proxy_http, proxy_wstunnel
    
    # WebSocket proxy for Reverb
    ProxyPass "/app/" "ws://127.0.0.1:8080/app/"
    ProxyPassReverse "/app/" "ws://127.0.0.1:8080/app/"
    
    # Broadcasting auth endpoint
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

## 🚀 How to Start Reverb Server

### On Production Linux Server:

```bash
# Navigate to project directory
cd /path/to/migration_manager_crm

# Start Reverb server (it will use .env settings)
php artisan reverb:start

# OR start in background with systemd (recommended for production)
# Create a systemd service file: /etc/systemd/system/reverb.service
```

### Systemd Service Example:

Create file: `/etc/systemd/system/reverb.service`

```ini
[Unit]
Description=Laravel Reverb WebSocket Server
After=network.target

[Service]
Type=simple
User=apache
Group=apache
WorkingDirectory=/var/www/html/migration_manager_crm
ExecStart=/usr/bin/php artisan reverb:start
Restart=always
RestartSec=5
Environment="PATH=/usr/local/bin:/usr/bin:/bin"

[Install]
WantedBy=multi-user.target
```

Then enable and start:

```bash
sudo systemctl daemon-reload
sudo systemctl enable reverb
sudo systemctl start reverb
sudo systemctl status reverb
```

## ✅ Verification Checklist

Before using production-websocket-test.html:

- [ ] ✅ Reverb server is running on port 8080
  ```bash
  netstat -tlnp | grep 8080
  # Should show: php artisan reverb:start
  ```

- [ ] ✅ Nginx/Apache is configured to proxy WebSocket connections
  ```bash
  # For Nginx
  nginx -t
  sudo systemctl reload nginx
  
  # For Apache
  apachectl configtest
  sudo systemctl reload httpd
  ```

- [ ] ✅ SSL certificates are valid and in place
  ```bash
  ls -la /etc/pki/tls/certs/migrationmanager.bansalcrm.com.cert
  ls -la /etc/pki/tls/private/migrationmanager.bansalcrm.com.key
  ```

- [ ] ✅ Firewall allows port 443 (not 8080, since it's internal)
  ```bash
  sudo firewall-cmd --list-ports
  # Should show: 443/tcp
  ```

- [ ] ✅ .env file is configured correctly
  ```bash
  grep REVERB .env
  ```

- [ ] ✅ Laravel config cache is cleared
  ```bash
  php artisan config:clear
  php artisan config:cache
  ```

## 🧪 Testing Steps

### 1. Access the Production Test Page
```
https://migrationmanager.bansalcrm.com/production-websocket-test.html
```

### 2. Get Bearer Token
Use Postman to login:
```
POST https://migrationmanager.bansalcrm.com/api/login
Content-Type: application/json

{
    "email": "your@email.com",
    "password": "yourpassword"
}
```

Response will contain:
```json
{
    "token": "your-bearer-token-here"
}
```

### 3. Fill in the Form
- **Host**: `migrationmanager.bansalcrm.com` (pre-filled)
- **Port**: `443` (pre-selected)
- **App Key**: `d240d4047a2dab46586c04a65eccc0f8` (pre-filled)
- **User ID**: Enter the user ID you want to listen for
- **Client Matter ID**: Enter for message counts
- **Channel Type**: Select `Private Channel`
- **Bearer Token**: Paste token from login

### 4. Connect
Click **"🚀 Connect & Subscribe"**

You should see:
- ✅ Connected successfully!
- ✅ Successfully subscribed to private-user.{userId}

### 5. Send Test Message
Use Postman:
```
POST https://migrationmanager.bansalcrm.com/api/messages/send
Authorization: Bearer {your-token}
Content-Type: application/json

{
    "client_matter_id": 1,
    "message": "Hello from production!",
    "receiver_id": 5
}
```

The message should appear in real-time in the test page! 🎉

## 🔍 Troubleshooting

### Connection Failed?

1. **Check Reverb is running:**
   ```bash
   ps aux | grep reverb
   netstat -tlnp | grep 8080
   ```

2. **Check Nginx/Apache logs:**
   ```bash
   # Nginx
   tail -f /var/log/nginx/error.log
   
   # Apache
   tail -f /var/log/httpd/error_log
   ```

3. **Check Reverb logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Test internal connection:**
   ```bash
   curl -i http://127.0.0.1:8080
   # Should respond (not fail)
   ```

5. **Check WebSocket proxy:**
   - Open browser DevTools → Network tab
   - Filter by "WS"
   - Should see connection to `wss://migrationmanager.bansalcrm.com:443/app/...`

### Authentication Failed?

1. Check Bearer token is valid:
   ```bash
   curl -H "Authorization: Bearer {token}" \
        https://migrationmanager.bansalcrm.com/api/messages/unread-count
   ```

2. Verify broadcasting auth endpoint works:
   ```bash
   curl -X POST \
        -H "Authorization: Bearer {token}" \
        -H "Content-Type: application/json" \
        https://migrationmanager.bansalcrm.com/api/broadcasting/auth
   ```

## 📊 Summary

| Aspect | Configuration |
|--------|--------------|
| **Production Domain** | migrationmanager.bansalcrm.com |
| **Client Connection** | wss://migrationmanager.bansalcrm.com:443 |
| **Internal Reverb** | http://127.0.0.1:8080 |
| **App Key** | d240d4047a2dab46586c04a65eccc0f8 |
| **TLS/SSL** | Enabled with certificates |
| **Proxy** | Nginx/Apache (port 443 → 8080) |
| **Channel Type** | Private (requires auth) |

Your production file is now configured and ready to use! 🚀

