-- Email Template: Recovery Amount Credited
-- Template Key: recovery_amount_credited
-- Category: recovery
-- Description: Email notification sent to users when a new recovery amount is credited to their case
-- Based on: wallet_verified template structure

INSERT INTO email_templates (
    template_key, 
    template_name, 
    subject, 
    content, 
    category, 
    is_active, 
    created_at, 
    updated_at
) VALUES (
    'recovery_amount_credited',
    'Rückerstattungsbetrag Verbucht',
    'Neuer Rückerstattungsbetrag verbucht - Fall {{case_number}} - {{brand_name}}',
    '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rückerstattungsbetrag Verbucht</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        
        <!-- Header with Gradient -->
        <div style="background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%); 
                    padding: 40px 20px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">
                {{brand_name}}
            </h1>
        </div>
        
        <!-- Main Content -->
        <div style="padding: 40px 30px;">
            
            <!-- Success Icon -->
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="background-color: #4CAF50; width: 80px; height: 80px; 
                            border-radius: 50%; margin: 0 auto; display: flex; 
                            align-items: center; justify-content: center;">
                    <span style="color: #ffffff; font-size: 48px;">💰</span>
                </div>
            </div>
            
            <!-- Heading -->
            <h2 style="color: #4CAF50; margin-top: 0; text-align: center;">
                Neuer Rückerstattungsbetrag verbucht!
            </h2>
            
            <!-- Greeting -->
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Sehr geehrte/r {{user_first_name}} {{user_last_name}},
            </p>
            
            <!-- Message -->
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Wir freuen uns, Ihnen mitteilen zu können, dass für Ihren Fall  
                <strong>{{case_number}}</strong> ein neuer Rückerstattungsbetrag verbucht wurde.
            </p>
            
            <!-- Recovery Details Box -->
            <div style="background-color: #e8f5e9; padding: 25px; border-radius: 8px; 
                        margin: 25px 0; border-left: 4px solid #4CAF50;">
                <h3 style="color: #2e7d32; margin-top: 0; font-size: 20px;">
                    💰 Erstattungsdetails
                </h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-weight: bold; width: 50%;">
                            Fallnummer:
                        </td>
                        <td style="padding: 10px 0; color: #2c3e50; font-weight: bold;">
                            {{case_number}}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-weight: bold;">
                            Ursprünglicher Betrag:
                        </td>
                        <td style="padding: 10px 0; color: #2c3e50;">
                            {{reported_amount}} €
                        </td>
                    </tr>
                    <tr style="background-color: #c8e6c9;">
                        <td style="padding: 12px 10px; color: #2e7d32; font-weight: bold; font-size: 16px;">
                            Neuer Rückerstattungsbetrag:
                        </td>
                        <td style="padding: 12px 10px; color: #2e7d32; font-weight: bold; font-size: 18px;">
                            {{recovered_amount}} €
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-weight: bold;">
                            Gesamtrückerstattung bisher:
                        </td>
                        <td style="padding: 10px 0; color: #2c3e50; font-weight: bold; font-size: 16px;">
                            {{total_recovered}} €
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-weight: bold;">
                            Datum der Erstattung:
                        </td>
                        <td style="padding: 10px 0; color: #2c3e50;">
                            {{recovery_date}}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-weight: bold; vertical-align: top;">
                            Notizen:
                        </td>
                        <td style="padding: 10px 0; color: #2c3e50; line-height: 1.6;">
                            {{recovery_notes}}
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Availability Message -->
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Der Betrag wurde Ihrem internen Konto gutgeschrieben und steht Ihnen  
                ab sofort zur Auszahlung im <strong>Kundenportal</strong> zur Verfügung.
            </p>
            
            <!-- Call to Action -->
            <div style="text-align: center; margin: 35px 0;">
                <a href="{{dashboard_url}}/dashboard" 
                   style="display: inline-block; padding: 15px 40px; 
                          background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%); 
                          color: #ffffff; text-decoration: none; border-radius: 5px; 
                          font-weight: bold; font-size: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    Zum Kundenportal
                </a>
            </div>
            
            <!-- Next Steps -->
            <div style="background-color: #e3f2fd; padding: 20px; border-radius: 8px; 
                        margin: 25px 0; border-left: 4px solid #2196F3;">
                <h3 style="color: #1565c0; margin-top: 0; font-size: 18px;">
                    📋 Nächste Schritte
                </h3>
                <ul style="color: #1565c0; line-height: 1.8; font-size: 14px; margin: 10px 0; padding-left: 20px;">
                    <li>Melden Sie sich in Ihrem Kundenportal an</li>
                    <li>Überprüfen Sie Ihren Kontostand unter "Guthaben"</li>
                    <li>Beantragen Sie bei Bedarf eine Auszahlung</li>
                    <li>Auszahlungen werden innerhalb von 3-5 Werktagen bearbeitet</li>
                </ul>
            </div>
            
            <!-- Additional Info -->
            <p style="color: #666; line-height: 1.8; font-size: 14px;">
                Bei Fragen zu Ihrer Rückerstattung oder dem Auszahlungsprozess stehen wir Ihnen gerne zur Verfügung. 
                Kontaktieren Sie uns unter 
                <a href="mailto:{{contact_email}}" style="color: #2950a8; text-decoration: none;">{{contact_email}}</a>.
            </p>
            
            <!-- Important Notice -->
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; 
                        margin: 20px 0; border-left: 4px solid #ffc107;">
                <p style="color: #856404; line-height: 1.8; font-size: 13px; margin: 0;">
                    <strong>⚠️ Wichtiger Hinweis:</strong> Die Auszahlung erfolgt ausschließlich auf 
                    verifizierte Konten und Wallets. Stellen Sie sicher, dass Ihre Auszahlungsmethoden 
                    verifiziert sind, bevor Sie eine Auszahlung beantragen.
                </p>
            </div>
            
            <!-- Support Box -->
            <div style="background-color: #e7f3ff; padding: 15px; border-radius: 8px; 
                        margin: 20px 0; border-left: 4px solid #0dcaf0;">
                <p style="color: #055160; line-height: 1.8; font-size: 14px; margin: 0;">
                    <strong>💡 Tipp:</strong> Sie können den Fortschritt Ihres Falls jederzeit 
                    in Ihrem Dashboard einsehen. Dort finden Sie auch alle bisherigen Erstattungen 
                    und können Ihre Auszahlungsmethoden verwalten.
                </p>
            </div>
            
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 30px; margin-top: 40px; 
                    border-top: 3px solid #2950a8;">
            
            <!-- Company Name -->
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="color: #2950a8; margin: 0; font-size: 20px;">
                    {{brand_name}}
                </h3>
            </div>
            
            <!-- Company Address -->
            <div style="text-align: center; margin-bottom: 15px;">
                <p style="color: #666; font-size: 13px; margin: 5px 0; line-height: 1.6;">
                    {{company_address}}
                </p>
            </div>
            
            <!-- Contact Information -->
            <div style="text-align: center; margin-bottom: 15px;">
                <p style="color: #666; font-size: 13px; margin: 5px 0;">
                    <strong>Kontakt:</strong> 
                    <a href="mailto:{{contact_email}}" style="color: #2950a8; text-decoration: none;">
                        {{contact_email}}
                    </a>
                </p>
                <p style="color: #666; font-size: 13px; margin: 5px 0;">
                    <strong>Telefon:</strong> {{contact_phone}}
                </p>
                <p style="color: #666; font-size: 13px; margin: 5px 0;">
                    <strong>Website:</strong> 
                    <a href="{{site_url}}" style="color: #2950a8; text-decoration: none;">
                        {{site_url}}
                    </a>
                </p>
            </div>
            
            <!-- FCA Reference -->
            <div style="text-align: center; margin-bottom: 15px;">
                <p style="color: #666; font-size: 13px; margin: 5px 0;">
                    <strong>FCA Referenz:</strong> {{fca_reference_number}}
                </p>
            </div>
            
            <!-- Copyright -->
            <div style="text-align: center; margin-top: 20px; padding-top: 20px; 
                        border-top: 1px solid #dee2e6;">
                <p style="color: #999; font-size: 12px; margin: 5px 0;">
                    © {{current_year}} {{brand_name}}. Alle Rechte vorbehalten.
                </p>
            </div>
            
            <!-- Links -->
            <div style="text-align: center; margin-top: 15px;">
                <p style="font-size: 12px; margin: 5px 0;">
                    <a href="{{dashboard_url}}" style="color: #2950a8; text-decoration: none; margin: 0 10px;">
                        Dashboard
                    </a> | 
                    <a href="{{site_url}}/impressum" style="color: #2950a8; text-decoration: none; margin: 0 10px;">
                        Impressum
                    </a> | 
                    <a href="{{site_url}}/datenschutz" style="color: #2950a8; text-decoration: none; margin: 0 10px;">
                        Datenschutz
                    </a>
                </p>
            </div>
        </div>
        
    </div>
    
    <!-- Tracking Pixel -->
    <img src="{{site_url}}/track_email.php?token={{tracking_token}}" 
         width="1" height="1" style="display:none;" alt="" />
</body>
</html>',
    'recovery',
    1,
    NOW(),
    NOW()
);
