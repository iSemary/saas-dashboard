# Nginx Configuration Documentation

This directory contains Nginx configuration files for the SaaS Dashboard application.

## Files

- `landlord.saas.test.conf` - Configuration for the landlord subdomain
- `tenant.saas.test.conf` - Sample configuration for tenant subdomains
- `README.md` - This documentation file

## Installation

### 1. Copy Configuration Files

```bash
# Copy landlord configuration
sudo cp documentation/servers/nginx/landlord.saas.test.conf /etc/nginx/sites-available/

# Copy tenant configuration (rename as needed)
sudo cp documentation/servers/nginx/tenant.saas.test.conf /etc/nginx/sites-available/customer1.saas.test.conf
sudo cp documentation/servers/nginx/tenant.saas.test.conf /etc/nginx/sites-available/customer2.saas.test.conf
```

### 2. Enable Sites

```bash
# Enable landlord site
sudo ln -s /etc/nginx/sites-available/landlord.saas.test.conf /etc/nginx/sites-enabled/

# Enable tenant sites
sudo ln -s /etc/nginx/sites-available/customer1.saas.test.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/customer2.saas.test.conf /etc/nginx/sites-enabled/
```

### 3. Test and Reload

```bash
# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

## Configuration Details

### Security Headers

All configurations include the following security headers:

- **X-Frame-Options**: Prevents clickjacking attacks
- **X-XSS-Protection**: Enables XSS filtering
- **X-Content-Type-Options**: Prevents MIME type sniffing
- **Referrer-Policy**: Controls referrer information
- **Content-Security-Policy**: Controls resource loading and execution

### Content Security Policy (CSP)

The CSP policy allows:

- **default-src**: `'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'`
- **connect-src**: `'self' ws: wss: http: https:`

**Important**: The `'unsafe-eval'` directive is required for DataTables to function properly. Without it, you'll encounter CSP violations when DataTables tries to use `eval()`.

### PHP Processing

- Uses PHP-FPM socket: `/var/run/php/php8.4-fpm.sock`
- Hides X-Powered-By header for security
- Includes standard FastCGI parameters

### Logging

Each site has separate access and error logs:

- **Landlord**: `/var/log/nginx/landlord.saas.test.access.log`
- **Tenant**: `/var/log/nginx/tenant.saas.test.access.log`

## Multi-Tenancy Setup

### Subdomain Configuration

The application uses subdomain-based multi-tenancy:

- `landlord.saas.test` - Main application dashboard
- `customer1.saas.test` - Tenant 1 dashboard
- `customer2.saas.test` - Tenant 2 dashboard
- `*.saas.test` - Additional tenant subdomains

### Adding New Tenants

1. Copy the tenant configuration file:
   ```bash
   sudo cp documentation/servers/nginx/tenant.saas.test.conf /etc/nginx/sites-available/newtenant.saas.test.conf
   ```

2. Update the server_name:
   ```bash
   sudo sed -i 's/tenant.saas.test/newtenant.saas.test/g' /etc/nginx/sites-available/newtenant.saas.test.conf
   ```

3. Update log file names:
   ```bash
   sudo sed -i 's/tenant.saas.test/newtenant.saas.test/g' /etc/nginx/sites-available/newtenant.saas.test.conf
   ```

4. Enable the site:
   ```bash
   sudo ln -s /etc/nginx/sites-available/newtenant.saas.test.conf /etc/nginx/sites-enabled/
   ```

5. Test and reload:
   ```bash
   sudo nginx -t && sudo systemctl reload nginx
   ```

## Troubleshooting

### Common Issues

#### 1. CSP Violations

**Problem**: DataTables shows "Refused to evaluate a string as JavaScript" errors

**Solution**: Ensure the CSP policy includes `'unsafe-eval'`:
```nginx
add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'; connect-src 'self' ws: wss: http: https:;" always;
```

#### 2. WebSocket Connection Errors

**Problem**: Socket.io connections are blocked

**Solution**: Ensure the CSP policy includes WebSocket connections:
```nginx
connect-src 'self' ws: wss: http: https:;
```

#### 3. PHP-FPM Socket Not Found

**Problem**: 502 Bad Gateway errors

**Solution**: Check if PHP-FPM is running and the socket exists:
```bash
sudo systemctl status php8.4-fpm
ls -la /var/run/php/php8.4-fpm.sock
```

#### 4. Permission Issues

**Problem**: 403 Forbidden errors

**Solution**: Check file permissions:
```bash
sudo chown -R www-data:www-data /var/www/saas-dashboard
sudo chmod -R 755 /var/www/saas-dashboard
sudo chmod -R 775 /var/www/saas-dashboard/storage
sudo chmod -R 775 /var/www/saas-dashboard/bootstrap/cache
```

### Log Analysis

Check error logs for specific issues:
```bash
# View recent errors
sudo tail -f /var/log/nginx/landlord.saas.test.error.log

# View access logs
sudo tail -f /var/log/nginx/landlord.saas.test.access.log
```

## SSL/TLS Configuration

For production environments, add SSL configuration:

```nginx
server {
    listen 443 ssl http2;
    server_name landlord.saas.test;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    # SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # ... rest of configuration
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name landlord.saas.test;
    return 301 https://$server_name$request_uri;
}
```

## Performance Optimization

### Caching

Add caching for static assets:

```nginx
location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}

location ~* \.(woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### Gzip Compression

Enable gzip compression:

```nginx
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
```

## Maintenance

### Regular Tasks

1. **Monitor logs** for errors and performance issues
2. **Update SSL certificates** before expiration
3. **Review CSP policies** for security updates
4. **Check disk space** for log files
5. **Update Nginx** for security patches

### Backup

Backup configuration files:
```bash
sudo cp -r /etc/nginx/sites-available/ /backup/nginx-config-$(date +%Y%m%d)/
```
