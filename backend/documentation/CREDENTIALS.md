# Default Login Credentials

This document contains the default login credentials for the SaaS Dashboard multi-tenant system.

## 🏢 Landlord (Platform Admin)

**URL:** http://landlord.saas.test

**Credentials:**
- **Email:** `admin@landlord.saas.test` (or from `DEFAULT_LANDLORD_EMAIL` env)
- **Username:** `landlord` (or from `DEFAULT_LANDLORD_USERNAME` env)
- **Password:** `password123` (or from `DEFAULT_LANDLORD_PASSWORD` env)

**Role:** Landlord (full platform access)

---

## 👤 Customer 1 (Tenant)

**URL:** http://customer1.saas.test

**Credentials:**
- **Email:** `admin@customer1.saas.test`
- **Username:** `admin`
- **Password:** `password123`

**Role:** Super Admin (full tenant access)

---

## 👤 Customer 2 (Tenant)

**URL:** http://customer2.saas.test

**Credentials:**
- **Email:** `admin@customer2.saas.test`
- **Username:** `admin`
- **Password:** `password123`

**Role:** Super Admin (full tenant access)

---

## 🔧 Creating Credentials

### Option 1: Run All Credentials Seeder (Recommended)

This seeder creates all credentials at once:

```bash
php artisan db:seed --class=Database\\Seeders\\Landlord\\AllCredentialsSeeder
```

### Option 2: Run Individual Seeders

```bash
# 1. Create landlord user
php artisan db:seed --class=Modules\\Auth\\Database\\Seeders\\LandlordUserSeeder

# 2. Create tenant records
php artisan db:seed --class=Database\\Seeders\\Landlord\\LandlordTenantSeeder

# 3. Create tenant users (creates customer1 and customer2)
php artisan db:seed --class=Database\\Seeders\\Landlord\\TenantUsersSeeder
```

### Option 3: Use Full Setup Command

```bash
php artisan app:start
```

This runs all migrations and seeders including:
- Landlord migrations
- Tenant records
- Roles and permissions
- Landlord user
- Modules and configurations

---

## 📝 Environment Variables

You can customize landlord credentials by setting these in your `.env` file:

```env
DEFAULT_LANDLORD_NAME=Landlord Admin
DEFAULT_LANDLORD_USERNAME=landlord
DEFAULT_LANDLORD_EMAIL=admin@landlord.saas.test
DEFAULT_LANDLORD_PASSWORD=password123
```

---

## 🔐 Security Notes

⚠️ **Important:** These are default credentials for development only!

- Change all passwords in production
- Use strong, unique passwords
- Enable 2FA for admin accounts
- Review and restrict permissions as needed

---

## 🧪 Testing Login

After running the seeders, you can test login at:

1. **Landlord:** http://landlord.saas.test/login
2. **Customer 1:** http://customer1.saas.test/login
3. **Customer 2:** http://customer2.saas.test/login

The login flow:
1. Enter organization name (if not in subdomain)
2. Enter username/email and password
3. Complete 2FA if enabled

---

## 📚 Related Documentation

- [Nginx Setup Guide](./NGINX_SETUP.md)
- [Database Setup](./README.md#database-setup)
- [Quick Start Guide](./README.md#quick-start)
