# IDENTICAL Schema Migration Guide

## ⚠️ CRITICAL WARNING

This migration makes the database schemas **100% IDENTICAL** by:
- ✅ Adding new tables and columns from kryptox
- ❌ **DROPPING** the `online_users.current_page` column
- ⚠️ **MODIFYING** column types in `online_users` table

**Data in the `current_page` column will be PERMANENTLY LOST!**

## 🆚 Difference from Previous Migration

### Previous Migration (Safe Mode)
- ✅ Added all new structures from kryptox
- ✅ Preserved ALL existing columns (including `current_page`)
- ✅ Zero data loss
- ❌ Result: Databases are **functionally equivalent** but not identical

### This Migration (Identical Mode)
- ✅ Added all new structures from kryptox
- ❌ **Removes** `current_page` column
- ⚠️ **Modifies** `online_users` columns to match kryptox exactly
- ❌ Data in `current_page` column is **LOST**
- ✅ Result: Databases are **100% IDENTICAL** in structure

## 📋 What Will Be Changed

### 1. New Tables (Same as Before)
- `email_templates_backup`
- `email_templates_backup1`
- `user_notifications`

### 2. New Columns (Same as Before)
- `case_recovery_transactions.added_by_admin_id`
- `deposits.admin_id`
- `support_tickets.assigned_admin_id`
- `user_documents.reviewed_by_admin_id`
- `withdrawals.admin_id`
- `withdrawals.processed_at`
- `withdrawals.processed_by`

### 3. ❌ DESTRUCTIVE CHANGES (NEW!)

#### Table: `online_users`

**Column Removed:**
```sql
DROP COLUMN `current_page`  -- varchar(255) DEFAULT NULL
-- ⚠️ All data in this column will be LOST
```

**Columns Modified:**
```sql
-- Change 1: Reduce session_id length
MODIFY COLUMN `session_id` 
  FROM: varchar(255) NOT NULL
  TO:   varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL
  
-- Change 2: Allow NULL for ip_address
MODIFY COLUMN `ip_address`
  FROM: varchar(45) NOT NULL
  TO:   varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL
  
-- Change 3: Update collation for user_agent
MODIFY COLUMN `user_agent`
  FROM: text
  TO:   text COLLATE utf8mb4_unicode_ci
```

## 🔍 Impact Assessment

### Data Loss Risk

**HIGH RISK:**
- ❌ `online_users.current_page` - All data in this column **WILL BE DELETED**

**MEDIUM RISK:**
- ⚠️ `online_users.session_id` - If any session IDs are > 128 characters, they will be **TRUNCATED**

**LOW RISK:**
- ✅ `online_users.ip_address` - Changing from NOT NULL to NULL is safe
- ✅ `online_users.user_agent` - Collation change is safe

### Application Impact

**Questions to Ask Before Running:**

1. **Does your application use the `current_page` column?**
   - Check your PHP/application code for references to this column
   - Search for: `current_page`, `currentPage`, `getCurrentPage()`
   
2. **Are session IDs longer than 128 characters?**
   - Check your session configuration
   - MySQL default session IDs are typically 32-64 characters (safe)
   
3. **Does your app require IP addresses to always be present?**
   - Making `ip_address` nullable might break validation
   - Update application code to handle NULL values

## ⚡ Migration Scripts

### For Command-Line (SSH/Terminal)

**File:** `migration_identical_schema.sql`

```bash
# 1. Backup first!
mysqldump -u username -p tradevcrypto > backup_before_identical_$(date +%Y%m%d).sql

# 2. Run migration
mysql -u username -p tradevcrypto < migration_identical_schema.sql

# 3. Verify
mysql -u username -p tradevcrypto -e "DESCRIBE online_users;"
```

### For phpMyAdmin (Web Interface)

**File:** `migration_phpmyadmin_identical.sql`

1. **Backup** - Export database (Export → SQL → Go)
2. **Select** - Click 'tradevcrypto' in left sidebar
3. **SQL Tab** - Click 'SQL' tab
4. **Paste** - Copy entire script content
5. **Execute** - Click 'Go' button

## 🛡️ Safety Checklist

Before running this migration:

- [ ] **Backup created** - Full database export saved
- [ ] **Tested in development** - Run on dev/staging first
- [ ] **Code reviewed** - Check for `current_page` usage
- [ ] **Session IDs checked** - Verified they're <= 128 chars
- [ ] **Application updated** - Code handles nullable `ip_address`
- [ ] **Team notified** - Everyone knows about the change
- [ ] **Rollback plan ready** - Know how to restore from backup
- [ ] **Low traffic time** - Scheduled during off-peak hours

## 🔄 Rollback Procedure

If something goes wrong:

### Option 1: Restore from Backup (Recommended)

```bash
# Drop the database
mysql -u username -p -e "DROP DATABASE tradevcrypto;"

# Recreate it
mysql -u username -p -e "CREATE DATABASE tradevcrypto;"

# Restore from backup
mysql -u username -p tradevcrypto < backup_before_identical_YYYYMMDD.sql
```

### Option 2: Manually Re-add current_page Column

```sql
-- This restores the column structure but NOT the data
ALTER TABLE `online_users` 
ADD COLUMN `current_page` varchar(255) DEFAULT NULL;
```

**Note:** Data in `current_page` cannot be recovered once dropped!

## 📊 Verification Steps

After migration, verify success:

### 1. Check Tables Created

```sql
SELECT TABLE_NAME 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'tradevcrypto' 
AND TABLE_NAME IN ('email_templates_backup', 'email_templates_backup1', 'user_notifications');
```

Expected: 3 rows

### 2. Check online_users Structure

```sql
DESCRIBE online_users;
```

Expected columns:
- `id`
- `user_id`
- `session_id` (varchar 128)
- `last_activity`
- `ip_address` (NULL allowed)
- `user_agent`

**NOT expected:** `current_page` ❌

### 3. Check New Columns

```sql
SHOW COLUMNS FROM withdrawals WHERE Field IN ('admin_id', 'processed_at', 'processed_by');
```

Expected: 3 rows

### 4. Verify Data Integrity

```sql
-- Count records (should match pre-migration)
SELECT COUNT(*) FROM online_users;
SELECT COUNT(*) FROM withdrawals;
SELECT COUNT(*) FROM deposits;
```

## 🎯 Post-Migration Tasks

### Immediate (Within 1 hour)

1. **Test application** - Verify all features work
2. **Check logs** - Look for errors related to `current_page`
3. **Monitor performance** - Ensure no slowdowns
4. **Verify user sessions** - Check users can log in/out

### Short-term (Within 1 day)

1. **Update application code** - Remove `current_page` references
2. **Deploy code changes** - Push updated code to production
3. **Monitor user reports** - Watch for issues
4. **Optimize queries** - Update any queries using dropped column

### Long-term (Within 1 week)

1. **Update documentation** - Reflect schema changes
2. **Update backups** - Ensure new schema is backed up
3. **Train team** - Inform about structural changes
4. **Archive old backups** - Keep pre-migration backup for 30 days

## 💡 Recommendations

### When to Use This Migration

✅ **Use this migration if:**
- You need 100% identical schemas for replication
- You're consolidating databases
- You don't use the `current_page` column
- You want exact parity with kryptox

❌ **Don't use this migration if:**
- You actively use the `current_page` data
- You're uncertain about the impact
- You haven't tested in development
- You need to keep all existing columns

### Alternative Approach

If you're unsure, use the **safe migration** instead:
- File: `migration_phpmyadmin.sql` (keeps all columns)
- Result: Functionally equivalent (but not identical)
- Risk: Zero data loss

## 📞 Troubleshooting

### Error: "Can't DROP COLUMN 'current_page'"

**Possible Causes:**
1. Column already dropped in previous run (safe - ignore)
2. Column doesn't exist (safe - ignore)
3. Foreign key constraint blocking drop (investigate)

**Solution:**
- Check if column exists: `SHOW COLUMNS FROM online_users LIKE 'current_page';`
- If it doesn't exist, the error is harmless

### Error: "Data too long for column 'session_id'"

**Cause:** Existing session IDs > 128 characters

**Solution:**
```sql
-- Check for long session IDs
SELECT id, user_id, LENGTH(session_id) as len 
FROM online_users 
WHERE LENGTH(session_id) > 128;

-- Option 1: Delete these sessions (users will need to re-login)
DELETE FROM online_users WHERE LENGTH(session_id) > 128;

-- Option 2: Truncate them (risky - may cause issues)
UPDATE online_users SET session_id = LEFT(session_id, 128) WHERE LENGTH(session_id) > 128;
```

### Application Errors After Migration

**Symptom:** PHP errors about undefined column 'current_page'

**Solution:**
```php
// Update your code - Example fixes:

// OLD CODE (will fail):
$page = $row['current_page'];

// NEW CODE (option 1 - remove reference):
// Delete the line

// NEW CODE (option 2 - use alternative):
$page = $_SERVER['REQUEST_URI'] ?? '/';
```

## 📈 Success Metrics

Migration is successful when:

- ✅ All new tables exist
- ✅ All new columns exist
- ✅ `current_page` column is gone
- ✅ All other data is intact
- ✅ Application works without errors
- ✅ Users can log in and use the system
- ✅ No data corruption reported

## 🔐 Security Notes

After migration:

1. **Update security scans** - Include new tables
2. **Review permissions** - Ensure proper access controls
3. **Audit trail** - New admin_id columns enable better auditing
4. **Notifications** - New user_notifications table for security alerts

---

**Last Updated:** February 11, 2026  
**Migration Type:** IDENTICAL (Destructive)  
**Risk Level:** MEDIUM (Data loss in specific column)  
**Recommended:** Test in development first!
