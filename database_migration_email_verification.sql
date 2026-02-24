-- Database migration for email verification system
-- Add missing columns to users table for email verification feature

-- Add email_verified_at column to track when email was verified
ALTER TABLE `users` 
ADD COLUMN `email_verified_at` DATETIME NULL DEFAULT NULL 
COMMENT 'Timestamp when user email was verified' 
AFTER `is_verified`;

-- Add verification_token_expires column to handle token expiration
ALTER TABLE `users` 
ADD COLUMN `verification_token_expires` DATETIME NULL DEFAULT NULL 
COMMENT 'Expiration time for verification token (typically 1 hour from generation)'
AFTER `verification_token`;

-- Add index for verification_token for faster lookups
CREATE INDEX idx_verification_token ON `users` (`verification_token`);

-- Add index for verification_token_expires for cleanup queries
CREATE INDEX idx_verification_expires ON `users` (`verification_token_expires`);

-- Optional: Update existing verified users to set email_verified_at
UPDATE `users` 
SET `email_verified_at` = `created_at` 
WHERE `is_verified` = 1 AND `email_verified_at` IS NULL;

-- Note: After running this migration, the email verification system will work properly
-- The send_verification_email.php and verify_email.php files expect these columns
