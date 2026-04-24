#!/bin/bash

# Setup script for Nginx virtual host configuration for saas-dashboard
# This script sets up the Nginx configuration and hosts file entries

set -e

PROJECT_DIR="/home/abdelrahman/me/freelance/saas/saas-dashboard"
NGINX_SITES_AVAILABLE="/etc/nginx/sites-available"
NGINX_SITES_ENABLED="/etc/nginx/sites-enabled"
NGINX_CONFIG_FILE="saas-dashboard.conf"
HOSTS_FILE="/etc/hosts"

echo "🚀 Setting up Nginx virtual host for saas-dashboard..."

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Please run as root (use sudo)"
    exit 1
fi

# Copy Nginx configuration
echo "📝 Copying Nginx configuration..."
cp "$PROJECT_DIR/nginx.conf" "$NGINX_SITES_AVAILABLE/$NGINX_CONFIG_FILE"

# Create symlink to enable site
echo "🔗 Enabling site..."
if [ -L "$NGINX_SITES_ENABLED/$NGINX_CONFIG_FILE" ]; then
    rm "$NGINX_SITES_ENABLED/$NGINX_CONFIG_FILE"
fi
ln -s "$NGINX_SITES_AVAILABLE/$NGINX_CONFIG_FILE" "$NGINX_SITES_ENABLED/$NGINX_CONFIG_FILE"

# Add hosts file entries
echo "📋 Adding hosts file entries..."
if ! grep -q "landlord.saas.test" "$HOSTS_FILE"; then
    echo "127.0.0.1 landlord.saas.test" >> "$HOSTS_FILE"
    echo "  ✓ Added landlord.saas.test"
fi

if ! grep -q "customer1.saas.test" "$HOSTS_FILE"; then
    echo "127.0.0.1 customer1.saas.test" >> "$HOSTS_FILE"
    echo "  ✓ Added customer1.saas.test"
fi

if ! grep -q "customer2.saas.test" "$HOSTS_FILE"; then
    echo "127.0.0.1 customer2.saas.test" >> "$HOSTS_FILE"
    echo "  ✓ Added customer2.saas.test"
fi

# Test Nginx configuration
echo "🧪 Testing Nginx configuration..."
if nginx -t; then
    echo "  ✓ Nginx configuration is valid"
else
    echo "  ❌ Nginx configuration test failed!"
    exit 1
fi

# Reload Nginx
echo "🔄 Reloading Nginx..."
systemctl reload nginx

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Your virtual hosts are now configured:"
echo "   - http://landlord.saas.test"
echo "   - http://customer1.saas.test"
echo "   - http://customer2.saas.test"
echo ""
echo "📝 Make sure your .env file has the correct APP_URL and database settings."
echo "📝 Ensure storage permissions are set correctly:"
echo "   sudo chown -R www-data:www-data $PROJECT_DIR/storage"
echo "   sudo chown -R www-data:www-data $PROJECT_DIR/bootstrap/cache"
echo "   sudo chmod -R 775 $PROJECT_DIR/storage"
echo "   sudo chmod -R 775 $PROJECT_DIR/bootstrap/cache"
