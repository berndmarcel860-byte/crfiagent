-- ================================================================
-- Professional German Onboarding Completion Email Template
-- Includes payment details and verification reminder
-- FORMATTED VERSION - Easier to read and maintain
-- ================================================================

INSERT INTO email_templates (name, subject, body, variables, created_at, updated_at) 
VALUES (
    'onboarding_completed',
    'Willkommen bei {{company_name}} - Verifizierung erforderlich',
    '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onboarding Abgeschlossen</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .alert-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .alert-box.warning { background: #f8d7da; border-left-color: #dc3545; }
        .alert-box.success { background: #d4edda; border-left-color: #28a745; }
        .payment-details { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px; margin: 20px 0; }
        .payment-details h3 { margin-top: 0; color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .detail-row { display: flex; padding: 8px 0; border-bottom: 1px solid #e9ecef; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 700; width: 180px; color: #666; }
        .detail-value { flex: 1; color: #333; }
        .steps { background: #e7f3ff; border-radius: 5px; padding: 20px; margin: 20px 0; }
        .step { display: flex; align-items: flex-start; margin-bottom: 15px; }
        .step-number { background: #667eea; color: #fff; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: 15px; flex-shrink: 0; }
        .step-content { flex: 1; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: 700; margin: 20px 0; text-align: center; }
        .footer { background: #2c3e50; color: #ecf0f1; padding: 30px; text-align: center; font-size: 14px; }
        .footer p { margin: 5px 0; }
        .footer a { color: #3498db; text-decoration: none; }
        .security-note { background: #e8f4f8; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .security-note strong { color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Willkommen bei {{company_name}}!</h1>
            <p>Ihr Onboarding wurde erfolgreich abgeschlossen</p>
        </div>
        <div class="content">
            <p>Sehr geehrte(r) {{user_name}},</p>
            <p>herzlichen Glückwunsch! Sie haben Ihre Registrierung bei {{company_name}} erfolgreich abgeschlossen.</p>
            
            <div class="alert-box success">
                <strong>✓ Konto erstellt</strong><br>
                Ihre Anmeldung wurde erfolgreich verarbeitet.
            </div>
            
            <div class="alert-box warning">
                <strong>⚠️ WICHTIG: Verifizierung erforderlich</strong><br>
                Bevor Sie Auszahlungen vornehmen können, müssen Sie den Besitz Ihrer Bankverbindung und Ihrer Krypto-Wallet-Adresse verifizieren. Dies ist eine wichtige Sicherheitsmaßnahme zum Schutz Ihrer Mittel.
            </div>
            
            <h2>Ihre hinterlegten Zahlungsmethoden:</h2>
            
            <div class="payment-details">
                <h3>🏦 Bankverbindung</h3>
                <div class="detail-row">
                    <div class="detail-label">Bankname:</div>
                    <div class="detail-value">{{bank_name}}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Kontoinhaber:</div>
                    <div class="detail-value">{{account_holder}}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">IBAN:</div>
                    <div class="detail-value">{{iban}}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">BIC/SWIFT:</div>
                    <div class="detail-value">{{bic}}</div>
                </div>
                <div class="alert-box" style="margin-top:15px">
                    <strong>Status:</strong> ⏳ Verifizierung ausstehend
                </div>
            </div>
            
            <div class="payment-details">
                <h3>💰 Krypto-Wallet</h3>
                <div class="detail-row">
                    <div class="detail-label">Kryptowährung:</div>
                    <div class="detail-value">{{cryptocurrency}}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Netzwerk:</div>
                    <div class="detail-value">{{network}}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Wallet-Adresse:</div>
                    <div class="detail-value" style="word-break:break-all">{{wallet_address}}</div>
                </div>
                <div class="alert-box" style="margin-top:15px">
                    <strong>Status:</strong> ⏳ Satoshi-Test erforderlich
                </div>
            </div>
            
            <h2>Nächste Schritte zur Verifizierung:</h2>
            
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <strong>Bankverbindung verifizieren</strong><br>
                        Unser Team wird die Verifizierung Ihrer Bankverbindung durchführen. Sie werden benachrichtigt, sobald dieser Vorgang abgeschlossen ist.
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <strong>Krypto-Wallet verifizieren (Satoshi-Test)</strong><br>
                        Sie erhalten Anweisungen für eine kleine Testeinzahlung, um den Besitz Ihrer Wallet-Adresse nachzuweisen. Der überwiesene Betrag wird Ihrem Konto gutgeschrieben.
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <strong>Verifizierung abschließen</strong><br>
                        Nach erfolgreicher Verifizierung können Sie Auszahlungen vornehmen und alle Funktionen nutzen.
                    </div>
                </div>
            </div>
            
            <div class="security-note">
                <strong>🛡️ Warum ist die Verifizierung wichtig?</strong><br><br>
                Die Verifizierung Ihrer Zahlungsmethoden ist eine essenzielle Sicherheitsmaßnahme, die:
                <ul style="margin:10px 0 0;padding-left:20px">
                    <li>Ihre Identität und den Besitz der angegebenen Konten bestätigt</li>
                    <li>Unbefugten Zugriff auf Ihre Mittel verhindert</li>
                    <li>Betrügerische Aktivitäten unterbindet</li>
                    <li>Regulatorische Anforderungen (AML/KYC) erfüllt</li>
                    <li>Ihre Gelder vor Verlust schützt</li>
                </ul>
            </div>
            
            <center>
                <a href="{{dashboard_url}}" class="cta-button">Zum Dashboard →</a>
            </center>
            
            <p style="margin-top:30px">Bei Fragen stehen wir Ihnen jederzeit gerne zur Verfügung. Kontaktieren Sie unseren Support unter:</p>
            <p style="text-align:center">
                📧 <a href="mailto:{{support_email}}">{{support_email}}</a><br>
                📞 {{support_phone}}
            </p>
            
            <p>Mit freundlichen Grüßen,<br><strong>Ihr {{company_name}} Team</strong></p>
        </div>
        <div class="footer">
            <p><strong>{{company_name}}</strong></p>
            <p>{{company_address}}</p>
            <p>{{company_city}}, {{company_country}}</p>
            <p style="margin-top:15px">
                <a href="{{website_url}}">Website</a> | 
                <a href="{{terms_url}}">AGB</a> | 
                <a href="{{privacy_url}}">Datenschutz</a>
            </p>
            <p style="margin-top:15px;font-size:12px;color:#95a5a6">
                © {{current_year}} {{company_name}}. Alle Rechte vorbehalten.
            </p>
        </div>
    </div>
</body>
</html>',
    'user_name,company_name,bank_name,account_holder,iban,bic,cryptocurrency,network,wallet_address,dashboard_url,support_email,support_phone,company_address,company_city,company_country,website_url,terms_url,privacy_url,current_year',
    NOW(),
    NOW()
);
