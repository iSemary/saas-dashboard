# Tenant Domain Column Convention

## ⚠️ CRITICAL: Tenant Domain Column Usage

### Domain Column Value Format

When creating tenant records in the `tenants` table, the **`domain`** column MUST contain:

- ✅ **ONLY the tenant name/subdomain**: `customer1`
- ❌ **NOT the full domain**: `customer1.saas.test`

### Examples

| Tenant Record | Domain Column Value | Full Domain (Constructed) |
|---------------|-------------------|---------------------------|
| Customer 1    | `customer1`       | `customer1.saas.test`     |
| Client ABC    | `clientabc`       | `clientabc.saas.test`    |
| Demo Site     | `demo`           | `demo.saas.test`         |

### Implementation Details

- **Full URLs are constructed** using `TenantHelper::generateURL($tenant->name)`
- **The domain column** stores the subdomain prefix only
- **Authentication and routing** depends on this convention
- **TenantHelper** methods expect the subdomain format

### Related Code References

- `TenantHelper::generateURL()` - Constructs full URLs
- `TenantHelper::getSubDomain()` - Extracts subdomain from request
- Organization check in `AuthController`
- Organization validation in `OrganizationController`
- `TenantRepository::generateDomain()` - **MUST return subdomain only**

### ⚠️ Common Pitfall

The `TenantRepository::generateDomain()` method was incorrectly generating full domains:

```php
// ❌ WRONG (fixed):
return $customerUsername . '.' . config('settings.domain');  // Creates "customer1.saas.test"

// ✅ CORRECT:
return $customerUsername;  // Creates "customer1"
```

**Full domains are constructed by `TenantHelper::generateURL()` method, not stored in database!**

### Migration Impact

This convention affects:
- Tenant creation commands
- Organization validation logic
- Domain-based routing
- Subdomain extraction middleware

**⚠️ Breaking this convention will cause authentication and routing failures!**
