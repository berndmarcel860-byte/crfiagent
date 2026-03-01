-- Email Templates for Deposit Notifications
-- These templates are used by AdminEmailHelper to send deposit confirmation emails
-- 
-- INSTALLATION:
-- mysql -u username -p database_name < email_template_deposit.sql
--
-- AVAILABLE VARIABLES:
-- User Variables: {first_name}, {last_name}, {full_name}, {email}, {balance}, {user_id}
-- Deposit Variables: {deposit_amount}, {deposit_reference}, {payment_method}, {deposit_status}, {date}
-- Company Variables: {brand_name}, {site_url}, {contact_email}, {contact_phone}, {company_address}
-- System Variables: {current_year}, {current_date}, {current_time}, {dashboard_url}, {login_url}

-- Template for Completed Deposits
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`) VALUES
('deposit_completed', 
 'Deposit Completed - €{deposit_amount}',
 '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #28a745; border-bottom: 3px solid #28a745; padding-bottom: 10px;">
        ✓ Deposit Completed
    </h2>
    
    <p>Dear {first_name} {last_name},</p>
    
    <p style="font-size: 16px; color: #333;">
        Great news! Your deposit has been successfully processed and your account balance has been updated.
    </p>
    
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #333;">Deposit Details</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Amount:</strong></td>
                <td style="padding: 8px 0; color: #28a745; font-size: 18px; font-weight: bold;">€{deposit_amount}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Reference:</strong></td>
                <td style="padding: 8px 0;">{deposit_reference}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Payment Method:</strong></td>
                <td style="padding: 8px 0;">{payment_method}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Status:</strong></td>
                <td style="padding: 8px 0; color: #28a745;"><strong>✓ Completed</strong></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Date:</strong></td>
                <td style="padding: 8px 0;">{date}</td>
            </tr>
        </table>
    </div>
    
    <div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #155724;">
            <strong>✓ Your account balance has been updated.</strong><br>
            You can view your updated balance and transaction history in your dashboard.
        </p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{dashboard_url}" style="background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            View Dashboard
        </a>
    </div>
    
    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">
    
    <p style="color: #666; font-size: 14px;">
        If you have any questions about this deposit, please contact our support team at 
        <a href="mailto:{contact_email}" style="color: #007bff;">{contact_email}</a>.
    </p>
    
    <p style="color: #999; font-size: 12px; margin-top: 30px;">
        This is an automated notification from {brand_name}. Please do not reply to this email.
    </p>
</div>',
 '["first_name", "last_name", "deposit_amount", "deposit_reference", "payment_method", "date", "dashboard_url", "contact_email", "brand_name"]'
)
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- Template for Pending Deposits
INSERT INTO `email_templates` (`template_key`, `subject`, `content`, `variables`) VALUES
('deposit_pending', 
 'Deposit Pending - €{deposit_amount}',
 '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #ffc107; border-bottom: 3px solid #ffc107; padding-bottom: 10px;">
        ⏳ Deposit Pending
    </h2>
    
    <p>Dear {first_name} {last_name},</p>
    
    <p style="font-size: 16px; color: #333;">
        We have received your deposit request and it is currently being processed.
    </p>
    
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #333;">Deposit Details</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Amount:</strong></td>
                <td style="padding: 8px 0; color: #ffc107; font-size: 18px; font-weight: bold;">€{deposit_amount}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Reference:</strong></td>
                <td style="padding: 8px 0;">{deposit_reference}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Payment Method:</strong></td>
                <td style="padding: 8px 0;">{payment_method}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Status:</strong></td>
                <td style="padding: 8px 0; color: #ffc107;"><strong>⏳ Pending</strong></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Date:</strong></td>
                <td style="padding: 8px 0;">{date}</td>
            </tr>
        </table>
    </div>
    
    <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #856404;">
            <strong>⏳ Your deposit is being processed.</strong><br>
            This usually takes 1-2 business days. You will receive another notification once your deposit is completed and your balance is updated.
        </p>
    </div>
    
    <div style="background-color: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 20px 0;">
        <p style="margin: 0; color: #004085; font-size: 14px;">
            <strong>ℹ️ What happens next?</strong><br>
            • Our team will verify your payment<br>
            • Once verified, your balance will be updated automatically<br>
            • You will receive a confirmation email<br>
            • Processing time: Usually within 1-2 business days
        </p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{dashboard_url}" style="background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
            View Dashboard
        </a>
    </div>
    
    <hr style="border: none; border-top: 1px solid #dee2e6; margin: 30px 0;">
    
    <p style="color: #666; font-size: 14px;">
        If you have any questions about this deposit, please contact our support team at 
        <a href="mailto:{contact_email}" style="color: #007bff;">{contact_email}</a>.
    </p>
    
    <p style="color: #999; font-size: 12px; margin-top: 30px;">
        This is an automated notification from {brand_name}. Please do not reply to this email.
    </p>
</div>',
 '["first_name", "last_name", "deposit_amount", "deposit_reference", "payment_method", "date", "dashboard_url", "contact_email", "brand_name"]'
)
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- Verify templates were inserted
SELECT 
    template_key, 
    subject, 
    LENGTH(content) as content_length,
    created_at,
    updated_at
FROM email_templates 
WHERE template_key IN ('deposit_completed', 'deposit_pending')
ORDER BY template_key;
