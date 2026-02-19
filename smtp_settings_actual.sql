-- ========================================================================
-- SMTP Settings Table - Actual Database Structure
-- ========================================================================
-- This matches your actual database structure without smtp_ prefix
-- ========================================================================

CREATE TABLE IF NOT EXISTS `smtp_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL COMMENT 'SMTP server hostname (e.g., smtp.hostinger.com)',
  `port` int NOT NULL DEFAULT 587 COMMENT 'SMTP port (587 for TLS, 465 for SSL)',
  `encryption` enum('tls','ssl','none') NOT NULL DEFAULT 'tls' COMMENT 'Encryption type',
  `username` varchar(255) NOT NULL COMMENT 'SMTP authentication username',
  `password` varchar(255) NOT NULL COMMENT 'SMTP authentication password',
  `from_email` varchar(255) NOT NULL COMMENT 'From email address',
  `from_name` varchar(255) NOT NULL COMMENT 'From name displayed in emails',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Is this configuration active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ========================================================================
-- Your Actual Configuration (Hostinger SMTP)
-- ========================================================================
-- WARNING: This contains your actual credentials. Keep this file secure!
-- ========================================================================

INSERT INTO `smtp_settings` (
  `id`,
  `host`,
  `port`,
  `encryption`,
  `username`,
  `password`,
  `from_email`,
  `from_name`,
  `is_active`,
  `created_at`
) VALUES (
  1,
  'smtp.hostinger.com',
  587,
  'tls',
  'no-reply@cryptofinanze.de',
  'Manta77.@@?',
  'no-reply@cryptofinanze.de',
  'Crypto Finanz',
  1,
  '2025-08-02 07:12:05'
) ON DUPLICATE KEY UPDATE
  `host` = VALUES(`host`),
  `port` = VALUES(`port`),
  `encryption` = VALUES(`encryption`),
  `username` = VALUES(`username`),
  `password` = VALUES(`password`),
  `from_email` = VALUES(`from_email`),
  `from_name` = VALUES(`from_name`),
  `is_active` = VALUES(`is_active`);

-- ========================================================================
-- Usage Instructions
-- ========================================================================
-- 1. This file matches your actual database structure
-- 2. The credentials are already configured for Hostinger
-- 3. To update settings:
--
--    UPDATE smtp_settings SET 
--      host = 'smtp.hostinger.com',
--      port = 587,
--      encryption = 'tls',
--      username = 'no-reply@cryptofinanze.de',
--      password = 'your-password',
--      from_email = 'no-reply@cryptofinanze.de',
--      from_name = 'Crypto Finanz'
--    WHERE id = 1;
--
-- 4. Test by completing onboarding in your application
-- 5. Check error logs: tail -f /var/log/apache2/error.log
-- ========================================================================

-- ========================================================================
-- Verification Query
-- ========================================================================
-- Run this to verify your SMTP settings:
--
-- SELECT * FROM smtp_settings WHERE id = 1;
--
-- Should show:
-- - host: smtp.hostinger.com
-- - port: 587
-- - encryption: tls
-- - from_email: no-reply@cryptofinanze.de
-- ========================================================================

-- ========================================================================
-- Security Notes
-- ========================================================================
-- * This file contains your actual password
-- * NEVER commit this file to public repositories
-- * Add smtp_settings_actual.sql to .gitignore
-- * Keep backups of this file securely
-- * Consider encrypting the password field in production
-- * Use strong, unique passwords for SMTP accounts
-- ========================================================================
