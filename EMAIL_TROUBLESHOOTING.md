# Email Troubleshooting Guide

## Quick Checklist

If you're not receiving emails from the onboarding system, follow this checklist:

### 1. Verify PHPMailer Installation

```bash
# Check if PHPMailer exists
ls vendor/phpmailer/phpmailer/

# OR check with PHP
php -r "echo class_exists('PHPMailer\PHPMailer\PHPMailer') ? 'PHPMailer OK' : 'NOT FOUND';"
```

**If NOT FOUND:**
```bash
# Install with Composer
composer require phpmailer/phpmailer

# OR download manually from:
# https://github.com/PHPMailer/PHPMailer
# Extract to: vendor/phpmailer/phpmailer/
```

---

### 2. Check SMTP Configuration

```sql
-- Query your SMTP settings
SELECT 
    smtp_host, 
    smtp_port, 
    smtp_encryption,
    smtp_username, 
    smtp_from_email 
FROM system_settings 
WHERE id = 1;
```

**Expected values:**
- `smtp_host`: Your mail server (e.g., 'smtp.gmail.com', 'smtp.office365.com')
- `smtp_port`: Usually 587 (TLS) or 465 (SSL)
- `smtp_encryption`: 'tls' or 'ssl'
- `smtp_username`: Your email account
- `smtp_from_email`: From address

**If NULL or empty:**
```sql
UPDATE system_settings SET 
    smtp_host = 'smtp.gmail.com',
    smtp_port = 587,
    smtp_encryption = 'tls',
    smtp_username = 'your-email@gmail.com',
    smtp_password = 'your-app-password',
    smtp_from_email = 'no-reply@cryptofinanze.de',
    smtp_from_name = 'Crypto Finanz'
WHERE id = 1;
```

---

### 3. Verify Email Template Exists

```sql
-- Check if template is loaded
SELECT 
    template_key, 
    subject, 
    LENGTH(content) as content_length 
FROM email_templates 
WHERE template_key = 'onboarding_complete';
```

**Should return:**
- 1 row
- content_length: ~7000-8000 characters

**If 0 rows:**
```bash
# Import the template
mysql -u username -p cryptofinanze < onboarding_email_template_only.sql
```

---

### 4. Check Server Logs

```bash
# PHP Error Log
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/php_errors.log

# Look for:
# "Onboarding completion email sent to: user@example.com" (SUCCESS)
# or
# "Failed to send onboarding email: [error message]" (ERROR)
```

**Common errors:**
- "PHPMailer class not found" → Install PHPMailer
- "SMTP connect() failed" → Check SMTP settings
- "SMTP Error: Could not authenticate" → Check username/password
- "Template not found" → Import email template

---

### 5. Test SMTP Connection

Create a simple test file: `test_smtp.php`

```php
<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

try {
    $pdo = new PDO("mysql:host=localhost;dbname=cryptofinanze", "username", "password");
    
    $stmt = $pdo->prepare("SELECT * FROM system_settings WHERE id = 1");
    $stmt->execute();
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $settings['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['smtp_username'];
    $mail->Password = $settings['smtp_password'];
    $mail->SMTPSecure = $settings['smtp_encryption'];
    $mail->Port = $settings['smtp_port'];
    
    $mail->setFrom($settings['smtp_from_email'], $settings['smtp_from_name']);
    $mail->addAddress('your-test-email@example.com');
    
    $mail->Subject = 'SMTP Test';
    $mail->Body = 'If you receive this, SMTP is working!';
    
    $mail->send();
    echo "✅ Email sent successfully!";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
```

Run: `php test_smtp.php`

---

### 6. Gmail-Specific Configuration

If using Gmail SMTP:

**Requirements:**
1. Enable 2-factor authentication on your Google account
2. Generate an "App Password" (not your regular password)
3. Use these settings:

```sql
UPDATE system_settings SET 
    smtp_host = 'smtp.gmail.com',
    smtp_port = 587,
    smtp_encryption = 'tls',
    smtp_username = 'your-email@gmail.com',
    smtp_password = 'your-16-char-app-password',  -- NOT your regular password!
    smtp_from_email = 'your-email@gmail.com',
    smtp_from_name = 'Crypto Finanz'
WHERE id = 1;
```

**Generate App Password:**
1. Go to: https://myaccount.google.com/security
2. Enable 2-Step Verification
3. Go to "App passwords"
4. Select "Mail" and generate
5. Copy the 16-character password
6. Use this in `smtp_password` field

---

### 7. Check Spam Folder

Sometimes emails go to spam, especially on first send:
- Check recipient's spam/junk folder
- Mark as "Not Spam" to train filter
- Add sender to contacts

---

### 8. Verify User Email

```sql
-- Check if user has valid email
SELECT id, name, email 
FROM users 
WHERE id = [user_id];
```

Make sure:
- Email is not NULL
- Email is valid format
- Email address is accessible

---

### 9. Check File Permissions

```bash
# onboarding.php should be readable
ls -l onboarding.php
# Should show: -rw-r--r-- or similar

# Vendor folder should be accessible
ls -l vendor/phpmailer/
```

---

### 10. Enable Debug Mode (Temporarily)

Add to onboarding.php email section (after `$mail = new PHPMailer(true);`):

```php
// Enable verbose debug output
$mail->SMTPDebug = 2; // 0 = off, 1 = client, 2 = client and server
$mail->Debugoutput = function($str, $level) {
    error_log("PHPMailer Debug ($level): $str");
};
```

This will show detailed SMTP conversation in error logs.

**REMOVE after debugging!**

---

## Common Solutions

### "Class 'PHPMailer\PHPMailer\PHPMailer' not found"

**Solution:**
```bash
composer require phpmailer/phpmailer
```

### "SMTP connect() failed"

**Solutions:**
1. Check firewall allows outbound SMTP (port 587 or 465)
2. Verify SMTP host is correct
3. Try different port (587 vs 465)
4. Check if hosting provider blocks SMTP

### "SMTP Error: Could not authenticate"

**Solutions:**
1. Double-check username and password
2. For Gmail: Use App Password, not regular password
3. Check if account requires additional security steps

### "Email sent but not received"

**Solutions:**
1. Check spam folder
2. Verify recipient email is correct
3. Check mail server logs
4. Try different recipient address
5. Check sender reputation (SPF/DKIM records)

---

## Still Not Working?

### Debug Step-by-Step:

1. **Test basic PHP mail:**
   ```php
   mail('test@example.com', 'Test', 'Test message');
   ```

2. **Check PHP mail settings:**
   ```bash
   php -i | grep mail
   ```

3. **Test SMTP with telnet:**
   ```bash
   telnet smtp.gmail.com 587
   ```

4. **Review complete error log:**
   ```bash
   tail -100 /var/log/apache2/error.log
   ```

5. **Contact hosting provider** if issue persists

---

## Success Indicators

When email is working correctly, you'll see:

**In error logs:**
```
Onboarding completion email sent to: user@example.com
```

**User receives:**
- Email in inbox (or spam folder)
- Subject: "Willkommen bei Crypto Finanz - Registrierung abgeschlossen"
- German HTML formatted content
- Company branding and contact info

**On completion:**
- User redirected to completion page
- Success message displayed
- Onboarding marked as complete

---

## Need Help?

If you've tried everything and it's still not working:

1. Check all items in this guide
2. Review error logs completely
3. Test with simple test_smtp.php script
4. Verify database tables exist
5. Contact your hosting provider about SMTP restrictions

---

**Remember:** Even if email fails, the onboarding will still complete successfully. The email failure won't block the user from accessing the platform.
