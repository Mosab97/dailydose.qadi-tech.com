# How to Run Migrations - Multi-Language Support

This guide explains how to run the database migrations for the multi-language feature.

## Prerequisites

- You must be logged in as an admin user
- Database connection must be configured correctly
- Migrations must be enabled in `application/config/migration.php`

## Method 1: Via Web Browser (Recommended)

### Step 1: Update Migration Version

The migration version in `application/config/migration.php` has been updated to `11` to include all three new migrations:
- 009_product_translations
- 010_product_add_on_translations  
- 011_populate_initial_translations

### Step 2: Access Migration Endpoint

1. **Log in to Admin Panel**
   - Navigate to: `http://your-domain.com/admin/login`
   - Log in with your admin credentials

2. **Run Migrations**
   - Navigate to: `http://your-domain.com/admin/migrate`
   - Or use the direct URL: `http://your-domain.com/admin/migrate/index`

3. **Expected Result**
   - If successful, you'll see: **"Migration Successfully"**
   - If there's an error, you'll see the error message

### Step 3: Verify Migrations

After running migrations, verify that the tables were created:

**Option A: Using phpMyAdmin**
1. Go to phpMyAdmin (usually at `http://localhost:9080` for Docker)
2. Select your database
3. Check for these new tables:
   - `product_translations`
   - `product_add_on_translations`
   - `migrations` (should show version 11)

**Option B: Using MySQL Command Line**
```sql
SHOW TABLES LIKE '%translation%';
SELECT * FROM migrations;
```

## Method 2: Via Command Line (If Available)

If you have CLI access to your CodeIgniter installation:

```bash
# Navigate to your project directory
cd /path/to/dailydose.qadi-tech.com

# Run migrations via PHP CLI
php index.php migrate index
```

## Method 3: Manual SQL Execution

If the migration controller doesn't work, you can manually execute the SQL:

### Step 1: Create product_translations Table

```sql
CREATE TABLE `product_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `name` varchar(512) NOT NULL,
  `short_description` mediumtext DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_lang_unique` (`product_id`, `language_code`),
  KEY `product_id` (`product_id`),
  KEY `language_code` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 2: Create product_add_on_translations Table

```sql
CREATE TABLE `product_add_on_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `add_on_id` int(11) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `title` varchar(30) NOT NULL,
  `description` varchar(60) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `addon_lang_unique` (`add_on_id`, `language_code`),
  KEY `add_on_id` (`add_on_id`),
  KEY `language_code` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 3: Populate Initial Translations

```sql
-- Populate product_translations with existing product data (English)
INSERT INTO product_translations (product_id, language_code, name, short_description, date_created)
SELECT id, 'en', name, short_description, date_added
FROM products
WHERE name IS NOT NULL AND name != '';

-- Populate product_add_on_translations with existing add-on data (English)
INSERT INTO product_add_on_translations (add_on_id, language_code, title, description, date_created)
SELECT id, 'en', title, description, date_created
FROM product_add_ons
WHERE title IS NOT NULL AND title != '';
```

### Step 4: Update Migrations Table

```sql
-- Update migrations table to reflect version 11
INSERT INTO migrations (version) VALUES (11)
ON DUPLICATE KEY UPDATE version = 11;
```

## Troubleshooting

### Issue: "Migration Successfully" but tables not created

**Solution**: 
1. Check database permissions
2. Verify database connection in `application/config/database.php`
3. Check for SQL errors in database logs

### Issue: "You are not authorized to do this"

**Solution**:
1. Make sure you're logged in as an admin user
2. Check that `$this->ion_auth->is_admin()` returns true
3. Verify your user has admin privileges

### Issue: Migration version mismatch

**Solution**:
1. Check `application/config/migration.php`
2. Ensure `migration_version` is set to `11`
3. Ensure `migration_enabled` is set to `TRUE`

### Issue: Duplicate key errors when populating

**Solution**:
- This means translations already exist. You can safely skip the populate step or use:
```sql
INSERT IGNORE INTO product_translations ...
INSERT IGNORE INTO product_add_on_translations ...
```

### Issue: Tables already exist

**Solution**:
- If tables already exist, you can either:
  1. Drop and recreate them (⚠️ **WARNING**: This will delete existing translations)
  2. Skip table creation and only run the populate step

## Verification Checklist

After running migrations, verify:

- [ ] `product_translations` table exists
- [ ] `product_add_on_translations` table exists
- [ ] `migrations` table shows version 11
- [ ] Existing products have English translations in `product_translations`
- [ ] Existing add-ons have English translations in `product_add_on_translations`

## Rollback (If Needed)

If you need to rollback migrations:

1. **Via Web**: `http://your-domain.com/admin/migrate/rollback/8`
   - This will rollback to version 8 (before our new migrations)
   - ⚠️ **WARNING**: This will drop the translation tables and lose all translation data

2. **Manual Rollback**:
```sql
DROP TABLE IF EXISTS product_add_on_translations;
DROP TABLE IF EXISTS product_translations;
UPDATE migrations SET version = 8;
```

## Next Steps

After successful migration:

1. ✅ Test adding a product with translations in admin panel
2. ✅ Test API with `language` parameter
3. ✅ Verify translations are saved and retrieved correctly
4. ✅ Add Arabic and Hebrew translations for existing products

## Support

If you encounter issues:
1. Check the error message from the migration endpoint
2. Review database logs
3. Verify all prerequisites are met
4. Check file permissions on migration files

---

**Migration Files**:
- `009_product_translations.php`
- `010_product_add_on_translations.php`
- `011_populate_initial_translations.php`

**Migration Version**: 11

