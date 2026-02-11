-- Migration: Add company_address and fca_reference_number to system_settings table
-- Date: 2026-02-11
-- Description: Adds dynamic address and FCA reference number columns to replace hardcoded values in email templates

-- Add company_address column
ALTER TABLE `system_settings`
ADD COLUMN `company_address` TEXT DEFAULT NULL AFTER `brand_name`;

-- Add fca_reference_number column
ALTER TABLE `system_settings`
ADD COLUMN `fca_reference_number` VARCHAR(50) DEFAULT NULL AFTER `company_address`;

-- Update existing record with current hardcoded values
UPDATE `system_settings` 
SET 
    `company_address` = 'Davidson House Forbury Square, Reading, RG1 3EU, UNITED KINGDOM',
    `fca_reference_number` = '910584'
WHERE `id` = 1;

-- Show updated structure
-- SHOW COLUMNS FROM system_settings;
