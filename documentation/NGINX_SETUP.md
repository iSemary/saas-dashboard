# Nginx Virtual Host Setup for SaaS Dashboard

This guide will help you set up Nginx virtual hosts for the multi-tenant SaaS dashboard with subdomain routing.

## Quick Setup (Automated)

Run the setup script as root:

```bash
sudo /home/abdelrahman/me/freelance/saas/saas-dashboard/setup-nginx.sh
```

This script will:
- Copy the Nginx configuration to `/etc/nginx/sites-available/`
- Enable the site by creating a symlink
- Add entries to `/etc/hosts` file
- Test and reload Nginx

## Manual Setup

### Step 1: Copy Nginx Configuration

```bash
sudo cp /home/abdelrahman/me/freelance/saas/saas-dashboard/nginx.conf /etc/nginx/sites-available/saas-dashboard.conf
```

### Step 2: Enable the Site

```bash
sudo ln -s /etc/nginx/sites-available/saas-dashboard.conf /etc/nginx/sites-enabled/saas-dashboard.conf
```

### Step 3: Add Hosts File Entries

Edit `/etc/hosts` file:

```bash
sudo nano /etc/hosts
```

Add these lines:

```
127.0.0.1 landlord.saas.test
127.0.0.1 customer1.saas.test
127.0.0.1 customer2.saas.test
```

### Step 4: Test and Reload Nginx

```bash
# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

## Configuration Details

The Nginx configuration:
- Handles wildcard subdomains (`*.saas.test`)
- Points to `/home/abdelrahman/me/freelance/saas/saas-dashboard/public`
- Uses PHP 8.4-FPM for processing
- Includes security headers
- Handles Laravel routing correctly

## PHP-FPM Socket

The configuration uses:
```
fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
```

If you're using a different PHP version, update this line in the configuration file.

## Storage Permissions

After setup, ensure proper permissions:

```bash
sudo chown -R www-data:www-data /home/abdelrahman/me/freelance/saas/saas-dashboard/storage
sudo chown -R www-data:www-data /home/abdelrahman/me/freelance/saas/saas-dashboard/bootstrap/cache
sudo chmod -R 775 /home/abdelrahman/me/freelance/saas/saas-dashboard/storage
sudo chmod -R 775 /home/abdelrahman/me/freelance/saas/saas-dashboard/bootstrap/cache
```

## Environment Configuration

Make sure your `.env` file has the correct settings:

```env
APP_URL=http://landlord.saas.test
APP_ENV=local
APP_DEBUG=true

# Database connections
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=landlord_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Tenant database connection
TENANT_DB_CONNECTION=mysql
TENANT_DB_HOST=127.0.0.1
TENANT_DB_PORT=3306
TENANT_DB_USERNAME=your_username
TENANT_DB_PASSWORD=your_password
```

## Testing

After setup, test the virtual hosts:

1. **Landlord Dashboard**: http://landlord.saas.test
2. **Customer 1**: http://customer1.saas.test
3. **Customer 2**: http://customer2.saas.test

## Troubleshooting

### 502 Bad Gateway
- Check PHP-FPM is running: `sudo systemctl status php8.4-fpm`
- Verify socket path: `ls -la /var/run/php/php8.4-fpm.sock`
- Check PHP-FPM user matches Nginx user

### 403 Forbidden
- Check file permissions on `storage` and `bootstrap/cache`
- Verify `root` path in Nginx config is correct
- Check SELinux/AppArmor if enabled

### 404 Not Found
- Verify Laravel routes are working
- Check `APP_URL` in `.env` matches your domain
- Ensure `storage:link` is created: `php artisan storage:link`

### Subdomain Not Working
- Verify hosts file entries are correct
- Check Nginx server_name includes wildcard: `*.saas.test`
- Clear browser cache and DNS cache

## Additional Subdomains

To add more tenant subdomains:

1. Add to `/etc/hosts`:
   ```
   127.0.0.1 newtenant.saas.test
   ```

2. No need to modify Nginx config (wildcard handles all subdomains)

3. Reload Nginx (optional, not required):
   ```bash
   sudo systemctl reload nginx
   ```

## Removing the Configuration

To remove the virtual host:

```bash
# Remove symlink
sudo rm /etc/nginx/sites-enabled/saas-dashboard.conf

# Remove configuration file (optional)
sudo rm /etc/nginx/sites-available/saas-dashboard.conf

# Remove hosts entries
sudo nano /etc/hosts
# Remove the three lines for *.saas.test

# Reload Nginx
sudo systemctl reload nginx
```
