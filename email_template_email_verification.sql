-- Email Verification Template
-- Based on welcome_email format from email_templates (11).sql

INSERT INTO email_templates (
    template_key,
    template_name,
    subject,
    content,
    variables,
    category,
    is_active,
    created_at,
    updated_at
) VALUES (
    'email_verification',
    'Email Verification',
    'Bestätigen Sie Ihre E-Mail-Adresse bei {brand_name}!',
    '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - {brand_name}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(90deg, #2950a8 0%, #2da9e3 100%); color: white; text-align: center; padding: 30px 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 25px; background: #f9f9f9; }
        .details { background: #fff; padding: 20px; border-left: 4px solid #007bff; border-radius: 6px; margin: 20px 0; }
        .btn { display: inline-block; background: #007bff; color: white; padding: 10px 18px; border-radius: 5px; text-decoration: none; font-weight: bold; }
        .footer { text-align: center; font-size: 12px; color: #666; padding: 20px; background: #f1f1f1; }
        .highlight { color: #007bff; font-weight: bold; }
        .signature { margin-top: 30px; border-top: 1px solid #e0e0e0; padding-top: 20px; font-size: 14px; color: #555; }
        .signature img { height: 45px; margin-bottom: 10px; }
        .signature p { margin: 4px 0; }
        @media only screen and (max-width: 600px) {
            .container { width: 95%; }
            .content { padding: 15px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>E-Mail-Adresse bestätigen</h1>
            <p>Verifizieren Sie Ihre E-Mail bei {brand_name}</p>
        </div>

        <div class="content">
            <p>Sehr geehrte/r {user_first_name},</p>
            <p>Vielen Dank für Ihre Registrierung bei <strong>{brand_name}</strong>! Um Ihren Account zu aktivieren, bestätigen Sie bitte Ihre E-Mail-Adresse.</p>

            <div class="details">
                <h4>✅ E-Mail-Adresse bestätigen</h4>
                <p>Klicken Sie auf den Button unten, um Ihre E-Mail-Adresse zu verifizieren:</p>
                <p style="margin-top: 15px; text-align: center;">
                    <a href="{verification_link}" class="btn">E-Mail jetzt bestätigen</a>
                </p>
            </div>

            <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 6px;">
                <p style="margin: 0;"><strong>⚠️ Wichtig:</strong> Dieser Link ist aus Sicherheitsgründen nur <strong>1 Stunde</strong> gültig.</p>
            </div>

            <p style="margin-top: 20px;"><strong>Warum ist die Verifizierung wichtig?</strong></p>
            <ul style="color: #666; line-height: 1.8;">
                <li>✓ Zugang zu allen Funktionen Ihres Accounts</li>
                <li>✓ Sicherheit: Schutz vor unbefugtem Zugriff</li>
                <li>✓ Benachrichtigungen über wichtige Account-Aktivitäten</li>
                <li>✓ Wiederherstellung Ihres Passworts bei Bedarf</li>
            </ul>

            <p style="margin-top: 20px; font-size: 13px; color: #666;">
                <em>Falls der Button nicht funktioniert, kopieren Sie bitte diesen Link in Ihren Browser:</em><br>
                <a href="{verification_link}" style="color: #007bff; word-break: break-all;">{verification_link}</a>
            </p>

            <p style="margin-top: 20px; font-size: 13px; color: #999;">
                Falls Sie diese E-Mail nicht angefordert haben, können Sie sie einfach ignorieren. Ihr Account wird ohne Bestätigung nicht aktiviert.
            </p>

            <p>Mit freundlichen Grüßen,</p>

            <div class="signature">
    <img src="{site_url}/assets/images/logo/logo.png" alt="{brand_name} Logo"><br>
    <strong>{brand_name} – Fallmanagement-Team</strong><br>
    {company_address}<br>
    E: <a href="mailto:{contact_email}">{contact_email}</a> | 
    W: <a href="{site_url}">{brand_name}</a>
    <p>
      BaFin Referenc Nr: {fca_reference_number}<br>
      <em>Hinweis:</em> Diese E-Mail kann vertrauliche oder rechtlich geschützte Informationen enthalten.  
      Wenn Sie nicht der richtige Adressat sind, informieren Sie uns bitte und löschen Sie diese Nachricht.
    </p>
</div>

<div class="footer">
    © {current_year} {brand_name}. Alle Rechte vorbehalten.
</div>
  </div>
</body>
</html>',
    '[\"user_first_name\",\"verification_link\",\"brand_name\",\"site_url\",\"company_address\",\"contact_email\",\"fca_reference_number\",\"current_year\"]',
    'account',
    1,
    NOW(),
    NOW()
);
