#!/bin/bash

###############################################################################
# Laravel Reverb Production Setup Script
# 
# This script automates the setup of Laravel Reverb on a Linux production server
# Usage: sudo bash setup_reverb_production.sh
###############################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get script directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║     Laravel Reverb Production Setup Script                    ║"
echo "║     Setting up Reverb on your Linux server...                 ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Check if running as root/sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}❌ Please run as root or with sudo${NC}"
    echo "   Usage: sudo bash setup_reverb_production.sh"
    exit 1
fi

# Detect the actual user (not root)
ACTUAL_USER="${SUDO_USER:-$USER}"
if [ "$ACTUAL_USER" = "root" ]; then
    ACTUAL_USER="www-data"
fi

echo -e "${BLUE}🔍 Detecting system configuration...${NC}"
echo ""

# Detect web server user
if id "www-data" &>/dev/null; then
    WEB_USER="www-data"
elif id "nginx" &>/dev/null; then
    WEB_USER="nginx"
elif id "apache" &>/dev/null; then
    WEB_USER="apache"
else
    WEB_USER="www-data"
fi

echo -e "${GREEN}   Web server user: $WEB_USER${NC}"
echo -e "${GREEN}   Project directory: $SCRIPT_DIR${NC}"
echo ""

# Step 1: Check if .env exists
echo -e "${BLUE}📝 Step 1: Checking .env file...${NC}"
if [ ! -f "$SCRIPT_DIR/.env" ]; then
    echo -e "${RED}❌ .env file not found!${NC}"
    echo "   Please create .env file first"
    exit 1
fi
echo -e "${GREEN}✅ .env file found${NC}"
echo ""

# Step 2: Generate Reverb keys if not exist
echo -e "${BLUE}🔑 Step 2: Generating Reverb keys...${NC}"

if ! grep -q "REVERB_APP_KEY" "$SCRIPT_DIR/.env"; then
    echo "   Generating new Reverb keys..."
    
    REVERB_APP_ID=$(php -r "echo bin2hex(random_bytes(8));")
    REVERB_APP_KEY=$(php -r "echo bin2hex(random_bytes(16));")
    REVERB_APP_SECRET=$(php -r "echo bin2hex(random_bytes(32));")
    
    echo "" >> "$SCRIPT_DIR/.env"
    echo "# Laravel Reverb Configuration (Auto-generated)" >> "$SCRIPT_DIR/.env"
    echo "BROADCAST_DRIVER=reverb" >> "$SCRIPT_DIR/.env"
    echo "REVERB_APP_ID=$REVERB_APP_ID" >> "$SCRIPT_DIR/.env"
    echo "REVERB_APP_KEY=$REVERB_APP_KEY" >> "$SCRIPT_DIR/.env"
    echo "REVERB_APP_SECRET=$REVERB_APP_SECRET" >> "$SCRIPT_DIR/.env"
    echo "REVERB_HOST=localhost" >> "$SCRIPT_DIR/.env"
    echo "REVERB_PORT=8080" >> "$SCRIPT_DIR/.env"
    echo "REVERB_SCHEME=http" >> "$SCRIPT_DIR/.env"
    echo "REVERB_SERVER_HOST=0.0.0.0" >> "$SCRIPT_DIR/.env"
    echo "REVERB_SERVER_PORT=8080" >> "$SCRIPT_DIR/.env"
    echo "VITE_REVERB_APP_KEY=\"\${REVERB_APP_KEY}\"" >> "$SCRIPT_DIR/.env"
    echo "VITE_REVERB_HOST=\"\${REVERB_HOST}\"" >> "$SCRIPT_DIR/.env"
    echo "VITE_REVERB_PORT=\"\${REVERB_PORT}\"" >> "$SCRIPT_DIR/.env"
    echo "VITE_REVERB_SCHEME=\"\${REVERB_SCHEME}\"" >> "$SCRIPT_DIR/.env"
    
    echo -e "${GREEN}✅ Reverb keys generated and added to .env${NC}"
    echo ""
    echo "   Generated keys:"
    echo "   REVERB_APP_ID=$REVERB_APP_ID"
    echo "   REVERB_APP_KEY=$REVERB_APP_KEY"
    echo ""
else
    echo -e "${GREEN}✅ Reverb keys already exist in .env${NC}"
fi
echo ""

# Step 3: Install Supervisor
echo -e "${BLUE}🔧 Step 3: Installing Supervisor...${NC}"
if command -v supervisorctl &> /dev/null; then
    echo -e "${GREEN}✅ Supervisor already installed${NC}"
else
    echo "   Installing Supervisor..."
    if [ -f /etc/debian_version ]; then
        apt-get update -qq
        apt-get install -y supervisor
    elif [ -f /etc/redhat-release ]; then
        yum install -y supervisor
    else
        echo -e "${YELLOW}⚠️  Cannot detect OS. Please install Supervisor manually${NC}"
    fi
    
    # Start and enable supervisor
    systemctl enable supervisor
    systemctl start supervisor
    echo -e "${GREEN}✅ Supervisor installed and started${NC}"
fi
echo ""

# Step 4: Create Supervisor configuration for Reverb
echo -e "${BLUE}⚙️  Step 4: Configuring Supervisor for Reverb...${NC}"
SUPERVISOR_CONF="/etc/supervisor/conf.d/reverb.conf"

cat > "$SUPERVISOR_CONF" <<EOF
[program:reverb]
process_name=%(program_name)s
command=php $SCRIPT_DIR/artisan reverb:start
directory=$SCRIPT_DIR
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=$WEB_USER
numprocs=1
redirect_stderr=true
stdout_logfile=$SCRIPT_DIR/storage/logs/reverb.log
stopwaitsecs=3600
EOF

echo -e "${GREEN}✅ Supervisor configuration created${NC}"
echo ""

# Step 5: Configure firewall
echo -e "${BLUE}🔥 Step 5: Configuring firewall...${NC}"
if command -v ufw &> /dev/null; then
    if ufw status | grep -q "Status: active"; then
        ufw allow 8080/tcp
        echo -e "${GREEN}✅ UFW: Allowed port 8080${NC}"
    else
        echo -e "${YELLOW}⚠️  UFW is not active${NC}"
    fi
elif command -v firewall-cmd &> /dev/null; then
    firewall-cmd --permanent --add-port=8080/tcp
    firewall-cmd --reload
    echo -e "${GREEN}✅ Firewalld: Allowed port 8080${NC}"
else
    echo -e "${YELLOW}⚠️  No firewall detected (or not managed by UFW/Firewalld)${NC}"
fi
echo ""

# Step 6: Set proper permissions
echo -e "${BLUE}🔒 Step 6: Setting file permissions...${NC}"
chown -R $WEB_USER:$WEB_USER "$SCRIPT_DIR/storage"
chmod -R 755 "$SCRIPT_DIR/storage"
echo -e "${GREEN}✅ Storage permissions set${NC}"
echo ""

# Step 7: Clear Laravel cache
echo -e "${BLUE}🧹 Step 7: Clearing Laravel cache...${NC}"
cd "$SCRIPT_DIR"
sudo -u $WEB_USER php artisan config:clear
sudo -u $WEB_USER php artisan cache:clear
sudo -u $WEB_USER php artisan config:cache
echo -e "${GREEN}✅ Laravel cache cleared${NC}"
echo ""

# Step 8: Start Reverb with Supervisor
echo -e "${BLUE}🚀 Step 8: Starting Reverb server...${NC}"
supervisorctl reread
supervisorctl update
supervisorctl start reverb

# Wait a moment for startup
sleep 2

# Check status
if supervisorctl status reverb | grep -q "RUNNING"; then
    echo -e "${GREEN}✅ Reverb server started successfully!${NC}"
else
    echo -e "${YELLOW}⚠️  Reverb may not have started. Check logs.${NC}"
fi
echo ""

# Step 9: Display next steps
echo "═══════════════════════════════════════════════════════════════"
echo -e "${GREEN}              ✅ SETUP COMPLETED!${NC}"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo -e "${BLUE}📋 Next Steps:${NC}"
echo ""
echo "1. Configure your web server (Nginx/Apache) for WebSocket proxy"
echo "   See: LARAVEL_REVERB_PRODUCTION_SETUP.md - Step 5"
echo ""
echo "2. Update REVERB_HOST in .env with your domain name"
echo "   Edit: $SCRIPT_DIR/.env"
echo "   Change: REVERB_HOST=your-domain.com"
echo ""
echo "3. For production with SSL, update these in .env:"
echo "   REVERB_SCHEME=https"
echo "   REVERB_PORT=443"
echo ""
echo "4. Check Reverb status:"
echo "   supervisorctl status reverb"
echo ""
echo "5. View Reverb logs:"
echo "   tail -f $SCRIPT_DIR/storage/logs/reverb.log"
echo ""
echo "6. Run diagnostic script:"
echo "   php $SCRIPT_DIR/check_reverb_production.php"
echo ""
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo -e "${GREEN}🎉 Reverb is now running on port 8080!${NC}"
echo ""

exit 0

