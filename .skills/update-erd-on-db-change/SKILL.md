---
description: Update ERD documentation when database migrations are added or modified
tags: [database, migrations, erd, documentation, laravel]
---

# Update ERD on Database Change Skill

This skill covers updating the ERD documentation files when database migrations are added or modified.

The ERD is split into three separate files by database type:
- `docs/erd/landlord-erd.md` - Landlord database tables
- `docs/erd/tenant-erd.md` - Tenant database tables
- `docs/erd/shared-erd.md` - Shared database tables

## When to Update ERD

Update the ERD document whenever:
- A new migration file is created (landlord, tenant, or shared)
- An existing migration is modified
- Tables are renamed, added, or removed
- Columns are added, removed, or renamed
- Relationships (foreign keys) are added or changed

## Migration File Locations

### Landlord Migrations
- Main: `backend/database/migrations/landlord/`
- Modules: `backend/modules/*/database/migrations/landlord/`

### Tenant Migrations
- Main: `backend/database/migrations/tenant/`
- Modules: `backend/modules/*/database/migrations/tenant/`

### Shared Migrations
- Main: `backend/database/migrations/shared/`
- Modules: `backend/modules/*/database/migrations/shared/`

## Steps to Update ERD

### 1. Read the New/Modified Migration File

```bash
# Read the migration file to understand the changes
read_file /path/to/migration/file.php
```

### 2. Update the Appropriate ERD File

Based on the database type, open the appropriate ERD file:

- **Landlord tables**: Open `docs/erd/landlord-erd.md`
- **Tenant tables**: Open `docs/erd/tenant-erd.md`
- **Shared tables**: Open `docs/erd/shared-erd.md`

Locate the appropriate module section within the file.

Within each section, tables are organized by module (e.g., "### API Module", "### Auth Module").

### 3. Add or Update Table Entry

Use this format for each table:

```markdown
#### table_name
- **PK**: primary_key_column
- **Columns**: column1, column2 (unique), column3, foreign_key_column
- **FK**: foreign_key_column → referenced_table (cascade delete/null)
- **Morph**: morph_field (polymorphic) - if applicable
- **Indexes**: index1, index2, unique [col1, col2]
- **Soft Deletes**: Yes - if soft deletes are enabled
```

### 4. Update Cross-Database Relationships (if applicable)

If the change affects cross-database relationships, update the "Cross-Database Relationships" section at the bottom of the appropriate ERD file.

## Common Patterns

### Adding a New Table

```markdown
#### new_table
- **PK**: id
- **Columns**: name, slug (unique), description, status, created_by
- **FK**: created_by → users
- **Indexes**: slug, status
- **Soft Deletes**: Yes
```

### Adding Foreign Key

```markdown
#### existing_table
- **PK**: id
- **Columns**: name, user_id, description
- **FK**: user_id → users (cascade delete)
```

### Polymorphic Relationship

```markdown
#### polymorphic_table
- **PK**: id
- **Columns**: related_type, related_id, description
- **FK**: related_type, related_id (morph)
- **Morph**: related (polymorphic)
```

### Cross-Database Reference

```markdown
#### tenant_table
- **PK**: id
- **Columns**: name, brand_id, description
- **FK**: brand_id → brands (landlord database, no FK constraint)
```

### Adding Index

```markdown
#### table
- **PK**: id
- **Columns**: name, email, status
- **Indexes**: email, unique [name, email], [status, created_at]
```

## Module Organization

Tables are organized by module within each database type:

**Landlord Modules:**
- Core Tables
- API Module
- Auth Module
- Customer Module
- Development Module
- Email Module
- Geography Module
- Localization Module
- Payment Module
- Static Pages Module
- Subscription Module
- Utilities Module
- Workflow Module

**Tenant Modules:**
- Core Tables
- Accounting Module
- Auth Module
- CRM Module
- Customer Module
- HR Module
- Inventory Module
- Reporting Module
- Sales Module
- Ticket Module

**Shared Modules:**
- Core Tables
- Auth Module
- Development Module
- Email Module
- FileManager Module
- Localization Module
- Notification Module

## Important Notes

1. **Soft Deletes**: Mark tables with `softDeletes()` in migrations as having "Soft Deletes: Yes"
2. **Unique Constraints**: Mark columns with `unique()` as "(unique)"
3. **Foreign Key Actions**: Note cascade delete, set null, or restrict actions
4. **Cross-Database Relationships**: Tenant tables often reference landlord tables without FK constraints
5. **Morph Relationships**: Use the "Morph" field for polymorphic relationships
6. **Composite Indexes**: Use format `[col1, col2]` for multi-column indexes
7. **Module Location**: Place new tables under the appropriate module section

## Verification

After updating the ERD:
1. Verify the table is in the correct section (Landlord/Tenant/Shared)
2. Verify the table is under the correct module
3. Check that all foreign keys are documented
4. Ensure indexes are listed accurately
5. Verify soft delete status is correct

## Generating ERD Diagram

To generate a visual ERD diagram from the markdown files:

### Prerequisites

1. Install Graphviz on your system:
   - Ubuntu/Debian: `sudo apt-get install graphviz`
   - macOS: `brew install graphviz`
   - Windows: Download from https://graphviz.org/download/

2. Install the Python graphviz package:
   ```bash
   cd docs/erd
   # Create a virtual environment (recommended)
   python3 -m venv venv
   source venv/bin/activate  # On Windows: venv\Scripts\activate
   pip install -r requirements.txt
   ```

### Generate the Diagram
```bash
cd docs/erd
source venv/bin/activate  # Activate virtual environment
python generate_erd.py
```

This will create `erd.jpg` in the `docs/erd` directory with a visual representation of the database schema.

### Regenerate After Changes
After updating the ERD markdown files, regenerate the diagram to keep it in sync with the documentation.
