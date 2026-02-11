-- ============================================================================
-- IDENTICAL Schema Migration: tradevcrypto (4) -> kryptox (18)
-- ============================================================================
-- Purpose: Make tradevcrypto database IDENTICAL to kryptox structure
-- This script adds new tables/columns AND removes columns not in kryptox
-- Data Safety: All data is preserved where possible
-- 
-- IMPORTANT WARNING:
-- 1. This script DROPS the 'current_page' column from 'online_users' table
-- 2. This script MODIFIES column types in 'online_users' table
-- 3. BACKUP YOUR DATABASE BEFORE RUNNING!
-- 4. Test in development environment first
-- 5. Data in dropped columns will be LOST
-- ============================================================================

USE `tradevcrypto`;

-- ============================================================================
-- SECTION 1: NEW TABLES (Same as before)
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
-- NOTE: If you see "Duplicate column name" or "Duplicate key name" errors,
--       this is NORMAL if running the script multiple times. These errors
--       indicate the column/index already exists and are safe to ignore.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: case_recovery_transactions
-- New Column: Track which admin added recovery transactions
-- ----------------------------------------------------------------------------
ALTER TABLE `case_recovery_transactions` 
ADD COLUMN `added_by_admin_id` int DEFAULT NULL COMMENT 'Admin who added this recovery';

-- Add index for performance
ALTER TABLE `case_recovery_transactions`
ADD INDEX `idx_added_by_admin` (`added_by_admin_id`);

-- ----------------------------------------------------------------------------
-- Table: deposits
-- New Column: Track which admin processed deposits
-- ----------------------------------------------------------------------------
ALTER TABLE `deposits` 
ADD COLUMN `admin_id` int DEFAULT NULL COMMENT 'Admin who processed this deposit';

-- Add index for performance
ALTER TABLE `deposits`
ADD INDEX `idx_admin_id` (`admin_id`);

-- ----------------------------------------------------------------------------
-- Table: support_tickets
-- New Column: Track admin assignment
-- ----------------------------------------------------------------------------
ALTER TABLE `support_tickets` 
ADD COLUMN `assigned_admin_id` int DEFAULT NULL COMMENT 'Admin assigned to this ticket';

-- Add index for performance
ALTER TABLE `support_tickets`
ADD INDEX `idx_assigned_admin` (`assigned_admin_id`);

-- ----------------------------------------------------------------------------
-- Table: user_documents
-- New Column: Track document reviewer
-- ----------------------------------------------------------------------------
ALTER TABLE `user_documents` 
ADD COLUMN `reviewed_by_admin_id` int DEFAULT NULL COMMENT 'Admin who reviewed this document';

-- Add index for performance
ALTER TABLE `user_documents`
ADD INDEX `idx_reviewed_by_admin` (`reviewed_by_admin_id`);

-- ----------------------------------------------------------------------------
-- Table: withdrawals
-- New Columns: Enhanced tracking for withdrawal processing
-- ----------------------------------------------------------------------------
ALTER TABLE `withdrawals` 
ADD COLUMN `admin_id` int DEFAULT NULL COMMENT 'Admin who processed this withdrawal';

ALTER TABLE `withdrawals` 
ADD COLUMN `processed_at` datetime DEFAULT NULL;

ALTER TABLE `withdrawals` 
ADD COLUMN `processed_by` int DEFAULT NULL;

-- Add indexes for performance
ALTER TABLE `withdrawals`
ADD INDEX `idx_admin_id` (`admin_id`);

ALTER TABLE `withdrawals`
ADD INDEX `idx_processed_at` (`processed_at`);

ALTER TABLE `withdrawals`
ADD INDEX `idx_processed_by` (`processed_by`);

-- ============================================================================
-- SECTION 3: MODIFY EXISTING TABLES TO MATCH KRYPTOX EXACTLY
-- ============================================================================
-- WARNING: This section makes destructive changes to achieve identical schemas
-- The 'current_page' column data will be LOST
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: online_users
-- Changes to match kryptox schema EXACTLY
-- ----------------------------------------------------------------------------

-- STEP 1: Drop the current_page column (not in kryptox schema)
-- WARNING: This will PERMANENTLY DELETE the data in this column
ALTER TABLE `online_users` 
DROP COLUMN IF EXISTS `current_page`;

-- STEP 2: Modify session_id column length from varchar(255) to varchar(128)
-- This is safe as long as session IDs are <= 128 characters
ALTER TABLE `online_users` 
MODIFY COLUMN `session_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL;

-- STEP 3: Modify ip_address to allow NULL values (matches kryptox)
ALTER TABLE `online_users` 
MODIFY COLUMN `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL;

-- STEP 4: Update collation for user_agent to match kryptox
ALTER TABLE `online_users` 
MODIFY COLUMN `user_agent` text COLLATE utf8mb4_unicode_ci;

-- ============================================================================
-- SECTION 4: FOREIGN KEY CONSTRAINTS (Optional - Uncomment if needed)
-- ============================================================================
-- Note: Foreign keys provide referential integrity but may impact performance
-- Only enable if your application requires strict database-level constraints

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
-- Summary of changes:
-- - Added 3 new tables: email_templates_backup, email_templates_backup1, user_notifications
-- - Added 7 new columns across 5 tables for admin tracking
-- - Added indexes for improved query performance
-- - DROPPED 1 column: online_users.current_page (data LOST)
-- - MODIFIED 3 columns in online_users to match kryptox exactly
-- 
-- Result: tradevcrypto database structure is now IDENTICAL to kryptox
-- ============================================================================
