-- =====================================================
-- BACKUP OF ORIGINAL email_templates TABLE
-- Created: 2026-02-20
-- Purpose: Backup before enhancing with tracking and dynamic variables
-- =====================================================

-- Drop backup table if exists
DROP TABLE IF EXISTS `email_templates_backup`;

-- Create backup table with same structure as original
CREATE TABLE `email_templates_backup` (
  `id` int NOT NULL,
  `template_key` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `variables` text COMMENT 'JSON array of available variables',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Copy all data from original email_templates to backup
INSERT INTO `email_templates_backup` 
SELECT * FROM `email_templates`;

-- Add note to backup
ALTER TABLE `email_templates_backup` 
ADD COLUMN `backup_note` VARCHAR(255) DEFAULT 'Backup created on 2026-02-20 before enhancement';

SELECT 'Backup completed successfully!' AS status;
