-- Migration: Add Wallet Verification System
-- Description: Adds columns for crypto wallet verification via satoshi test deposits
-- Date: 2026-02-16

-- Add verification columns to user_payment_methods table
ALTER TABLE `user_payment_methods`
ADD COLUMN `verification_status` ENUM('pending', 'verifying', 'verified', 'failed') DEFAULT 'pending' AFTER `status`,
ADD COLUMN `verification_amount` DECIMAL(20,10) DEFAULT NULL COMMENT 'Test deposit amount in smallest unit' AFTER `verification_status`,
ADD COLUMN `verification_address` VARCHAR(255) DEFAULT NULL COMMENT 'Platform wallet address for test deposit' AFTER `verification_amount`,
ADD COLUMN `verification_txid` VARCHAR(255) DEFAULT NULL COMMENT 'User transaction hash' AFTER `verification_address`,
ADD COLUMN `verification_requested_at` TIMESTAMP NULL DEFAULT NULL AFTER `verification_txid`,
ADD COLUMN `verified_by` INT DEFAULT NULL COMMENT 'Admin ID who verified' AFTER `verification_requested_at`,
ADD COLUMN `verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `verified_by`,
ADD COLUMN `verification_notes` TEXT DEFAULT NULL COMMENT 'Admin notes' AFTER `verified_at`;

-- Add indexes for performance
ALTER TABLE `user_payment_methods`
ADD INDEX `idx_verification_status` (`verification_status`),
ADD INDEX `idx_verified_by` (`verified_by`);

-- Update existing crypto payment methods to pending verification status
UPDATE `user_payment_methods`
SET `verification_status` = 'pending'
WHERE `type` = 'crypto' AND `verification_status` IS NULL;

-- Add foreign key for verified_by (links to admins table if it exists)
-- ALTER TABLE `user_payment_methods`
-- ADD CONSTRAINT `fk_verified_by_admin`
-- FOREIGN KEY (`verified_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

-- Migration complete
SELECT 'Wallet verification system migration completed successfully' AS status;
