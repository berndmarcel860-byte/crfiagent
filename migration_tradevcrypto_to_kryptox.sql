-- ============================================================================
-- Migration Script: tradevcrypto (4) -> kryptox (18)
-- ============================================================================
-- Purpose: Update tradevcrypto database schema to match kryptox structure
-- This script ONLY adds new tables and columns - it does NOT remove anything
-- Data Safety: All existing data will be preserved
-- 
-- IMPORTANT: 
-- 1. Backup your database before running this script
-- 2. Review each statement before executing
-- 3. Test in a development environment first
-- ============================================================================

USE `tradevcrypto`;

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
-- SECTION 3: FOREIGN KEY CONSTRAINTS (Optional - Uncomment if needed)
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
-- - Added new columns to 5 existing tables
-- - Added indexes for improved query performance
-- - All existing data preserved
-- ============================================================================
