-- ========================================
-- German Email Templates for FundTracer AI
-- Deutsche E-Mail-Vorlagen für FundTracer AI
-- ========================================

-- Insert German email templates
-- Diese Vorlagen verwenden {{variable}} Syntax für dynamische Inhalte

-- 1. KYC Erinnerung (KYC Reminder in German)
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('kyc_reminder_de', 
 'Vervollständigen Sie Ihre KYC-Verifizierung - FundTracer AI',
 '<h2>Hallo {{first_name}},</h2>
<p>Wir haben festgestellt, dass Sie Ihre <strong>KYC (Know Your Customer) Verifizierung</strong> noch nicht abgeschlossen haben.</p>

<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
    <h3 style="color: #856404; margin-top: 0;">⚠️ KYC Erforderlich</h3>
    <p style="margin: 0;">Die KYC-Verifizierung ist für die Bearbeitung der Fondsrückgewinnung und sichere Transaktionen erforderlich.</p>
</div>

<p><strong>Warum ist KYC wichtig?</strong></p>
<ul>
    <li>✅ Erforderlich für die Bearbeitung der Fondsrückgewinnung</li>
    <li>✅ Gewährleistet sichere Transaktionen</li>
    <li>✅ Schützt Ihr Konto</li>
    <li>✅ Schaltet alle Plattformfunktionen frei</li>
    <li>✅ Einhaltung der Finanzvorschriften</li>
</ul>

<div style="background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #0c5460;">📋 Vervollständigen Sie Ihre KYC in 3 einfachen Schritten:</h3>
    <ol>
        <li>Laden Sie einen gültigen Personalausweis hoch (Pass, Führerschein, Personalausweis)</li>
        <li>Stellen Sie eine aktuelle Rechnung oder einen Kontoauszug zur Verfügung (zur Adressbestätigung)</li>
        <li>Machen Sie ein Selfie mit Ihrem Ausweis (zur Identitätsbestätigung)</li>
    </ol>
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{kyc_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        KYC-Verifizierung jetzt abschließen
    </a>
</p>

<p><strong>Benötigen Sie Hilfe?</strong> Unser Support-Team steht Ihnen rund um die Uhr zur Verfügung.</p>

<p>Mit freundlichen Grüßen,<br>
<strong>FundTracer AI Compliance Team</strong></p>',
 '["first_name", "last_name", "email", "kyc_url", "support_email"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 2. Login-Erinnerung (Login Reminder in German)
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('login_reminder_de',
 'Melden Sie sich bei Ihrem FundTracer AI Konto an',
 '<h2>Hallo {{first_name}},</h2>
<p>Wir haben bemerkt, dass Sie sich noch nie bei Ihrem FundTracer AI Konto angemeldet haben.</p>

<div style="background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin: 20px 0;">
    <h3 style="color: #0c5460; margin-top: 0;">🚀 Starten Sie Ihre Fondsrückgewinnung</h3>
    <p style="margin: 0;">Ihr Konto ist bereit! Melden Sie sich an, um mit der Wiederherstellung Ihrer Gelder zu beginnen.</p>
</div>

<p><strong>Was Sie nach der Anmeldung erwartet:</strong></p>
<ul>
    <li>🎯 KI-gestützte Fallanalyse</li>
    <li>📊 Echtzeit-Dashboard mit Fallstatus</li>
    <li>💼 Professionelles Recovery-Team</li>
    <li>🔒 Sichere Plattform mit 2FA</li>
    <li>📈 Fortschrittsverfolgung</li>
</ul>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{login_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Jetzt anmelden
    </a>
</p>

<p><strong>Ihre Zugangsdaten:</strong></p>
<ul>
    <li>E-Mail: {{email}}</li>
    <li>Passwort: Das von Ihnen festgelegte Passwort</li>
</ul>

<p>Passwort vergessen? <a href="{{reset_password_url}}">Hier zurücksetzen</a></p>

<p>Mit freundlichen Grüßen,<br>
<strong>FundTracer AI Team</strong></p>',
 '["first_name", "last_name", "email", "login_url", "reset_password_url"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 3. Auszahlungserinnerung (Withdrawal Reminder in German)
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('withdraw_reminder_de',
 'Guthaben verfügbar - Jetzt Auszahlung beantragen',
 '<h2>Hallo {{first_name}},</h2>
<p>Wir haben festgestellt, dass Sie ein Guthaben von <strong>{{balance}}€</strong> auf Ihrem Konto haben.</p>

<div style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
    <h3 style="color: #155724; margin-top: 0;">💰 Guthaben verfügbar</h3>
    <p style="margin: 0;">Ihr Guthaben steht zur Auszahlung bereit. Beantragen Sie jetzt Ihre Auszahlung!</p>
</div>

<p><strong>Aktueller Kontostand:</strong> {{balance}}€</p>

<p><strong>Auszahlung beantragen:</strong></p>
<ol>
    <li>Melden Sie sich in Ihrem Dashboard an</li>
    <li>Gehen Sie zum Bereich "Auszahlungen"</li>
    <li>Geben Sie den gewünschten Betrag ein</li>
    <li>Bestätigen Sie Ihre Bankdaten</li>
</ol>

<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p style="margin: 0;"><strong>⚡ Schnelle Bearbeitung:</strong> Auszahlungen werden in der Regel innerhalb von 24-48 Stunden bearbeitet.</p>
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{withdrawal_url}}" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Auszahlung beantragen
    </a>
</p>

<p><strong>Fragen?</strong> Unser Support-Team hilft Ihnen gerne weiter.</p>

<p>Mit freundlichen Grüßen,<br>
<strong>FundTracer AI Finance Team</strong></p>',
 '["first_name", "last_name", "email", "balance", "withdrawal_url", "support_email"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 4. Onboarding-Erinnerung (Onboarding Reminder in German)
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('onboarding_reminder_de',
 'Vervollständigen Sie Ihr Profil - FundTracer AI',
 '<h2>Hallo {{first_name}},</h2>
<p>Ihr FundTracer AI Profil ist noch nicht vollständig. Vervollständigen Sie Ihr Onboarding für den vollen Zugriff!</p>

<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
    <h3 style="color: #856404; margin-top: 0;">📝 Profil unvollständig</h3>
    <p style="margin: 0;">Vervollständigen Sie Ihr Profil, um alle Funktionen freizuschalten.</p>
</div>

<p><strong>Fehlende Schritte:</strong></p>
<ul>
    <li>{{missing_step_1}}</li>
    <li>{{missing_step_2}}</li>
    <li>{{missing_step_3}}</li>
</ul>

<p><strong>Vorteile eines vollständigen Profils:</strong></p>
<ul>
    <li>✅ Zugang zu allen Funktionen</li>
    <li>✅ Schnellere Fallbearbeitung</li>
    <li>✅ Höhere Erfolgsquote bei der Fondsrückgewinnung</li>
    <li>✅ Prioritärer Support</li>
</ul>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{onboarding_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Onboarding abschließen
    </a>
</p>

<p>Nur noch wenige Schritte, bis Ihr Profil vollständig ist!</p>

<p>Mit freundlichen Grüßen,<br>
<strong>FundTracer AI Team</strong></p>',
 '["first_name", "last_name", "email", "onboarding_url", "missing_step_1", "missing_step_2", "missing_step_3"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 5. Inaktivitätserinnerung (Inactivity Reminder in German)
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('inactive_user_de',
 'Wir vermissen Sie bei FundTracer AI - {{first_name}}',
 '<h2>Hallo {{first_name}},</h2>
<p>Wir haben festgestellt, dass Sie sich seit {{days_inactive}} Tagen nicht mehr angemeldet haben.</p>

<div style="background: #f0f8ff; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <h3 style="color: #2950a8;">🤖 KI-Update: Ihr Fall ist aktiv!</h3>
    <p>Unser KI-gestütztes System arbeitet weiterhin an Ihrem Fall und wir haben wichtige Updates zu teilen.</p>
</div>

<p><strong>Neuigkeiten:</strong></p>
<ul>
    <li>✅ Fortgeschrittene KI-Analyse Ihres Falls</li>
    <li>✅ Neue Wiederherstellungsstrategien identifiziert</li>
    <li>✅ Potenzielle Hinweise zur Fondsrückgewinnung</li>
    <li>✅ Verbesserte Betrugserkennungs-Erkenntnisse</li>
</ul>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{login_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Zum Dashboard anmelden
    </a>
</p>

<p>Lassen Sie Ihren Fall nicht kalt werden. Jeder Tag zählt bei der Fondsrückgewinnung.</p>
<p><strong>Benötigen Sie Hilfe?</strong> Unser 24/7 Support-Team steht Ihnen zur Verfügung.</p>

<p>Mit freundlichen Grüßen,<br>
<strong>FundTracer AI Team</strong></p>',
 '["first_name", "last_name", "days_inactive", "login_url", "email", "case_number"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 6. Guthaben-Benachrichtigung (Balance Alert in German)
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('balance_alert_de',
 '💰 Wichtig: Guthaben auf Ihrem Konto - {{first_name}}',
 '<h2>Hallo {{first_name}},</h2>

<div style="background: #d4edda; border-left: 4px solid #28a745; padding: 20px; margin: 20px 0;">
    <h3 style="color: #155724; margin-top: 0;">💰 Guthaben verfügbar!</h3>
    <p style="margin: 0; font-size: 18px;"><strong>Aktueller Kontostand: {{balance}}€</strong></p>
</div>

<p>Sie haben ein Guthaben auf Ihrem FundTracer AI Konto. Wir empfehlen Ihnen, dieses Guthaben zeitnah abzuheben.</p>

<p><strong>Warum jetzt abheben?</strong></p>
<ul>
    <li>💳 Schnelle Bearbeitung (24-48 Stunden)</li>
    <li>🔒 Sichere Überweisung auf Ihr Bankkonto</li>
    <li>✅ Keine versteckten Gebühren</li>
    <li>📝 Einfacher Auszahlungsprozess</li>
</ul>

<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p style="margin: 0;"><strong>💡 Hinweis:</strong> Stellen Sie sicher, dass Ihre Bankdaten aktuell sind, bevor Sie eine Auszahlung beantragen.</p>
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{withdrawal_url}}" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Jetzt auszahlen lassen
    </a>
</p>

<p><strong>Auszahlungslimit:</strong> Mindestbetrag {{min_withdrawal}}€ | Maximalbetrag {{max_withdrawal}}€</p>

<p>Bei Fragen zur Auszahlung kontaktieren Sie bitte unser Support-Team.</p>

<p>Mit freundlichen Grüßen,<br>
<strong>FundTracer AI Finance Team</strong></p>',
 '["first_name", "last_name", "email", "balance", "withdrawal_url", "min_withdrawal", "max_withdrawal", "support_email"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- Success message
SELECT 'German email templates created/updated successfully!' as Status, 
COUNT(*) as Templates_Count 
FROM email_templates 
WHERE template_key IN (
    'kyc_reminder_de',
    'login_reminder_de', 
    'withdraw_reminder_de',
    'onboarding_reminder_de',
    'inactive_user_de',
    'balance_alert_de'
);
