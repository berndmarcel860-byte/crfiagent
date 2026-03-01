-- Email template for withdrawal_rejected
-- This template is sent when a withdrawal request is rejected by an administrator

INSERT INTO `email_templates` (
    `template_key`, 
    `template_name`, 
    `subject`, 
    `body`, 
    `variables`, 
    `is_active`, 
    `created_at`, 
    `updated_at`
) VALUES (
    'withdrawal_rejected',
    'Withdrawal Request Rejected',
    'Auszahlungsantrag Abgelehnt - {reference}',
    '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Request Rejected</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .message {
            font-size: 14px;
            color: #555555;
            margin-bottom: 25px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin: 0 0 15px 0;
            color: #dc3545;
            font-size: 16px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #333333;
        }
        .info-value {
            color: #555555;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            background-color: #dc3545;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .alert-box {
            background-color: #f8d7da;
            border: 1px solid #dc3545;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-box p {
            margin: 0;
            color: #721c24;
            font-size: 14px;
        }
        .reason-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
        }
        .reason-box h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 16px;
        }
        .reason-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
            line-height: 1.6;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .support-box {
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            border-radius: 4px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .support-box h4 {
            margin: 0 0 10px 0;
            color: #0c5460;
            font-size: 16px;
        }
        .support-box p {
            margin: 5px 0;
            color: #0c5460;
            font-size: 14px;
        }
        .support-box a {
            color: #0c5460;
            font-weight: 600;
            text-decoration: none;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #e0e0e0;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 25px 0;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            .info-row {
                flex-direction: column;
            }
            .info-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>❌ Auszahlungsantrag Abgelehnt</h1>
        </div>
        
        <div class="email-body">
            <div class="greeting">
                <p>Sehr geehrte/r {first_name} {last_name},</p>
            </div>
            
            <div class="message">
                <p>Leider müssen wir Ihnen mitteilen, dass Ihr Auszahlungsantrag abgelehnt wurde.</p>
                <p>Wir haben Ihren Antrag sorgfältig geprüft, konnten ihn jedoch aus den unten genannten Gründen nicht genehmigen.</p>
            </div>
            
            <div class="reason-box">
                <h4>📝 Ablehnungsgrund:</h4>
                <p>{rejection_reason}</p>
            </div>
            
            <div class="info-box">
                <h3>📋 Antragsdetails</h3>
                
                <div class="info-row">
                    <span class="info-label">Referenznummer:</span>
                    <span class="info-value"><strong>{reference}</strong></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Betrag:</span>
                    <span class="info-value"><strong>{amount}</strong></span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Zahlungsmethode:</span>
                    <span class="info-value">{payment_method}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Zahlungsdetails:</span>
                    <span class="info-value">{payment_details}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Transaktions-ID:</span>
                    <span class="info-value">{transaction_id}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Antragsdatum:</span>
                    <span class="info-value">{transaction_date}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Ablehnungsdatum:</span>
                    <span class="info-value">{rejected_at}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="status-badge">Abgelehnt</span></span>
                </div>
            </div>
            
            <div class="alert-box">
                <p><strong>⚠️ Wichtig:</strong> Der abgelehnte Betrag wurde Ihrem Guthaben wieder gutgeschrieben und steht Ihnen zur Verfügung.</p>
            </div>
            
            <div class="support-box">
                <h4>💬 Haben Sie Fragen?</h4>
                <p>Unser Support-Team steht Ihnen gerne zur Verfügung, um die Ablehnung zu erklären oder Sie bei einem neuen Antrag zu unterstützen.</p>
                <p style="margin-top: 15px;">
                    <strong>E-Mail:</strong> <a href="mailto:{support_email}">{support_email}</a>
                </p>
                <p>
                    <strong>Kontakt:</strong> <a href="mailto:{contact_email}">{contact_email}</a>
                </p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{site_url}/transactions.php" class="cta-button">Zu Transaktionen</a>
            </div>
            
            <div class="divider"></div>
            
            <div class="message">
                <p><strong>Nächste Schritte:</strong></p>
                <ul style="color: #555555; font-size: 14px;">
                    <li>Überprüfen Sie den Ablehnungsgrund sorgfältig</li>
                    <li>Korrigieren Sie gegebenenfalls die angegebenen Informationen</li>
                    <li>Stellen Sie sicher, dass alle Verifizierungsanforderungen erfüllt sind</li>
                    <li>Kontaktieren Sie bei Unklarheiten unseren Support</li>
                    <li>Sie können einen neuen Auszahlungsantrag stellen, sobald die Probleme behoben sind</li>
                </ul>
            </div>
            
            <div class="message" style="margin-top: 25px;">
                <p style="color: #666; font-size: 13px; font-style: italic;">
                    Wir bedauern, dass wir Ihren Antrag nicht genehmigen konnten, und freuen uns darauf, Sie bei einem zukünftigen Antrag zu unterstützen.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>{brand_name}</strong></p>
            <p>{company_address}</p>
            <p>
                <a href="{site_url}">Website besuchen</a> | 
                <a href="mailto:{support_email}">Support kontaktieren</a>
            </p>
            <p style="margin-top: 15px; color: #999999;">
                Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
            </p>
            <p style="margin-top: 10px; color: #999999;">
                &copy; {current_year} {brand_name}. Alle Rechte vorbehalten.
            </p>
        </div>
    </div>
</body>
</html>',
    'first_name, last_name, user_email, amount, reference, payment_method, payment_details, transaction_id, transaction_date, rejection_reason, rejected_by, rejected_at, brand_name, site_url, support_email, contact_email, company_address, current_year',
    1,
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    `template_name` = VALUES(`template_name`),
    `subject` = VALUES(`subject`),
    `body` = VALUES(`body`),
    `variables` = VALUES(`variables`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();
