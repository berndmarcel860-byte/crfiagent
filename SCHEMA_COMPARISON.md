# Schema Comparison: tradevcrypto vs kryptox

## Summary

This document provides a detailed comparison between the old `tradevcrypto (4).sql` schema and the new `kryptox (18).sql` schema.

## Overview Statistics

| Metric | tradevcrypto (4) | kryptox (18) | Change |
|--------|------------------|--------------|--------|
| Total Tables | 41 | 44 | +3 |
| Total Size | ~1.3 MB | ~199 KB | Schema only |

## New Tables (3)

### 1. email_templates_backup
**Purpose**: Backup storage for email templates

| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary identifier |
| template_key | varchar(100) | Template identifier key |
| subject | varchar(255) | Email subject line |
| content | text | Email body content |
| variables | text | JSON array of template variables |
| created_at | datetime | Creation timestamp |
| updated_at | datetime | Last update timestamp |

### 2. email_templates_backup1
**Purpose**: Secondary backup for email templates (same structure as email_templates_backup)

| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary identifier |
| template_key | varchar(100) | Template identifier key |
| subject | varchar(255) | Email subject line |
| content | text | Email body content |
| variables | text | JSON array of template variables |
| created_at | datetime | Creation timestamp |
| updated_at | datetime | Last update timestamp |

### 3. user_notifications
**Purpose**: User notification system for in-app notifications

| Column | Type | Description |
|--------|------|-------------|
| id | int | Primary identifier (AUTO_INCREMENT) |
| user_id | int | User receiving the notification |
| title | varchar(255) | Notification title |
| message | text | Notification message content |
| type | enum | Notification type: info, success, warning, error |
| is_read | tinyint(1) | Read status flag |
| related_entity | varchar(50) | Related entity type (optional) |
| related_id | varchar(100) | Related entity ID (optional) |
| created_at | datetime | Creation timestamp |

**Indexes**:
- PRIMARY KEY (`id`)
- KEY `idx_user_id` (`user_id`)
- KEY `idx_is_read` (`is_read`)
- KEY `idx_created_at` (`created_at`)

## Modified Tables (5)

### 1. case_recovery_transactions
**New Columns**:
- `added_by_admin_id` (int, nullable) - Tracks which admin added the recovery transaction

**Purpose**: Audit trail for recovery transaction management

### 2. deposits  
**New Columns**:
- `admin_id` (int, nullable) - Tracks which admin processed the deposit

**Purpose**: Admin accountability for deposit processing

### 3. support_tickets
**New Columns**:
- `assigned_admin_id` (int, nullable) - Tracks which admin is assigned to the ticket

**Purpose**: Ticket assignment and workload management

### 4. user_documents
**New Columns**:
- `reviewed_by_admin_id` (int, nullable) - Tracks which admin reviewed the document

**Purpose**: Document review audit trail

### 5. withdrawals
**New Columns**:
- `admin_id` (int, nullable) - Tracks which admin processed the withdrawal
- `processed_at` (datetime, nullable) - Timestamp of processing
- `processed_by` (int, nullable) - Admin who processed (note: redundant with admin_id)

**Purpose**: Enhanced audit trail for withdrawal processing

## Tables with Removed Columns (1)

### online_users
**Removed Column**:
- `current_page` - No longer tracked in new schema

**Note**: Migration script does NOT remove this column to preserve data safety.

## Key Improvements in kryptox Schema

### 1. Enhanced Admin Tracking
- Multiple tables now track which admin performed actions
- Better accountability and audit capabilities
- Supports admin performance analytics

### 2. User Notification System
- New dedicated table for user notifications
- Support for different notification types
- Read/unread tracking
- Related entity linking for contextual notifications

### 3. Email Template Management
- Backup tables for email templates
- Protects against accidental template changes
- Enables template version control

### 4. Better Indexing
- Additional indexes on new columns for performance
- Optimized for common query patterns

## Migration Strategy

### Safe Approach
The migration follows these principles:
1. **Only Add, Never Remove**: No tables or columns are dropped
2. **Nullable Columns**: All new columns allow NULL for backward compatibility  
3. **IF NOT EXISTS**: Safe to run multiple times
4. **Preserve Data**: All existing data remains intact

### Backward Compatibility
- Applications using old schema will continue to work
- New columns being NULL won't break existing queries
- No changes to existing column types or constraints

### Forward Compatibility  
- New features can gradually adopt new columns
- Notification system can be enabled incrementally
- Admin tracking can be phased in over time

## Use Cases Enabled by New Schema

### 1. Admin Activity Monitoring
```sql
-- See which admin has processed the most withdrawals
SELECT admin_id, COUNT(*) as withdrawal_count
FROM withdrawals 
WHERE admin_id IS NOT NULL
GROUP BY admin_id
ORDER BY withdrawal_count DESC;
```

### 2. User Notification Management
```sql
-- Get unread notifications for a user
SELECT * FROM user_notifications
WHERE user_id = ? AND is_read = 0
ORDER BY created_at DESC;
```

### 3. Support Ticket Assignment
```sql
-- See admin workload
SELECT assigned_admin_id, COUNT(*) as ticket_count
FROM support_tickets
WHERE status = 'open'
GROUP BY assigned_admin_id;
```

### 4. Document Review Tracking
```sql
-- Find documents pending review
SELECT * FROM user_documents
WHERE reviewed_by_admin_id IS NULL
ORDER BY created_at ASC;
```

## Recommendations

### Immediate Actions
1. ✅ Run migration in development environment
2. ✅ Test all application features
3. ✅ Update application code to use new columns
4. ✅ Create database backup before production migration

### Short-term (1-2 weeks)
1. Implement notification system UI
2. Add admin assignment to support tickets
3. Update admin dashboard to show tracked activities

### Long-term (1-3 months)
1. Build admin performance analytics
2. Implement email template versioning
3. Create audit reports using new tracking columns
4. Consider adding foreign key constraints

## Technical Notes

### Character Sets
- Both schemas use `utf8mb4` encoding
- Collation: `utf8mb4_unicode_ci` and `utf8mb4_0900_ai_ci`
- Full Unicode support including emojis

### Storage Engines
- All tables use InnoDB
- Supports transactions and foreign keys
- Better crash recovery

### MySQL Version
- Designed for MySQL 8.0+
- Uses newer features like `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`

---

**Generated**: February 11, 2026  
**Schema Versions**: tradevcrypto (4) → kryptox (18)
