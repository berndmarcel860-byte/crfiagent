-- Migration: Enhance user_payment_methods table for fiat and crypto support
-- Date: 2026-02-16
-- Purpose: Add fields to support both fiat payment methods and cryptocurrency wallets

-- Add new columns to user_payment_methods table
ALTER TABLE `user_payment_methods`
ADD COLUMN `type` ENUM('fiat', 'crypto') NOT NULL DEFAULT 'fiat' AFTER `payment_method`,
ADD COLUMN `label` VARCHAR(100) DEFAULT NULL COMMENT 'User-friendly label for the payment method',

-- Fiat payment details
ADD COLUMN `account_holder` VARCHAR(255) DEFAULT NULL COMMENT 'Name of account holder',
ADD COLUMN `bank_name` VARCHAR(255) DEFAULT NULL COMMENT 'Name of bank',
ADD COLUMN `iban` VARCHAR(34) DEFAULT NULL COMMENT 'International Bank Account Number',
ADD COLUMN `bic` VARCHAR(11) DEFAULT NULL COMMENT 'Bank Identifier Code / SWIFT',
ADD COLUMN `account_number` VARCHAR(50) DEFAULT NULL COMMENT 'Bank account number (non-IBAN)',
ADD COLUMN `routing_number` VARCHAR(20) DEFAULT NULL COMMENT 'Routing number (US)',
ADD COLUMN `sort_code` VARCHAR(10) DEFAULT NULL COMMENT 'Sort code (UK)',

-- Crypto wallet details
ADD COLUMN `wallet_address` VARCHAR(255) DEFAULT NULL COMMENT 'Cryptocurrency wallet address',
ADD COLUMN `cryptocurrency` VARCHAR(20) DEFAULT NULL COMMENT 'Type of cryptocurrency (BTC, ETH, USDT, etc.)',
ADD COLUMN `network` VARCHAR(50) DEFAULT NULL COMMENT 'Blockchain network (ERC-20, TRC-20, BEP-20, etc.)',

-- Status and metadata
ADD COLUMN `is_verified` TINYINT(1) DEFAULT 0 COMMENT 'Whether payment method has been verified',
ADD COLUMN `verification_date` TIMESTAMP NULL DEFAULT NULL COMMENT 'Date when verification was completed',
ADD COLUMN `last_used_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Last time this method was used',
ADD COLUMN `status` ENUM('active', 'pending', 'suspended') DEFAULT 'active' COMMENT 'Payment method status',
ADD COLUMN `notes` TEXT DEFAULT NULL COMMENT 'Additional notes or instructions',
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp';

-- Add indexes for better performance
ALTER TABLE `user_payment_methods`
ADD INDEX `idx_user_type` (`user_id`, `type`),
ADD INDEX `idx_user_default` (`user_id`, `is_default`),
ADD INDEX `idx_cryptocurrency` (`cryptocurrency`),
ADD INDEX `idx_status` (`status`);

-- Update existing records to have proper type based on payment_method value
UPDATE `user_payment_methods` 
SET `type` = CASE 
    WHEN `payment_method` IN ('Bitcoin', 'Ethereum', 'USDT', 'USDC', 'BTC', 'ETH', 'Crypto', 'Cryptocurrency') THEN 'crypto'
    ELSE 'fiat'
END,
`label` = `payment_method`,
`status` = 'active'
WHERE `type` IS NULL OR `type` = 'fiat';

-- Add comment to table
ALTER TABLE `user_payment_methods` 
COMMENT = 'Stores user payment methods for both fiat and cryptocurrency transactions';
