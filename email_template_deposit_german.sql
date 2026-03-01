-- ========================================
-- German Deposit Email Templates
-- Deutsche Einzahlungs-E-Mail-Vorlagen
-- ========================================
-- 
-- INSTALLATION:
-- mysql -u username -p database_name < email_template_deposit_german.sql
--
-- VERFÜGBARE VARIABLEN (Available Variables):
-- Benutzer: {first_name}, {last_name}, {full_name}, {email}, {balance}, {user_id}
-- Einzahlung: {deposit_amount}, {deposit_reference}, {payment_method}, {deposit_status}, {date}
-- Unternehmen: {brand_name}, {site_url}, {contact_email}, {contact_phone}, {company_address}
-- System: {current_year}, {current_date}, {current_time}, {dashboard_url}, {login_url}

-- ========================================
-- 1. Vorlage für Abgeschlossene Einzahlungen
-- Template for Completed Deposits (German)
-- ========================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`) VALUES
('deposit_completed_de', 
 'Einzahlung Abgeschlossen - €{deposit_amount}',
 '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #28a745; border-bottom: 3px solid #28a745; padding-bottom: 10px;">
        ✓ Einzahlung Abgeschlossen
    </h2>
    
    <p>Sehr geehrte/r {first_name} {last_name},</p>
    
    <p style="font-size: 16px; color: #333;">
        Gute Nachrichten! Ihre Einzahlung wurde erfolgreich verarbeitet und Ihr Kontoguthaben wurde aktualisiert.
    </p>
    
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #333;">Einzahlungsdetails</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Betrag:</strong></td>
                <td style="padding: 8px 0; color: #28a745; font-size: 18px; font-weight: bold;">€{deposit_amount}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Referenznummer:</strong></td>
                <td style="padding: 8px 0;">{deposit_reference}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Zahlungsmethode:</strong></td>
                <td style="padding: 8px 0;">{payment_method}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Status:</strong></td>
                <td style="padding: 8px 0; color: #28a745;"><strong>✓ Abgeschlossen</strong></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Datum:</strong></td>
                <td style="padding: 8px 0;">{date}</td>
            </tr>
        </table>
    </div>
    
    <div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #155724;">
            <strong>✓ Ihr Kontoguthaben wurde aktualisiert.</strong><br>
            Sie können Ihr aktualisiertes Guthaben und Ihre Transaktionshistorie in Ihrem Dashboard einsehen.
        </p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{dashboard_url}" style="background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            Dashboard Anzeigen
        </a>
    </div>
    
    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">
    
    <p style="color: #666; font-size: 14px;">
        Wenn Sie Fragen zu dieser Einzahlung haben, kontaktieren Sie bitte unser Support-Team unter 
        <a href="mailto:{contact_email}" style="color: #007bff;">{contact_email}</a>.
    </p>
    
    <p style="color: #999; font-size: 12px; margin-top: 30px;">
        Dies ist eine automatische Benachrichtigung von {brand_name}. Bitte antworten Sie nicht auf diese E-Mail.
    </p>
</div>',
 '["first_name", "last_name", "deposit_amount", "deposit_reference", "payment_method", "date", "dashboard_url", "contact_email", "brand_name"]'
)
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- ========================================
-- 2. Vorlage für Ausstehende Einzahlungen
-- Template for Pending Deposits (German)
-- ========================================
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`) VALUES
('deposit_pending_de', 
 'Einzahlung In Bearbeitung - €{deposit_amount}',
 '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #ffc107; border-bottom: 3px solid #ffc107; padding-bottom: 10px;">
        ⏳ Einzahlung In Bearbeitung
    </h2>
    
    <p>Sehr geehrte/r {first_name} {last_name},</p>
    
    <p style="font-size: 16px; color: #333;">
        Wir haben Ihre Einzahlungsanfrage erhalten und sie wird derzeit bearbeitet.
    </p>
    
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #333;">Einzahlungsdetails</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Betrag:</strong></td>
                <td style="padding: 8px 0; color: #ffc107; font-size: 18px; font-weight: bold;">€{deposit_amount}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Referenznummer:</strong></td>
                <td style="padding: 8px 0;">{deposit_reference}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Zahlungsmethode:</strong></td>
                <td style="padding: 8px 0;">{payment_method}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Status:</strong></td>
                <td style="padding: 8px 0; color: #ffc107;"><strong>⏳ In Bearbeitung</strong></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Datum:</strong></td>
                <td style="padding: 8px 0;">{date}</td>
            </tr>
        </table>
    </div>
    
    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #856404;">
            <strong>⏳ Ihre Einzahlung wird bearbeitet.</strong><br>
            Dies dauert normalerweise 1-2 Werktage. Sie erhalten eine weitere Benachrichtigung, sobald Ihre Einzahlung abgeschlossen ist und Ihr Guthaben aktualisiert wurde.
        </p>
    </div>
    
    <div style="background-color: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #004085; font-size: 14px;">
            <strong>ℹ️ Was passiert als Nächstes?</strong><br>
            • Unser Team überprüft Ihre Zahlung<br>
            • Nach der Überprüfung wird Ihr Guthaben automatisch aktualisiert<br>
            • Sie erhalten eine Bestätigungs-E-Mail<br>
            • Bearbeitungszeit: Normalerweise innerhalb von 1-2 Werktagen
        </p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{dashboard_url}" style="background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            Dashboard Anzeigen
        </a>
    </div>
    
    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">
    
    <p style="color: #666; font-size: 14px;">
        Wenn Sie Fragen zu dieser Einzahlung haben, kontaktieren Sie bitte unser Support-Team unter 
        <a href="mailto:{contact_email}" style="color: #007bff;">{contact_email}</a>.
    </p>
    
    <p style="color: #999; font-size: 12px; margin-top: 30px;">
        Dies ist eine automatische Benachrichtigung von {brand_name}. Bitte antworten Sie nicht auf diese E-Mail.
    </p>
</div>',
 '["first_name", "last_name", "deposit_amount", "deposit_reference", "payment_method", "date", "dashboard_url", "contact_email", "brand_name"]'
)
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- ========================================
-- Überprüfung der Installation
-- Verify templates were inserted
-- ========================================
SELECT 
    template_key, 
    subject, 
    LENGTH(content) as content_length,
    created_at,
    updated_at
FROM email_templates 
WHERE template_key IN ('deposit_completed_de', 'deposit_pending_de')
ORDER BY template_key;

-- Success message
SELECT 'Deutsche Einzahlungs-E-Mail-Vorlagen erfolgreich erstellt/aktualisiert!' as Status,
       'German deposit email templates successfully created/updated!' as Status_EN;
