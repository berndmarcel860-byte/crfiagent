-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 11, 2026 at 12:08 AM
-- Server version: 8.0.42-0ubuntu0.20.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kryptox`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `role` enum('superadmin','admin','support') NOT NULL DEFAULT 'support',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_logs`
--

CREATE TABLE `admin_login_logs` (
  `id` int NOT NULL,
  `admin_id` int DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `success` tinyint(1) DEFAULT '1',
  `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int NOT NULL,
  `admin_id` int NOT NULL,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` int DEFAULT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int NOT NULL,
  `admin_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','danger','success') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_remember_tokens`
--

CREATE TABLE `admin_remember_tokens` (
  `id` int NOT NULL,
  `admin_id` int NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL COMMENT 'NULL if system action',
  `admin_id` int DEFAULT NULL COMMENT 'NULL if not admin action',
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` int DEFAULT NULL,
  `old_value` text,
  `new_value` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `id` int NOT NULL,
  `case_number` varchar(20) NOT NULL,
  `user_id` int NOT NULL,
  `platform_id` int NOT NULL,
  `reported_amount` decimal(15,2) NOT NULL,
  `recovered_amount` decimal(15,2) DEFAULT '0.00',
  `status` enum('open','documents_required','under_review','refund_approved','refund_rejected','closed') DEFAULT 'open',
  `description` text,
  `admin_notes` text,
  `assigned_to` int DEFAULT NULL,
  `last_updated_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `recovery_stage` varchar(50) DEFAULT 'initial',
  `recovery_progress` int DEFAULT '0',
  `admin_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_documents`
--

CREATE TABLE `case_documents` (
  `id` int NOT NULL,
  `case_id` int NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` int NOT NULL COMMENT 'User ID who uploaded',
  `notes` text,
  `verified` tinyint(1) DEFAULT '0',
  `verified_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_recovery_transactions`
--

CREATE TABLE `case_recovery_transactions` (
  `id` int NOT NULL,
  `case_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_by` int NOT NULL COMMENT 'Admin ID who processed this',
  `notes` text,
  `added_by_admin_id` int DEFAULT NULL COMMENT 'Admin who added this recovery'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `case_recovery_transactions`
--
DELIMITER $$
CREATE TRIGGER `after_recovery_insert` AFTER INSERT ON `case_recovery_transactions` FOR EACH ROW BEGIN

    -- Update case recovered amount

    UPDATE cases 

    SET recovered_amount = (

        SELECT SUM(amount) 

        FROM case_recovery_transactions 

        WHERE case_id = NEW.case_id

    )

    WHERE id = NEW.case_id;

    

    -- Update user balance

    UPDATE users u

    JOIN cases c ON u.id = c.user_id

    SET u.balance = u.balance + NEW.amount

    WHERE c.id = NEW.case_id;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `case_status_history`
--

CREATE TABLE `case_status_history` (
  `id` int NOT NULL,
  `case_id` int NOT NULL,
  `old_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` int DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method_code` varchar(50) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `proof_path` varchar(255) NOT NULL,
  `payment_details` text,
  `admin_notes` text,
  `processed_by` int DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `status` enum('pending','completed','failed','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `admin_id` int DEFAULT NULL COMMENT 'Admin who processed this deposit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `case_id` int DEFAULT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int NOT NULL,
  `template_id` int DEFAULT NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('sent','failed','delivered','opened') DEFAULT 'sent',
  `tracking_token` varchar(255) DEFAULT NULL,
  `error_message` text,
  `opened_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int NOT NULL,
  `template_key` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `variables` text COMMENT 'JSON array of available variables',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `template_key`, `subject`, `content`, `variables`, `created_at`) VALUES
(1, 'user_registration', 'Willkommen bei ScamRecovery - Ihr Konto wurde erstellt', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n\r\n<head>\r\n\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n\r\n<meta name=\"color-scheme\" content=\"light\">\r\n\r\n<meta name=\"supported-color-schemes\" content=\"light\">\r\n\r\n<style>\r\n\r\n@media only screen and (max-width: 600px) {\r\n\r\n.inner-body {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n.footer {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n@media only screen and (max-width: 500px) {\r\n\r\n.button {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n</style>\r\n\r\n</head>\r\n\r\n<body style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;\">\r\n\r\n<table class=\"wrapper\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"content\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td class=\"header\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; padding: 25px 0; text-align: center;\">\r\n\r\n<h1>Willkommen bei ScamRecovery</h1>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td class=\"body\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<table class=\"inner-body\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n<p>vielen Dank für Ihre Registrierung bei ScamRecovery. Ihr Konto wurde erfolgreich erstellt.</p>\r\n\r\n<p>Ihre Anmeldedaten:</p>\r\n\r\n<ul>\r\n\r\n<li>E-Mail: {email}</li>\r\n\r\n<li>Passwort: Das von Ihnen gewählte Passwort</li>\r\n\r\n</ul>\r\n\r\n<p style=\"text-align: center; margin: 30px 0;\">\r\n\r\n<a href=\"{verification_link}\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; display: inline-block; padding: 10px 18px; color: #ffffff; background-color: #1a202c; border-radius: 3px; text-decoration: none;\">E-Mail bestätigen</a>\r\n\r\n</p>\r\n\r\n<p>Falls Sie sich nicht registriert haben, ignorieren Sie bitte diese E-Mail.</p>\r\n\r\n<p>Mit freundlichen Grüßen,<br>Ihr ScamRecovery Team</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"footer\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">© 2025 ScamRecovery. Alle Rechte vorbehalten.</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</body>\r\n\r\n</html>', '[\"first_name\", \"last_name\", \"email\", \"verification_link\"]', '2025-08-02 06:52:21'),
(2, 'case_created1', 'Neuer Fall erstellt - Fallnummer: {case_number}', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n\r\n<head>\r\n\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n\r\n<meta name=\"color-scheme\" content=\"light\">\r\n\r\n<meta name=\"supported-color-schemes\" content=\"light\">\r\n\r\n<style>\r\n\r\n@media only screen and (max-width: 600px) {\r\n\r\n.inner-body {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n.footer {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n@media only screen and (max-width: 500px) {\r\n\r\n.button {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n</style>\r\n\r\n</head>\r\n\r\n<body style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;\">\r\n\r\n<table class=\"wrapper\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"content\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td class=\"header\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; padding: 25px 0; text-align: center;\">\r\n\r\n<h1>Neuer Fall erstellt</h1>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td class=\"body\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<table class=\"inner-body\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n<p>vielen Dank für die Einreichung Ihres Falls bei ScamRecovery. Wir haben Ihren Fall erhalten und werden uns so schnell wie möglich bei Ihnen melden.</p>\r\n\r\n<div style=\"background-color: #fff; border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 5px;\">\r\n\r\n<h3>Falldetails:</h3>\r\n\r\n<p><strong>Fallnummer:</strong> {case_number}</p>\r\n\r\n<p><strong>Plattform:</strong> {platform_name}</p>\r\n\r\n<p><strong>Gemeldeter Betrag:</strong> {reported_amount} €</p>\r\n\r\n<p><strong>Beschreibung:</strong> {case_description}</p>\r\n\r\n<p><strong>Status:</strong> {case_status}</p>\r\n\r\n</div>\r\n\r\n<p>Bitte stellen Sie alle relevanten Dokumente über Ihr Kundenportal bereit, um den Prozess zu beschleunigen.</p>\r\n\r\n<p>Sie können den Fortschritt Ihres Falls jederzeit in Ihrem Konto einsehen.</p>\r\n\r\n<p>Mit freundlichen Grüßen,<br>Ihr ScamRecovery Team</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"footer\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">© 2025 ScamRecovery. Alle Rechte vorbehalten.</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</body>\r\n\r\n</html>', '[\"first_name\", \"last_name\", \"case_number\", \"platform_name\", \"reported_amount\", \"case_description\", \"case_status\"]', '2025-08-02 06:52:21'),
(3, 'case_status_updated', 'Fallstatus aktualisiert - Fallnummer: {case_number}', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Fallstatus aktualisiert – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n    .highlight-box h3 {\r\n      margin-top: 0;\r\n      color: #007bff;\r\n    }\r\n    .highlight-box p {\r\n      margin: 6px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 10px 18px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin-top: 15px;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Fallstatus aktualisiert – {case_number}</h1>\r\n      <p>Aktuelle Informationen zu Ihrem Fall</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Der Status Ihres Falls wurde erfolgreich aktualisiert.  \r\n        Nachfolgend finden Sie die neuesten Informationen:\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>📄 Aktualisierte Falldetails</h3>\r\n        <p><strong>Fallnummer:</strong> {case_number}</p>\r\n        <p><strong>Vorheriger Status:</strong> {old_status}</p>\r\n        <p><strong>Neuer Status:</strong> {new_status}</p>\r\n        <p><strong>Grund / Notizen:</strong> {status_notes}</p>\r\n        <p><strong>Datum der Änderung:</strong> {update_date}</p>\r\n      </div>\r\n\r\n      <p>\r\n        Sie können den aktuellen Stand Ihres Falls jederzeit in Ihrem\r\n        <strong>Kundenportal</strong> einsehen und relevante Unterlagen hochladen.\r\n      </p>\r\n\r\n      <p><a href=\"{site_url}/login.php\" class=\"btn\">Zum Kundenportal</a></p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Fallmanagement-Team</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\", \"case_number\", \"old_status\", \"new_status\",\"site_url\", \"status_notes\", \"update_date\"]', '2025-08-02 06:52:21'),
(4, 'documents_required', 'Dokumente erforderlich für Fallnummer: {case_number}', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n\r\n<head>\r\n\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n\r\n<meta name=\"color-scheme\" content=\"light\">\r\n\r\n<meta name=\"supported-color-schemes\" content=\"light\">\r\n\r\n<style>\r\n\r\n@media only screen and (max-width: 600px) {\r\n\r\n.inner-body {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n.footer {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n@media only screen and (max-width: 500px) {\r\n\r\n.button {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n</style>\r\n\r\n</head>\r\n\r\n<body style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;\">\r\n\r\n<table class=\"wrapper\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"content\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td class=\"header\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; padding: 25px 0; text-align: center;\">\r\n\r\n<h1>Dokumente erforderlich</h1>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td class=\"body\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<table class=\"inner-body\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n<p>um Ihren Fall ({case_number}) weiter bearbeiten zu können, benötigen wir folgende Dokumente von Ihnen:</p>\r\n\r\n<div style=\"background-color: #fff; border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 5px;\">\r\n\r\n<h3>Erforderliche Dokumente:</h3>\r\n\r\n<ul>\r\n\r\n{#each required_documents}\r\n\r\n<li>{this}</li>\r\n\r\n{/each}\r\n\r\n</ul>\r\n\r\n<p><strong>Hinweise:</strong> {additional_notes}</p>\r\n\r\n</div>\r\n\r\n<p style=\"text-align: center; margin: 30px 0;\">\r\n\r\n<a href=\"{upload_link}\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; display: inline-block; padding: 10px 18px; color: #ffffff; background-color: #1a202c; border-radius: 3px; text-decoration: none;\">Dokumente hochladen</a>\r\n\r\n</p>\r\n\r\n<p>Bitte laden Sie die Dokumente so bald wie möglich hoch, um Verzögerungen in der Bearbeitung zu vermeiden.</p>\r\n\r\n<p>Mit freundlichen Grüßen,<br>Ihr ScamRecovery Team</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"footer\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">© 2025 ScamRecovery. Alle Rechte vorbehalten.</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</body>\r\n\r\n</html>', '[\"first_name\", \"last_name\", \"case_number\", \"required_documents\", \"additional_notes\", \"upload_link\"]', '2025-08-02 06:52:21'),
(5, 'recovery_amount_updated', 'Erstattungsbetrag aktualisiert - Fallnummer: {case_number}', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Erstattungsbetrag aktualisiert – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n    .highlight-box h3 {\r\n      margin-top: 0;\r\n      color: #007bff;\r\n    }\r\n    .highlight-box p {\r\n      margin: 6px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 10px 18px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin-top: 15px;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Erstattungsbetrag aktualisiert – {case_number}</h1>\r\n      <p>Neue Rückerstattungsinformationen verfügbar</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Wir freuen uns, Ihnen mitteilen zu können, dass für Ihren Fall  \r\n        <strong>{case_number}</strong> ein neuer Rückerstattungsbetrag verbucht wurde.\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>💰 Erstattungsdetails</h3>\r\n        <p><strong>Fallnummer:</strong> {case_number}</p>\r\n        <p><strong>Ursprünglicher Betrag:</strong> {reported_amount} €</p>\r\n        <p><strong>Neuer Rückerstattungsbetrag:</strong> {recovered_amount} €</p>\r\n        <p><strong>Gesamtrückerstattung bisher:</strong> {total_recovered} €</p>\r\n        <p><strong>Datum der Erstattung:</strong> {recovery_date}</p>\r\n        <p><strong>Notizen:</strong> {recovery_notes}</p>\r\n      </div>\r\n\r\n      <p>\r\n        Der Betrag wurde Ihrem internen Konto gutgeschrieben und steht Ihnen  \r\n        ab sofort zur Auszahlung im <strong>Kundenportal</strong> zur Verfügung.\r\n      </p>\r\n\r\n      <p><a href=\"{site_url}/login.php\" class=\"btn\">Zum Kundenportal</a></p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Rückerstattungsabteilung</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\", \"case_number\", \"reported_amount\",\"site_url\", \"recovered_amount\", \"total_recovered\", \"recovery_date\", \"recovery_notes\"]', '2025-08-02 06:52:21'),
(6, 'kyc_approved', 'Ihre KYC-Verifizierung wurde genehmigt', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>KYC-Verifizierung erfolgreich – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #28a74510 0%, #28a74505 100%);\r\n      border-left: 5px solid #28a745;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n    .highlight-box h3 {\r\n      margin-top: 0;\r\n      color: #28a745;\r\n    }\r\n    .highlight-box p {\r\n      margin: 6px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 10px 18px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin-top: 15px;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>KYC-Verifizierung erfolgreich</h1>\r\n      <p>Ihr Konto ist jetzt vollständig verifiziert</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Wir freuen uns, Ihnen mitteilen zu können, dass Ihre  \r\n        <strong>KYC-Verifizierung (Know Your Customer)</strong> erfolgreich abgeschlossen wurde.\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>✅ Verifizierungsdetails</h3>\r\n        <p><strong>Verifiziertes Konto:</strong> {email}</p>\r\n        <p><strong>Datum der Verifizierung:</strong> {kyc_date}</p>\r\n        <p><strong>Status:</strong> Erfolgreich abgeschlossen</p>\r\n      </div>\r\n\r\n      <p>\r\n        Ihr Konto ist nun vollständig freigeschaltet und Sie können alle Funktionen  \r\n        unserer Plattform uneingeschränkt nutzen – inklusive Auszahlungen, Fallmanagement  \r\n        und Transaktionsverfolgung in Echtzeit.\r\n      </p>\r\n\r\n      <p>\r\n        Sollten Sie Fragen haben oder weitere Unterstützung benötigen, steht Ihnen  \r\n        unser <strong>Support-Team</strong> jederzeit zur Verfügung.\r\n      </p>\r\n\r\n      <p><a href=\"{site_url}/login.php\" class=\"btn\">Zum Kundenportal</a></p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Verifizierungsteam</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\",\"site_url\"]', '2025-08-02 06:52:21'),
(7, 'kyc_rejected', 'Ihre KYC-Verifizierung erfordert weitere Schritte', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>KYC-Verifizierung nicht erfolgreich – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #a83232 0%, #d64545 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #ff000010 0%, #ff000005 100%);\r\n      border-left: 5px solid #d93025;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n    .highlight-box h3 {\r\n      margin-top: 0;\r\n      color: #d93025;\r\n    }\r\n    .highlight-box p {\r\n      margin: 6px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #d93025;\r\n      color: white;\r\n      padding: 10px 18px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin-top: 15px;\r\n    }\r\n\r\n    .btn:hover {\r\n      background: #b1271f;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>KYC-Verifizierung nicht erfolgreich</h1>\r\n      <p>Überprüfung Ihrer Unterlagen fehlgeschlagen</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Leider konnten wir Ihre <strong>KYC-Verifizierung (Know Your Customer)</strong>  \r\n        nicht erfolgreich abschließen. Bitte beachten Sie die unten aufgeführten Hinweise.\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>❗ Gründe für die Ablehnung</h3>\r\n        <p>{rejection_reason}</p>\r\n      </div>\r\n\r\n      <p>\r\n        Um den Prozess fortzusetzen, reichen Sie bitte die fehlenden oder korrigierten  \r\n        Dokumente über unser sicheres <strong>Kundenportal</strong> erneut ein.\r\n      </p>\r\n\r\n      <p style=\"text-align:center;\">\r\n        <a href=\"{resubmit_link}\" class=\"btn\">Dokumente erneut einreichen</a>\r\n      </p>\r\n\r\n      <p>\r\n        Nach erfolgreicher Überprüfung werden Sie automatisch per E-Mail informiert.  \r\n        Sollten Sie Fragen zur Ablehnung oder zu den nächsten Schritten haben,  \r\n        steht Ihnen unser Support-Team gerne zur Verfügung.\r\n      </p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Verifizierungsteam</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\", \"rejection_reason\", \"resubmit_link\",\"site_url\"]', '2025-08-02 06:52:21'),
(8, 'deposit_received', 'Einzahlung erhalten - Betrag: {amount}', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Einzahlung erhalten – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n    .highlight-box h3 {\r\n      margin-top: 0;\r\n      color: #007bff;\r\n    }\r\n    .highlight-box p {\r\n      margin: 6px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 10px 18px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin-top: 15px;\r\n    }\r\n\r\n    .btn:hover {\r\n      background: #005dc1;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Einzahlung erhalten</h1>\r\n      <p>Ihre Transaktion wurde erfolgreich verbucht</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Wir bestätigen den erfolgreichen Eingang Ihrer <strong>Einzahlung</strong>.  \r\n        Der Betrag wurde Ihrem Kontoguthaben gutgeschrieben und steht Ihnen ab sofort zur Verfügung.\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>💳 Transaktionsdetails</h3>\r\n        <p><strong>Betrag:</strong> {amount} </p>\r\n        <p><strong>Zahlungsmethode:</strong> {payment_method}</p>\r\n        <p><strong>Transaktions-ID:</strong> {transaction_id}</p>\r\n        <p><strong>Datum:</strong> {transaction_date}</p>\r\n        <p><strong>Status:</strong> {transaction_status}</p>\r\n      </div>\r\n\r\n      <p>\r\n        Sie können Ihre vollständige Transaktionshistorie sowie Ihr aktuelles Guthaben  \r\n        jederzeit in Ihrem <strong>Kundenportal</strong> einsehen.\r\n      </p>\r\n\r\n      <p><a href=\"{site_url}/login.php\" class=\"btn\">Zum Kundenportal</a></p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Finanzabteilung</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\", \"amount\", \"payment_method\", \"transaction_id\", \"transaction_date\", \"transaction_status\",\"site_url\"]', '2025-08-02 06:52:21');
INSERT INTO `email_templates` (`id`, `template_key`, `subject`, `content`, `variables`, `created_at`) VALUES
(9, 'withdrawal_requested', 'Auszahlungsanfrage erhalten - Betrag: {amount}', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Auszahlungsanfrage erhalten – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n    .highlight-box h3 {\r\n      margin-top: 0;\r\n      color: #007bff;\r\n    }\r\n    .highlight-box p {\r\n      margin: 6px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 10px 18px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin-top: 15px;\r\n    }\r\n\r\n    .btn:hover {\r\n      background: #005dc1;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Auszahlungsanfrage erhalten</h1>\r\n      <p>Ihre Anfrage wird derzeit geprüft</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Wir bestätigen den Eingang Ihrer <strong>Auszahlungsanfrage</strong>.  \r\n        Unser Team hat die Bearbeitung eingeleitet. Die Details Ihrer Anfrage finden Sie unten:\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>💸 Transaktionsdetails</h3>\r\n        <p><strong>Betrag:</strong> {amount} €</p>\r\n        <p><strong>Zahlungsmethode:</strong> {payment_method}</p>\r\n        <p><strong>Zahlungsdetails:</strong> {payment_details}</p>\r\n        <p><strong>Transaktions-ID:</strong> {transaction_id}</p>\r\n        <p><strong>Datum der Anfrage:</strong> {transaction_date}</p>\r\n        <p><strong>Status:</strong> {transaction_status}</p>\r\n      </div>\r\n\r\n      <p>\r\n        Die Bearbeitung Ihrer Auszahlung kann bis zu <strong>3 Werktage</strong> in Anspruch nehmen.  \r\n        Sie werden automatisch benachrichtigt, sobald die Transaktion abgeschlossen ist.\r\n      </p>\r\n\r\n      <p>\r\n        Den aktuellen Fortschritt Ihrer Auszahlung können Sie jederzeit  \r\n        in Ihrem <strong>Kundenportal</strong> einsehen.\r\n      </p>\r\n\r\n      <p><a href=\"{site_url}/login.php\" class=\"btn\">Zum Kundenportal</a></p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Finanzabteilung</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\", \"amount\", \"payment_method\", \"payment_details\", \"transaction_id\", \"transaction_date\", \"transaction_status\",\"site_url\"]', '2025-08-02 06:52:21'),
(10, 'withdrawal_completed', 'Auszahlung abgeschlossen - Betrag: {amount}', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Auszahlung abgeschlossen – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 12px 20px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .btn:hover {\r\n      background: #005dc1;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Auszahlung abgeschlossen</h1>\r\n      <p>Ihre Auszahlungsanfrage wurde erfolgreich bearbeitet</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Ihre Auszahlungsanfrage wurde erfolgreich bearbeitet.  \r\n        Der Betrag wurde an Sie überwiesen.\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>💳 Transaktionsdetails</h3>\r\n        <p><strong>Betrag:</strong> {amount} €</p>\r\n        <p><strong>Zahlungsmethode:</strong> {payment_method}</p>\r\n        <p><strong>Zahlungsdetails:</strong> {payment_details}</p>\r\n        <p><strong>Transaktions-ID:</strong> {transaction_id}</p>\r\n        <p><strong>Datum der Auszahlung:</strong> {transaction_date}</p>\r\n        <p><strong>Status:</strong> {transaction_status}</p>\r\n      </div>\r\n\r\n      <p>\r\n        Bitte beachten Sie, dass es je nach Zahlungsmethode einige Tage dauern kann,  \r\n        bis der Betrag auf Ihrem Konto erscheint.\r\n      </p>\r\n\r\n      <p style=\"text-align:center;\">\r\n        <a href=\"{site_url}/login.php\" class=\"btn\">Zum Kundenportal</a>\r\n      </p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          Sitz der Gesellschaft: Frankfurt am Main – Registergericht: Frankfurt am Main – HRB: 10162132<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\", \"amount\", \"payment_method\", \"payment_details\", \"transaction_id\", \"transaction_date\", \"transaction_status\",\"site_url\"]', '2025-08-02 06:52:21'),
(11, 'password_reset', 'Passwort zurücksetzen - ScamRecovery', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Passwort zurücksetzen – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 12px 20px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .btn:hover {\r\n      background: #005dc1;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Passwort zurücksetzen</h1>\r\n      <p>Sichere Wiederherstellung Ihres Kontozugangs</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Wir haben eine Anfrage zum <strong>Zurücksetzen Ihres Passworts</strong> erhalten.  \r\n        Um ein neues Passwort festzulegen, klicken Sie bitte auf den folgenden Button:\r\n      </p>\r\n\r\n      <p style=\"text-align:center;\">\r\n        <a href=\"{reset_link}\" class=\"btn\">Passwort jetzt zurücksetzen</a>\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <p>🔒 Aus Sicherheitsgründen ist dieser Link nur <strong>24 Stunden gültig</strong>.</p>\r\n        <p>Wenn Sie diese Anfrage <strong>nicht gestellt</strong> haben, ignorieren Sie bitte diese E-Mail oder wenden Sie sich an unseren Support.</p>\r\n      </div>\r\n\r\n      <p>\r\n        Nach erfolgreichem Zurücksetzen können Sie sich mit Ihrem neuen Passwort im  \r\n        <strong>Kundenportal</strong> anmelden.\r\n      </p>\r\n\r\n      <p><a href=\"{site_url}/login.php\" class=\"btn\">Zum Login</a></p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Sicherheitsteam</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\", \"reset_link\",\"site_url\"]', '2025-08-02 06:52:21'),
(12, 'support_ticket_created', 'Support-Ticket erstellt - Ticketnummer: {ticket_number}', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n\r\n<head>\r\n\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n\r\n<meta name=\"color-scheme\" content=\"light\">\r\n\r\n<meta name=\"supported-color-schemes\" content=\"light\">\r\n\r\n<style>\r\n\r\n@media only screen and (max-width: 600px) {\r\n\r\n.inner-body {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n.footer {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n@media only screen and (max-width: 500px) {\r\n\r\n.button {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n</style>\r\n\r\n</head>\r\n\r\n<body style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;\">\r\n\r\n<table class=\"wrapper\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"content\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td class=\"header\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; padding: 25px 0; text-align: center;\">\r\n\r\n<h1>Support-Ticket erstellt</h1>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td class=\"body\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<table class=\"inner-body\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n<p>vielen Dank für die Kontaktaufnahme mit unserem Support-Team. Wir haben Ihr Ticket erhalten und werden uns so schnell wie möglich bei Ihnen melden.</p>\r\n\r\n<div style=\"background-color: #fff; border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 5px;\">\r\n\r\n<h3>Ticketdetails:</h3>\r\n\r\n<p><strong>Ticketnummer:</strong> {ticket_number}</p>\r\n\r\n<p><strong>Betreff:</strong> {ticket_subject}</p>\r\n\r\n<p><strong>Kategorie:</strong> {ticket_category}</p>\r\n\r\n<p><strong>Priorität:</strong> {ticket_priority}</p>\r\n\r\n<p><strong>Status:</strong> {ticket_status}</p>\r\n\r\n<p><strong>Ihre Nachricht:</strong><br>{ticket_message}</p>\r\n\r\n</div>\r\n\r\n<p>Sie können den Status Ihres Tickets jederzeit in Ihrem Kundenportal einsehen.</p>\r\n\r\n<p>Mit freundlichen Grüßen,<br>Ihr ScamRecovery Team</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"footer\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">© 2025 ScamRecovery. Alle Rechte vorbehalten.</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</body>\r\n\r\n</html>', '[\"first_name\", \"last_name\", \"ticket_number\", \"ticket_subject\", \"ticket_category\", \"ticket_priority\", \"ticket_status\", \"ticket_message\"]', '2025-08-02 06:52:21'),
(13, 'support_ticket_updated', 'Aktualisierung zu Ihrem Support-Ticket - Ticketnummer: {ticket_number}', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n\r\n<head>\r\n\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n\r\n<meta name=\"color-scheme\" content=\"light\">\r\n\r\n<meta name=\"supported-color-schemes\" content=\"light\">\r\n\r\n<style>\r\n\r\n@media only screen and (max-width: 600px) {\r\n\r\n.inner-body {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n.footer {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n@media only screen and (max-width: 500px) {\r\n\r\n.button {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n</style>\r\n\r\n</head>\r\n\r\n<body style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;\">\r\n\r\n<table class=\"wrapper\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"content\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td class=\"header\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; padding: 25px 0; text-align: center;\">\r\n\r\n<h1>Support-Ticket aktualisiert</h1>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td class=\"body\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<table class=\"inner-body\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n<p>es gibt eine Aktualisierung zu Ihrem Support-Ticket ({ticket_number}). Hier sind die Details:</p>\r\n\r\n<div style=\"background-color: #fff; border: 1px solid #ddd; padding: 15px; margin: 15px 0; border-radius: 5px;\">\r\n\r\n<h3>Ticketaktualisierung:</h3>\r\n\r\n<p><strong>Ticketnummer:</strong> {ticket_number}</p>\r\n\r\n<p><strong>Status:</strong> {ticket_status}</p>\r\n\r\n<p><strong>Aktualisierung vom:</strong> {update_date}</p>\r\n\r\n<p><strong>Antwort von Support:</strong><br>{ticket_response}</p>\r\n\r\n</div>\r\n\r\n<p>Sie können auf diese E-Mail antworten oder das Ticket direkt in Ihrem Kundenportal aktualisieren.</p>\r\n\r\n<p>Mit freundlichen Grüßen,<br>Ihr ScamRecovery Team</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"footer\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">© 2025 ScamRecovery. Alle Rechte vorbehalten.</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</body>\r\n\r\n</html>', '[\"first_name\", \"last_name\", \"ticket_number\", \"ticket_status\", \"update_date\", \"ticket_response\"]', '2025-08-02 06:52:21'),
(14, 'admin_created_user', 'Your account has been created by an administrator', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n\r\n<head>\r\n\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n\r\n<meta name=\"color-scheme\" content=\"light\">\r\n\r\n<meta name=\"supported-color-schemes\" content=\"light\">\r\n\r\n<style>\r\n\r\n@media only screen and (max-width: 600px) {\r\n\r\n.inner-body {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n.footer {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n@media only screen and (max-width: 500px) {\r\n\r\n.button {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n</style>\r\n\r\n</head>\r\n\r\n<body style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;\">\r\n\r\n<table class=\"wrapper\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"content\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td class=\"header\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; padding: 25px 0; text-align: center;\">\r\n\r\n<h1>Account Created</h1>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td class=\"body\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<table class=\"inner-body\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p>Dear {first_name} {last_name},</p>\r\n\r\n<p>An administrator ({admin_name}) has created an account for you on our platform.</p>\r\n\r\n<p>Your login details:</p>\r\n\r\n<ul>\r\n\r\n<li>Email: {email}</li>\r\n\r\n<li>Password: The one provided by the administrator</li>\r\n\r\n</ul>\r\n\r\n<p style=\"text-align: center; margin: 30px 0;\">\r\n\r\n<a href=\"{login_link}\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; display: inline-block; padding: 10px 18px; color: #ffffff; background-color: #1a202c; border-radius: 3px; text-decoration: none;\">Login</a>\r\n\r\n</p>\r\n\r\n<p>Please change your password after your first login.</p>\r\n\r\n<p>If you did not request this account, please contact our support team immediately.</p>\r\n\r\n<p>Best regards,<br>Your ScamRecovery Team</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"footer\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">© 2025 ScamRecovery. All rights reserved.</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</body>\r\n\r\n</html>', '[\"first_name\",\"last_name\",\"email\",\"admin_name\",\"login_link\"]', '2025-08-02 07:15:59'),
(15, 'welcome_email1', 'Herzlich willkommen bei {sbrand}!', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n\r\n<head>\r\n\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n\r\n<meta name=\"color-scheme\" content=\"light\">\r\n\r\n<meta name=\"supported-color-schemes\" content=\"light\">\r\n\r\n<style>\r\n\r\n@media only screen and (max-width: 600px) {\r\n\r\n.inner-body {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n.footer {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n@media only screen and (max-width: 500px) {\r\n\r\n.button {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n</style>\r\n\r\n</head>\r\n\r\n<body style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;\">\r\n\r\n<table class=\"wrapper\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"content\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td class=\"header\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; padding: 25px 0; text-align: center;\">\r\n\r\n<h1>Welcome to {sbrand}</h1>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td class=\"body\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<table class=\"inner-body\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p>Sehr geehrte/r {last_name},</p>\r\n\r\n<p>Herzlich willkommen bei {sbrand}! Wir freuen uns, dass Sie sich für uns entschieden haben.</p>\r\n\r\n<p style=\"text-align: center; margin: 30px 0;\">\r\n\r\n<a href=\"{surl}/login.php\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; display: inline-block; padding: 10px 18px; color: #ffffff; background-color: #1a202c; border-radius: 3px; text-decoration: none;\">Zum Login</a>\r\n\r\n</p>\r\n\r\n<p>Hier sind Ihre Anmeldedaten:</p>\r\n\r\n<p><b>Benutzername:</b> {email}</p>\r\n\r\n<p><b>Passwort:</b> {pass}</p>\r\n\r\n<p>Bitte ändern Sie Ihr Passwort nach der ersten Anmeldung in Ihrem Profil unter \"Profil bearbeiten\" -> \"Passwort ändern\".</p>\r\n\r\n<p>Vielen Dank für Ihr Vertrauen. Sollten Sie Fragen haben, stehen wir Ihnen gerne zur Verfügung.</p>\r\n\r\n<p>Mit freundlichen Grüßen,<br>{sbrand}</p>\r\n\r\n<p><b>E-Mail: {semail}</b></p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"footer\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">© 2025 {sbrand}. Alle Rechte vorbehalten.</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</body>\r\n\r\n</html>', '[\"last_name\",\"email\",\"pass\",\"sbrand\",\"surl\",\"semail\"]', '2025-08-02 07:54:52');
INSERT INTO `email_templates` (`id`, `template_key`, `subject`, `content`, `variables`, `created_at`) VALUES
(16, 'welcome_email_text', 'Herzlich willkommen bei {sbrand}!', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\">\r\n\r\n<head>\r\n\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\r\n\r\n<meta name=\"color-scheme\" content=\"light\">\r\n\r\n<meta name=\"supported-color-schemes\" content=\"light\">\r\n\r\n<style>\r\n\r\n@media only screen and (max-width: 600px) {\r\n\r\n.inner-body {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n.footer {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n@media only screen and (max-width: 500px) {\r\n\r\n.button {\r\n\r\nwidth: 100% !important;\r\n\r\n}\r\n\r\n}\r\n\r\n</style>\r\n\r\n</head>\r\n\r\n<body style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -webkit-text-size-adjust: none; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100% !important;\">\r\n\r\n<table class=\"wrapper\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"content\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<tr>\r\n\r\n<td class=\"header\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; padding: 25px 0; text-align: center;\">\r\n\r\n<h1>Welcome to {sbrand}</h1>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td class=\"body\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%; background-color: #edf2f7; border-bottom: 1px solid #edf2f7; border-top: 1px solid #edf2f7; margin: 0; padding: 0; width: 100%;\">\r\n\r\n<table class=\"inner-body\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015); margin: 0 auto; padding: 0; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p>Sehr geehrte/r {{name}},</p>\r\n\r\n<p>Herzlich willkommen bei {{sbrand}}! Wir freuen uns, dass Sie sich für uns entschieden haben.</p>\r\n\r\n<p>Hier sind Ihre Anmeldedaten:</p>\r\n\r\n<p><b>Benutzername:</b> {{email}}</p>\r\n\r\n<p><b>Passwort:</b> {{pass}}</p>\r\n\r\n<p style=\"text-align: center; margin: 30px 0;\">\r\n\r\n<a href=\"{surl}/login.php\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; display: inline-block; padding: 10px 18px; color: #ffffff; background-color: #1a202c; border-radius: 3px; text-decoration: none;\">Zum Login</a>\r\n\r\n</p>\r\n\r\n<p>Bitte ändern Sie Ihr Passwort nach der ersten Anmeldung in Ihrem Profil unter \"Profil bearbeiten\" -> \"Passwort ändern\".</p>\r\n\r\n<p>Vielen Dank für Ihr Vertrauen. Sollten Sie Fragen haben, stehen wir Ihnen gerne zur Verfügung.</p>\r\n\r\n<p>Mit freundlichen Grüßen,<br>{{sbrand}}</p>\r\n\r\n<p><b>Tel: {{sphone}}</b></p>\r\n\r\n<p><b>E-Mail: {{semail}}</b></p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n<tr>\r\n\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative;\">\r\n\r\n<table class=\"footer\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px; margin: 0 auto; padding: 0; text-align: center; width: 570px;\">\r\n\r\n<tr>\r\n\r\n<td class=\"content-cell\" align=\"center\" style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; max-width: 100vw; padding: 32px;\">\r\n\r\n<p style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, Helvetica, Arial, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Emoji&quot;; position: relative; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">© 2025 {{sbrand}}. Alle Rechte vorbehalten.</p>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</td>\r\n\r\n</tr>\r\n\r\n</table>\r\n\r\n</body>\r\n\r\n</html>', '[\"name\",\"email\",\"pass\",\"sbrand\",\"surl\",\"sphone\",\"semail\"]', '2025-08-02 07:54:52'),
(17, 'deposit_confirmation', 'Einzahlungsbestätigung - Referenz: {reference}', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Auszahlung abgeschlossen – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 12px 20px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .btn:hover {\r\n      background: #005dc1;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Auszahlung abgeschlossen</h1>\r\n      <p>Ihre Auszahlungsanfrage wurde erfolgreich bearbeitet</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Wir freuen uns, Ihnen mitzuteilen, dass Ihre Auszahlungsanfrage  \r\n        erfolgreich abgeschlossen wurde. Der Betrag wurde an Ihr angegebenes  \r\n        Konto überwiesen.\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>💳 Transaktionsdetails</h3>\r\n        <p><strong>Betrag:</strong> {amount} €</p>\r\n        <p><strong>Zahlungsmethode:</strong> {payment_method}</p>\r\n        <p><strong>Zahlungsdetails:</strong> {payment_details}</p>\r\n        <p><strong>Transaktions-ID:</strong> {transaction_id}</p>\r\n        <p><strong>Datum der Auszahlung:</strong> {transaction_date}</p>\r\n        <p><strong>Status:</strong> {transaction_status}</p>\r\n      </div>\r\n\r\n      <p>\r\n        Bitte beachten Sie, dass es je nach Zahlungsanbieter bis zu  \r\n        <strong>3 Werktage</strong> dauern kann, bis der Betrag auf Ihrem  \r\n        Konto erscheint.\r\n      </p>\r\n\r\n      <p style=\"text-align:center;\">\r\n        <a href=\"{site_url}/login.php\" class=\"btn\">Zum Kundenportal</a>\r\n      </p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Finanzabteilung</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\", \"username\", \"amount\", \"reference\", \"payment_method\", \"date\", \"current_year\", \"site_name\", \"site_url\", \"support_email\",\"site_url\"]', '2025-08-19 04:53:51'),
(18, 'kyc_pending', 'KYC-Verifizierung ausstehend - {kyc_id}', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>KYC-Verifizierung ausstehend – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .info-box {\r\n      background: #e8f4f8;\r\n      border-left: 5px solid #2950a8;\r\n      border-radius: 6px;\r\n      padding: 18px;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .info-box h3 {\r\n      margin-top: 0;\r\n      color: #2950a8;\r\n    }\r\n\r\n    .info-box table {\r\n      width: 100%;\r\n      border-collapse: collapse;\r\n      font-size: 14px;\r\n    }\r\n\r\n    .info-box td {\r\n      padding: 6px 4px;\r\n    }\r\n\r\n    .success-box {\r\n      background: #d4edda;\r\n      border-left: 5px solid #28a745;\r\n      border-radius: 6px;\r\n      padding: 18px;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .success-box h3 {\r\n      color: #155724;\r\n      margin-top: 0;\r\n    }\r\n\r\n    .warning-box {\r\n      background: #fff3cd;\r\n      border-left: 5px solid #ffc107;\r\n      border-radius: 6px;\r\n      padding: 18px;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .warning-box h3 {\r\n      color: #856404;\r\n      margin-top: 0;\r\n    }\r\n\r\n    .alert-box {\r\n      background: #f8d7da;\r\n      border-left: 5px solid #dc3545;\r\n      border-radius: 6px;\r\n      padding: 15px;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container { width: 94%; }\r\n      .header h1 { font-size: 22px; }\r\n      .signature img { height: 45px; }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>KYC-Verifizierung ausstehend</h1>\r\n      <p>Ihre Identitätsprüfung wird derzeit überprüft</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>vielen Dank für die Einreichung Ihrer KYC-Dokumente (<em>Know Your Customer</em>) bei KryptoX.</p>\r\n\r\n      <div class=\"info-box\">\r\n        <h3>📋 Verifizierungsdetails</h3>\r\n        <table>\r\n          <tr><td><strong>KYC-ID:</strong></td><td>{kyc_id}</td></tr>\r\n          <tr><td><strong>Dokumenttyp:</strong></td><td>{document_type}</td></tr>\r\n          <tr><td><strong>Datum & Uhrzeit:</strong></td><td>{date}</td></tr>\r\n          <tr><td><strong>Status:</strong></td><td><span style=\"background:#ffc107; color:#000; padding:2px 8px; border-radius:10px;\">⏳ In Bearbeitung</span></td></tr>\r\n        </table>\r\n      </div>\r\n\r\n      <div class=\"success-box\">\r\n        <h3>✅ Dokumente erfolgreich erhalten</h3>\r\n        <ul>\r\n          <li>Vorderseite des Ausweisdokuments</li>\r\n          <li>Rückseite des Ausweisdokuments (falls zutreffend)</li>\r\n          <li>Selfie mit Ausweisdokument</li>\r\n          <li>Adressnachweis</li>\r\n        </ul>\r\n      </div>\r\n\r\n      <div class=\"warning-box\">\r\n        <h3>🔄 Nächste Schritte</h3>\r\n        <ul>\r\n          <li><strong>Überprüfung:</strong> Unser Compliance-Team prüft Ihre Unterlagen innerhalb von 1–3 Werktagen</li>\r\n          <li><strong>Verifizierung:</strong> Wir bestätigen die Echtheit und Lesbarkeit der eingereichten Dokumente</li>\r\n          <li><strong>Kontofreischaltung:</strong> Nach erfolgreicher Prüfung wird Ihr Konto vollständig freigeschaltet</li>\r\n          <li><strong>Benachrichtigung:</strong> Sie erhalten automatisch eine E-Mail, sobald die Überprüfung abgeschlossen ist</li>\r\n        </ul>\r\n      </div>\r\n\r\n      <p>Sie können den Status Ihrer KYC-Verifizierung jederzeit in Ihrem Dashboard einsehen.</p>\r\n\r\n      <div class=\"alert-box\">\r\n        <p><strong>⚠️ Sicherheitshinweis:</strong> Falls Sie diese KYC-Einreichung nicht autorisiert haben, kontaktieren Sie bitte umgehend unseren Support unter <a href=\"mailto:{support_email}\">{support_email}</a> mit der KYC-ID <strong>{kyc_id}</strong>.</p>\r\n      </div>\r\n\r\n      <p>Unser Support-Team steht Ihnen 24/7 zur Verfügung, um Sie bei Fragen zu Ihrer KYC-Verifizierung oder Ihrem Konto zu unterstützen.</p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Compliance & Verification Team</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.  \r\n      <br>🔒 Diese Nachricht wurde automatisch generiert – bitte antworten Sie nicht direkt.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', 'first_name,last_name,kyc_id,document_type,date,support_email,current_year,site_name,site_url', '2025-08-20 16:16:28'),
(19, 'payout_confirmation_document_send1', 'Ihre Auszahlungsbestätigung & Rechnung – {invoice_no}', '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\"><head>\r\n\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\r\n\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\r\n\r\n<meta name=\"color-scheme\" content=\"light\" />\r\n\r\n<meta name=\"supported-color-schemes\" content=\"light\" />\r\n\r\n<style>@media only screen and (max-width:600px){.inner-body{width:100%!important}.footer{width:100%!important}}@media only screen and (max-width:500px){.button{width:100%!important}}</style>\r\n\r\n</head><body style=\"background-color:#ffffff;color:#718096;margin:0;padding:0;width:100%!important;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif\">\r\n\r\n<table class=\"wrapper\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"background-color:#edf2f7;margin:0;padding:0;width:100%\"><tr><td align=\"center\">\r\n\r\n<table class=\"content\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\">\r\n\r\n<tr><td class=\"header\" style=\"padding:25px 0;text-align:center\"><h1 style=\"margin:0;color:#1a202c\">{brand_name}</h1></td></tr>\r\n\r\n<tr><td class=\"body\" width=\"100%\" style=\"background-color:#edf2f7;border-top:1px solid #edf2f7;border-bottom:1px solid #edf2f7\">\r\n\r\n<table class=\"inner-body\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"background-color:#ffffff;border:1px solid #e8e5ef;border-radius:2px;margin:0 auto;width:570px\">\r\n\r\n<tr><td class=\"content-cell\" style=\"padding:32px\">\r\n\r\n<p style=\"margin-top:0;color:#2d3748\">Guten Tag {full_name},</p>\r\n\r\n<p style=\"color:#2d3748\">anbei erhalten Sie Ihre <strong>Auszahlungsbestätigung</strong> und die dazugehörige <strong>Rechnung</strong>.</p>\r\n\r\n<table style=\"border-collapse:collapse;font-size:14px;color:#2d3748\">\r\n\r\n<tr><td style=\"padding:6px 8px;border-bottom:1px solid #edf2f7\">Rechnungsnummer:</td><td style=\"padding:6px 8px;border-bottom:1px solid #edf2f7\"><strong>{invoice_no}</strong></td></tr>\r\n\r\n<tr><td style=\"padding:6px 8px;border-bottom:1px solid #edf2f7\">Rechnungsdatum:</td><td style=\"padding:6px 8px;border-bottom:1px solid #edf2f7\">{invoice_date}</td></tr>\r\n\r\n<tr><td style=\"padding:6px 8px\">Servicegebühr:</td><td style=\"padding:6px 8px\">{service_fee} €</td></tr>\r\n\r\n</table>\r\n\r\n<p style=\"color:#2d3748\">Viele Grüße<br />{brand_name}</p>\r\n\r\n</td></tr></table>\r\n\r\n</td></tr>\r\n\r\n<tr><td>\r\n\r\n<table class=\"footer\" align=\"center\" width=\"570\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"text-align:center;margin:0 auto;width:570px\"><tr><td class=\"content-cell\" align=\"center\" style=\"padding:32px\">\r\n\r\n<p style=\"margin:0;color:#b0adc5;font-size:12px;text-align:center\">© 2025 {brand_name}. Alle Rechte vorbehalten.</p>\r\n\r\n</td></tr></table>\r\n\r\n</td></tr></table></td></tr></table></body></html>', '[\"full_name\", \"invoice_no\", \"invoice_date\", \"service_fee\", \"brand_name\"]', '2025-09-10 03:45:40'),
(20, 'payout_confirmation_document_send', 'Ihre Auszahlungsbestätigung & Rechnung – {invoice_no}', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Auszahlungsbestätigung & Rechnung – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    table.details {\r\n      width: 100%;\r\n      border-collapse: collapse;\r\n      margin-top: 15px;\r\n      background: #fff;\r\n      border-radius: 6px;\r\n      overflow: hidden;\r\n      box-shadow: 0 2px 8px rgba(0,0,0,0.03);\r\n    }\r\n\r\n    table.details td {\r\n      padding: 10px 12px;\r\n      border-bottom: 1px solid #eee;\r\n      font-size: 14px;\r\n    }\r\n\r\n    table.details tr:last-child td {\r\n      border-bottom: none;\r\n    }\r\n\r\n    table.details td:first-child {\r\n      color: #555;\r\n      width: 50%;\r\n    }\r\n\r\n    table.details td:last-child {\r\n      text-align: right;\r\n      font-weight: bold;\r\n      color: #111;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 18px;\r\n      border-radius: 6px;\r\n      margin: 25px 0;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container { width: 94%; }\r\n      .header h1 { font-size: 22px; }\r\n      .signature img { height: 45px; }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Auszahlungsbestätigung</h1>\r\n      <p>Ihre Auszahlung wurde erfolgreich verarbeitet</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Guten Tag {full_name},</p>\r\n\r\n      <p>\r\n        anbei erhalten Sie die <strong>Bestätigung Ihrer Auszahlung</strong> sowie  \r\n        die zugehörige <strong>Rechnung</strong> für Ihre Unterlagen.\r\n      </p>\r\n\r\n      <table class=\"details\">\r\n        <tr>\r\n          <td>Rechnungsnummer:</td>\r\n          <td>{invoice_no}</td>\r\n        </tr>\r\n        <tr>\r\n          <td>Rechnungsdatum:</td>\r\n          <td>{invoice_date}</td>\r\n        </tr>\r\n        <tr>\r\n          <td>Erstattungsbetrag:</td>\r\n          <td>{lost_amount} €</td>\r\n        </tr>\r\n        <tr>\r\n          <td>Servicegebühr:</td>\r\n          <td>{service_fee} €</td>\r\n        </tr>\r\n      </table>\r\n\r\n      <div class=\"highlight-box\">\r\n        <p>\r\n          💡 Bitte beachten Sie, dass der ausgewiesene Betrag je nach Bank  \r\n          innerhalb von 1–3 Werktagen auf Ihrem Konto eingehen kann.\r\n        </p>\r\n      </div>\r\n\r\n      <p>\r\n        Falls Sie Rückfragen zu dieser Auszahlung oder Rechnung haben,  \r\n        steht Ihnen unser Support-Team gerne zur Verfügung.\r\n      </p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Finanzabteilung</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"full_name\", \"invoice_no\", \"invoice_date\", \"lost_amount\", \"service_fee\", \"brand_name\",\"site_url\"]', '2025-09-10 03:45:40'),
(21, 'welcome_email', 'Herzlich willkommen bei {sbrand}!', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n    <meta charset=\"utf-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Welcome to {sbrand}</title>\r\n    <style>\r\n        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; margin: 0; padding: 0; }\r\n        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); overflow: hidden; }\r\n        .header { background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%); color: white; text-align: center; padding: 30px 20px; }\r\n        .header h1 { margin: 0; font-size: 24px; }\r\n        .content { padding: 25px; background: #f9f9f9; }\r\n        .details { background: #fff; padding: 20px; border-left: 4px solid #007bff; border-radius: 6px; margin: 20px 0; }\r\n        .btn { display: inline-block; background: #007bff; color: white; padding: 10px 18px; border-radius: 5px; text-decoration: none; font-weight: bold; }\r\n        .footer { text-align: center; font-size: 12px; color: #666; padding: 20px; background: #f1f1f1; }\r\n        .highlight { color: #007bff; font-weight: bold; }\r\n        .signature { margin-top: 30px; border-top: 1px solid #e0e0e0; padding-top: 20px; font-size: 14px; color: #555; }\r\n        .signature img { height: 45px; margin-bottom: 10px; }\r\n        .signature p { margin: 4px 0; }\r\n        @media only screen and (max-width: 600px) {\r\n            .container { width: 95%; }\r\n            .content { padding: 15px; }\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n    <div class=\"container\">\r\n        <div class=\"header\">\r\n            <h1>Willkommen bei {sbrand}</h1>\r\n            <p>Ihr Zugang ist erfolgreich eingerichtet!</p>\r\n        </div>\r\n\r\n        <div class=\"content\">\r\n            <p>Sehr geehrte/r {last_name},</p>\r\n            <p>Herzlich willkommen bei <strong>{sbrand}</strong>! Wir freuen uns, dass Sie sich für uns entschieden haben.</p>\r\n\r\n            <div class=\"details\">\r\n                <h4>🔐 Ihre Zugangsdaten</h4>\r\n                <p><strong>Benutzername:</strong> {email}<br>\r\n                <strong>Passwort:</strong> {pass}</p>\r\n                <p style=\"margin-top: 10px;\">\r\n                    <a href=\"{surl}/login.php\" class=\"btn\">Zum Login</a>\r\n                </p>\r\n            </div>\r\n\r\n            <div style=\"background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 6px;\">\r\n                <p style=\"margin: 0;\"><strong>⚠️ Sicherheitshinweis:</strong> Bitte ändern Sie Ihr Passwort nach der ersten Anmeldung unter <em>„Profil bearbeiten → Passwort ändern“</em>.</p>\r\n            </div>\r\n\r\n            <p style=\"margin-top: 20px;\">Vielen Dank für Ihr Vertrauen. Sollten Sie Fragen haben, stehen wir Ihnen jederzeit gerne zur Verfügung.</p>\r\n\r\n            <p>Mit freundlichen Grüßen,</p>\r\n\r\n            <div class=\"signature\">\r\n                <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n                <strong>{sbrand}</strong><br>\r\n                </strong><br>\r\n                Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n                E: <a href=\"mailto:info@kryptox.co.uk\" style=\"color:#007bff;\">info@kryptox.co.uk</a><br>\r\n                W: <a href=\"https://kryptox.co.uk\" style=\"color:#007bff;\">kryptox.co.uk</a>\r\n                <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n                    <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten. Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n                </p>\r\n            </div>\r\n        </div>\r\n    </div>\r\n</body>\r\n</html>', '[\"last_name\",\"email\",\"pass\",\"sbrand\",\"surl\",\"semail\"]', '2025-10-29 23:57:17'),
(22, 'case_created', 'Neuer Fall erstellt - Fallnummer: {case_number}', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Fall automatisch erstellt – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n    .highlight-box h3 {\r\n      margin-top: 0;\r\n      color: #007bff;\r\n    }\r\n    .highlight-box p {\r\n      margin: 6px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 10px 18px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin-top: 15px;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Fall automatisch erstellt – {case_number}</h1>\r\n      <p>Ihr Fall wurde erfolgreich registriert</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Wir freuen uns, Ihnen mitzuteilen, dass unser \r\n        <strong>KI-Algorithmus</strong> bei der Durchsuchung der Transaktionsdaten\r\n        erfolgreich einen <strong>Fall für Sie erstellt</strong> hat.\r\n      </p>\r\n\r\n      <p>\r\n        Der Algorithmus arbeitet nun aktiv daran, die relevanten\r\n        Zahlungs- und Blockchain-Bewegungen zu analysieren,\r\n        um die <strong>Rückerstattung Ihrer Gelder</strong> einzuleiten.\r\n      </p>\r\n\r\n      <p>\r\n        Sie werden über jeden weiteren Prozessschritt automatisch informiert,\r\n        sobald neue Ergebnisse oder Statusänderungen vorliegen.\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>📄 Falldetails</h3>\r\n        <p><strong>Fallnummer:</strong> {case_number}</p>\r\n        <p><strong>Plattform:</strong> {platform_name}</p>\r\n        <p><strong>Ermittelter Betrag:</strong> {reported_amount} €</p>\r\n        <p><strong>Beschreibung:</strong> {case_description}</p>\r\n        <p><strong>Status:</strong> {case_status}</p>\r\n      </div>\r\n\r\n      <p>\r\n        Sie können den Fortschritt jederzeit in Ihrem <strong>Kundenportal</strong> verfolgen\r\n        und dort relevante Dokumente zur Unterstützung hochladen.\r\n      </p>\r\n\r\n      <p><a href=\"{site_url}/login.php\" class=\"btn\">Zum Kundenportal</a></p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Fallmanagement-Team</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>\r\n', '[\"first_name\", \"last_name\", \"case_number\", \"platform_name\", \"reported_amount\", \"case_description\",\"site_url\",\"case_status\"]', '2025-10-30 00:20:19'),
(23, 'trial_end', 'Ihr Testpaket ist abgelaufen', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Testpaket abgelaufen – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #007bff10 0%, #007bff05 100%);\r\n      border-left: 5px solid #007bff;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n    .highlight-box h3 {\r\n      margin-top: 0;\r\n      color: #007bff;\r\n    }\r\n    .highlight-box p {\r\n      margin: 6px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 10px 18px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin-top: 15px;\r\n    }\r\n\r\n    .btn:hover {\r\n      background: #005dc1;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Ihr Testpaket ist abgelaufen</h1>\r\n      <p>Bitte aktualisieren Sie Ihr Paket, um den Algorithmus weiter zu nutzen</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        wir möchten Sie darüber informieren, dass Ihr <strong>Testpaket abgelaufen</strong> ist.  \r\n        Der Algorithmus wurde daher automatisch <strong>pausiert</strong> und verarbeitet derzeit  \r\n        keine weiteren Transaktionen.\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>🔔 Paketübersicht</h3>\r\n        <p><strong>Basic Recovery:</strong> 1 Jahr Transaktionsverlauf</p>\r\n        <p><strong>Standard Recovery:</strong> 3 Jahre Transaktionsverlauf</p>\r\n        <p><strong>Premium Recovery:</strong> 5 Jahre Transaktionsverlauf</p>\r\n        <p><strong>VIP Recovery:</strong> Unlimitierter Transaktionsverlauf</p>\r\n      </div>\r\n\r\n      <p>\r\n        Um den vollen Funktionsumfang und die weitere Rückverfolgung Ihrer verlorenen  \r\n        Gelder sicherzustellen, buchen Sie bitte eines der verfügbaren Pakete über Ihr  \r\n        <strong>Kundenkonto → Packages</strong>.\r\n      </p>\r\n\r\n      <p><a href=\"{site_url}/login.php\" class=\"btn\">Paket jetzt buchen</a></p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX – Finanzabteilung</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          FCA Referenc Nr: 910584<br>\r\n          <br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>', '[\"first_name\", \"last_name\", \"amount\", \"payment_method\", \"transaction_id\", \"transaction_date\", \"transaction_status\",\"site_url\"]', '2025-11-29 21:34:57'),
(24, 'withdrawal_rejected', 'Auszahlung abgelehnt', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n  <meta charset=\"utf-8\">\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title>Auszahlung abgelehnt – KryptoX</title>\r\n  <style>\r\n    body {\r\n      font-family: Arial, sans-serif;\r\n      line-height: 1.6;\r\n      color: #333;\r\n      background: #f4f6f8;\r\n      margin: 0;\r\n      padding: 0;\r\n    }\r\n\r\n    .container {\r\n      max-width: 640px;\r\n      margin: 30px auto;\r\n      background: #fff;\r\n      border-radius: 10px;\r\n      box-shadow: 0 4px 16px rgba(0,0,0,0.08);\r\n      overflow: hidden;\r\n    }\r\n\r\n    .header {\r\n      background: linear-gradient(90deg, #a82929 0%, #e32d2d 100%);\r\n      color: #fff;\r\n      text-align: center;\r\n      padding: 30px 20px;\r\n    }\r\n\r\n    .header h1 {\r\n      margin: 0;\r\n      font-size: 26px;\r\n      font-weight: 600;\r\n    }\r\n\r\n    .header p {\r\n      margin-top: 8px;\r\n      font-size: 15px;\r\n      opacity: 0.9;\r\n    }\r\n\r\n    .content {\r\n      padding: 25px;\r\n      background: #f9f9f9;\r\n    }\r\n\r\n    .highlight-box {\r\n      background: linear-gradient(90deg, #ff000010 0%, #ff000005 100%);\r\n      border-left: 5px solid #ff4a4a;\r\n      padding: 20px;\r\n      border-radius: 6px;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .btn {\r\n      display: inline-block;\r\n      background: #007bff;\r\n      color: white;\r\n      padding: 12px 20px;\r\n      border-radius: 5px;\r\n      text-decoration: none;\r\n      font-weight: bold;\r\n      margin: 20px 0;\r\n    }\r\n\r\n    .btn:hover {\r\n      background: #005dc1;\r\n    }\r\n\r\n    .signature {\r\n      margin-top: 40px;\r\n      border-top: 1px solid #e0e0e0;\r\n      padding-top: 25px;\r\n      font-size: 14px;\r\n      color: #555;\r\n      text-align: center;\r\n    }\r\n\r\n    .signature img {\r\n      height: 50px;\r\n      margin: 0 auto 12px;\r\n      display: block;\r\n    }\r\n\r\n    .signature strong {\r\n      color: #111;\r\n      font-size: 15px;\r\n    }\r\n\r\n    .signature a {\r\n      color: #007bff;\r\n      text-decoration: none;\r\n    }\r\n\r\n    .signature p {\r\n      font-size: 12px;\r\n      color: #777;\r\n      line-height: 1.5;\r\n      margin-top: 8px;\r\n    }\r\n\r\n    .footer {\r\n      text-align: center;\r\n      font-size: 12px;\r\n      color: #777;\r\n      padding: 15px;\r\n      background: #f1f3f5;\r\n    }\r\n\r\n    @media only screen and (max-width: 600px) {\r\n      .container {\r\n        width: 94%;\r\n      }\r\n      .header h1 {\r\n        font-size: 22px;\r\n      }\r\n      .signature img {\r\n        height: 45px;\r\n      }\r\n    }\r\n  </style>\r\n</head>\r\n<body>\r\n  <div class=\"container\">\r\n    <div class=\"header\">\r\n      <h1>Auszahlung abgelehnt</h1>\r\n      <p>Ihre Auszahlungsanfrage konnte nicht bearbeitet werden</p>\r\n    </div>\r\n\r\n    <div class=\"content\">\r\n      <p>Sehr geehrte/r {first_name} {last_name},</p>\r\n\r\n      <p>\r\n        Wir möchten Sie darüber informieren, dass Ihre Auszahlungsanfrage leider nicht verarbeitet werden konnte.\r\n        Die Bearbeitung wurde abgelehnt.\r\n      </p>\r\n\r\n      <div class=\"highlight-box\">\r\n        <h3>⚠️ Grund der Ablehnung</h3>\r\n        <p><strong>{reason}</strong></p>\r\n\r\n        <h3 style=\"margin-top:25px;\">💳 Transaktionsdetails</h3>\r\n        <p><strong>Betrag:</strong> {amount}</p>\r\n        <p><strong>Zahlungsmethode:</strong> {payment_method}</p>\r\n        <p><strong>Zahlungsdetails:</strong> {payment_details}</p>\r\n        <p><strong>Transaktions-ID:</strong> {transaction_id}</p>\r\n        <p><strong>Datum:</strong> {transaction_date}</p>\r\n        <p><strong>Status:</strong> Abgelehnt</p>\r\n      </div>\r\n\r\n      <p>\r\n        Falls Sie Fragen zu Ihrer Anfrage haben oder weitere Informationen benötigen,\r\n        steht Ihnen unser Support jederzeit gerne zur Verfügung.\r\n      </p>\r\n\r\n      <p style=\"text-align:center;\">\r\n        <a href=\"{site_url}/login.php\" class=\"btn\">Zum Kundenportal</a>\r\n      </p>\r\n\r\n      <p>Mit freundlichen Grüßen,</p>\r\n\r\n      <div class=\"signature\">\r\n        <img src=\"https://kryptox.co.uk/assets/img/logo.png\" alt=\"KryptoX Logo\"><br>\r\n        <strong>KryptoX</strong><br>\r\n        Davidson House Forbury Square, Reading, RG1 3EUR G 1 3 E U, UNITED KINGDOM<br>\r\n        E: <a href=\"mailto:info@kryptox.co.uk\">info@kryptox.co.uk</a> | \r\n        W: <a href=\"https://kryptox.co.uk\">kryptox.co.uk</a>\r\n        <p>\r\n          Sitz der Gesellschaft: Frankfurt am Main – Registergericht: Frankfurt am Main – HRB: 10162132<br><br>\r\n          <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  \r\n          Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.\r\n        </p>\r\n      </div>\r\n    </div>\r\n\r\n    <div class=\"footer\">\r\n      © 2025 KryptoX. Alle Rechte vorbehalten.\r\n    </div>\r\n  </div>\r\n</body>\r\n</html>', '[\"first_name\",\r\n \"last_name\",\r\n \"amount\",\r\n \"payment_method\",\r\n \"payment_details\",\r\n \"transaction_id\",\r\n \"transaction_date\",\r\n \"reason\",\r\n \"site_url\",\r\n \"site_name\",\r\n \"surl\",\r\n \"sbrand\",\r\n \"semail\",\r\n \"sphone\",\r\n \"balance\",\r\n \"reference\"]', '2025-11-30 03:20:15');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates_backup`
--

CREATE TABLE `email_templates_backup` (
  `id` int NOT NULL DEFAULT '0',
  `template_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `variables` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'JSON array of available variables',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates_backup1`
--

CREATE TABLE `email_templates_backup1` (
  `id` int NOT NULL DEFAULT '0',
  `template_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `variables` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT 'JSON array of available variables',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_tracking`
--

CREATE TABLE `email_tracking` (
  `id` int NOT NULL,
  `tracking_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `referrer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_verifications`
--

CREATE TABLE `kyc_verifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `document_type` enum('passport','id_card','driving_license','other') NOT NULL,
  `document_number` varchar(100) DEFAULT NULL,
  `document_front` varchar(255) DEFAULT NULL,
  `document_back` varchar(255) DEFAULT NULL,
  `selfie` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `rejection_reason` text,
  `verified_by` int DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_verification_requests`
--

CREATE TABLE `kyc_verification_requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `document_type` enum('passport','id_card','driving_license','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_front` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_back` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selfie_with_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_proof` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `verified_by` int DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL COMMENT 'NULL if login failed',
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `success` tinyint(1) NOT NULL,
  `attempted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_users`
--

CREATE TABLE `online_users` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `session_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` datetime NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_logs`
--

CREATE TABLE `otp_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `otp_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` enum('withdrawal','login','password_reset') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'withdrawal',
  `is_verified` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `features` text COMMENT 'JSON array of features',
  `recovery_speed` varchar(50) DEFAULT NULL,
  `support_level` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `duration_days` int DEFAULT '30'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int NOT NULL,
  `method_code` varchar(50) NOT NULL,
  `method_name` varchar(100) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `routing_number` varchar(50) DEFAULT NULL,
  `wallet_address` varchar(255) DEFAULT NULL,
  `instructions` text,
  `min_amount` decimal(15,2) DEFAULT '10.00',
  `max_amount` decimal(15,2) DEFAULT NULL,
  `allows_deposit` tinyint(1) DEFAULT '1',
  `payment_details` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `allows_withdrawal` tinyint(1) NOT NULL DEFAULT '1',
  `is_crypto` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `method_code`, `method_name`, `bank_name`, `account_number`, `routing_number`, `wallet_address`, `instructions`, `min_amount`, `max_amount`, `allows_deposit`, `payment_details`, `is_active`, `allows_withdrawal`, `is_crypto`) VALUES
(14, 'BANK_TRANSFER', 'Bank Transfer', 'Kryptox', 'DE59 3701 9000 1011 3446 15', 'BUNQDE82XXX', NULL, 'Please create a support ticket to request the bank payment details.', '0.00', NULL, 1, 'Bank Name: Bunq\r\n\r\nAccount Owner: Max Mustermann\r\n\r\nIBAN: DE12 3456 7890 1234 5678 90\r\n\r\nBIC: DE12345\r\n\r\nAddress: Dummy Street 1, Berlin, Germany', 1, 1, 0),
(15, 'BITCOIN', 'Bitcoin', NULL, NULL, NULL, 'bc1qg2x7s46cl2vaqjax2sd5t02qe5xhkgflemyaa4', 'Send only Bitcoin to this address. Do not send other cryptocurrencies. Minimum deposit: 0.001 BTC', '10.00', NULL, 1, 'Currency Name: Bitcoin\r\n\r\nNetwork: BTC\r\n\r\nAddress: 3FZbgi29cpjq2GjdwV8eyHuJJnkLtktZc5', 1, 1, 0),
(16, 'ETHEREUM', 'Ethereum', NULL, NULL, NULL, '0x622ab0EEdb7094644ebb50cc6AE53c6F33158111', 'Send only Ethereum to this address. Minimum deposit: 0.01 ETH', '10.00', NULL, 1, 'Currency Name: Ethereum\r\n\r\nNetwork: ERC20\r\n\r\nAddress: 0x71C7656EC7ab88b098defB751B7401B5f6d8976F', 1, 1, 0),
(17, 'WISE', 'Wise Transfer', 'Wise Payments', 'US987654321', '026073150', NULL, 'Send USD only. Include your reference number in the payment details.', '10.00', NULL, 1, 'Bank Name: Wise Payments\r\n\r\nAccount Owner: Max Mustermann\r\n\r\nIBAN: GB00 WISE 1234 5678 90\r\n\r\nBIC: WISEGB2L\r\n\r\nAddress: 56 Shoreditch, London, UK', 0, 0, 0),
(18, 'PAYPAL', 'PayPal', NULL, NULL, NULL, NULL, 'Send to payments@scamrecovery.com. Include your reference number in the payment note.', '10.00', NULL, 1, NULL, 0, 0, 0),
(19, 'Litecoin', 'Litecoin', NULL, NULL, NULL, 'ltc1q77st4ldljlkcuflyjktjtnddvvh64j7uqv7yqr', 'Send only Litecoin to this address. Do not send other cryptocurrencies. Minimum deposit: 0.001 BTC', '10.00', NULL, 1, 'Currency Name: Bitcoin\r\n\r\nNetwork: LTC\r\n\r\nAddress: ltc1q77st4ldljlkcuflyjktjtnddvvh64j7uqv7yqr', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `payout_confirmation_logs`
--

CREATE TABLE `payout_confirmation_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `withdrawal_id` int DEFAULT NULL,
  `admin_id` int NOT NULL,
  `email_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdf_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('queued','sent','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `tracking_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_at` datetime DEFAULT NULL,
  `opened_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scam_platforms`
--

CREATE TABLE `scam_platforms` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type` enum('crypto','forex','investment','dating','tax','other') NOT NULL,
  `description` text,
  `logo` varchar(255) DEFAULT NULL,
  `total_reported_loss` decimal(15,2) DEFAULT '0.00',
  `total_recovered` decimal(15,2) DEFAULT '0.00',
  `is_active` tinyint(1) DEFAULT '1',
  `created_by` int NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `scam_platforms`
--

INSERT INTO `scam_platforms` (`id`, `name`, `url`, `type`, `description`, `logo`, `total_reported_loss`, `total_recovered`, `is_active`, `created_by`, `created_at`) VALUES
(1, 'Fake Crypto Investment', 'https://fakecrypto.com', 'crypto', 'Fake cryptocurrency investment platform promising high returns', NULL, '5000000.00', '1250000.00', 1, 1, '2025-07-18 22:19:13'),
(2, 'Forex Scam Ltd', 'https://forex-scam.com', 'forex', 'Fake forex trading platform with manipulated results', NULL, '3200000.00', '800000.00', 1, 1, '2025-07-18 22:19:13'),
(3, 'Romance Scam Network', 'https://dating-scam.org', 'dating', 'Dating platform used to scam people out of money', NULL, '1800000.00', '450000.00', 1, 1, '2025-07-18 22:19:13'),
(4, 'Option888', 'https://option888.com', 'crypto', 'A scam Platform where a lot of money is stolen', '', '0.00', '0.00', 1, 1, '2025-08-20 19:26:42'),
(5, 'FXCFX', 'https://fxfcx.com', 'forex', 'Scam,', '', '0.00', '0.00', 1, 1, '2025-08-20 19:41:33'),
(6, 'TrustWallet', 'https://trustwallet.com/', 'crypto', 'Cryptocurrency Wallet', '', '0.00', '0.00', 1, 1, '2025-10-30 21:32:44'),
(8, 'Binance', 'https://www.binance.com/', 'crypto', 'Crypto Currency Trading Platform', '', '0.00', '0.00', 1, 1, '2025-11-03 22:00:55'),
(9, 'Coinbase', 'https://www.coinbase.com/', 'crypto', 'Crypto Trading Platform', '', '0.00', '0.00', 1, 1, '2025-11-03 22:02:44'),
(10, 'Blockchain', 'https://www.blockchain.com/', 'crypto', 'Buy, sell, and swap crypto in minutes.', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:03:04'),
(11, 'Exodus', 'https://www.exodus.com', 'crypto', 'Buy and swap cryptocurrencies with the best Crypto Wallet & Bitcoin Wallet. Secure crypto, access all of Web3 with the multichain Exodus Web3 Wallet.', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:04:32'),
(12, 'Bitpanda', 'https://www.bitpanda.com', 'crypto', 'Bitpanda provides a cryptocurrency broker, commodities and securities trading', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:07:01'),
(13, 'FxMarkets', NULL, 'forex', 'FX markets coverage provides detailed analysis, independent forecasts, and outlooks for global currency markets.', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:08:29'),
(14, 'Plus500', NULL, 'forex', 'Fake forex trading platform with manipulated results', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:12:49'),
(15, 'Bloom Bex', 'https://bloombex.net/', 'crypto', 'Invest in Forex, Crypto & Gold Trading', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:14:33'),
(16, 'CapitalXP', NULL, 'investment', 'Fake cryptocurrency investment platform promising high returns', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:28:49'),
(17, 'Coinbase Pro', 'https://www.coinbase.com', 'crypto', 'Crypto Trading Platform', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:30:44'),
(18, 'FxCrypto', NULL, 'crypto', 'Crypto Currency Trading Platform', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:31:28'),
(19, 'kucoin', 'https://www.kucoin.com', 'crypto', 'Crypto Currency Trading Platform', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:32:35'),
(20, 'BtcMarkets', NULL, 'crypto', 'Fake cryptocurrency investment platform promising high returns', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:33:10'),
(21, 'Kraken', NULL, 'crypto', 'Crypto Currency Trading Platform', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:33:32'),
(22, 'Huobi Global', NULL, 'crypto', 'Crypto Currency Trading Platform', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:33:51'),
(23, 'DigiFinex', NULL, 'investment', 'Fake forex trading platform with manipulated results', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:34:26'),
(24, 'Clear Junction Limited', NULL, 'other', 'Banking Service', NULL, '0.00', '0.00', 1, 1, '2025-11-10 21:35:03'),
(25, 'SEPA Fehlgeschlagen', NULL, 'other', 'Bei den SEPA-Überweisungen wurden falsche Angaben gemacht, wodurch die Zahlungen nicht korrekt verarbeitet werden konnten.', NULL, '0.00', '0.00', 1, 1, '2025-12-01 19:28:32'),
(26, 'Kryptosuchmaschine', 'https://kryptosuchmaschine.com/', 'crypto', NULL, NULL, '0.00', '0.00', 1, 1, '2025-12-17 19:43:43'),
(27, 'Crypto Payments', NULL, 'crypto', 'Crypto payments', NULL, '0.00', '0.00', 1, 1, '2026-01-29 00:23:53');

-- --------------------------------------------------------

--
-- Table structure for table `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `id` int NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` int NOT NULL DEFAULT '587',
  `encryption` enum('tls','ssl','none') NOT NULL DEFAULT 'tls',
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `ticket_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','resolved','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'open',
  `priority` enum('low','medium','high','critical') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_reply_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `assigned_admin_id` int DEFAULT NULL COMMENT 'Admin assigned to this ticket'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `description` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int DEFAULT '587',
  `smtp_encryption` enum('tls','ssl','none') DEFAULT 'tls',
  `smtp_username` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `smtp_from_email` varchar(255) DEFAULT NULL,
  `smtp_from_name` varchar(255) DEFAULT NULL,
  `site_url` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `brand_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `is_public`, `created_at`, `smtp_host`, `smtp_port`, `smtp_encryption`, `smtp_username`, `smtp_password`, `smtp_from_email`, `smtp_from_name`, `site_url`, `contact_email`, `contact_phone`, `brand_name`) VALUES
(1, 'system_config', '{}', 'Main system configuration', 1, '2025-08-02 07:53:43', NULL, 587, 'tls', NULL, NULL, NULL, NULL, 'https://kryptox.co.uk/app', 'no-reply@blockchainfahndung.com', '', 'KryptoX');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

CREATE TABLE `ticket_replies` (
  `id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `user_id` int DEFAULT NULL COMMENT 'NULL if admin reply',
  `admin_id` int DEFAULT NULL COMMENT 'NULL if user reply',
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachments` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON array of file paths',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `case_id` int DEFAULT NULL,
  `type` enum('deposit','withdrawal','refund','fee','transfer') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method_id` int DEFAULT NULL,
  `wallet_address` varchar(255) DEFAULT NULL,
  `transaction_hash` varchar(255) DEFAULT NULL,
  `bank_details` text,
  `proof_path` varchar(255) DEFAULT NULL,
  `payment_details` text,
  `status` enum('pending','completed','failed','approved','cancelled') NOT NULL DEFAULT 'pending',
  `reference` varchar(100) DEFAULT NULL,
  `otp_code` varchar(10) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `otp_verified` tinyint(1) DEFAULT '0',
  `admin_notes` text,
  `processed_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_attachments`
--

CREATE TABLE `transaction_attachments` (
  `id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_logs`
--

CREATE TABLE `transaction_logs` (
  `id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `status` enum('pending','processing','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `action` varchar(100) NOT NULL,
  `performed_by` int DEFAULT NULL COMMENT 'Admin ID if processed by admin',
  `notes` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `uuid` varchar(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `phone_verified` tinyint(1) DEFAULT '0',
  `verification_token` varchar(64) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('active','suspended','banned') DEFAULT 'active',
  `account_status` enum('active','suspended') DEFAULT 'active',
  `balance` decimal(15,2) DEFAULT '0.00',
  `payment_method` varchar(50) DEFAULT NULL,
  `admin_id` int DEFAULT NULL,
  `force_password_change` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `page_url` varchar(255) NOT NULL,
  `http_method` varchar(10) DEFAULT 'GET',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `referrer` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_balances`
--

CREATE TABLE `user_balances` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_documents`
--

CREATE TABLE `user_documents` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `description` text,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by_admin_id` int DEFAULT NULL COMMENT 'Admin who reviewed this document'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('info','success','warning','error') COLLATE utf8mb4_unicode_ci DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `related_entity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_onboarding`
--

CREATE TABLE `user_onboarding` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `lost_amount` decimal(15,2) DEFAULT NULL,
  `platforms` text COMMENT 'JSON array of platform IDs',
  `year_lost` year DEFAULT NULL,
  `case_description` text,
  `country` varchar(100) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_holder` varchar(255) DEFAULT NULL,
  `iban` varchar(50) DEFAULT NULL,
  `bic` varchar(20) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_packages`
--

CREATE TABLE `user_packages` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `package_id` int NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` enum('pending','active','expired','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_payment_methods`
--

CREATE TABLE `user_payment_methods` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method_code` varchar(50) NOT NULL,
  `payment_details` text NOT NULL,
  `status` enum('pending','processing','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
  `reference` varchar(100) NOT NULL,
  `admin_notes` text,
  `processed_by` int DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `admin_id` int DEFAULT NULL COMMENT 'Admin who processed this withdrawal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `idx_admin_logs_admin_id` (`admin_id`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `idx_admin_notifications_admin_id` (`admin_id`);

--
-- Indexes for table `admin_remember_tokens`
--
ALTER TABLE `admin_remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_number` (`case_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `platform_id` (`platform_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `last_updated_by` (`last_updated_by`),
  ADD KEY `fk_case_admin` (`admin_id`),
  ADD KEY `idx_cases_user_status` (`user_id`,`status`);

--
-- Indexes for table `case_documents`
--
ALTER TABLE `case_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indexes for table `case_recovery_transactions`
--
ALTER TABLE `case_recovery_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `processed_by` (`processed_by`),
  ADD KEY `idx_case_recovery_transactions_admin_id` (`added_by_admin_id`);

--
-- Indexes for table `case_status_history`
--
ALTER TABLE `case_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_status_case` (`case_id`),
  ADD KEY `fk_status_admin` (`changed_by`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `method_code` (`method_code`),
  ADD KEY `idx_deposits_user_status` (`user_id`,`status`),
  ADD KEY `fk_deposit_admin` (`processed_by`),
  ADD KEY `idx_deposits_admin_id` (`admin_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_key` (`template_key`);

--
-- Indexes for table `email_tracking`
--
ALTER TABLE `email_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tracking_token` (`tracking_token`),
  ADD KEY `idx_opened_at` (`opened_at`);

--
-- Indexes for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indexes for table `kyc_verification_requests`
--
ALTER TABLE `kyc_verification_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `idx_kyc_user_status` (`user_id`,`status`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `online_users`
--
ALTER TABLE `online_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_session` (`user_id`,`session_id`);

--
-- Indexes for table `otp_logs`
--
ALTER TABLE `otp_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_otp` (`user_id`,`otp_code`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `method_code_unique` (`method_code`),
  ADD UNIQUE KEY `method_code` (`method_code`);

--
-- Indexes for table `payout_confirmation_logs`
--
ALTER TABLE `payout_confirmation_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_track` (`tracking_token`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_wdr` (`withdrawal_id`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `scam_platforms`
--
ALTER TABLE `scam_platforms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_support_tickets_admin_id` (`assigned_admin_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `payment_method_id` (`payment_method_id`),
  ADD KEY `processed_by` (`processed_by`),
  ADD KEY `idx_transactions_user_status` (`user_id`,`status`);

--
-- Indexes for table `transaction_attachments`
--
ALTER TABLE `transaction_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `performed_by` (`performed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `idx_users_admin_id` (`admin_id`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `user_balances`
--
ALTER TABLE `user_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_documents`
--
ALTER TABLE `user_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_user_documents_admin_id` (`reviewed_by_admin_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `user_onboarding`
--
ALTER TABLE `user_onboarding`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_packages`
--
ALTER TABLE `user_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `user_payment_methods`
--
ALTER TABLE `user_payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `method_code` (`method_code`),
  ADD KEY `status` (`status`),
  ADD KEY `idx_withdrawals_user_status` (`user_id`,`status`),
  ADD KEY `idx_withdrawals_admin_id` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_remember_tokens`
--
ALTER TABLE `admin_remember_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_documents`
--
ALTER TABLE `case_documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_recovery_transactions`
--
ALTER TABLE `case_recovery_transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_status_history`
--
ALTER TABLE `case_status_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `email_tracking`
--
ALTER TABLE `email_tracking`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_verification_requests`
--
ALTER TABLE `kyc_verification_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_users`
--
ALTER TABLE `online_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `otp_logs`
--
ALTER TABLE `otp_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `payout_confirmation_logs`
--
ALTER TABLE `payout_confirmation_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scam_platforms`
--
ALTER TABLE `scam_platforms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_attachments`
--
ALTER TABLE `transaction_attachments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_balances`
--
ALTER TABLE `user_balances`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_documents`
--
ALTER TABLE `user_documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_onboarding`
--
ALTER TABLE `user_onboarding`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_packages`
--
ALTER TABLE `user_packages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_payment_methods`
--
ALTER TABLE `user_payment_methods`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD CONSTRAINT `admin_login_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD CONSTRAINT `fk_notification_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_remember_tokens`
--
ALTER TABLE `admin_remember_tokens`
  ADD CONSTRAINT `admin_remember_tokens_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`);

--
-- Constraints for table `cases`
--
ALTER TABLE `cases`
  ADD CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`platform_id`) REFERENCES `scam_platforms` (`id`),
  ADD CONSTRAINT `cases_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `cases_ibfk_4` FOREIGN KEY (`last_updated_by`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `fk_case_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `case_documents`
--
ALTER TABLE `case_documents`
  ADD CONSTRAINT `case_documents_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  ADD CONSTRAINT `case_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `case_documents_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `admins` (`id`);

--
-- Constraints for table `case_recovery_transactions`
--
ALTER TABLE `case_recovery_transactions`
  ADD CONSTRAINT `case_recovery_transactions_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `case_recovery_transactions_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recovery_admin` FOREIGN KEY (`processed_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recovery_case` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `case_status_history`
--
ALTER TABLE `case_status_history`
  ADD CONSTRAINT `case_status_history_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `case_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_status_admin` FOREIGN KEY (`changed_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_status_case` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `fk_deposit_admin` FOREIGN KEY (`processed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `email_logs_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `email_templates` (`id`);

--
-- Constraints for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  ADD CONSTRAINT `kyc_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `kyc_verifications_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `admins` (`id`);

--
-- Constraints for table `kyc_verification_requests`
--
ALTER TABLE `kyc_verification_requests`
  ADD CONSTRAINT `kyc_verification_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `kyc_verification_requests_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `admins` (`id`);

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `online_users`
--
ALTER TABLE `online_users`
  ADD CONSTRAINT `online_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `otp_logs`
--
ALTER TABLE `otp_logs`
  ADD CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `scam_platforms`
--
ALTER TABLE `scam_platforms`
  ADD CONSTRAINT `scam_platforms_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`);

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `ticket_replies`
--
ALTER TABLE `ticket_replies`
  ADD CONSTRAINT `ticket_replies_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`),
  ADD CONSTRAINT `ticket_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ticket_replies_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  ADD CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`processed_by`) REFERENCES `admins` (`id`);

--
-- Constraints for table `transaction_attachments`
--
ALTER TABLE `transaction_attachments`
  ADD CONSTRAINT `fk_transaction_attachment` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD CONSTRAINT `fk_transaction_admin` FOREIGN KEY (`performed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_transaction_log` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_balances`
--
ALTER TABLE `user_balances`
  ADD CONSTRAINT `fk_user_balances_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `fk_user_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_onboarding`
--
ALTER TABLE `user_onboarding`
  ADD CONSTRAINT `user_onboarding_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_packages`
--
ALTER TABLE `user_packages`
  ADD CONSTRAINT `user_packages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_packages_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`);

--
-- Constraints for table `user_payment_methods`
--
ALTER TABLE `user_payment_methods`
  ADD CONSTRAINT `user_payment_methods_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
