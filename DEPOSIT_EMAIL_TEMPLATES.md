# Deposit Email Templates Guide

This document explains how to use email templates for deposit notifications instead of hardcoded email content.

## Overview

The deposit notification system now uses database-stored email templates instead of hardcoded HTML in the PHP files. This provides several benefits:

- ✅ **Centralized Management**: Update email content without modifying code
- ✅ **Consistency**: Same professional template format across all emails
- ✅ **Easy Customization**: Change email text through database updates
- ✅ **Multilingual Support**: Easy to add multiple language versions
- ✅ **Version Control**: Track email template changes in database
- ✅ **No Code Deployment**: Update email content without deploying code changes

## Templates Created

Two email templates have been created for deposit notifications:

### 1. deposit_completed
**Template Key:** `deposit_completed`
**Subject:** `Deposit Completed - €{deposit_amount}`
**When Used:** When a deposit status is "completed"

**Features:**
- Green color scheme (success)
- Shows balance has been updated
- Link to dashboard
- Professional formatting
- All deposit details displayed

### 2. deposit_pending
**Template Key:** `deposit_pending`
**Subject:** `Deposit Pending - €{deposit_amount}`
**When Used:** When a deposit status is "pending"

**Features:**
- Yellow/orange color scheme (pending)
- Explains processing timeline (1-2 business days)
- What happens next section
- Link to dashboard
- Professional formatting

## Installation

### Step 1: Install Email Templates

Run the SQL file to insert the templates into your database:

```bash
mysql -u username -p database_name < email_template_deposit.sql
```

Or execute the SQL directly in phpMyAdmin or your preferred database tool.

### Step 2: Verify Installation

Check that the templates were installed:

```sql
SELECT template_key, subject, created_at 
FROM email_templates 
WHERE template_key IN ('deposit_completed', 'deposit_pending');
```

You should see two records.

### Step 3: Code Already Updated

The `admin/admin_ajax/add_deposit.php` file has been updated to use these templates automatically. No additional changes needed.

## Available Variables

The following variables are available in the deposit email templates:

### Custom Deposit Variables (Passed by add_deposit.php)
- `{deposit_amount}` - Formatted deposit amount (e.g., "1,000.00")
- `{deposit_reference}` - Unique deposit reference number
- `{deposit_status}` - Status of deposit (Completed, Pending, etc.)
- `{payment_method}` - Payment method code used
- `{date}` - Date and time of deposit (format: DD.MM.YYYY HH:mm)

### Standard User Variables (Automatically Available - 41+)
- `{user_id}` - User's ID
- `{first_name}` - User's first name
- `{last_name}` - User's last name
- `{full_name}` - User's full name
- `{email}` - User's email address
- `{balance}` - User's current balance
- `{status}` - User's account status

### Company Variables
- `{brand_name}` - Company brand name
- `{site_url}` - Website URL
- `{contact_email}` - Support email address
- `{contact_phone}` - Support phone number
- `{company_address}` - Company address
- `{fca_reference_number}` - FCA reference number

### System Variables
- `{current_year}` - Current year
- `{current_date}` - Current date
- `{current_time}` - Current time
- `{dashboard_url}` - Link to user dashboard
- `{login_url}` - Link to login page

## How It Works

### Before (Hardcoded Email)

```php
// Old method - hardcoded HTML in PHP
$emailContent = "
    <h2>Deposit {$statusText}</h2>
    <p>Dear {$user['first_name']},</p>
    <p>A deposit has been recorded...</p>
";
$emailHelper->sendDirectEmail($userId, $subject, $emailContent, $customVars);
```

### After (Template-Based Email)

```php
// New method - uses database template
$customVars = [
    'deposit_amount' => number_format($amount, 2),
    'deposit_reference' => $reference,
    'deposit_status' => ucfirst($status),
    'payment_method' => $methodCode,
    'date' => date('d.m.Y H:i')
];

$templateKey = ($status === 'completed') ? 'deposit_completed' : 'deposit_pending';
$emailHelper->sendTemplateEmail($templateKey, $userId, $customVars);
```

## Customizing Templates

### Method 1: Direct Database Update

Update templates directly in the database:

```sql
UPDATE email_templates 
SET content = 'Your new HTML content here with {variables}'
WHERE template_key = 'deposit_completed';
```

### Method 2: Using phpMyAdmin

1. Open phpMyAdmin
2. Navigate to `email_templates` table
3. Find the record with `template_key = 'deposit_completed'` or `'deposit_pending'`
4. Edit the `content` field
5. Save changes

### Method 3: Admin Panel (If Available)

If your admin panel has an email template editor, use that interface to update templates.

## Testing

### Test Completed Deposit Email

```php
// Create a completed deposit to test the email
POST /admin/admin_ajax/add_deposit.php
{
    "user_id": 1,
    "amount": 1000,
    "method_code": "bank_transfer",
    "status": "completed",
    "admin_notes": "Test deposit"
}
```

Check:
- ✅ Email received by user
- ✅ Green success styling
- ✅ Balance updated message shown
- ✅ Dashboard link works
- ✅ All variables replaced correctly

### Test Pending Deposit Email

```php
POST /admin/admin_ajax/add_deposit.php
{
    "user_id": 1,
    "amount": 500,
    "method_code": "bitcoin",
    "status": "pending",
    "admin_notes": "Test pending deposit"
}
```

Check:
- ✅ Email received by user
- ✅ Yellow/orange pending styling
- ✅ Processing timeline shown
- ✅ "What happens next" section displayed
- ✅ All variables replaced correctly

## Troubleshooting

### Template Not Found Error

**Error:** `Template not found: deposit_completed`

**Solution:** 
1. Verify templates are installed: `SELECT * FROM email_templates WHERE template_key LIKE 'deposit_%'`
2. If missing, run the SQL installation file again
3. Check for typos in template_key

### Variables Not Replaced

**Symptom:** Email shows `{first_name}` instead of actual name

**Solution:**
1. Check variable names match exactly (case-sensitive)
2. Verify custom variables are passed correctly in `$customVars` array
3. Check AdminEmailHelper is fetching user data correctly

### Email Not Sending

**Symptom:** No email received

**Solution:**
1. Check error logs: `error_log` entries for email failures
2. Verify SMTP settings in database
3. Test PHPMailer configuration
4. Check user email address is valid
5. Look for email in spam folder

## Code Reference

### AdminEmailHelper Methods

```php
// Send template-based email
$emailHelper->sendTemplateEmail($templateKey, $userId, $customVars);

// Send direct HTML email (not using template)
$emailHelper->sendDirectEmail($userId, $subject, $htmlBody, $customVars);
```

### add_deposit.php Implementation

Location: `admin/admin_ajax/add_deposit.php` (lines 161-189)

```php
// Prepare custom variables
$customVars = [
    'deposit_amount' => number_format($amount, 2),
    'deposit_reference' => $reference,
    'deposit_status' => ucfirst($status),
    'payment_method' => $methodCode,
    'date' => date('d.m.Y H:i')
];

// Choose template based on status
$templateKey = ($status === 'completed') ? 'deposit_completed' : 'deposit_pending';

// Send email using template
$success = $emailHelper->sendTemplateEmail($templateKey, $userId, $customVars);
```

## Benefits of Template System

### For Administrators
- Update email content through database/admin panel
- No code changes required
- Immediate updates (no deployment needed)
- Easy A/B testing of different email versions
- Consistent branding across all emails

### For Developers
- Cleaner code (less HTML in PHP files)
- Easier to maintain
- Template reusability
- Centralized email management
- Better separation of concerns

### For Users
- Professional, consistent email design
- Clear, formatted information
- Easy-to-read status updates
- Working links to dashboard
- Better user experience

## Future Enhancements

Potential improvements to the template system:

1. **Multilingual Templates**: Add language-specific templates
2. **Template Versioning**: Track template changes over time
3. **A/B Testing**: Test different email versions
4. **Email Analytics**: Track open rates, click rates
5. **Preview System**: Preview templates before sending
6. **Template Categories**: Organize templates by type
7. **Dynamic Blocks**: Reusable email components

## Related Files

- `admin/admin_ajax/add_deposit.php` - Main deposit creation file
- `admin/AdminEmailHelper.php` - Email helper class
- `email_template_deposit.sql` - SQL file with template definitions
- Database table: `email_templates` - Stores all email templates

## Support

If you need help with email templates:

1. Check the error logs for specific error messages
2. Verify database connection and table structure
3. Review AdminEmailHelper.php documentation
4. Test with simple template first
5. Contact technical support if issues persist

## Summary

The deposit email system now uses professional, database-stored templates that are:
- Easy to update without code changes
- Consistent with other system emails
- Professional and well-formatted
- Fully customizable through database
- Properly logged and tracked

This provides a better user experience and easier maintenance for administrators.
