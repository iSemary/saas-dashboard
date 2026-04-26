# Nginx Configuration Templates

This directory contains nginx configuration templates for the SaaS Dashboard multi-tenant platform.

## Files

- **tenant-config.conf** - Template for tenant nginx configuration
- **landlord-config.conf** - Template for landlord nginx configuration

## Usage

### Tenant Configuration

Replace placeholders in `tenant-config.conf`:
- `{DOMAIN}` - Tenant domain (e.g., `customer1.saas.test`)
- `{PROJECT_PATH}` - Full path to project (e.g., `/var/www/saas-dashboard`)

Example:
```bash
sed -e 's/{DOMAIN}/customer1.saas.test/g' \
    -e 's/{PROJECT_PATH}\/var\/www\/saas-dashboard/g' \
    tenant-config.conf > /etc/nginx/sites-available/customer1.saas.test
```

### Landlord Configuration

Replace placeholders in `landlord-config.conf`:
- `{PROJECT_PATH}` - Full path to project (e.g., `/var/www/saas-dashboard`)

Example:
```bash
sed -e 's/{PROJECT_PATH}\/var\/www\/saas-dashboard/g' \
    landlord-config.conf > /etc/nginx/sites-available/landlord.saas.test
```

## Activation

After creating the config file, enable it:
```bash
ln -s /etc/nginx/sites-available/{domain} /etc/nginx/sites-enabled/{domain}
nginx -t
systemctl reload nginx
```

## Local Development

For local development, add entries to `/etc/hosts`:
```
127.0.0.1 landlord.saas.test
127.0.0.1 customer1.saas.test
127.0.0.1 customer2.saas.test
```
