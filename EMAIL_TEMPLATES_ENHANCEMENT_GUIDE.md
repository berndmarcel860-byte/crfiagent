# Email Templates Enhancement Guide

## Overview

This guide documents the enhanced email templates system with dynamic variables from database tables and email tracking functionality.

---

## 📋 Files Created

### 1. **email_templates_backup.sql**
- Backup script for original email_templates table
- Creates `email_templates_backup` table with all original data
- Run BEFORE applying enhancements

### 2. **email_templates_enhanced.sql**
- Enhanced email_templates table with new structure
- Includes improved templates with dynamic variables
- Adds tracking pixel integration

### 3. **track_email.php**
- Email tracking pixel handler
- Tracks when emails are opened
- Records IP address, user agent, referrer
- Updates email_logs and email_tracking tables

### 4. **EmailHelper.php**
- PHP class for sending tracked emails
- Automatically fetches data from database tables
- Replaces variables in templates
- Handles conditional blocks
- Generates tracking tokens
- Integrates with PHPMailer

### 5. **EMAIL_TEMPLATES_ENHANCEMENT_GUIDE.md**
- This documentation file

---

## 🗄️ Database Structure

### Tables Used

#### email_templates (Enhanced)
```sql
- id (auto_increment)
- template_key (unique)
- subject
- content (HTML with variables)
- variables (JSON array)
- description
- category
- is_active
- created_at
- updated_at
```

#### email_logs
```sql
- id
- template_id
- recipient
- subject
- content
- tracking_token (unique per email)
- status (sent/failed/delivered/opened)
- sent_at
- opened_at
- error_message
```

#### email_tracking
```sql
- id
- tracking_token
- ip_address
- user_agent
- referrer
- opened_at
```

---

## 🔧 Installation Steps

### Step 1: Backup Current Templates
```bash
mysql -u username -p database_name < email_templates_backup.sql
```

### Step 2: Apply Enhanced Templates
```bash
mysql -u username -p database_name < email_templates_enhanced.sql
```

### Step 3: Upload PHP Files
```bash
# Upload these files to your web root:
- track_email.php
- EmailHelper.php
```

### Step 4: Verify Tables Exist
```sql
SHOW TABLES LIKE 'email%';
-- Should show: email_templates, email_logs, email_tracking
```

---

## 📝 Available Variables

### From Users Table
- `{{user_first_name}}` - First name
- `{{user_last_name}}` - Last name
- `{{user_email}}` - Email address
- `{{user_created_at}}` - Registration date (formatted)

### From user_payment_methods Table (Bank)
- `{{has_bank_account}}` - "yes" or "no"
- `{{bank_name}}` - Bank name
- `{{account_holder}}` - Account holder name
- `{{iban}}` - IBAN number
- `{{bic}}` - BIC/SWIFT code

### From user_payment_methods Table (Crypto)
- `{{has_crypto_wallet}}` - "yes" or "no"
- `{{cryptocurrency}}` - Cryptocurrency type (BTC, ETH, etc.)
- `{{network}}` - Network (Bitcoin, Ethereum, etc.)
- `{{wallet_address}}` - Wallet address

### From system_settings Table
- `{{brand_name}}` - Company/brand name
- `{{company_address}}` - Company address
- `{{contact_email}}` - Contact email
- `{{contact_phone}}` - Contact phone
- `{{fca_reference_number}}` - FCA reference number
- `{{site_url}}` - Website URL

### System Variables
- `{{tracking_token}}` - Unique tracking token
- `{{current_year}}` - Current year
- `{{update_date}}` - Current date/time

---

## 💻 Usage Examples

### Example 1: Send Onboarding Email
```php
<?php
require_once 'EmailHelper.php';
require_once 'config.php';

$emailHelper = new EmailHelper($pdo);

// Send onboarding email
$success = $emailHelper->sendEmail('onboarding_complete', $userId);

if ($success) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email";
}
```

### Example 2: Send OTP Email with Custom Variables
```php
$customVariables = [
    'otp_code' => '123456'
];

$emailHelper->sendEmail('otp_login', $userId, $customVariables);
```

### Example 3: Send Case Status Update
```php
$customVariables = [
    'case_number' => 'CASE-2024-001',
    'new_status' => 'In Bearbeitung',
    'status_notes' => 'Ihr Fall wird geprüft',
    'update_date' => date('d.m.Y H:i')
];

$emailHelper->sendEmail('case_status_update', $userId, $customVariables);
```

---

## 📊 Email Tracking

### How Tracking Works

1. **Email Sent**
   - Unique `tracking_token` generated (MD5 hash)
   - Token stored in `email_logs` table
   - Tracking pixel added to email HTML

2. **Email Received**
   - Status: `sent` in email_logs
   - Record created with timestamp

3. **Email Opened**
   - User's email client loads tracking pixel
   - Request to `track_email.php?token=XXX`
   - Status updated to `opened` in email_logs
   - Record created in `email_tracking` with:
     - IP address
     - User agent
     - Referrer
     - Timestamp

### Tracking Pixel Code
```html
<img src="{{site_url}}/track_email.php?token={{tracking_token}}" 
     width="1" height="1" alt="" style="display:block;" />
```

### View Tracking Stats
```sql
-- Get email open rate
SELECT 
    (SELECT COUNT(*) FROM email_logs WHERE status = 'opened') * 100.0 / 
    (SELECT COUNT(*) FROM email_logs) AS open_rate_percentage;

-- Get emails by status
SELECT status, COUNT(*) as count 
FROM email_logs 
GROUP BY status;

-- Get detailed tracking info
SELECT 
    el.recipient,
    el.subject,
    el.sent_at,
    el.opened_at,
    et.ip_address,
    et.user_agent
FROM email_logs el
LEFT JOIN email_tracking et ON el.tracking_token = et.tracking_token
WHERE el.status = 'opened'
ORDER BY el.sent_at DESC;
```

---

## 🎨 Template Conditionals

### If Statement
Show content only if variable is truthy:

```html
{{#if has_bank_account}}
<p>Bank: {{bank_name}}</p>
<p>IBAN: {{iban}}</p>
{{/if}}
```

### Multiple Conditionals
```html
{{#if has_bank_account}}
<!-- Bank info -->
{{/if}}

{{#if has_crypto_wallet}}
<!-- Crypto wallet info -->
{{/if}}
```

---

## 🔐 Security Considerations

### 1. Tracking Token
- Generated using `md5(uniqid($email, true))`
- Unique per email
- Not guessable
- Links email to recipient

### 2. SQL Injection Prevention
- All database queries use prepared statements
- User input sanitized

### 3. XSS Prevention
- Email content escaped
- Variables sanitized before replacement

### 4. Privacy
- IP addresses collected for tracking
- Comply with GDPR/privacy laws
- Provide opt-out mechanism if required

---

## 🛠️ Customization

### Add New Template
```sql
INSERT INTO email_templates (
    template_key, 
    subject, 
    content, 
    variables, 
    description, 
    category
) VALUES (
    'my_new_template',
    'Subject with {{user_first_name}}',
    '<html>Email content here</html>',
    '["user_first_name", "user_email"]',
    'Description of template',
    'general'
);
```

### Add New Variable
Edit EmailHelper.php and add to `$variables` array:
```php
'my_new_variable' => $someValue,
```

### Modify Template HTML
```sql
UPDATE email_templates 
SET content = 'New HTML content here'
WHERE template_key = 'template_name';
```

---

## 📈 Monitoring

### Check Email Send Status
```sql
SELECT 
    DATE(sent_at) as date,
    status,
    COUNT(*) as count
FROM email_logs
GROUP BY DATE(sent_at), status
ORDER BY date DESC;
```

### Find Failed Emails
```sql
SELECT 
    recipient, 
    subject, 
    error_message, 
    sent_at
FROM email_logs
WHERE status = 'failed'
ORDER BY sent_at DESC;
```

### Track Open Rates by Template
```sql
SELECT 
    t.template_key,
    t.subject,
    COUNT(l.id) as total_sent,
    SUM(CASE WHEN l.status = 'opened' THEN 1 ELSE 0 END) as opened,
    ROUND(SUM(CASE WHEN l.status = 'opened' THEN 1 ELSE 0 END) * 100.0 / COUNT(l.id), 2) as open_rate
FROM email_templates t
LEFT JOIN email_logs l ON t.id = l.template_id
GROUP BY t.id
ORDER BY total_sent DESC;
```

---

## 🐛 Troubleshooting

### Email Not Sending
1. Check SMTP settings in database
2. Verify PHPMailer is installed
3. Check error logs: `tail -f /var/log/apache2/error.log`
4. Test SMTP connection manually

### Tracking Not Working
1. Verify `track_email.php` is accessible
2. Check database connection in track_email.php
3. Ensure tracking pixel is in email HTML
4. Some email clients block images (expected)

### Variables Not Replaced
1. Check variable spelling: `{{variable_name}}`
2. Verify data exists in database
3. Check EmailHelper.php for variable mapping
4. Test with simple template first

### Conditional Not Working
1. Verify syntax: `{{#if var}}...{{/if}}`
2. Check variable value (must be truthy)
3. Test conditional logic in EmailHelper.php

---

## 📞 Support

For issues or questions:
- Review error logs
- Check database structure
- Verify all files uploaded
- Test with simple template first

---

## ✅ Checklist

- [ ] Backup created (email_templates_backup.sql)
- [ ] Enhanced templates applied (email_templates_enhanced.sql)
- [ ] track_email.php uploaded
- [ ] EmailHelper.php uploaded
- [ ] SMTP settings configured
- [ ] Test email sent successfully
- [ ] Tracking pixel working
- [ ] Variables replaced correctly
- [ ] Conditional blocks working

---

**Status:** ✅ READY FOR PRODUCTION

**Version:** 1.0  
**Date:** 2026-02-20  
**Author:** Email System Enhancement Project
