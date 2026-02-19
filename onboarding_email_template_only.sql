-- ================================================================
-- Onboarding Email Template Only (for existing setups)
-- Use this if you already have email_templates, settings, and smtp_settings tables
-- ================================================================

SET NAMES utf8mb4;

-- Insert or replace the onboarding completion email template
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`)
VALUES (
'onboarding_complete',
'Willkommen bei {{company_name}} - Registrierung abgeschlossen',
'<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung abgeschlossen</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: white; padding: 0; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin: 10px 0 0 0; font-size: 16px; opacity: 0.9; }
        .content { padding: 30px; }
        .greeting { font-size: 16px; margin-bottom: 20px; }
        .section { margin: 25px 0; padding: 20px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #667eea; }
        .section h2 { margin-top: 0; color: #667eea; font-size: 20px; }
        .payment-method { background: white; padding: 15px; margin: 15px 0; border-radius: 6px; border: 1px solid #e0e0e0; }
        .payment-method h3 { margin: 0 0 10px 0; color: #333; font-size: 18px; }
        .payment-detail { margin: 8px 0; padding-left: 20px; }
        .payment-detail strong { display: inline-block; width: 150px; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 6px; }
        .warning h3 { margin: 0 0 10px 0; color: #856404; }
        .warning ul { margin: 10px 0; padding-left: 20px; }
        .warning li { margin: 5px 0; }
        .cta-button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; font-weight: bold; }
        .cta-button:hover { background: #5568d3; }
        .steps { background: #e8f4fd; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .steps h3 { margin-top: 0; color: #1976d2; }
        .steps ol { margin: 10px 0; padding-left: 20px; }
        .steps li { margin: 10px 0; }
        .footer { background: #f8f9fa; padding: 25px; text-align: center; border-top: 1px solid #e0e0e0; }
        .footer p { margin: 5px 0; font-size: 14px; color: #666; }
        .footer a { color: #667eea; text-decoration: none; }
        .divider { height: 1px; background: #e0e0e0; margin: 25px 0; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Herzlich Willkommen!</h1>
            <p>Ihre Registrierung wurde erfolgreich abgeschlossen</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                <p>Sehr geehrte(r) {{user_name}},</p>
                <p>herzlichen Glückwunsch! Ihre Registrierung bei {{company_name}} wurde erfolgreich abgeschlossen. Wir freuen uns, Sie in unserer Community begrüßen zu dürfen.</p>
            </div>

            <div class="divider"></div>

            <!-- Payment Methods Section -->
            <div class="section">
                <h2>📋 Ihre hinzugefügten Zahlungsmethoden</h2>
                
                <!-- Bank Account (conditional) -->
                {{#if_bank}}
                <div class="payment-method">
                    <h3>🏦 Bankkonto</h3>
                    <div class="payment-detail"><strong>Bank:</strong> {{bank_name}}</div>
                    <div class="payment-detail"><strong>Kontoinhaber:</strong> {{account_holder}}</div>
                    <div class="payment-detail"><strong>IBAN:</strong> {{iban}}</div>
                    <div class="payment-detail"><strong>BIC/SWIFT:</strong> {{bic}}</div>
                    <div class="payment-detail"><strong>Status:</strong> ⏳ Verifizierung ausstehend</div>
                </div>
                {{/if_bank}}

                <!-- Crypto Wallet (conditional) -->
                {{#if_crypto}}
                <div class="payment-method">
                    <h3>💰 Krypto-Wallet</h3>
                    <div class="payment-detail"><strong>Kryptowährung:</strong> {{cryptocurrency}}</div>
                    <div class="payment-detail"><strong>Netzwerk:</strong> {{network}}</div>
                    <div class="payment-detail"><strong>Wallet-Adresse:</strong> {{wallet_address}}</div>
                    <div class="payment-detail"><strong>Status:</strong> ⏳ Satoshi-Test erforderlich</div>
                </div>
                {{/if_crypto}}
            </div>

            <!-- Security Warning (if crypto added) -->
            {{#if_crypto}}
            <div class="warning">
                <h3>⚠️ WICHTIG: Verifizierung erforderlich</h3>
                <p>Aus Sicherheitsgründen müssen Sie Ihre Krypto-Wallet-Adresse verifizieren, bevor Sie Auszahlungen vornehmen können.</p>
                
                <p><strong>Die Verifizierung schützt:</strong></p>
                <ul>
                    <li>✓ Ihre Gelder vor unbefugtem Zugriff</li>
                    <li>✓ Verhindert betrügerische Transaktionen</li>
                    <li>✓ Gewährleistet die Einhaltung von Sicherheitsstandards</li>
                    <li>✓ Schützt vor Identitätsdiebstahl</li>
                </ul>
                
                <p>Bitte vervollständigen Sie die Verifizierung so bald wie möglich, um alle Funktionen nutzen zu können.</p>
            </div>
            {{/if_crypto}}

            <!-- Next Steps -->
            <div class="steps">
                <h3>📝 Nächste Schritte</h3>
                <ol>
                    <li>Vervollständigen Sie Ihr Profil im Dashboard</li>
                    <li>Verifizieren Sie Ihre hinzugefügten Zahlungsmethoden</li>
                    <li>Beginnen Sie mit der Nutzung unserer Dienste</li>
                </ol>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{dashboard_url}}" class="cta-button">Zum Dashboard</a>
            </div>

            <div class="divider"></div>

            <!-- Support -->
            <p style="text-align: center; color: #666;">
                <strong>Benötigen Sie Hilfe?</strong><br>
                Unser Support-Team steht Ihnen gerne zur Verfügung.<br>
                📧 {{support_email}} | 📞 {{support_phone}}
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Mit freundlichen Grüßen,</strong></p>
            <p>Das {{company_name}} Team</p>
            <div class="divider"></div>
            <p>{{company_name}}</p>
            <p>{{company_address}}</p>
            <p>{{company_city}}, {{company_country}}</p>
            <p style="margin-top: 15px;">
                <a href="{{website_url}}">Website</a> | 
                <a href="{{terms_url}}">AGB</a> | 
                <a href="{{privacy_url}}">Datenschutz</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                © {{current_year}} {{company_name}}. Alle Rechte vorbehalten.
            </p>
        </div>
    </div>
</body>
</html>',
'user_name,company_name,bank_name,account_holder,iban,bic,cryptocurrency,network,wallet_address,dashboard_url,support_email,support_phone,company_address,company_city,company_country,website_url,terms_url,privacy_url,current_year'
)
ON DUPLICATE KEY UPDATE
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- ================================================================
-- Template added/updated successfully!
-- ================================================================
