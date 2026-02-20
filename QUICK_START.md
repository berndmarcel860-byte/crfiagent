# Email Templates Enhancement - Quick Start

## 🚀 5-Minute Setup

### Prerequisites
- MySQL/MariaDB database
- PHP 7.4+
- PHPMailer installed
- Access to cryptofinanze database

---

## Step 1: Backup (1 minute)

```bash
mysql -u username -p cryptofinanze < email_templates_backup.sql
```

**Verifies backup was created:**
```sql
SELECT COUNT(*) FROM email_templates_backup;
```

---

## Step 2: Apply Enhanced Templates (1 minute)

```bash
mysql -u username -p cryptofinanze < email_templates_enhanced.sql
```

**Verify new templates:**
```sql
SELECT template_key, category FROM email_templates;
```

Expected output:
- onboarding_complete
- otp_login
- case_status_update

---

## Step 3: Upload PHP Files (1 minute)

Upload to your web root:
```
/your-web-root/
  ├── track_email.php
  └── EmailHelper.php
```

**Test file accessibility:**
```
https://yourdomain.com/track_email.php
(Should return 1x1 transparent GIF)
```

---

## Step 4: Send Test Email (2 minutes)

Create test file `test_email.php`:

```php
<?php
require_once 'config.php';
require_once 'EmailHelper.php';

$emailHelper = new EmailHelper($pdo);

// Send test email to user ID 1
$success = $emailHelper->sendEmail('onboarding_complete', 1);

if ($success) {
    echo "✅ Email sent successfully!";
} else {
    echo "❌ Email failed to send";
}
```

Run: `php test_email.php`

---

## Step 5: Verify Tracking (1 minute)

**Check email was logged:**
```sql
SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 1;
```

**Open email and check tracking:**
```sql
SELECT * FROM email_tracking ORDER BY opened_at DESC LIMIT 1;
```

---

## ✅ Success Checklist

- [ ] Backup created in email_templates_backup table
- [ ] 3 new templates in email_templates
- [ ] track_email.php accessible via web
- [ ] EmailHelper.php uploaded
- [ ] Test email sent successfully
- [ ] Email logged in email_logs
- [ ] Tracking pixel works (check email_tracking after opening email)

---

## 🎯 Next Steps

### Add Custom Template

```sql
INSERT INTO email_templates (
    template_key, 
    subject, 
    content, 
    variables
) VALUES (
    'my_template',
    'Subject with {{user_first_name}}',
    '<html>Content here</html>',
    '["user_first_name", "user_email"]'
);
```

### Send Custom Email

```php
$emailHelper->sendEmail('my_template', $userId, [
    'custom_var' => 'custom_value'
]);
```

---

## 📊 Monitor

**View statistics:**
```sql
-- Email stats
SELECT status, COUNT(*) FROM email_logs GROUP BY status;

-- Open rate
SELECT 
    COUNT(CASE WHEN status='opened' THEN 1 END) * 100.0 / COUNT(*) as open_rate
FROM email_logs;
```

---

## 🐛 Troubleshooting

**Email not sending?**
1. Check SMTP settings: `SELECT * FROM smtp_settings;`
2. Check PHP error log: `tail -f /var/log/apache2/error.log`
3. Verify PHPMailer: `ls vendor/phpmailer/`

**Tracking not working?**
1. Open email in email client
2. Check if images are blocked
3. Verify tracking pixel URL is correct
4. Some clients block tracking (normal)

---

## 📚 Documentation

Full guides available:
- **EMAIL_TEMPLATES_ENHANCEMENT_GUIDE.md** - Complete guide
- **EMAIL_ENHANCEMENT_SUMMARY.md** - Project summary

---

## ✅ Done!

Your email system is now enhanced with:
- ✅ Dynamic variables from database
- ✅ Email tracking (sent + opened)
- ✅ Professional HTML templates
- ✅ Easy-to-use PHP class

**Status:** Ready to use! 🚀
