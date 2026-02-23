-- Email Template for KYC Pending Notification
-- This template is used when a user submits KYC documents and they are pending review

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
    'kyc_pending',
    'KYC Dokumente unter Überprüfung',
    'Ihre KYC-Dokumente wurden eingereicht - {{brand_name}}',
    '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Dokumente eingereicht</title>
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
            
            <!-- Greeting -->
            <h2 style="color: #2c3e50; margin-top: 0;">
                KYC-Dokumente erfolgreich eingereicht
            </h2>
            
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Hallo {{first_name}} {{last_name}},
            </p>
            
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Ihre KYC-Dokumente wurden erfolgreich bei uns eingereicht und werden nun überprüft.
            </p>
            
            <!-- Document Information Box -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; 
                        margin: 20px 0; border-left: 4px solid #2950a8;">
                <h3 style="color: #2950a8; margin-top: 0;">Dokumentinformationen</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #666;"><strong>Dokumenttyp:</strong></td>
                        <td style="padding: 8px 0; color: #2c3e50;">{{document_type}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666;"><strong>KYC-ID:</strong></td>
                        <td style="padding: 8px 0; color: #2c3e50;">{{kyc_id}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666;"><strong>Eingereicht am:</strong></td>
                        <td style="padding: 8px 0; color: #2c3e50;">{{date}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666;"><strong>Status:</strong></td>
                        <td style="padding: 8px 0; color: #2c3e50;">
                            <span style="background-color: #ffc107; color: #000; padding: 4px 12px; 
                                         border-radius: 4px; font-weight: bold;">
                                Überprüfung ausstehend
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- What's Next Section -->
            <div style="margin: 30px 0;">
                <h3 style="color: #2950a8;">Was passiert als nächstes?</h3>
                <p style="color: #666; line-height: 1.8; font-size: 15px;">
                    Unser Compliance-Team wird Ihre Dokumente sorgfältig prüfen. 
                    Dieser Prozess dauert normalerweise <strong>1-3 Werktage</strong>. 
                    Sie erhalten eine E-Mail, sobald die Überprüfung abgeschlossen ist.
                </p>
                <p style="color: #666; line-height: 1.8; font-size: 15px;">
                    Falls zusätzliche Informationen oder Dokumente benötigt werden, 
                    werden wir uns umgehend bei Ihnen melden.
                </p>
            </div>
            
            <!-- Support Contact -->
            <div style="background-color: #e8f4f8; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <p style="color: #666; line-height: 1.8; font-size: 14px; margin: 0;">
                    <strong>Fragen?</strong> Unser Support-Team steht Ihnen gerne zur Verfügung unter:
                    <br><a href="mailto:{{support_email}}" 
                          style="color: #2950a8; text-decoration: none;">{{support_email}}</a>
                </p>
            </div>
            
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 30px; margin-top: 40px; 
                    border-top: 3px solid #2950a8;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="color: #2950a8; margin: 0; font-size: 20px;">
                    {{brand_name}}
                </h3>
            </div>
            
            <div style="text-align: center; margin-bottom: 15px;">
                <p style="color: #666; font-size: 13px; margin: 5px 0; line-height: 1.6;">
                    {{company_address}}
                </p>
            </div>
            
            <div style="text-align: center; margin-bottom: 15px;">
                <p style="color: #666; font-size: 13px; margin: 5px 0;">
                    <strong>Kontakt:</strong> 
                    <a href="mailto:{{contact_email}}" 
                       style="color: #2950a8; text-decoration: none;">{{contact_email}}</a>
                </p>
                <p style="color: #666; font-size: 13px; margin: 5px 0;">
                    <strong>Website:</strong> 
                    <a href="{{site_url}}" 
                       style="color: #2950a8; text-decoration: none;">{{site_name}}</a>
                </p>
            </div>
            
            <div style="text-align: center; margin-bottom: 15px;">
                <p style="color: #666; font-size: 13px; margin: 5px 0;">
                    <strong>FCA Referenz:</strong> {{fca_reference_number}}
                </p>
            </div>
            
            <div style="text-align: center; margin-top: 20px; padding-top: 20px; 
                        border-top: 1px solid #dee2e6;">
                <p style="color: #999; font-size: 12px; margin: 5px 0;">
                    © {{current_year}} {{brand_name}}. Alle Rechte vorbehalten.
                </p>
            </div>
        </div>
        
    </div>
    
    <!-- Email Tracking Pixel -->
    <img src="{{site_url}}/track_email.php?token={{tracking_token}}" 
         width="1" height="1" style="display:none;" alt="" />
         
</body>
</html>',
    'kyc',
    1,
    NOW(),
    NOW()
);
