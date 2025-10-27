# Unified Folder Structure Convention

## 🎯 **Objective**

Create a completely unified folder structure based on **Laravel's official naming conventions** (lowercase) to eliminate all inconsistencies.

## 📋 **Laravel Official Conventions**

Based on Laravel's official documentation and core structure:

- **`database`** (lowercase d)
- **`migrations`** (lowercase m)  
- **`seeders`** (lowercase s)
- **`factories`** (lowercase f)

## 🔧 **Unified Structure**

### **Main Database Structure**
```
database/
├── migrations/
│   ├── landlord/              # Landlord-specific migrations
│   ├── tenant/                # Tenant-specific migrations
│   └── shared/                # Shared migrations
├── seeders/
│   ├── landlord/              # Landlord-specific seeders
│   ├── tenant/                # Tenant-specific seeders
│   └── shared/                # Shared seeders
└── factories/                 # Model factories
```

### **Module Structure**
```
modules/ModuleName/
├── database/
│   ├── migrations/
│   │   ├── landlord/          # Landlord database migrations
│   │   ├── tenant/            # Tenant database migrations  
│   │   └── shared/            # Shared migrations
│   ├── seeders/
│   │   ├── landlord/          # Landlord database seeders
│   │   ├── tenant/            # Tenant database seeders
│   │   └── shared/            # Shared seeders
│   └── factories/             # Model factories
├── app/
├── config/
├── resources/
├── routes/
└── tests/
```

## 🚨 **Current Inconsistencies to Fix**

### **1. Database Folder**
- ❌ Some modules use `Database` (capital D)
- ✅ Should be `database` (lowercase d)

### **2. Migrations Folder**  
- ❌ Some modules use `Migrations` (capital M)
- ✅ Should be `migrations` (lowercase m)

### **3. Seeders Folder**
- ❌ Some modules use `Seeders` (capital S)  
- ✅ Should be `seeders` (lowercase s)

### **4. Subfolder Naming**
- ❌ Mixed: `Landlord`, `Tenant`, `Shared` (capital L, T, S)
- ✅ Should be: `landlord`, `tenant`, `shared` (lowercase)

## 🔄 **Migration Plan**

### **Phase 1: Rename Module Folders**
```bash
# Rename Database to database
find modules -name "Database" -type d -exec sh -c 'mv "$1" "$(dirname "$1")/database"' _ {} \;

# Rename Migrations to migrations  
find modules -name "Migrations" -type d -exec sh -c 'mv "$1" "$(dirname "$1")/migrations"' _ {} \;

# Rename Seeders to seeders
find modules -name "Seeders" -type d -exec sh -c 'mv "$1" "$(dirname "$1")/seeders"' _ {} \;
```

### **Phase 2: Rename Subfolders**
```bash
# Rename Landlord to landlord
find modules -name "Landlord" -type d -exec sh -c 'mv "$1" "$(dirname "$1")/landlord"' _ {} \;

# Rename Tenant to tenant
find modules -name "Tenant" -type d -exec sh -c 'mv "$1" "$(dirname "$1")/tenant"' _ {} \;

# Rename Shared to shared
find modules -name "Shared" -type d -exec sh -c 'mv "$1" "$(dirname "$1")/shared"' _ {} \;
```

### **Phase 3: Update Commands**
- Update `LandlordSetupCommand` to use lowercase paths
- Update `TenantSetupCommand` to use lowercase paths
- Update all path references in the codebase

## ✅ **Expected Final Structure**

```
modules/ModuleName/
├── database/
│   ├── migrations/
│   │   ├── landlord/
│   │   ├── tenant/
│   │   └── shared/
│   └── seeders/
│       ├── landlord/
│       ├── tenant/
│       └── shared/
```

## 🎯 **Benefits**

1. **✅ Laravel Compliance**: Follows Laravel's official lowercase convention
2. **✅ Consistency**: All folders use the same naming pattern
3. **✅ Cross-Platform**: Works consistently across different operating systems
4. **✅ Maintainability**: Easier to locate and manage files
5. **✅ Developer Experience**: Predictable structure for all developers

## 🔍 **Verification Commands**

```bash
# Check for any remaining capitalized folders
find modules -type d -name "*atabase*" -o -name "*igration*" -o -name "*eeder*" | grep -E "[A-Z]"

# Should return empty if fully standardized
```
