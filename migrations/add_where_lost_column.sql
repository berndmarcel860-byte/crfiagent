-- Migration: Add where_lost column to user_onboarding table
-- Date: 2026-03-02
-- Purpose: Store platform/exchange where user lost funds
-- Required for: German onboarding Step 1 enhancement

ALTER TABLE user_onboarding 
ADD COLUMN IF NOT EXISTS where_lost VARCHAR(255) DEFAULT NULL 
COMMENT 'Platform or exchange where funds were lost (e.g., Binance, Coinbase)'
AFTER year_lost;

-- Verify column was added
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'user_onboarding' 
AND COLUMN_NAME = 'where_lost';
