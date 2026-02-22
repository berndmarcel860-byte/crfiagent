-- Email Template: Wallet Verified
-- Template Key: wallet_verified
-- Category: wallet
-- Description: Email notification sent to users when their cryptocurrency wallet is verified
-- Based on: case_created template structure

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
    'wallet_verified',
    'Wallet Verifiziert',
    'Ihre Wallet wurde verifiziert - {{brand_name}}',
    '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Verifiziert</title>
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
                    <span style="color: #ffffff; font-size: 48px;">✓</span>
                </div>
            </div>
            
            <!-- Heading -->
            <h2 style="color: #2c3e50; margin-top: 0; text-align: center;">
                Wallet erfolgreich verifiziert!
            </h2>
            
            <!-- Greeting -->
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Hallo {{user_first_name}},
            </p>
            
            <!-- Message -->
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Ihre Wallet wurde erfolgreich verifiziert und ist nun für Transaktionen aktiv.
            </p>
            
            <!-- Wallet Details Box -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; 
                        margin: 20px 0; border-left: 4px solid #2950a8;">
                <h3 style="color: #2950a8; margin-top: 0; font-size: 18px;">
                    Wallet-Details
                </h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-weight: bold;">
                            Kryptowährung:
                        </td>
                        <td style="padding: 8px 0; color: #2c3e50;">
                            {{cryptocurrency}}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-weight: bold;">
                            Wallet ID:
                        </td>
                        <td style="padding: 8px 0; color: #2c3e50;">
                            {{wallet_id}}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-weight: bold;">
                            Verifizierungs-TXID:
                        </td>
                        <td style="padding: 8px 0; color: #2c3e50; word-break: break-all; font-size: 12px;">
                            {{verification_txid}}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-weight: bold;">
                            Verifiziert am:
                        </td>
                        <td style="padding: 8px 0; color: #2c3e50;">
                            {{verification_date}}
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Next Steps -->
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Sie können jetzt Einzahlungen und Auszahlungen mit dieser Wallet durchführen.
            </p>
            
            <!-- Call to Action -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{dashboard_url}}/wallets" 
                   style="display: inline-block; padding: 15px 40px; 
                          background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%); 
                          color: #ffffff; text-decoration: none; border-radius: 5px; 
                          font-weight: bold; font-size: 16px;">
                    Zu meinen Wallets
                </a>
            </div>
            
            <!-- Additional Info -->
            <p style="color: #666; line-height: 1.8; font-size: 14px;">
                Bei Fragen zu Ihrer Wallet stehen wir Ihnen gerne zur Verfügung. 
                Kontaktieren Sie uns unter 
                <a href="mailto:{{contact_email}}" style="color: #2950a8;">{{contact_email}}</a>.
            </p>
            
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
    'wallet',
    1,
    NOW(),
    NOW()
);
