-- Database Migration: Add admin_notes column to kyc_verification_requests table
-- Date: 2026-03-01
-- Purpose: Fix missing admin_notes column error in admin KYC creation

-- Add admin_notes column to kyc_verification_requests table
ALTER TABLE `kyc_verification_requests` 
ADD COLUMN `admin_notes` TEXT NULL COMMENT 'Notes added by admin when creating/processing KYC' 
AFTER `address_proof`;

-- Verify the column was added (optional - for testing)
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_COMMENT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'kyc_verification_requests' AND COLUMN_NAME = 'admin_notes';
