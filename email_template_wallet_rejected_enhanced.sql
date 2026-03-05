-- Enhanced Email Template: Wallet Rejected
-- Template Key: wallet_rejected
-- Category: wallet
-- Description: Enhanced email notification with formal greeting and helpful guidance when wallet verification is rejected
-- This is an UPDATE statement to replace the existing template with enhanced content

UPDATE email_templates SET
    subject = 'Wallet Verifizierung abgelehnt - {{cryptocurrency}} - {{brand_name}}',
    content = '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Verifizierung Abgelehnt</title>
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
            
            <!-- Error Icon -->
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="background-color: #dc3545; width: 80px; height: 80px; 
                            border-radius: 50%; margin: 0 auto; display: flex; 
                            align-items: center; justify-content: center;">
                    <span style="color: #ffffff; font-size: 48px; font-weight: bold;">✕</span>
                </div>
            </div>
            
            <!-- Heading -->
            <h2 style="color: #dc3545; margin-top: 0; text-align: center; font-size: 24px;">
                Wallet Verifizierung abgelehnt
            </h2>
            
            <!-- Greeting -->
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Sehr geehrte/r {{user_first_name}} {{user_last_name}},
            </p>
            
            <!-- Message -->
            <p style="color: #666; line-height: 1.8; font-size: 15px;">
                Leider konnten wir Ihre Wallet-Verifizierung für <strong>{{cryptocurrency}}</strong> 
                nicht genehmigen. Bitte überprüfen Sie die unten angegebenen Details und die Ablehnungsgründe.
            </p>
            
            <!-- Wallet Details Box -->
            <div style="background-color: #fef2f2; padding: 25px; border-radius: 8px; 
                        margin: 25px 0; border-left: 4px solid #dc3545;">
                <h3 style="color: #dc3545; margin-top: 0; font-size: 18px;">
                    💳 Wallet-Details
                </h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-weight: bold; width: 40%;">
                            Kryptowährung:
                        </td>
                        <td style="padding: 10px 0; color: #2c3e50;">
                            {{cryptocurrency}}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-weight: bold;">
                            Wallet ID:
                        </td>
                        <td style="padding: 10px 0; color: #2c3e50;">
                            #{{wallet_id}}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #666; font-weight: bold;">
                            Eingereichte TXID:
                        </td>
                        <td style="padding: 10px 0; color: #2c3e50; word-break: break-all; font-size: 12px;">
                            {{verification_txid}}
                        </td>
                    </tr>
                    <tr style="background-color: #fee2e2;">
                        <td style="padding: 10px 0; color: #666; font-weight: bold;">
                            Abgelehnt am:
                        </td>
                        <td style="padding: 10px 0; color: #991b1b; font-weight: bold;">
                            {{rejection_date}}
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Rejection Reason Box -->
            <div style="background-color: #fff3cd; padding: 25px; border-radius: 8px; 
                        margin: 25px 0; border-left: 4px solid #ffc107;">
                <h3 style="color: #856404; margin-top: 0; font-size: 18px;">
                    ⚠️ Ablehnungsgrund
                </h3>
                <p style="color: #856404; line-height: 1.8; font-size: 15px; margin: 0; font-weight: 500;">
                    {{rejection_reason}}
                </p>
            </div>
            
            <!-- Common Rejection Reasons -->
            <div style="margin: 30px 0;">
                <h3 style="color: #2c3e50; font-size: 18px; margin-bottom: 15px;">
                    Häufige Ablehnungsgründe
                </h3>
                <ul style="color: #666; line-height: 1.8; font-size: 15px; padding-left: 20px;">
                    <li><strong>Falsche Transaktions-ID:</strong> Die TXID stimmt nicht mit der Blockchain überein</li>
                    <li><strong>Falscher Betrag:</strong> Der gesendete Betrag entspricht nicht dem Verifizierungsbetrag</li>
                    <li><strong>Falsche Adresse:</strong> Die Transaktion wurde an eine andere Adresse gesendet</li>
                    <li><strong>Unzureichende Bestätigungen:</strong> Die Transaktion ist noch nicht vollständig bestätigt</li>
                </ul>
            </div>
            
            <!-- Next Steps -->
            <div style="background-color: #dbeafe; padding: 20px; border-radius: 8px; 
                        margin: 25px 0; border-left: 4px solid #2196F3;">
                <h3 style="color: #1e40af; margin-top: 0; font-size: 18px;">
                    📋 Nächste Schritte
                </h3>
                <ol style="color: #1e3a8a; line-height: 1.8; font-size: 15px; margin: 10px 0; padding-left: 20px;">
                    <li>Überprüfen Sie die angegebene Transaktions-ID auf Richtigkeit</li>
                    <li>Stellen Sie sicher, dass Sie den exakten Betrag an die richtige Adresse gesendet haben</li>
                    <li>Warten Sie, bis die Transaktion auf der Blockchain vollständig bestätigt wurde (mindestens 3 Bestätigungen empfohlen)</li>
                    <li>Reichen Sie eine neue Verifizierung mit den korrekten Daten ein über Ihr Dashboard</li>
                </ol>
            </div>
            
            <!-- Call to Action -->
            <div style="text-align: center; margin: 35px 0;">
                <a href="{{dashboard_url}}/payment-methods.php" 
                   style="display: inline-block; padding: 15px 40px; 
                          background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%); 
                          color: #ffffff; text-decoration: none; border-radius: 8px; 
                          font-weight: bold; font-size: 16px; box-shadow: 0 4px 6px rgba(41, 80, 168, 0.3);">
                    Zu meinen Wallets →
                </a>
            </div>
            
            <!-- Support Tip -->
            <div style="background-color: #e7f3ff; padding: 20px; border-radius: 8px; 
                        margin: 25px 0; border-left: 4px solid #0dcaf0;">
                <p style="color: #055160; line-height: 1.8; font-size: 14px; margin: 0;">
                    <strong>💡 Tipp:</strong> Verwenden Sie einen Blockchain-Explorer 
                    (z.B. blockchain.com, etherscan.io), um Ihre Transaktion zu überprüfen, 
                    bevor Sie sie einreichen. Die Transaktions-ID muss exakt mit der auf der Blockchain übereinstimmen.
                </p>
            </div>
            
            <!-- Additional Info -->
            <p style="color: #666; line-height: 1.8; font-size: 14px; margin-top: 30px;">
                Bei Fragen zur Ablehnung oder Hilfe bei der erneuten Verifizierung stehen wir Ihnen gerne zur Verfügung. 
                Kontaktieren Sie uns unter 
                <a href="mailto:{{contact_email}}" style="color: #2950a8; text-decoration: none;">{{contact_email}}</a> 
                oder telefonisch unter {{contact_phone}}.
            </p>
            
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 30px; margin-top: 40px; 
                    border-top: 3px solid #2950a8;">
            
            <!-- Company Name -->
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="color: #2950a8; margin: 0; font-size: 20px; font-weight: bold;">
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
                    <strong>E-Mail:</strong> 
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
</html>'
WHERE template_key = 'wallet_rejected';
