# Email Verification Troubleshooting Guide

## Bug Fix Summary

### Issue
Users were getting "An error occurred. Please try again later." when clicking the send email verification button.

### Root Cause
**Variable name mismatch** between the ajax endpoint and email template:
- `ajax/send_verification_email.php` was sending: `verification_url`
- `email_template_email_verification.sql` expects: `verification_link`

### Fix Applied
Changed line 74 in `ajax/send_verification_email.php`:
```php
// Before:
$customVars = [
    'verification_url' => $verificationUrl,
    'verification_token' => $token,
    'expires_in' => '1 hour'
];

// After:
$customVars = [
    'verification_link' => $verificationLink
];
```

### How This Fixes The Error
1. EmailHelper now receives the correct variable name
2. Template can properly replace `{verification_link}` placeholder
3. Email sends successfully with working verification link
4. No more "An error occurred" message

---

## Testing Checklist

After applying the fix, verify everything works:

### 1. Verify Template Installation
```sql
SELECT * FROM email_templates WHERE template_key = 'email_verification';
```
Should return 1 row with the email verification template.

### 2. Check Database Columns
```sql
DESCRIBE users;
```
Ensure these columns exist:
- `verification_token` (VARCHAR 64)
- `verification_token_expires` (DATETIME)
- `is_verified` (TINYINT)
- `email_verified_at` (DATETIME)

### 3. Test Email Sending
1. Log into user dashboard
2. Go to profile page
3. Click "Resend Verification Email" button
4. Should see: "Verification email sent successfully!"
5. Check email inbox

### 4. Verify Link Works
1. Open verification email
2. Click "E-Mail jetzt bestätigen" button
3. Should redirect to verify_email.php
4. Should see success message
5. Email should be marked as verified

### 5. Check Complete Flow
```
Login → Profile → Send Email → Receive Email → Click Link → Verify → Success
```

---

## Common Issues & Solutions

### Issue 1: Email Not Sending
**Symptoms:** Ajax returns success but no email arrives

**Solutions:**
1. Check EmailHelper PHPMailer configuration
   ```bash
   tail -f /var/log/apache2/error.log
   ```

2. Verify SMTP settings in database:
   ```sql
   SELECT * FROM system_settings WHERE setting_key LIKE 'smtp_%';
   ```

3. Test PHPMailer directly:
   ```php
   $emailHelper = new EmailHelper($pdo);
   $test = $emailHelper->sendEmail('email_verification', $userId, ['verification_link' => 'test']);
   var_dump($test);
   ```

### Issue 2: Template Not Found
**Symptoms:** Error log shows "Template not found"

**Solutions:**
1. Install template:
   ```bash
   mysql -u user -p database < email_template_email_verification.sql
   ```

2. Verify installation:
   ```sql
   SELECT template_key, is_active FROM email_templates WHERE template_key = 'email_verification';
   ```

3. Check template is active:
   ```sql
   UPDATE email_templates SET is_active = 1 WHERE template_key = 'email_verification';
   ```

### Issue 3: Verification Link Not Working
**Symptoms:** Click link but nothing happens

**Solutions:**
1. Check token was generated:
   ```sql
   SELECT verification_token, verification_token_expires FROM users WHERE id = ?;
   ```

2. Verify token in URL matches database:
   - URL: `verify_email.php?token=abc123...`
   - Database: Should have matching token

3. Check token hasn't expired:
   ```sql
   SELECT verification_token_expires > NOW() as is_valid FROM users WHERE id = ?;
   ```

### Issue 4: "Email Already Verified" Error
**Symptoms:** Can't send verification email

**Solutions:**
1. Check verification status:
   ```sql
   SELECT is_verified, email_verified_at FROM users WHERE id = ?;
   ```

2. If incorrectly marked verified, reset:
   ```sql
   UPDATE users SET is_verified = 0, email_verified_at = NULL WHERE id = ?;
   ```

### Issue 5: Rate Limiting
**Symptoms:** "Please wait X seconds before requesting another email"

**Solutions:**
1. This is expected behavior (1 email per minute)
2. Wait the specified time
3. Or clear session (logout/login)
4. Or adjust rate limit in ajax file (line 42)

---

## Debug Steps

### Enable Error Logging

1. **PHP Error Log:**
   ```php
   // Add to ajax/send_verification_email.php temporarily
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

2. **Check Apache/PHP Error Logs:**
   ```bash
   # Apache
   tail -f /var/log/apache2/error.log
   
   # PHP-FPM
   tail -f /var/log/php-fpm/error.log
   ```

3. **Add Debug Logging:**
   ```php
   // In ajax/send_verification_email.php after line 79
   error_log("Email sent result: " . ($emailSent ? 'true' : 'false'));
   error_log("Template key: email_verification");
   error_log("User ID: " . $userId);
   error_log("Variables: " . json_encode($customVars));
   ```

### Verify Database Queries

```php
// Test database connection
try {
    $stmt = $pdo->query("SELECT 1");
    echo "Database connected!";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
```

### Test EmailHelper Directly

```php
// Create test script: test_email.php
<?php
require_once 'config.php';
require_once 'EmailHelper.php';

$emailHelper = new EmailHelper($pdo);
$result = $emailHelper->sendEmail('email_verification', 1, [
    'verification_link' => 'http://example.com/verify?token=test123'
]);

echo $result ? "Email sent!" : "Email failed!";
```

### Check SMTP Configuration

```php
// Verify SMTP settings
$stmt = $pdo->query("SELECT * FROM system_settings WHERE setting_key LIKE 'smtp_%'");
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($settings);
```

---

## Server Requirements

### Required PHP Extensions
- PDO (for database)
- PDO_MySQL (for MySQL connection)
- OpenSSL (for secure tokens)
- mbstring (for email encoding)

### Check Extensions:
```bash
php -m | grep -E "PDO|pdo_mysql|openssl|mbstring"
```

### Required Files
- `/config.php` - Database configuration
- `/EmailHelper.php` - Email helper class
- `/ajax/send_verification_email.php` - Ajax endpoint
- `/verify_email.php` - Verification handler
- PHPMailer library in vendor/

### Check File Permissions
```bash
# Should be readable by web server
ls -l config.php EmailHelper.php ajax/send_verification_email.php verify_email.php
```

---

## Additional Checks

### 1. Verify Email Template Variables

Template expects these variables:
- `{user_first_name}` - Auto-populated by EmailHelper
- `{verification_link}` - Custom variable (the fix!)
- `{brand_name}` - Auto-populated from system_settings
- `{site_url}` - Auto-populated from system_settings
- `{company_address}` - Auto-populated from system_settings
- `{contact_email}` - Auto-populated from system_settings
- `{fca_reference_number}` - Auto-populated from system_settings
- `{current_year}` - Auto-populated by EmailHelper

### 2. Check System Settings

```sql
SELECT * FROM system_settings WHERE setting_key IN (
    'brand_name',
    'site_url', 
    'company_address',
    'contact_email',
    'fca_reference_number'
);
```

### 3. Verify PHPMailer Configuration

EmailHelper.php should have PHPMailer use statements at top:
```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
```

### 4. Check Session Working

```php
// Test session
session_start();
$_SESSION['test'] = 'working';
echo $_SESSION['test']; // Should output: working
```

---

## Success Indicators

When everything is working correctly:

1. ✅ Click "Resend Verification Email" shows success message
2. ✅ Email arrives in inbox within 1-2 minutes
3. ✅ Email has professional branding and formatting
4. ✅ Verification button/link is present and clickable
5. ✅ Clicking link redirects to verify_email.php
6. ✅ Verification page shows success message
7. ✅ Database updated: `is_verified = 1`, `email_verified_at` set
8. ✅ User can access verified-only features
9. ✅ Resending shows "Email already verified"

---

## Still Having Issues?

If problems persist after checking everything above:

1. **Check Browser Console** for JavaScript errors
2. **Check Network Tab** to see actual Ajax response
3. **Enable PHP Error Display** temporarily
4. **Check All Log Files** (Apache, PHP, MySQL)
5. **Test with Different Email Address** (some providers block automated emails)
6. **Verify Server Time** is correct (affects token expiration)
7. **Check Firewall Rules** (SMTP port 587/465 open?)
8. **Review EmailHelper.php** implementation
9. **Contact Hosting Provider** if SMTP issues

---

## Summary

The main fix was simple: changing `verification_url` to `verification_link` in the ajax file to match the email template variable name. This ensures EmailHelper can properly replace the placeholder in the template with the actual verification link.

If you're still seeing "An error occurred. Please try again later." after this fix, use the troubleshooting steps above to identify the specific issue.
