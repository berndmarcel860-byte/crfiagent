-- ========================================
-- Default Email Templates for FundTracer AI
-- AI-Powered Fund Recovery Platform
-- ========================================

-- Insert default email templates if they don't exist
-- These templates use {{variable}} syntax for dynamic content

-- 1. Inactive User Reminder (General)
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('inactive_user_reminder', 
 'We Miss You at FundTracer AI! - {{first_name}}',
 '<h2>Hello {{first_name}},</h2>
<p>We noticed you haven\'t logged in for {{days_inactive}} days.</p>

<div style="background: #f0f8ff; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <h3 style="color: #2950a8;">🤖 AI Update: Your Case is Active!</h3>
    <p>Our AI-powered system has been working on your behalf, and we have important updates to share.</p>
</div>

<p><strong>What\'s New:</strong></p>
<ul>
    <li>✅ Advanced AI analysis of your case</li>
    <li>✅ New recovery strategies identified</li>
    <li>✅ Potential leads for fund recovery</li>
    <li>✅ Enhanced fraud detection insights</li>
</ul>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{login_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Login to Your Dashboard
    </a>
</p>

<p>Don\'t let your case go cold. Every day matters in fund recovery.</p>
<p><strong>Need Help?</strong> Our 24/7 support team is here to assist you.</p>

<p>Best regards,<br>
<strong>FundTracer AI Team</strong></p>',
 '["first_name", "last_name", "days_inactive", "login_url", "email"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 2. Inactive User - 7 Days
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('inactive_user_7_days',
 '⏰ Quick Check-In: Your Case Needs Attention',
 '<h2>Hi {{first_name}},</h2>
<p>It\'s been a week since your last login, and we wanted to check in!</p>

<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
    <h4 style="margin-top: 0;">⚡ Quick Actions Needed</h4>
    <p style="margin: 0;">Your fund recovery case may require your attention for optimal results.</p>
</div>

<p><strong>In the last 7 days:</strong></p>
<ul>
    <li>🔍 AI analyzed {{analysis_count}} new data points</li>
    <li>📊 Case status may have updates</li>
    <li>💼 New recovery options may be available</li>
</ul>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{login_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Check My Dashboard
    </a>
</p>

<p>Stay on top of your recovery journey!</p>

<p>Best regards,<br>
<strong>FundTracer AI Team</strong></p>',
 '["first_name", "last_name", "days_inactive", "login_url", "email", "analysis_count"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 3. Inactive User - 30 Days
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('inactive_user_30_days',
 '🚨 Important: Your Case Requires Immediate Attention',
 '<h2>{{first_name}}, We Need to Talk!</h2>
<p>It\'s been 30 days since your last login, and your case status is at risk.</p>

<div style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0;">
    <h4 style="margin-top: 0; color: #721c24;">⚠️ Critical: Action Required</h4>
    <p style="margin: 0;">Extended inactivity may impact your fund recovery prospects. Our AI system has identified time-sensitive opportunities.</p>
</div>

<p><strong>Why You Should Log In Now:</strong></p>
<ul>
    <li>⏰ Time-sensitive recovery windows may be closing</li>
    <li>🎯 AI has identified new leads specific to your case</li>
    <li>📄 Documents may need review or submission</li>
    <li>💬 Our team has important updates to share</li>
</ul>

<div style="background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin: 20px 0;">
    <p style="margin: 0;"><strong>Don\'t Wait:</strong> Recovery success rates decrease with delayed action. Your case deserves immediate attention.</p>
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{login_url}}" style="background: linear-gradient(135deg, #dc3545, #ff6b6b); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Access My Case NOW
    </a>
</p>

<p>If you\'re facing any issues accessing your account, please contact our support team immediately.</p>

<p>Urgently yours,<br>
<strong>FundTracer AI Recovery Team</strong></p>',
 '["first_name", "last_name", "days_inactive", "login_url", "email", "case_number"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 4. Inactive User - 60 Days (Critical)
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('inactive_user_60_days',
 '🆘 URGENT: Your Case Will Be Archived - {{first_name}}',
 '<h2>URGENT NOTICE: {{first_name}}</h2>

<div style="background: #dc3545; color: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: white;">🆘 CRITICAL: 60 Days Without Activity</h3>
    <p style="margin: 0;">Your fund recovery case will be automatically archived if no action is taken within 7 days.</p>
</div>

<p>We understand life gets busy, but your case cannot wait any longer.</p>

<p><strong>⚠️ What Happens If Your Case is Archived:</strong></p>
<ul>
    <li>❌ Active recovery efforts will be suspended</li>
    <li>❌ AI monitoring will be paused</li>
    <li>❌ Priority support access will be removed</li>
    <li>❌ Time-sensitive opportunities may be lost forever</li>
</ul>

<div style="background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p><strong>💡 Don\'t Give Up:</strong> Many successful recoveries happen after extended periods. Your case deserves a chance!</p>
</div>

<p><strong>Take Action Now:</strong></p>
<ol>
    <li>Login to your dashboard immediately</li>
    <li>Review any pending documents or requests</li>
    <li>Contact our support team if you need assistance</li>
</ol>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{login_url}}" style="background: linear-gradient(135deg, #dc3545, #ff0000); color: white; padding: 18px 35px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; font-size: 16px;">
        REACTIVATE MY CASE NOW
    </a>
</p>

<p><strong>Need Help?</strong><br>
If you\'re experiencing difficulties or have questions, please reply to this email or contact us at support@fundtracerai.com</p>

<p>We\'re here to help you recover your funds,<br>
<strong>FundTracer AI Support Team</strong></p>',
 '["first_name", "last_name", "days_inactive", "login_url", "email", "case_number", "support_email"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 5. KYC Reminder Template
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('kyc_reminder',
 'Complete Your KYC Verification - FundTracer AI',
 '<h2>Hello {{first_name}},</h2>
<p>We noticed that you haven\'t completed your <strong>KYC (Know Your Customer) verification</strong> yet.</p>

<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
    <h3 style="color: #856404; margin-top: 0;">⚠️ KYC Required</h3>
    <p style="margin: 0;">KYC verification is mandatory for fund recovery processing and secure transactions.</p>
</div>

<p><strong>Why is KYC Important?</strong></p>
<ul>
    <li>✅ Required for fund recovery processing</li>
    <li>✅ Ensures secure transactions</li>
    <li>✅ Protects your account</li>
    <li>✅ Unlocks full platform features</li>
    <li>✅ Compliance with financial regulations</li>
</ul>

<div style="background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #0c5460;">📋 Complete Your KYC in 3 Easy Steps:</h3>
    <ol>
        <li>Upload a valid government-issued ID (Passport, Driver\'s License, National ID)</li>
        <li>Provide a recent utility bill or bank statement (for address verification)</li>
        <li>Take a selfie holding your ID (for identity confirmation)</li>
    </ol>
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{kyc_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Complete KYC Verification Now
    </a>
</p>

<p><strong>Need Help?</strong> Our support team is available 24/7 to assist you with the verification process.</p>

<p>Best regards,<br>
<strong>FundTracer AI Compliance Team</strong></p>',
 '["first_name", "last_name", "email", "kyc_url", "support_email"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 6. Scam Platform Alert Template
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('scam_platform_alert',
 '⚠️ SCAM ALERT: {{platform_name}} Detected',
 '<h2>⚠️ New Scam Platform Alert</h2>
<p>Hello {{first_name}},</p>

<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
    <h3 style="color: #856404; margin-top: 0;">⚠️ SCAM ALERT</h3>
    <p style="margin: 0;"><strong>{{platform_name}}</strong> has been identified as a scam platform and added to our database.</p>
</div>

<p><strong>Platform Details:</strong></p>
<ul>
    <li><strong>Name:</strong> {{platform_name}}</li>
    <li><strong>URL:</strong> {{platform_url}}</li>
    <li><strong>Type:</strong> {{platform_type}}</li>
    <li><strong>Description:</strong> {{platform_description}}</li>
</ul>

<div style="background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin: 20px 0;">
    <h3 style="color: #0c5460; margin-top: 0;">🛡️ We\'re Here to Help</h3>
    <p>If you have invested funds with <strong>{{platform_name}}</strong>, our AI-powered recovery team is already working to assist you.</p>
    <p><strong>What We\'re Doing:</strong></p>
    <ul>
        <li>✅ AI analysis of recovery options</li>
        <li>✅ Tracking fund movements</li>
        <li>✅ Legal documentation preparation</li>
        <li>✅ Expert recovery strategies</li>
    </ul>
</div>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{report_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Report Your Case
    </a>
</p>

<p><strong>Stay Safe:</strong></p>
<ul>
    <li>Never share your personal information with suspicious platforms</li>
    <li>Always verify platform legitimacy before investing</li>
    <li>Report any suspicious activity immediately</li>
    <li>Enable two-factor authentication on all accounts</li>
</ul>

<p>Best regards,<br>
<strong>FundTracer AI Security Team</strong></p>',
 '["first_name", "last_name", "email", "platform_name", "platform_url", "platform_type", "platform_description", "report_url"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 7. Case Update Notification Template
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('case_update_notification',
 '📊 Case Update: {{case_number}} - Status Changed',
 '<h2>Case Update for {{first_name}}</h2>
<p>We have important updates regarding your case.</p>

<div style="background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: #0c5460;">📊 Case Information</h3>
    <p><strong>Case Number:</strong> {{case_number}}</p>
    <p><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">{{case_status}}</span></p>
    <p><strong>Last Updated:</strong> {{update_date}}</p>
</div>

<p><strong>Update Details:</strong></p>
<p>{{update_message}}</p>

<p><strong>Next Steps:</strong></p>
<ul>
    <li>Review the update in your dashboard</li>
    <li>Check for any required documents or actions</li>
    <li>Contact our team if you have questions</li>
</ul>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{case_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        View Case Details
    </a>
</p>

<p>We\'re working hard to recover your funds!</p>

<p>Best regards,<br>
<strong>FundTracer AI Case Management Team</strong></p>',
 '["first_name", "last_name", "email", "case_number", "case_status", "update_date", "update_message", "case_url"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- 8. AI Recovery Update Template
INSERT INTO email_templates (template_key, subject, content, variables) VALUES
('ai_recovery_update',
 '🤖 AI Update: New Recovery Insights - {{case_number}}',
 '<h2>AI-Powered Recovery Update</h2>
<p>Hello {{first_name}},</p>

<div style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
    <h3 style="margin-top: 0; color: white;">🤖 AI Analysis Complete</h3>
    <p style="margin: 0;">Our AI system has analyzed your case and identified new recovery opportunities.</p>
</div>

<p><strong>Case: {{case_number}}</strong></p>

<p><strong>AI Findings:</strong></p>
<ul>
    <li>🔍 {{ai_finding_1}}</li>
    <li>📊 {{ai_finding_2}}</li>
    <li>💡 {{ai_finding_3}}</li>
</ul>

<div style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
    <h4 style="margin-top: 0; color: #155724;">✅ Recommended Actions</h4>
    <p>{{recommended_actions}}</p>
</div>

<p><strong>Recovery Probability:</strong> {{recovery_probability}}%</p>

<p style="text-align: center; margin: 30px 0;">
    <a href="{{dashboard_url}}" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        View Full AI Report
    </a>
</p>

<p>Our AI continues to monitor your case 24/7 for new developments.</p>

<p>Best regards,<br>
<strong>FundTracer AI Intelligence Team</strong></p>',
 '["first_name", "last_name", "email", "case_number", "ai_finding_1", "ai_finding_2", "ai_finding_3", "recommended_actions", "recovery_probability", "dashboard_url"]')
ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    variables = VALUES(variables),
    updated_at = CURRENT_TIMESTAMP;

-- Success message
SELECT 'Email templates created/updated successfully!' as Status, COUNT(*) as Templates_Count FROM email_templates WHERE template_key IN (
    'inactive_user_reminder',
    'inactive_user_7_days', 
    'inactive_user_30_days',
    'inactive_user_60_days',
    'kyc_reminder',
    'scam_platform_alert',
    'case_update_notification',
    'ai_recovery_update'
);
