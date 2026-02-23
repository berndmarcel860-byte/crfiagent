# User Ajax Email Migration Guide

## Overview
This guide documents the migration of user ajax files from manual PHPMailer implementation to EmailHelper.php, matching the pattern used in admin ajax files with AdminEmailHelper.

## Files Status

### ✅ Completed (1/4)
1. **ajax/kyc_submit.php** - Already using EmailHelper
2. **ajax/otp-handler.php** - Updated (124 → 106 lines, 15% reduction)

### ⏳ Pending (2/4)
3. **ajax/process-deposit.php** - Needs update (435 lines)
4. **ajax/process-withdrawal.php** - Needs update (317 lines)

---

## Migration Pattern

### Before (Manual PHPMailer):
```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ... 100+ lines of:
// - SMTP configuration
// - Template loading
// - Variable replacement
// - PHPMailer setup
// - Email sending
// - Error handling
// - Default template function
```

### After (EmailHelper):
```php
require_once __DIR__ . '/../EmailHelper.php';

// ... simple email sending:
try {
    $emailHelper = new EmailHelper($pdo);
    $customVars = [
        'amount' => '$1,000.00',
        'reference' => 'REF-12345',
        // ... other custom variables
    ];
    $emailHelper->sendEmail('template_key', $userId, $customVars);
} catch (Exception $e) {
    error_log("Email failed: " . $e->getMessage());
}
```

---

## process-deposit.php Update Plan

### Current Structure (435 lines):
- Lines 3-5: PHPMailer use statements
- Lines 12-17: PHPMailer availability check
- Lines 240-360: Manual email sending (~120 lines)
- Lines 366-435: getDefaultDepositTemplate() function (~70 lines)

### Changes Required:

#### 1. Remove PHPMailer Use Statements (Lines 3-5)
```php
// REMOVE:
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
```

#### 2. Remove PHPMailer Check (Lines 12-17)
```php
// REMOVE:
$phpMailerAvailable = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $phpMailerAvailable = true;
}
```

#### 3. Replace Email Sending Section (Lines 240-360)

**Remove:** ~120 lines of manual email code including:
- System settings fetch
- Template loading
- Variable replacement
- PHPMailer configuration
- Email sending with fallback
- Email logging

**Replace with:**
```php
// Send confirmation email using EmailHelper
try {
    require_once __DIR__ . '/../EmailHelper.php';
    $emailHelper = new EmailHelper($pdo);
    
    $customVars = [
        'amount' => '$' . number_format($amount, 2),
        'reference' => $reference,
        'payment_method' => $paymentMethod,
        'transaction_date' => date('Y-m-d H:i:s'),
        'status' => 'Pending Processing'
    ];
    
    $emailHelper->sendEmail('deposit_submitted', $user['id'], $customVars);
    error_log("Deposit confirmation email sent to: " . $user['email']);
} catch (Exception $e) {
    error_log("Email sending failed: " . $e->getMessage());
    // Don't fail deposit if email fails
}
```

#### 4. Remove Default Template Function (Lines 366-435)
```php
// REMOVE ENTIRELY:
function getDefaultDepositTemplate() {
    return '...'; // 70 lines of HTML
}
```

### Expected Result:
- **Before:** 435 lines
- **After:** ~240 lines
- **Reduction:** ~195 lines (45%)

### Template Key:
- Use: `deposit_submitted`
- Category: deposit
- Variables passed: amount, reference, payment_method, transaction_date, status
- Auto-populated by EmailHelper: user_first_name, user_last_name, user_email, brand_name, company_address, contact_email, site_url, current_year, etc.

---

## process-withdrawal.php Update Plan

### Current Structure (317 lines):
Similar pattern to process-deposit.php with PHPMailer manual implementation.

### Changes Required:
1. Remove PHPMailer use statements
2. Remove PHPMailer availability check
3. Replace manual email sending with EmailHelper
4. Remove any default template functions
5. Use 'withdrawal_submitted' template key

### Expected Result:
- **Before:** 317 lines
- **After:** ~200 lines
- **Reduction:** ~117 lines (37%)

### Template Key:
- Use: `withdrawal_submitted`
- Category: withdrawal
- Variables: amount, reference, payment_method, transaction_date, status

---

## Required Email Templates

These templates need to exist in the `email_templates` table:

### 1. otp_code ✅
- **Status:** Created
- **Variables:** otp_code, expires_minutes, purpose
- **Used by:** otp-handler.php

### 2. deposit_submitted
- **Status:** Needs creation
- **Variables:** amount, reference, payment_method, transaction_date, status
- **Used by:** process-deposit.php
- **Subject:** Deposit Confirmation - {{reference}}

### 3. withdrawal_submitted
- **Status:** Needs creation
- **Variables:** amount, reference, payment_method, transaction_date, status
- **Used by:** process-withdrawal.php
- **Subject:** Withdrawal Request Submitted - {{reference}}

---

## Benefits of Migration

### Code Quality:
- ✅ **Less Code:** ~45% average reduction
- ✅ **Cleaner:** No hardcoded HTML templates
- ✅ **Maintainable:** Database-driven templates
- ✅ **Consistent:** Same pattern as admin files

### Features:
- ✅ **Template System:** All templates in database
- ✅ **Auto-Variables:** User data, system settings auto-populated
- ✅ **Email Tracking:** Built-in tracking pixel support
- ✅ **Error Handling:** Consistent exception handling
- ✅ **No Fallback Needed:** EmailHelper handles SMTP internally

### User Experience:
- ✅ **Professional Emails:** Consistent branding
- ✅ **Easy Updates:** Templates updatable via admin panel
- ✅ **Multi-language:** Template system supports translations
- ✅ **Tracking:** Email open tracking for analytics

---

## Testing Checklist

After updating each file:

### PHP Validation:
- [ ] `php -l ajax/process-deposit.php` - No syntax errors
- [ ] `php -l ajax/process-withdrawal.php` - No syntax errors

### Functionality:
- [ ] Deposit submission works
- [ ] Deposit email received
- [ ] All variables replaced correctly
- [ ] Withdrawal submission works
- [ ] Withdrawal email received
- [ ] All variables replaced correctly

### Template Verification:
- [ ] deposit_submitted template exists in database
- [ ] withdrawal_submitted template exists in database
- [ ] Templates have correct variables
- [ ] Subject lines formatted correctly

---

## Implementation Steps

### For process-deposit.php:
1. Back up current file
2. Remove PHPMailer use statements (lines 3-5)
3. Remove PHPMailer check (lines 12-17)
4. Replace email section (lines 240-360) with EmailHelper code
5. Remove getDefaultDepositTemplate function (lines 366-435)
6. Validate PHP syntax
7. Test deposit submission
8. Verify email sent and formatted correctly

### For process-withdrawal.php:
1. Back up current file
2. Apply similar changes as deposit file
3. Use 'withdrawal_submitted' template key
4. Validate PHP syntax
5. Test withdrawal submission
6. Verify email sent and formatted correctly

---

## Summary

### Total Impact:
- **Files to update:** 2 (deposit, withdrawal)
- **Lines to remove:** ~312 lines
- **Reduction:** ~40% average
- **Templates needed:** 2 (deposit_submitted, withdrawal_submitted)

### Current Progress:
- ✅ kyc_submit.php (already using EmailHelper)
- ✅ otp-handler.php (updated)
- ⏳ process-deposit.php (documented, ready to update)
- ⏳ process-withdrawal.php (documented, ready to update)

### Next Steps:
1. Create deposit_submitted email template SQL
2. Create withdrawal_submitted email template SQL
3. Update process-deposit.php
4. Update process-withdrawal.php
5. Test all email functionality
6. Document completion

---

**Status:** Documentation complete. Ready for implementation of remaining files.
