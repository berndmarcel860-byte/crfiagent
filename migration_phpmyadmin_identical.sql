-- ============================================================================
-- phpMyAdmin IDENTICAL Schema Migration: tradevcrypto (4) -> kryptox (18)
-- ============================================================================
-- Purpose: Make tradevcrypto database IDENTICAL to kryptox structure
-- This script adds new tables/columns AND removes columns not in kryptox
-- Data Safety: All data is preserved except for dropped columns
-- 
-- IMPORTANT WARNING FOR PHPMYADMIN:
-- 1. This script DROPS the 'current_page' column from 'online_users' table
-- 2. This script MODIFIES column types in 'online_users' table  
-- 3. Data in the 'current_page' column will be PERMANENTLY LOST
-- 4. BACKUP YOUR DATABASE FIRST (Export > SQL > Go)
-- 5. Select 'tradevcrypto' database before running
-- 6. Test in development environment first
-- 
-- INSTRUCTIONS FOR PHPMYADMIN:
-- 1. BACKUP: Export your database first!
-- 2. SELECT: Click 'tradevcrypto' database in left sidebar
-- 3. SQL TAB: Click 'SQL' tab at the top
-- 4. PASTE: Copy this entire script into the SQL box
-- 5. EXECUTE: Click 'Go' button
-- 6. REVIEW: Check results - some "duplicate" errors are normal
-- ============================================================================

-- ============================================================================
-- SECTION 1: NEW TABLES
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: email_templates_backup
-- Purpose: Backup of email templates
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_templates_backup` (
  `id` int NOT NULL DEFAULT '0',
  `template_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `variables` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'JSON array of available variables',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: email_templates_backup1
-- Purpose: Secondary backup of email templates
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `email_templates_backup1` (
  `id` int NOT NULL DEFAULT '0',
  `template_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `variables` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'JSON array of available variables',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: user_notifications
-- Purpose: Store user notifications for the system
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('info','success','warning','error') COLLATE utf8mb4_unicode_ci DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `related_entity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SECTION 2: ALTER EXISTING TABLES - ADD NEW COLUMNS
-- ============================================================================
-- NOTE: You may see "Duplicate column name" errors if columns already exist.
-- This is NORMAL and SAFE - it means the column was added in a previous run.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: case_recovery_transactions
-- ----------------------------------------------------------------------------
ALTER TABLE `case_recovery_transactions` 
ADD COLUMN `added_by_admin_id` int DEFAULT NULL COMMENT 'Admin who added this recovery';

ALTER TABLE `case_recovery_transactions`
ADD INDEX `idx_added_by_admin` (`added_by_admin_id`);

-- ----------------------------------------------------------------------------
-- Table: deposits
-- ----------------------------------------------------------------------------
ALTER TABLE `deposits` 
ADD COLUMN `admin_id` int DEFAULT NULL COMMENT 'Admin who processed this deposit';

ALTER TABLE `deposits`
ADD INDEX `idx_admin_id` (`admin_id`);

-- ----------------------------------------------------------------------------
-- Table: support_tickets
-- ----------------------------------------------------------------------------
ALTER TABLE `support_tickets` 
ADD COLUMN `assigned_admin_id` int DEFAULT NULL COMMENT 'Admin assigned to this ticket';

ALTER TABLE `support_tickets`
ADD INDEX `idx_assigned_admin` (`assigned_admin_id`);

-- ----------------------------------------------------------------------------
-- Table: user_documents
-- ----------------------------------------------------------------------------
ALTER TABLE `user_documents` 
ADD COLUMN `reviewed_by_admin_id` int DEFAULT NULL COMMENT 'Admin who reviewed this document';

ALTER TABLE `user_documents`
ADD INDEX `idx_reviewed_by_admin` (`reviewed_by_admin_id`);

-- ----------------------------------------------------------------------------
-- Table: withdrawals
-- ----------------------------------------------------------------------------
ALTER TABLE `withdrawals` 
ADD COLUMN `admin_id` int DEFAULT NULL COMMENT 'Admin who processed this withdrawal';

ALTER TABLE `withdrawals` 
ADD COLUMN `processed_at` datetime DEFAULT NULL;

ALTER TABLE `withdrawals` 
ADD COLUMN `processed_by` int DEFAULT NULL;

ALTER TABLE `withdrawals`
ADD INDEX `idx_admin_id` (`admin_id`);

ALTER TABLE `withdrawals`
ADD INDEX `idx_processed_at` (`processed_at`);

ALTER TABLE `withdrawals`
ADD INDEX `idx_processed_by` (`processed_by`);

-- ============================================================================
-- SECTION 3: MAKE SCHEMA IDENTICAL - DESTRUCTIVE CHANGES
-- ============================================================================
-- WARNING: This section REMOVES columns and MODIFIES data types
-- to make the schema 100% identical to kryptox
-- 
-- BEFORE PROCEEDING:
-- - Ensure you have a backup!
-- - Verify you don't need the 'current_page' column data
-- - Test in development first
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: online_users - Make IDENTICAL to kryptox schema
-- ----------------------------------------------------------------------------

-- ⚠️ WARNING: DROP COLUMN - This DELETES all data in 'current_page' column
-- If you see "Can't DROP 'current_page'" error, the column is already gone (safe)
ALTER TABLE `online_users` 
DROP COLUMN `current_page`;

-- Modify session_id: varchar(255) → varchar(128)
-- Safe as long as session IDs are <= 128 characters
ALTER TABLE `online_users` 
MODIFY COLUMN `session_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL;

-- Modify ip_address: NOT NULL → NULL (allow empty values)
ALTER TABLE `online_users` 
MODIFY COLUMN `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL;

-- Update collation for user_agent
ALTER TABLE `online_users` 
MODIFY COLUMN `user_agent` text COLLATE utf8mb4_unicode_ci;

-- ============================================================================
-- SECTION 4: FOREIGN KEY CONSTRAINTS (OPTIONAL)
-- ============================================================================
-- Uncomment if you want strict referential integrity
-- WARNING: May cause errors if data doesn't match constraints
-- ============================================================================

-- ALTER TABLE `case_recovery_transactions`
-- ADD CONSTRAINT `fk_case_recovery_admin` 
-- FOREIGN KEY (`added_by_admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL;

-- ALTER TABLE `deposits`
-- ADD CONSTRAINT `fk_deposits_admin` 
-- FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL;

-- ALTER TABLE `support_tickets`
-- ADD CONSTRAINT `fk_tickets_assigned_admin` 
-- FOREIGN KEY (`assigned_admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL;

-- ALTER TABLE `user_documents`
-- ADD CONSTRAINT `fk_documents_reviewer_admin` 
-- FOREIGN KEY (`reviewed_by_admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL;

-- ALTER TABLE `user_notifications`
-- ADD CONSTRAINT `fk_notifications_user` 
-- FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;

-- ALTER TABLE `withdrawals`
-- ADD CONSTRAINT `fk_withdrawals_admin` 
-- FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL;

-- ALTER TABLE `withdrawals`
-- ADD CONSTRAINT `fk_withdrawals_processed_by` 
-- FOREIGN KEY (`processed_by`) REFERENCES `admins`(`id`) ON DELETE SET NULL;

-- ============================================================================
-- MIGRATION COMPLETE
-- ============================================================================
-- 
-- ✅ WHAT WAS DONE:
-- - Added 3 new tables
-- - Added 7 new columns for admin tracking
-- - Added performance indexes
-- - ❌ DROPPED: online_users.current_page column (data lost)
-- - Modified 3 columns in online_users table
--
-- 🎯 RESULT: 
-- Your tradevcrypto database structure is now 100% IDENTICAL to kryptox!
--
-- 📊 VERIFICATION:
-- 1. Check left sidebar - new tables should appear
-- 2. Click 'online_users' → Structure tab
-- 3. Verify 'current_page' column is GONE
-- 4. All other data should be intact
--
-- ⚠️ IMPORTANT NOTES:
-- - "Can't DROP 'current_page'" error = Column already removed (safe)
-- - "Duplicate column" errors = Columns already added (safe)
-- - "Duplicate key" errors = Indexes already added (safe)
-- - Any other errors = Review and investigate before continuing
--
-- ============================================================================
