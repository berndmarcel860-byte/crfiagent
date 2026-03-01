# Database Fix: Missing admin_notes Column

## Error Description

**Error Message:**
```
Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'admin_notes' in 'field list'
```

**When it occurs:**
- When admin tries to add KYC documents for a user via `admin/admin_kyc.php`
- The error happens during the INSERT operation into the `kyc_verification_requests` table

## Root Cause

The `kyc_verification_requests` table is missing the `admin_notes` column that the application code expects to exist.

**Current table structure:**
```sql
CREATE TABLE `kyc_verification_requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `document_type` enum('passport','id_card','driving_license','other'),
  `document_front` varchar(255),
  `document_back` varchar(255),
  `selfie_with_id` varchar(255),
  `address_proof` varchar(255),
  `status` enum('pending','approved','rejected'),
  `rejection_reason` text,
  `verified_by` int,
  `verified_at` datetime,
  `created_at` datetime,
  `updated_at` datetime
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Missing column:** `admin_notes` TEXT

## Quick Fix (5 minutes)

### Step 1: Run the Migration SQL

Run the migration file to add the missing column:

```bash
mysql -u your_username -p your_database_name < database_migration_add_admin_notes_to_kyc.sql
```

Or execute directly in MySQL:

```sql
USE your_database_name;

ALTER TABLE `kyc_verification_requests` 
ADD COLUMN `admin_notes` TEXT NULL COMMENT 'Notes added by admin when creating/processing KYC' 
AFTER `address_proof`;
```

### Step 2: Verify the Fix

Check that the column was added successfully:

```sql
DESCRIBE kyc_verification_requests;
```

You should see the `admin_notes` column in the output.

### Step 3: Test

1. Log in to the admin panel
2. Go to KYC management
3. Try to add KYC documents for a user
4. The operation should now complete without errors

## Detailed Migration Instructions

### For Production Server

1. **Backup your database first:**
   ```bash
   mysqldump -u username -p database_name > backup_before_migration_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Apply the migration:**
   ```bash
   mysql -u username -p database_name < database_migration_add_admin_notes_to_kyc.sql
   ```

3. **Verify the change:**
   ```bash
   mysql -u username -p database_name -e "DESCRIBE kyc_verification_requests;"
   ```

4. **Test the functionality:**
   - Admin can add KYC for users
   - Admin notes are saved correctly
   - No database errors occur

### For Development Environment

```bash
# Navigate to the project directory
cd /path/to/crfiagent

# Run the migration
mysql -u root -p your_dev_database < database_migration_add_admin_notes_to_kyc.sql

# Or use phpMyAdmin:
# 1. Open phpMyAdmin
# 2. Select your database
# 3. Go to SQL tab
# 4. Paste the ALTER TABLE command
# 5. Click "Go"
```

## What This Column Does

The `admin_notes` column allows administrators to:
- Add notes when creating KYC documents for users
- Document any special circumstances or requirements
- Track administrative decisions and reasons
- Provide context for KYC submissions

**Example usage:**
```php
$adminNotes = "Documents provided by user via email - verified by phone call";
// This note is saved in the admin_notes column
```

## Updated Table Structure

After applying the migration, the table will have this structure:

```sql
CREATE TABLE `kyc_verification_requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `document_type` enum('passport','id_card','driving_license','other'),
  `document_front` varchar(255),
  `document_back` varchar(255),
  `selfie_with_id` varchar(255),
  `address_proof` varchar(255),
  `admin_notes` text,                    -- ✅ NEW COLUMN ADDED
  `status` enum('pending','approved','rejected'),
  `rejection_reason` text,
  `verified_by` int,
  `verified_at` datetime,
  `created_at` datetime,
  `updated_at` datetime
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Affected Files

The following files reference the `admin_notes` column:

1. **admin/admin_ajax/add_kyc.php** (line 138)
   - INSERT operation that requires admin_notes column
   
2. **admin/admin_kyc.php**
   - Admin interface for adding KYC documents
   - Includes form field for admin notes

## Rollback (if needed)

If you need to remove the column (not recommended):

```sql
ALTER TABLE `kyc_verification_requests` DROP COLUMN `admin_notes`;
```

## Verification Checklist

After applying the fix:

- [ ] Database migration completed without errors
- [ ] `admin_notes` column exists in `kyc_verification_requests` table
- [ ] Admin can add KYC documents for users
- [ ] Admin notes are saved correctly
- [ ] No database errors in error logs
- [ ] Application functions normally

## Troubleshooting

### Error: "Table doesn't exist"

Make sure you're using the correct database name:
```bash
mysql -u username -p -e "SHOW DATABASES;"
```

### Error: "Access denied"

Check your MySQL user permissions:
```bash
GRANT ALTER ON database_name.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

### Error: "Column already exists"

The column was already added. Check with:
```sql
SHOW COLUMNS FROM kyc_verification_requests LIKE 'admin_notes';
```

## Support

If you encounter any issues:

1. Check the MySQL error log: `/var/log/mysql/error.log`
2. Verify database connection settings in `includes/config.php`
3. Ensure you have ALTER TABLE privileges
4. Review the migration file for any syntax errors

## Related Documentation

- **SERVER_CONFIGURATION.md** - General server setup
- **NGINX_UPLOAD_FIX.md** - File upload configuration
- **cryptofinanze (5).sql** - Complete database schema

---

**Migration file:** `database_migration_add_admin_notes_to_kyc.sql`  
**Status:** Ready to apply  
**Risk level:** Low (only adds a column, doesn't modify existing data)  
**Downtime required:** None (ALTER TABLE is instant for empty/small tables)
