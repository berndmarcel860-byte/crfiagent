# Database Migration Guide: tradevcrypto → kryptox

This guide explains how to safely migrate your `tradevcrypto` database schema to match the new `kryptox` schema design.

## 📋 Overview

The migration adds new features to your database while **preserving all existing data**:
- ✅ 3 new tables
- ✅ 7 new columns across 5 existing tables  
- ✅ Performance indexes
- ✅ Zero data loss

## 🎯 What Changes?

### New Tables
1. **`email_templates_backup`** - Backup storage for email templates
2. **`email_templates_backup1`** - Secondary backup for email templates
3. **`user_notifications`** - User notification system

### Enhanced Tables (New Columns)
1. **`case_recovery_transactions`**
   - `added_by_admin_id` - Track which admin added recovery transactions

2. **`deposits`**
   - `admin_id` - Track which admin processed the deposit

3. **`support_tickets`**
   - `assigned_admin_id` - Track admin ticket assignments

4. **`user_documents`**
   - `reviewed_by_admin_id` - Track which admin reviewed documents

5. **`withdrawals`**
   - `admin_id` - Track which admin processed the withdrawal
   - `processed_at` - Timestamp when processing occurred
   - `processed_by` - Admin who processed (redundant with admin_id)

## 🔒 Safety Guarantees

- ✅ **No data deletion** - Script only adds structures, never removes
- ✅ **Idempotent** - Can be run multiple times (duplicate errors are safe to ignore)
- ✅ **No table drops** - All existing tables are preserved
- ✅ **No column drops** - All existing columns are preserved
- ✅ **Backward compatible** - New columns are nullable, won't break existing code
- ✅ **MySQL 5.7+ compatible** - Works with older MySQL versions

## 📝 Files Included

1. **`migration_tradevcrypto_to_kryptox.sql`** - The migration script
2. **`validate_migration.py`** - Validation tool to verify safety
3. **`MIGRATION_GUIDE.md`** - This guide

## 🚀 Migration Steps

### Step 1: Backup Your Database

**CRITICAL: Always backup before migration!**

```bash
# Create a complete backup
mysqldump -u your_username -p tradevcrypto > tradevcrypto_backup_$(date +%Y%m%d_%H%M%S).sql

# Verify the backup was created
ls -lh tradevcrypto_backup_*.sql
```

### Step 2: Validate the Migration (Optional but Recommended)

```bash
# Run the validation script
python3 validate_migration.py
```

This will show:
- What tables will be created
- What columns will be added
- Safety checks results
- Recommendations

### Step 3: Test in Development First

**Do NOT run directly in production!**

```bash
# 1. Restore your backup to a test database
mysql -u your_username -p -e "CREATE DATABASE tradevcrypto_test;"
mysql -u your_username -p tradevcrypto_test < tradevcrypto_backup_*.sql

# 2. Run the migration on test database
mysql -u your_username -p tradevcrypto_test < migration_tradevcrypto_to_kryptox.sql

# 3. Verify the migration worked
mysql -u your_username -p tradevcrypto_test -e "SHOW TABLES;"
mysql -u your_username -p tradevcrypto_test -e "DESCRIBE user_notifications;"

# 4. Test your application with the test database
# Update your app config to point to tradevcrypto_test
# Run your application and verify everything works
```

### Step 4: Run Migration in Production

**Only after successful testing!**

```bash
# Run the migration
mysql -u your_username -p tradevcrypto < migration_tradevcrypto_to_kryptox.sql

# Monitor for errors
# If you see any errors, STOP and review them before proceeding
```

### Step 5: Verify the Migration

```bash
# Check new tables were created
mysql -u your_username -p tradevcrypto -e "
  SELECT TABLE_NAME 
  FROM information_schema.TABLES 
  WHERE TABLE_SCHEMA = 'tradevcrypto' 
  AND TABLE_NAME IN ('email_templates_backup', 'email_templates_backup1', 'user_notifications');
"

# Check new columns were added
mysql -u your_username -p tradevcrypto -e "
  SHOW COLUMNS FROM withdrawals WHERE Field IN ('admin_id', 'processed_at', 'processed_by');
"

# Check indexes were created
mysql -u your_username -p tradevcrypto -e "
  SHOW INDEXES FROM user_notifications;
"
```

## 🔧 Advanced Options

### Foreign Key Constraints

The migration script includes commented-out foreign key constraints. These provide referential integrity but may impact performance.

To enable them, edit `migration_tradevcrypto_to_kryptox.sql` and uncomment SECTION 3.

⚠️ **Warning**: Foreign keys will prevent you from deleting admin records if they're referenced in other tables.

### Rollback (If Needed)

If you need to rollback (though this should be rare since we only add, not remove):

```bash
# Restore from backup
mysql -u your_username -p -e "DROP DATABASE tradevcrypto;"
mysql -u your_username -p -e "CREATE DATABASE tradevcrypto;"
mysql -u your_username -p tradevcrypto < tradevcrypto_backup_*.sql
```

## 📊 Migration Impact

### Downtime
- **Expected**: None (or minimal, seconds)
- **Reason**: Script only adds structures, doesn't modify existing data

### Performance
- **During Migration**: Minimal impact (acquiring metadata locks)
- **After Migration**: Slight improvement due to new indexes

### Disk Space
- **Estimated**: < 1 MB (for new tables)
- **Note**: New columns in existing tables use minimal space when NULL

## ✅ Post-Migration Checklist

- [ ] All new tables exist
- [ ] All new columns exist
- [ ] Application functions correctly
- [ ] No errors in application logs
- [ ] Database performance is acceptable
- [ ] Backup is safely stored
- [ ] Team is notified of schema changes

## 🆘 Troubleshooting

### Error: "Table already exists"
**Solution**: This is normal if re-running. Tables use `CREATE TABLE IF NOT EXISTS` so they're safely skipped if they exist.

### Error: "Duplicate column name" or "Duplicate key name"
**Solution**: This is NORMAL and SAFE if you're re-running the script. It means the column or index already exists from a previous run. You can safely ignore these errors and continue. The migration will complete successfully.

### Error: "Access denied"
**Solution**: Ensure your database user has CREATE, ALTER, and INDEX privileges.

```sql
GRANT CREATE, ALTER, INDEX ON tradevcrypto.* TO 'your_username'@'localhost';
FLUSH PRIVILEGES;
```

### Application Errors After Migration
**Solution**: 
1. Check application logs for specific errors
2. Verify database connection settings
3. Clear application cache if applicable
4. Ensure code is compatible with new schema

## 📞 Support

If you encounter issues:
1. Check the validation script output
2. Review error messages carefully
3. Ensure backup exists before attempting fixes
4. Test fixes in development environment first

## 📄 Schema Documentation

### Database: tradevcrypto (original)
- Tables: 41
- Based on: `tradevcrypto (4).sql`

### Database: kryptox (new design)
- Tables: 44 (+3 new)
- Based on: `kryptox (18).sql`
- Enhancements: Better admin tracking, notifications system

---

**Last Updated**: February 11, 2026  
**Migration Script Version**: 1.0  
**Compatibility**: MySQL 8.0+
