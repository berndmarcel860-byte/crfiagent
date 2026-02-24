# Email Verification Testing Guide

## Quick Test Steps

After installing the fixes (commit 9a94ab5), test the email verification:

### 1. Install Email Template

```bash
mysql -u username -p database_name < email_template_email_verification.sql
```

**Verify installation:**
```sql
SELECT template_key, subject FROM email_templates WHERE template_key = 'email_verification';
```

Should return: `email_verification` | `Bestätigen Sie Ihre E-Mail-Adresse bei {brand_name}!`

### 2. Test from User Dashboard

1. **Navigate to Profile:**
   - Login as unverified user
   - Go to Profile page

2. **Click Button:**
   - Click "Resend Verification Email"
   - Should show loading state

3. **Expected Results:**
   - ✅ Success message: "Verification email sent successfully! Please check your inbox."
   - ✅ Button disabled for 60 seconds
   - ✅ No error messages

4. **Check Email:**
   - Email should arrive in inbox
   - Subject: "Bestätigen Sie Ihre E-Mail-Adresse bei [Brand Name]!"
   - Contains verification link button
   - Professional design with company branding

5. **Click Verification Link:**
   - Opens verify_email.php page
   - Shows success message
   - Email status updated to verified

### 3. Debug If Still Failing

**Check PHP Error Logs:**
```bash
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/php/error.log
```

**Enable Debug Mode in send_verification_email.php:**

Add after line 20:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**Test EmailHelper Directly:**
```php
<?php
require_once 'config.php';
require_once 'EmailHelper.php';

$emailHelper = new EmailHelper($pdo);
$result = $emailHelper->sendEmail('email_verification', 1, [
    'verification_link' => 'http://example.com/verify'
]);

var_dump($result);
```

**Check Database Connection:**
```php
var_dump($pdo); // Should show PDO object
```

**Check Template Exists:**
```php
$stmt = $pdo->prepare("SELECT * FROM email_templates WHERE template_key = ?");
$stmt->execute(['email_verification']);
$template = $stmt->fetch(PDO::FETCH_ASSOC);
var_dump($template); // Should show template data
```

### 4. Common Issues & Solutions

**Issue: Still getting "An error occurred"**
- Check PHP error logs for specific error
- Verify email template installed in database
- Check SMTP settings in smtp_settings table
- Verify PHPMailer vendor files exist

**Issue: Email not arriving**
- Check SMTP settings are correct
- Verify email_logs table for send status
- Check spam folder
- Test SMTP connection manually

**Issue: Variables not replaced**
- Template should use `{variable}` not `{{variable}}`
- EmailHelper now uses single braces
- Check template in database has correct syntax

**Issue: SQL error on INSERT**
- Use updated SQL with 4 columns only
- Don't use template_name, category, is_active, timestamps
- These columns don't exist in email_templates table

### 5. Verification Checklist

Before testing:
- [ ] email_template_email_verification.sql installed
- [ ] SMTP settings configured in database
- [ ] PHPMailer vendor files present
- [ ] User has unverified email (is_verified = 0)

During test:
- [ ] No console errors in browser
- [ ] Ajax request returns success
- [ ] Button shows cooldown timer
- [ ] Email appears in inbox

After clicking link:
- [ ] verify_email.php shows success
- [ ] Database updated (is_verified = 1)
- [ ] verification_token cleared

## What Was Fixed

### Bug 1: Variable Format Mismatch
**Problem:** EmailHelper replaced `{{variable}}` but template uses `{variable}`
**Solution:** Changed replaceVariables() to use single braces
**File:** EmailHelper.php lines 165-172

### Bug 2: Non-Existent Column
**Problem:** Query checked `is_active = 1` but column doesn't exist
**Solution:** Removed is_active check from WHERE clause
**File:** EmailHelper.php line 51

### Bug 3: SQL Schema Mismatch
**Problem:** INSERT had 9 columns but table only has 4
**Solution:** Removed non-existent columns from INSERT
**File:** email_template_email_verification.sql

## Next Steps

1. Install the template SQL
2. Test verification email sending
3. Test complete verification flow
4. Monitor email_logs table for sent emails
5. Check for any remaining issues

## Support

If still experiencing issues after these fixes:
1. Check PHP error logs for specific errors
2. Verify database schema matches cryptofinanze (5).sql
3. Test SMTP configuration
4. Check EmailHelper.php is loading PHPMailer correctly
