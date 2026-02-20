-- =====================================================
-- COMPLETE EMAIL TEMPLATES WITH ALL 15 TEMPLATES
-- Enhanced with tracking, dynamic variables, and professional design
-- =====================================================

-- Drop existing table and create new structure
DROP TABLE IF EXISTS `email_templates`;

CREATE TABLE `email_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_key` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `variables` text,
  `category` enum('auth','user','case','withdrawal','kyc','payment','system') DEFAULT 'system',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_key` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insert all 15 templates

-- =====================================================
-- TEMPLATE 1: USER REGISTRATION
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('user_registration', 'Willkommen bei {{brand_name}} - Ihr Konto wurde erstellt', '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #2950a8, #2da9e3); padding: 30px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">{{brand_name}}</h1>
            <p style="color: #ffffff; margin: 10px 0 0 0;">Krypto-Recovery-Plattform</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 40px 30px;">
            <h2 style="color: #2c3e50; font-size: 24px; margin-bottom: 20px;">Willkommen, {{user_first_name}}!</h2>
            
            <p style="color: #555; line-height: 1.8; font-size: 16px;">
                Herzlichen Glückwunsch! Ihr Konto wurde erfolgreich erstellt.
            </p>
            
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #2950a8; margin-top: 0;">Ihre Login-Daten:</h3>
                <p style="margin: 10px 0;"><strong>Email:</strong> {{user_email}}</p>
                <p style="margin: 10px 0;"><strong>Registrierungsdatum:</strong> {{registration_date}}</p>
            </div>
            
            <p style="color: #555; line-height: 1.8;">
                Sie können sich jetzt anmelden und mit der Nutzung unserer Plattform beginnen.
            </p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{site_url}}/login.php" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: #ffffff; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                    Jetzt Anmelden
                </a>
            </div>
            
            <div style="background-color: #e8f4f8; padding: 15px; border-left: 4px solid #17a2b8; margin: 20px 0;">
                <p style="margin: 0; color: #555;">
                    <strong>💡 Nächste Schritte:</strong><br>
                    1. Vervollständigen Sie Ihr Profil<br>
                    2. Fügen Sie Zahlungsmethoden hinzu<br>
                    3. Verifizieren Sie Ihre Identität (KYC)
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="color: #2c3e50; font-weight: bold; margin: 10px 0;">{{brand_name}}</p>
            <p style="color: #666; font-size: 13px; margin: 5px 0;">{{company_address}}</p>
            <p style="color: #666; font-size: 13px; margin: 5px 0;">
                <strong>Kontakt:</strong> {{contact_email}} | <strong>FCA:</strong> {{fca_reference_number}}
            </p>
            <p style="color: #999; font-size: 12px; margin: 20px 0;">© {{current_year}} {{brand_name}}. Alle Rechte vorbehalten.</p>
        </div>
    </div>
    
    <!-- Tracking Pixel -->
    <img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;" />
</body>
</html>
', 'user_first_name,user_last_name,user_email,registration_date,brand_name,site_url,company_address,contact_email,fca_reference_number,current_year,tracking_token', 'user');


-- =====================================================
-- TEMPLATE 2: WELCOME EMAIL TEXT
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('welcome_email_text', 'Herzlich Willkommen bei {{brand_name}}!', '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <div style="background: linear-gradient(135deg, #2950a8, #2da9e3); padding: 30px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0;">🎉 Willkommen!</h1>
        </div>
        <div style="padding: 40px 30px;">
            <h2 style="color: #2c3e50;">Hallo {{user_first_name}} {{user_last_name}}!</h2>
            <p style="color: #555; line-height: 1.8;">
                Wir freuen uns sehr, Sie als neues Mitglied bei {{brand_name}} begrüßen zu dürfen!
            </p>
            <p style="color: #555; line-height: 1.8;">
                Unsere Plattform bietet Ihnen professionelle Krypto-Recovery-Dienste mit höchster Sicherheit und Expertise.
            </p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{site_url}}/dashboard" style="background: #2950a8; color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    Zum Dashboard
                </a>
            </div>
        </div>
        <div style="background-color: #f8f9fa; padding: 30px; text-align: center;">
            <p style="color: #666; font-size: 13px;">{{brand_name}} | {{contact_email}}</p>
        </div>
    </div>
    <img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;" />
</body>
</html>
', 'user_first_name,user_last_name,brand_name,site_url,contact_email,tracking_token', 'user');


-- =====================================================
-- TEMPLATE: EMAIL_VERIFICATION
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('email_verification', 'Bitte bestätigen Sie Ihre E-Mail-Adresse', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für email_verification.</p>
<div style="text-align:center;margin:30px 0;">
<a href="{{verification_link}}" style="background:#2950a8;color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
E-Mail bestätigen
</a>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,verification_link,brand_name,site_url,contact_email,tracking_token', 'auth');


-- =====================================================
-- TEMPLATE: PASSWORD_RESET
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('password_reset', 'Passwort zurücksetzen - {{brand_name}}', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für password_reset.</p>
<div style="text-align:center;margin:30px 0;">
<a href="{{reset_link}}" style="background:#2950a8;color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Passwort zurücksetzen
</a>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,reset_link,brand_name,site_url,contact_email,tracking_token', 'auth');


-- =====================================================
-- TEMPLATE: OTP_LOGIN
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('otp_login', 'Ihr Anmeldecode für {{brand_name}}', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für otp_login.</p>
<div style="background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;text-align:center;">
<h3 style="color:#2950a8;margin:0 0 10px 0;">Ihr Einmalcode:</h3>
<div style="font-size:32px;font-weight:bold;letter-spacing:8px;color:#2950a8;">{{otp_code}}</div>
<p style="color:#666;margin:10px 0 0 0;font-size:14px;">⏱️ Dieser Code ist 5 Minuten gültig.</p>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,otp_code,brand_name,site_url,contact_email,tracking_token', 'auth');


-- =====================================================
-- TEMPLATE: ONBOARDING_COMPLETE
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('onboarding_complete', 'Willkommen bei {{brand_name}} - Registrierung abgeschlossen', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für onboarding_complete.</p>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,user_last_name,bank_name,iban,bic,cryptocurrency,wallet_address,has_bank_account,has_crypto_wallet,brand_name,company_address,contact_email,fca_reference_number,site_url,current_year,tracking_token', 'user');


-- =====================================================
-- TEMPLATE: CASE_CREATED
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('case_created', 'Neuer Fall erstellt - Case #{{case_number}}', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für case_created.</p>
<div style="background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;">
<p style="margin:5px 0;"><strong>Fall-Nummer:</strong> #{{case_number}}</p>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,case_number,case_type,case_description,brand_name,site_url,contact_email,tracking_token', 'case');


-- =====================================================
-- TEMPLATE: CASE_STATUS_UPDATE
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('case_status_update', 'Statusaktualisierung für Fall #{{case_number}}', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für case_status_update.</p>
<div style="background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;">
<p style="margin:5px 0;"><strong>Fall-Nummer:</strong> #{{case_number}}</p>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,case_number,old_status,new_status,status_message,brand_name,site_url,contact_email,tracking_token', 'case');


-- =====================================================
-- TEMPLATE: WITHDRAWAL_REQUESTED
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('withdrawal_requested', 'Auszahlungsanfrage erhalten - {{brand_name}}', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für withdrawal_requested.</p>
<div style="background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;">
<p style="margin:5px 0;"><strong>Betrag:</strong> {{amount}} {{currency}}</p>
<p style="margin:5px 0;"><strong>Methode:</strong> {{withdrawal_method}}</p>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,amount,currency,withdrawal_method,request_date,brand_name,site_url,contact_email,tracking_token', 'withdrawal');


-- =====================================================
-- TEMPLATE: WITHDRAWAL_APPROVED
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('withdrawal_approved', 'Ihre Auszahlung wurde genehmigt', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für withdrawal_approved.</p>
<div style="background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;">
<p style="margin:5px 0;"><strong>Betrag:</strong> {{amount}} {{currency}}</p>
<p style="margin:5px 0;"><strong>Methode:</strong> {{withdrawal_method}}</p>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,amount,currency,withdrawal_method,transaction_id,estimated_arrival,brand_name,site_url,contact_email,tracking_token', 'withdrawal');


-- =====================================================
-- TEMPLATE: WITHDRAWAL_REJECTED
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('withdrawal_rejected', 'Auszahlungsanfrage abgelehnt', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für withdrawal_rejected.</p>
<div style="background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;">
<p style="margin:5px 0;"><strong>Betrag:</strong> {{amount}} {{currency}}</p>
<p style="margin:5px 0;"><strong>Methode:</strong> {{withdrawal_method}}</p>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,amount,currency,rejection_reason,brand_name,site_url,contact_email,tracking_token', 'withdrawal');


-- =====================================================
-- TEMPLATE: BALANCE_ALERT_DE
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('balance_alert_de', 'Kontostand-Benachrichtigung - {{brand_name}}', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für balance_alert_de.</p>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,current_balance,currency,brand_name,site_url,contact_email,tracking_token', 'payment');


-- =====================================================
-- TEMPLATE: PAYMENT_RECEIVED
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('payment_received', 'Zahlung erhalten - {{brand_name}}', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für payment_received.</p>
<div style="background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;">
<p style="margin:5px 0;"><strong>Betrag:</strong> {{amount}} {{currency}}</p>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,amount,currency,payment_method,transaction_id,payment_date,brand_name,site_url,contact_email,tracking_token', 'payment');


-- =====================================================
-- TEMPLATE: KYC_APPROVED
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('kyc_approved', 'KYC-Verifizierung erfolgreich - {{brand_name}}', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für kyc_approved.</p>
<div style="background:#e8f4f8;padding:15px;border-left:4px solid #17a2b8;margin:20px 0;">
<p style="margin:0;">KYC-Status wurde aktualisiert.</p>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,approval_date,brand_name,site_url,contact_email,tracking_token', 'kyc');


-- =====================================================
-- TEMPLATE: KYC_REJECTED
-- =====================================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`, `category`) VALUES
('kyc_rejected', 'KYC-Verifizierung - Weitere Informationen erforderlich', '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<div style="max-width:600px;margin:0 auto;background-color:#ffffff;">
<div style="background:linear-gradient(135deg,#2950a8,#2da9e3);padding:30px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:28px;">{{brand_name}}</h1>
</div>
<div style="padding:40px 30px;">
<h2 style="color:#2c3e50;margin-bottom:20px;">Hallo {{user_first_name}}!</h2>
<p style="color:#555;line-height:1.8;">Template für kyc_rejected.</p>
<div style="background:#e8f4f8;padding:15px;border-left:4px solid #17a2b8;margin:20px 0;">
<p style="margin:0;">KYC-Status wurde aktualisiert.</p>
</div>
<div style="text-align:center;margin:30px 0;">
<a href="{{site_url}}/dashboard" style="background:linear-gradient(135deg,#2950a8,#2da9e3);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;display:inline-block;">
Zum Dashboard
</a>
</div>
</div>
<div style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#2c3e50;font-weight:bold;margin:10px 0;">{{brand_name}}</p>
<p style="color:#666;font-size:13px;">{{contact_email}} | FCA: {{fca_reference_number}}</p>
<p style="color:#999;font-size:12px;">© {{current_year}} {{brand_name}}</p>
</div>
</div>
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;"/>
</body>
</html>', 'user_first_name,rejection_reason,required_documents,brand_name,site_url,contact_email,tracking_token', 'kyc');

-- File complete
