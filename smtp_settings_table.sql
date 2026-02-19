-- ========================================================================
-- SMTP Settings Table for PHPMailer Configuration
-- ========================================================================
-- This table stores SMTP configuration for sending emails via PHPMailer
-- ========================================================================

CREATE TABLE IF NOT EXISTS `smtp_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `smtp_host` varchar(255) NOT NULL COMMENT 'SMTP server hostname (e.g., smtp.gmail.com)',
  `smtp_port` int NOT NULL DEFAULT 587 COMMENT 'SMTP port (587 for TLS, 465 for SSL)',
  `smtp_username` varchar(255) DEFAULT NULL COMMENT 'SMTP authentication username',
  `smtp_password` varchar(255) DEFAULT NULL COMMENT 'SMTP authentication password',
  `smtp_encryption` enum('tls','ssl','none') DEFAULT 'tls' COMMENT 'Encryption type',
  `smtp_from_email` varchar(255) NOT NULL COMMENT 'From email address',
  `smtp_from_name` varchar(255) DEFAULT NULL COMMENT 'From name displayed in emails',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Is this configuration active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- Sample SMTP Configuration (Gmail Example)
-- ========================================================================
-- IMPORTANT: Replace with your actual SMTP credentials before using!
-- For Gmail: Use App Password, not your regular password
-- Generate App Password at: https://myaccount.google.com/security
-- ========================================================================

INSERT INTO `smtp_settings` (
  `smtp_host`,
  `smtp_port`,
  `smtp_username`,
  `smtp_password`,
  `smtp_encryption`,
  `smtp_from_email`,
  `smtp_from_name`,
  `is_active`
) VALUES (
  'smtp.gmail.com',
  587,
  'your-email@gmail.com',
  'your-16-char-app-password',
  'tls',
  'noreply@yourdomain.com',
  'Your Company Name',
  1
) ON DUPLICATE KEY UPDATE
  smtp_host = VALUES(smtp_host),
  smtp_port = VALUES(smtp_port),
  smtp_username = VALUES(smtp_username),
  smtp_password = VALUES(smtp_password),
  smtp_encryption = VALUES(smtp_encryption),
  smtp_from_email = VALUES(smtp_from_email),
  smtp_from_name = VALUES(smtp_from_name);

-- ========================================================================
-- Common SMTP Configurations
-- ========================================================================

-- Gmail (TLS - Port 587)
-- Host: smtp.gmail.com
-- Port: 587
-- Encryption: tls
-- Username: your-email@gmail.com
-- Password: Use App Password (16 characters)

-- Gmail (SSL - Port 465)
-- Host: smtp.gmail.com
-- Port: 465
-- Encryption: ssl
-- Username: your-email@gmail.com
-- Password: Use App Password (16 characters)

-- Outlook/Office 365
-- Host: smtp.office365.com
-- Port: 587
-- Encryption: tls
-- Username: your-email@outlook.com
-- Password: Your password

-- Yahoo Mail
-- Host: smtp.mail.yahoo.com
-- Port: 587
-- Encryption: tls
-- Username: your-email@yahoo.com
-- Password: Use App Password

-- SendGrid
-- Host: smtp.sendgrid.net
-- Port: 587
-- Encryption: tls
-- Username: apikey
-- Password: Your SendGrid API key

-- Mailgun
-- Host: smtp.mailgun.org
-- Port: 587
-- Encryption: tls
-- Username: postmaster@your-domain.mailgun.org
-- Password: Your Mailgun password

-- Generic SMTP Server
-- Host: mail.yourdomain.com
-- Port: 587 (or 465 for SSL)
-- Encryption: tls (or ssl)
-- Username: user@yourdomain.com
-- Password: Your password

-- ========================================================================
-- Usage Instructions
-- ========================================================================
-- 1. Import this file to create the smtp_settings table
-- 2. Update the INSERT statement with your actual SMTP credentials
-- 3. Or use UPDATE statement after import:
--
--    UPDATE smtp_settings SET 
--      smtp_host = 'your-smtp-host',
--      smtp_port = 587,
--      smtp_username = 'your-username',
--      smtp_password = 'your-password',
--      smtp_encryption = 'tls',
--      smtp_from_email = 'noreply@yourdomain.com',
--      smtp_from_name = 'Your Company'
--    WHERE id = 1;
--
-- 4. Test by completing onboarding in your application
-- 5. Check error logs for any email sending issues
-- ========================================================================

-- ========================================================================
-- Security Notes
-- ========================================================================
-- * NEVER commit real passwords to version control
-- * Use App Passwords for Gmail (not your account password)
-- * Store smtp_password encrypted if possible
-- * Restrict database access to this table
-- * Use environment variables for sensitive data in production
-- * Enable 2FA on email accounts for better security
-- ========================================================================
