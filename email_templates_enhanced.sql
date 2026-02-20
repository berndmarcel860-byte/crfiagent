-- =====================================================
-- ENHANCED email_templates TABLE
-- Created: 2026-02-20
-- Purpose: Enhanced templates with dynamic variables and tracking
-- =====================================================

-- Drop and recreate email_templates with enhanced structure
DROP TABLE IF EXISTS `email_templates`;

CREATE TABLE `email_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_key` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `variables` text COMMENT 'JSON array of available variables',
  `description` varchar(500) DEFAULT NULL COMMENT 'Template description',
  `category` varchar(50) DEFAULT 'general' COMMENT 'Template category',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- TEMPLATE 1: Onboarding Complete
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `description`, `category`) VALUES 
('onboarding_complete', 
'Willkommen bei {{brand_name}} - Registrierung abgeschlossen',
'<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Willkommen bei {{brand_name}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">{{brand_name}}</h1>
                            <p style="color: #ffffff; margin: 10px 0 0 0; opacity: 0.9;">Willkommen in Ihrem Account</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #2c3e50; margin: 0 0 20px 0;">Herzlichen Glückwunsch, {{user_first_name}}!</h2>
                            
                            <p style="color: #555; line-height: 1.6; margin: 0 0 15px 0;">
                                Ihre Registrierung bei <strong>{{brand_name}}</strong> wurde erfolgreich abgeschlossen. 
                                Wir freuen uns, Sie in unserer Community begrüßen zu dürfen!
                            </p>
                            
                            <!-- Account Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f9fa; border-left: 4px solid #2950a8; margin: 25px 0; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #2950a8; margin: 0 0 15px 0; font-size: 18px;">👤 Ihre Account-Details</h3>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Name:</strong> {{user_first_name}} {{user_last_name}}
                                        </p>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>E-Mail:</strong> {{user_email}}
                                        </p>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Mitglied seit:</strong> {{user_created_at}}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Payment Methods (if available) -->
                            {{#if has_bank_account}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #e8f5e9; border-left: 4px solid #28a745; margin: 25px 0; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #28a745; margin: 0 0 15px 0; font-size: 18px;">🏦 Hinterlegtes Bankkonto</h3>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Bank:</strong> {{bank_name}}
                                        </p>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Kontoinhaber:</strong> {{account_holder}}
                                        </p>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>IBAN:</strong> {{iban}}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            {{/if}}
                            
                            {{#if has_crypto_wallet}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #fff3e0; border-left: 4px solid #ff9800; margin: 25px 0; border-radius: 4px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #ff9800; margin: 0 0 15px 0; font-size: 18px;">💰 Hinterlegte Krypto-Wallet</h3>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Währung:</strong> {{cryptocurrency}}
                                        </p>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Netzwerk:</strong> {{network}}
                                        </p>
                                        <p style="margin: 8px 0; color: #555; word-break: break-all;">
                                            <strong>Wallet-Adresse:</strong> {{wallet_address}}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            {{/if}}
                            
                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{site_url}}/dashboard.php" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #2950a8, #2da9e3); color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                                            Zum Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #555; line-height: 1.6; margin: 20px 0 0 0; font-size: 14px;">
                                Bei Fragen stehen wir Ihnen gerne zur Verfügung unter 
                                <a href="mailto:{{contact_email}}" style="color: #2950a8;">{{contact_email}}</a>
                                {{#if contact_phone}}oder telefonisch unter {{contact_phone}}{{/if}}.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #dee2e6;">
                            <p style="margin: 0 0 10px 0; font-size: 16px; font-weight: bold; color: #2c3e50;">
                                {{brand_name}}
                            </p>
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #6c757d; white-space: pre-line;">
                                {{company_address}}
                            </p>
                            {{#if fca_reference_number}}
                            <p style="margin: 10px 0; font-size: 13px; color: #6c757d;">
                                FCA Referenznummer: {{fca_reference_number}}
                            </p>
                            {{/if}}
                            <p style="margin: 15px 0 0 0; font-size: 12px; color: #999;">
                                © {{current_year}} {{brand_name}}. Alle Rechte vorbehalten.
                            </p>
                        </td>
                    </tr>
                </table>
                
                <!-- Tracking Pixel -->
                <img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" alt="" style="display:block;" />
            </td>
        </tr>
    </table>
</body>
</html>',
'["user_first_name", "user_last_name", "user_email", "user_created_at", "brand_name", "company_address", "contact_email", "contact_phone", "fca_reference_number", "site_url", "current_year", "bank_name", "account_holder", "iban", "bic", "has_bank_account", "cryptocurrency", "network", "wallet_address", "has_crypto_wallet", "tracking_token"]',
'Welcome email sent after completing onboarding with payment methods',
'onboarding');

-- =====================================================
-- TEMPLATE 2: OTP Login Code
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `description`, `category`) VALUES 
('otp_login', 
'Ihr Anmeldecode für {{brand_name}}',
'<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #2950a8, #2da9e3); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0;">{{brand_name}}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #2c3e50; margin: 0 0 20px 0;">Ihr Einmalcode</h2>
                            <p style="color: #555; margin: 0 0 20px 0;">
                                Hallo {{user_first_name}},
                            </p>
                            <p style="color: #555; margin: 0 0 20px 0;">
                                Verwenden Sie diesen Code, um sich bei Ihrem Account anzumelden:
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center" style="background-color: #f8f9fa; padding: 30px; border-radius: 8px;">
                                        <div style="font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #2950a8;">
                                            {{otp_code}}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <p style="color: #dc3545; margin: 20px 0; font-weight: bold;">
                                ⏱️ Dieser Code ist 5 Minuten gültig.
                            </p>
                            <p style="color: #555; margin: 20px 0;">
                                🔒 Aus Sicherheitsgründen teilen Sie diesen Code niemals mit anderen.
                            </p>
                            <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">
                            <p style="color: #999; font-size: 12px; margin: 0;">
                                Wenn Sie sich nicht angemeldet haben, ignorieren Sie diese E-Mail.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #999;">
                                © {{current_year}} {{brand_name}}
                            </p>
                        </td>
                    </tr>
                </table>
                <img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" alt="" />
            </td>
        </tr>
    </table>
</body>
</html>',
'["user_first_name", "otp_code", "brand_name", "site_url", "current_year", "tracking_token"]',
'OTP code for login authentication',
'security');

-- =====================================================
-- TEMPLATE 3: Case Status Update
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `description`, `category`) VALUES 
('case_status_update', 
'Fallstatus aktualisiert - {{case_number}}',
'<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #2950a8, #2da9e3); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0;">Fallstatus aktualisiert</h1>
                            <p style="color: #ffffff; margin: 10px 0 0 0; opacity: 0.9;">{{case_number}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="color: #555; margin: 0 0 20px 0;">
                                Sehr geehrte/r {{user_first_name}} {{user_last_name}},
                            </p>
                            <p style="color: #555; margin: 0 0 30px 0;">
                                Der Status Ihres Falls wurde aktualisiert. Nachfolgend finden Sie die Details:
                            </p>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f9fa; border-left: 4px solid #2950a8; border-radius: 4px; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #2950a8; margin: 0 0 15px 0;">📋 Falldetails</h3>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Fallnummer:</strong> {{case_number}}
                                        </p>
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Neuer Status:</strong> <span style="color: #28a745; font-weight: bold;">{{new_status}}</span>
                                        </p>
                                        {{#if status_notes}}
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Hinweise:</strong> {{status_notes}}
                                        </p>
                                        {{/if}}
                                        <p style="margin: 8px 0; color: #555;">
                                            <strong>Aktualisiert am:</strong> {{update_date}}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{site_url}}/dashboard.php" style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #2950a8, #2da9e3); color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                            Fall Details ansehen
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="color: #555; margin: 20px 0 0 0;">
                                Bei Fragen kontaktieren Sie uns unter <a href="mailto:{{contact_email}}" style="color: #2950a8;">{{contact_email}}</a>.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center;">
                            <p style="margin: 0 0 10px 0; font-weight: bold; color: #2c3e50;">{{brand_name}}</p>
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #6c757d; white-space: pre-line;">{{company_address}}</p>
                            <p style="margin: 0; font-size: 12px; color: #999;">© {{current_year}} {{brand_name}}</p>
                        </td>
                    </tr>
                </table>
                <img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" alt="" />
            </td>
        </tr>
    </table>
</body>
</html>',
'["user_first_name", "user_last_name", "case_number", "new_status", "status_notes", "update_date", "brand_name", "company_address", "contact_email", "site_url", "current_year", "tracking_token"]',
'Notification when case status is updated',
'cases');

SELECT 'Email templates enhanced successfully!' AS status;
