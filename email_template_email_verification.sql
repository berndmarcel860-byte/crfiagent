-- Email Verification Template
-- Insert email template for email verification

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
    'email_verification',
    'Email Verification',
    'Verify Your Email Address - {{brand_name}}',
    '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    
                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;">
                                {{brand_name}}
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #333333; margin-top: 0; font-size: 24px;">
                                Verify Your Email Address
                            </h2>
                            
                            <p style="color: #666666; font-size: 16px; line-height: 1.6; margin: 20px 0;">
                                Hello {{user_first_name}},
                            </p>
                            
                            <p style="color: #666666; font-size: 16px; line-height: 1.6; margin: 20px 0;">
                                Thank you for registering with {{brand_name}}. To complete your registration and access all features, please verify your email address by clicking the button below.
                            </p>
                            
                            <!-- Verification Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{verification_url}}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 50px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                            Verify Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Alternative Link -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 30px 0; border-radius: 4px;">
                                <p style="color: #666666; font-size: 14px; margin: 0 0 10px 0;">
                                    <strong>If the button doesn''t work, copy and paste this link into your browser:</strong>
                                </p>
                                <p style="color: #667eea; font-size: 14px; margin: 0; word-break: break-all;">
                                    {{verification_url}}
                                </p>
                            </div>
                            
                            <!-- Important Info -->
                            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                                <p style="color: #856404; font-size: 14px; margin: 0;">
                                    <strong>⚠️ Important:</strong> This verification link will expire in {{expires_in}}. If it expires, you can request a new one from your profile page.
                                </p>
                            </div>
                            
                            <p style="color: #666666; font-size: 16px; line-height: 1.6; margin: 20px 0;">
                                If you didn''t create an account with {{brand_name}}, please ignore this email or contact our support team if you have concerns.
                            </p>
                            
                            <!-- Benefits Section -->
                            <div style="margin-top: 30px;">
                                <h3 style="color: #333333; font-size: 18px; margin-bottom: 15px;">
                                    Why verify your email?
                                </h3>
                                <ul style="color: #666666; font-size: 14px; line-height: 1.8; padding-left: 20px;">
                                    <li>Secure your account</li>
                                    <li>Receive important notifications</li>
                                    <li>Access all platform features</li>
                                    <li>Enhance account recovery options</li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Support Section -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; border-top: 1px solid #e9ecef;">
                            <p style="color: #666666; font-size: 14px; text-align: center; margin: 0 0 10px 0;">
                                Need help? Contact our support team:
                            </p>
                            <p style="color: #667eea; font-size: 14px; text-align: center; margin: 0;">
                                <a href="mailto:{{contact_email}}" style="color: #667eea; text-decoration: none;">
                                    {{contact_email}}
                                </a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #2c3e50; padding: 30px; text-align: center;">
                            <p style="color: #ffffff; font-size: 14px; margin: 0 0 10px 0;">
                                <strong>{{brand_name}}</strong>
                            </p>
                            <p style="color: #bdc3c7; font-size: 12px; margin: 5px 0;">
                                {{company_address}}
                            </p>
                            <p style="color: #bdc3c7; font-size: 12px; margin: 5px 0;">
                                {{contact_email}} | {{contact_phone}}
                            </p>
                            <p style="color: #bdc3c7; font-size: 12px; margin: 5px 0;">
                                FCA Reference: {{fca_reference_number}}
                            </p>
                            <p style="color: #95a5a6; font-size: 11px; margin: 15px 0 0 0;">
                                © {{current_year}} {{brand_name}}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
    
    <!-- Tracking Pixel -->
    <img src="{{site_url}}/track_email.php?token={{tracking_token}}" width="1" height="1" style="display:none;" alt="" />
</body>
</html>',
    'account',
    1,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    template_name = VALUES(template_name),
    subject = VALUES(subject),
    content = VALUES(content),
    category = VALUES(category),
    is_active = VALUES(is_active),
    updated_at = NOW();
