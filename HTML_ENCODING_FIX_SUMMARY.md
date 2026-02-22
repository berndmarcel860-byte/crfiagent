# HTML Encoding Fix Summary

## Issue Reported
Lines 87 and 165 in `admin/admin_settings.php` showing `"&gt;` error

## Root Cause
Missing `htmlspecialchars()` function when outputting CSRF tokens in form hidden inputs.

## Solution
Added proper HTML escaping to both lines:

### Line 87 (System Settings Form)
```php
// Before:
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// After:
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
```

### Line 165 (SMTP Settings Form)
```php
// Before:
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// After:
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
```

## Benefits
- ✅ Prevents XSS vulnerabilities
- ✅ Ensures proper HTML structure
- ✅ Follows PHP security best practices
- ✅ Consistent with other form fields

## Verification
```bash
$ php -l admin/admin_settings.php
No syntax errors detected ✅
```

## Status
✅ **FIXED** - Both lines now properly escape CSRF tokens
