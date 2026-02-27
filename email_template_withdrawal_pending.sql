-- Email template for withdrawal_pending
-- This template is sent when a user submits a withdrawal request

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
    'withdrawal_pending',
    'Withdrawal Request Pending',
    'Withdrawal Request Received - {reference}',
    '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal Request Pending</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin: 0 0 15px 0;
            color: #667eea;
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
            background-color: #ffc107;
            color: #000000;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .alert-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
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
            <h1>💰 Auszahlungsanfrage Eingegangen</h1>
        </div>
        
        <div class="email-body">
            <div class="greeting">
                <p>Sehr geehrte/r {first_name} {last_name},</p>
            </div>
            
            <div class="message">
                <p>Wir haben Ihre Auszahlungsanfrage erfolgreich erhalten und werden diese schnellstmöglich bearbeiten.</p>
                <p>Ihr Antrag wird derzeit überprüft und bearbeitet. Sie erhalten eine weitere Benachrichtigung, sobald die Auszahlung genehmigt und verarbeitet wurde.</p>
            </div>
            
            <div class="info-box">
                <h3>📋 Auszahlungsdetails</h3>
                
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
                    <span class="info-label">Datum:</span>
                    <span class="info-value">{transaction_date}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><span class="status-badge">Ausstehend</span></span>
                </div>
            </div>
            
            <div class="alert-box">
                <p><strong>⏳ Bearbeitungszeit:</strong> Auszahlungsanträge werden in der Regel innerhalb von 1-3 Werktagen bearbeitet. Bei Fragen können Sie sich jederzeit an unser Support-Team wenden.</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{site_url}/transactions.php" class="cta-button">Transaktionen anzeigen</a>
            </div>
            
            <div class="divider"></div>
            
            <div class="message">
                <p><strong>Wichtige Hinweise:</strong></p>
                <ul style="color: #555555; font-size: 14px;">
                    <li>Bitte stellen Sie sicher, dass Ihre Zahlungsdetails korrekt sind</li>
                    <li>Die Bearbeitung kann je nach Zahlungsmethode variieren</li>
                    <li>Sie erhalten eine Bestätigung, sobald die Auszahlung abgeschlossen ist</li>
                    <li>Bei Fragen kontaktieren Sie uns unter {support_email}</li>
                </ul>
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
    'first_name, last_name, user_email, amount, reference, payment_method, payment_details, transaction_id, transaction_date, status, brand_name, site_url, support_email, contact_email, company_address, current_year',
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
