-- Email template for withdrawal_rejected (Body Content Only)
-- This template is sent when a withdrawal request is rejected by an administrator
-- AdminEmailHelper will automatically wrap this content in professional HTML with logo, header, footer

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
    '<h2 style="color: #dc3545; margin-bottom: 20px;">❌ Auszahlungsantrag Abgelehnt</h2>

<p style="font-size: 16px; margin-bottom: 20px;">Sehr geehrte/r {first_name} {last_name},</p>

<p style="font-size: 14px; color: #555555; margin-bottom: 15px;">
    Leider müssen wir Ihnen mitteilen, dass Ihr Auszahlungsantrag abgelehnt wurde.
</p>
<p style="font-size: 14px; color: #555555; margin-bottom: 25px;">
    Wir haben Ihren Antrag sorgfältig geprüft, konnten ihn jedoch aus den unten genannten Gründen nicht genehmigen.
</p>

<div style="background-color: #fff3cd; border: 1px solid #ffc107; border-left: 4px solid #ffc107; border-radius: 4px; padding: 20px; margin: 20px 0;">
    <h4 style="margin: 0 0 10px 0; color: #856404; font-size: 16px;">📝 Ablehnungsgrund:</h4>
    <p style="margin: 0; color: #856404; font-size: 14px; line-height: 1.6;">{rejection_reason}</p>
</div>

<div style="background-color: #f8f9fa; border-left: 4px solid #dc3545; padding: 20px; margin: 25px 0; border-radius: 4px;">
    <h3 style="margin: 0 0 15px 0; color: #dc3545; font-size: 16px;">📋 Antragsdetails</h3>
    
    <table style="width: 100%; border-collapse: collapse;">
        <tr style="border-bottom: 1px solid #e0e0e0;">
            <td style="padding: 12px 0; font-weight: 600; color: #333333;">Referenznummer:</td>
            <td style="padding: 12px 0; text-align: right; color: #555555;"><strong>{reference}</strong></td>
        </tr>
        <tr style="border-bottom: 1px solid #e0e0e0;">
            <td style="padding: 12px 0; font-weight: 600; color: #333333;">Betrag:</td>
            <td style="padding: 12px 0; text-align: right; color: #555555;"><strong>{amount}</strong></td>
        </tr>
        <tr style="border-bottom: 1px solid #e0e0e0;">
            <td style="padding: 12px 0; font-weight: 600; color: #333333;">Zahlungsmethode:</td>
            <td style="padding: 12px 0; text-align: right; color: #555555;">{payment_method}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e0e0e0;">
            <td style="padding: 12px 0; font-weight: 600; color: #333333;">Zahlungsdetails:</td>
            <td style="padding: 12px 0; text-align: right; color: #555555;">{payment_details}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e0e0e0;">
            <td style="padding: 12px 0; font-weight: 600; color: #333333;">Transaktions-ID:</td>
            <td style="padding: 12px 0; text-align: right; color: #555555;">{transaction_id}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e0e0e0;">
            <td style="padding: 12px 0; font-weight: 600; color: #333333;">Antragsdatum:</td>
            <td style="padding: 12px 0; text-align: right; color: #555555;">{transaction_date}</td>
        </tr>
        <tr style="border-bottom: 1px solid #e0e0e0;">
            <td style="padding: 12px 0; font-weight: 600; color: #333333;">Ablehnungsdatum:</td>
            <td style="padding: 12px 0; text-align: right; color: #555555;">{rejected_at}</td>
        </tr>
        <tr>
            <td style="padding: 12px 0; font-weight: 600; color: #333333;">Status:</td>
            <td style="padding: 12px 0; text-align: right; color: #555555;">
                <span style="display: inline-block; background-color: #dc3545; color: #ffffff; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase;">Abgelehnt</span>
            </td>
        </tr>
    </table>
</div>

<div style="background-color: #f8d7da; border: 1px solid #dc3545; border-radius: 4px; padding: 15px; margin: 20px 0;">
    <p style="margin: 0; color: #721c24; font-size: 14px;">
        <strong>⚠️ Wichtig:</strong> Der abgelehnte Betrag wurde Ihrem Guthaben wieder gutgeschrieben und steht Ihnen zur Verfügung.
    </p>
</div>

<div style="background-color: #d1ecf1; border: 1px solid #17a2b8; border-radius: 4px; padding: 20px; margin: 25px 0; text-align: center;">
    <h4 style="margin: 0 0 10px 0; color: #0c5460; font-size: 16px;">💬 Haben Sie Fragen?</h4>
    <p style="margin: 5px 0; color: #0c5460; font-size: 14px;">
        Unser Support-Team steht Ihnen gerne zur Verfügung, um die Ablehnung zu erklären oder Sie bei einem neuen Antrag zu unterstützen.
    </p>
    <p style="margin: 15px 0 5px; color: #0c5460; font-size: 14px;">
        <strong>E-Mail:</strong> <a href="mailto:{support_email}" style="color: #0c5460; font-weight: 600; text-decoration: none;">{support_email}</a>
    </p>
    <p style="margin: 5px 0; color: #0c5460; font-size: 14px;">
        <strong>Kontakt:</strong> <a href="mailto:{contact_email}" style="color: #0c5460; font-weight: 600; text-decoration: none;">{contact_email}</a>
    </p>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{site_url}/transactions.php" style="display: inline-block; background: linear-gradient(135deg, #2950a8, #2da9e3); color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 25px; font-weight: 600;">Zu Transaktionen</a>
</div>

<div style="height: 1px; background-color: #e0e0e0; margin: 25px 0;"></div>

<div style="font-size: 14px; color: #555555; margin-bottom: 25px;">
    <p><strong>Nächste Schritte:</strong></p>
    <ul style="color: #555555; font-size: 14px; line-height: 1.8;">
        <li>Überprüfen Sie den Ablehnungsgrund sorgfältig</li>
        <li>Korrigieren Sie gegebenenfalls die angegebenen Informationen</li>
        <li>Stellen Sie sicher, dass alle Verifizierungsanforderungen erfüllt sind</li>
        <li>Kontaktieren Sie bei Unklarheiten unseren Support</li>
        <li>Sie können einen neuen Auszahlungsantrag stellen, sobald die Probleme behoben sind</li>
    </ul>
</div>

<p style="color: #666; font-size: 13px; font-style: italic; margin-top: 25px;">
    Wir bedauern, dass wir Ihren Antrag nicht genehmigen konnten, und freuen uns darauf, Sie bei einem zukünftigen Antrag zu unterstützen.
</p>',
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
