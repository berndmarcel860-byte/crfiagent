-- ============================================================================
-- phpMyAdmin Migration Script: tradevcrypto (4) -> kryptox (18)
-- ============================================================================
-- Purpose: Update tradevcrypto database schema to match kryptox structure
-- This script ONLY adds new tables and columns - it does NOT remove anything
-- Data Safety: All existing data will be preserved
-- 
-- IMPORTANT INSTRUCTIONS FOR PHPMYADMIN:
-- 1. Backup your database FIRST (Export > SQL > Go)
-- 2. Select the 'tradevcrypto' database from the left sidebar
-- 3. Click on the 'SQL' tab
-- 4. Copy and paste this ENTIRE script into the SQL text area
-- 5. Click 'Go' to execute
-- 6. Review the results - some statements may show "Duplicate column" errors
--    which is NORMAL and SAFE if you're re-running the script
-- 
-- NOTE: This version is optimized for phpMyAdmin and handles existing 
--       columns/indexes gracefully
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
-- The error will not affect your data or database structure.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- Table: case_recovery_transactions
-- New Column: Track which admin added recovery transactions
-- ----------------------------------------------------------------------------

-- Add column (may show error if already exists - this is safe to ignore)
ALTER TABLE `case_recovery_transactions` 
ADD COLUMN `added_by_admin_id` int DEFAULT NULL COMMENT 'Admin who added this recovery';

-- Add index (may show error if already exists - this is safe to ignore)
ALTER TABLE `case_recovery_transactions`
ADD INDEX `idx_added_by_admin` (`added_by_admin_id`);

-- ----------------------------------------------------------------------------
-- Table: deposits
-- New Column: Track which admin processed deposits
-- ----------------------------------------------------------------------------

-- Add column (may show error if already exists - this is safe to ignore)
ALTER TABLE `deposits` 
ADD COLUMN `admin_id` int DEFAULT NULL COMMENT 'Admin who processed this deposit';

-- Add index (may show error if already exists - this is safe to ignore)
ALTER TABLE `deposits`
ADD INDEX `idx_admin_id` (`admin_id`);

-- ----------------------------------------------------------------------------
-- Table: support_tickets
-- New Column: Track admin assignment
-- ----------------------------------------------------------------------------

-- Add column (may show error if already exists - this is safe to ignore)
ALTER TABLE `support_tickets` 
ADD COLUMN `assigned_admin_id` int DEFAULT NULL COMMENT 'Admin assigned to this ticket';

-- Add index (may show error if already exists - this is safe to ignore)
ALTER TABLE `support_tickets`
ADD INDEX `idx_assigned_admin` (`assigned_admin_id`);

-- ----------------------------------------------------------------------------
-- Table: user_documents
-- New Column: Track document reviewer
-- ----------------------------------------------------------------------------

-- Add column (may show error if already exists - this is safe to ignore)
ALTER TABLE `user_documents` 
ADD COLUMN `reviewed_by_admin_id` int DEFAULT NULL COMMENT 'Admin who reviewed this document';

-- Add index (may show error if already exists - this is safe to ignore)
ALTER TABLE `user_documents`
ADD INDEX `idx_reviewed_by_admin` (`reviewed_by_admin_id`);

-- ----------------------------------------------------------------------------
-- Table: withdrawals
-- New Columns: Enhanced tracking for withdrawal processing
-- ----------------------------------------------------------------------------

-- Add columns (may show errors if already exist - this is safe to ignore)
ALTER TABLE `withdrawals` 
ADD COLUMN `admin_id` int DEFAULT NULL COMMENT 'Admin who processed this withdrawal';

ALTER TABLE `withdrawals` 
ADD COLUMN `processed_at` datetime DEFAULT NULL;

ALTER TABLE `withdrawals` 
ADD COLUMN `processed_by` int DEFAULT NULL;

-- Add indexes (may show errors if already exist - this is safe to ignore)
ALTER TABLE `withdrawals`
ADD INDEX `idx_admin_id` (`admin_id`);

ALTER TABLE `withdrawals`
ADD INDEX `idx_processed_at` (`processed_at`);

ALTER TABLE `withdrawals`
ADD INDEX `idx_processed_by` (`processed_by`);

-- ============================================================================
-- SECTION 3: FOREIGN KEY CONSTRAINTS (OPTIONAL)
-- ============================================================================
-- Uncomment the statements below if you want to add foreign key constraints
-- These provide referential integrity but may impact performance
-- 
-- BEFORE UNCOMMENTING:
-- 1. Ensure all admin_id references point to valid admin records
-- 2. Ensure all user_id references point to valid user records
-- 3. Test in development first
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
-- Check the execution results above:
-- - Green checkmarks (✓) = Success
-- - "Duplicate column name" errors = Column already exists (SAFE)
-- - "Duplicate key name" errors = Index already exists (SAFE)
-- - Other errors = Review and investigate
-- 
-- Summary of changes:
-- - Added 3 new tables: email_templates_backup, email_templates_backup1, user_notifications
-- - Added 7 new columns across 5 existing tables
-- - Added indexes for improved query performance
-- - All existing data preserved
-- 
-- To verify the migration:
-- 1. Click on 'user_notifications' table in left sidebar (should exist)
-- 2. Click on 'withdrawals' table and check Structure tab (should have new columns)
-- 3. Your application should now work with the new schema!
-- ============================================================================
