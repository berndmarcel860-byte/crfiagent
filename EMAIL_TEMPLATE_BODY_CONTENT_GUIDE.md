# Email Template Body Content Only - Implementation Guide

## Overview

This guide explains how to update email templates to use body content only, with AdminEmailHelper providing the professional HTML wrapper automatically.

## What Was Changed

### Problem
Email templates in the database contained complete HTML documents with:
- `<!DOCTYPE html>`, `<html>`, `<head>`, `<body>` tags
- Complete CSS in `<style>` blocks (200+ lines)
- Full page structure with headers, footers
- Duplicated styling across all templates

### Solution
Templates now contain only body content:
- Headings (h2, h3, h4)
- Paragraphs (p)
- Tables, divs, lists
- Inline styles for specific formatting
- Variable placeholders

AdminEmailHelper automatically wraps this content in professional HTML structure with:
- Logo in header and signature
- Gradient blue header
- Professional typography
- Company information
- FCA reference
- Footer with copyright
- Responsive design

## Templates Updated

### ✅ Completed:
1. **deposit_completed** - Body content only
2. **deposit_pending** - Body content only
3. **deposit_completed_de** - Body content only (German)
4. **deposit_pending_de** - Body content only (German)
5. **withdrawal_rejected** - Body content only ← NEW

### 🔄 Remaining to Update:
- withdrawal_pending
- kyc_pending
- email_verification
- wallet_verified
- And others with complete HTML

## How to Update a Template

### Step 1: Identify Content Section

In the old template, find the content between `<div class="email-body">` and `</div>`:

```html
<!-- OLD TEMPLATE (extract this part) -->
<div class="email-body">
    <h1>Welcome</h1>
    <p>Dear {first_name},</p>
    <p>Your message here...</p>
</div>
```

### Step 2: Remove Page Structure

Remove:
- ❌ `<!DOCTYPE html>`
- ❌ `<html>`, `</html>`
- ❌ `<head>`, `</head>`
- ❌ `<style>` tags with CSS
- ❌ `<body>`, `</body>`
- ❌ `.email-container` wrapper
- ❌ `.email-header` section
- ❌ `.email-footer` section
- ❌ Complete footer with copyright

### Step 3: Keep Content Only

Keep:
- ✅ Headings (h1, h2, h3, h4, h5, h6)
- ✅ Paragraphs (p)
- ✅ Tables (table, tr, td, th)
- ✅ Lists (ul, ol, li)
- ✅ Divs for grouping content
- ✅ Links (a href)
- ✅ Bold/italic (strong, em, b, i)
- ✅ Line breaks (br)
- ✅ Inline styles for specific formatting
- ✅ Variable placeholders {variable_name}

### Step 4: Convert CSS Classes to Inline Styles

Convert class-based styling to inline styles for specific formatting needs:

```html
<!-- Before -->
<div class="info-box">
    <h3>Details</h3>
</div>

<!-- After -->
<div style="background-color: #f8f9fa; border-left: 4px solid #2950a8; padding: 20px; margin: 25px 0; border-radius: 4px;">
    <h3 style="margin: 0 0 15px 0; color: #2950a8; font-size: 16px;">Details</h3>
</div>
```

## Example: withdrawal_rejected Template

### Before (334 lines):
```html
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .email-container { max-width: 600px; }
        .email-header { background: gradient... }
        /* 200+ lines of CSS */
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>❌ Auszahlungsantrag Abgelehnt</h1>
        </div>
        <div class="email-body">
            <!-- CONTENT HERE -->
            <p>Dear {first_name},</p>
            <p>Your withdrawal was rejected...</p>
        </div>
        <div class="footer">
            © 2026 Brand Name
        </div>
    </div>
</body>
</html>
```

### After (103 lines):
```html
<h2 style="color: #dc3545; margin-bottom: 20px;">❌ Auszahlungsantrag Abgelehnt</h2>

<p style="font-size: 16px; margin-bottom: 20px;">Sehr geehrte/r {first_name} {last_name},</p>

<p style="font-size: 14px; color: #555555;">
    Leider müssen wir Ihnen mitteilen, dass Ihr Auszahlungsantrag abgelehnt wurde.
</p>

<div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0;">
    <h4 style="color: #856404;">📝 Ablehnungsgrund:</h4>
    <p style="color: #856404;">{rejection_reason}</p>
</div>

<!-- More content... -->
```

## Result with AdminEmailHelper

When sent through AdminEmailHelper, the content gets wrapped:

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>/* AdminEmailHelper CSS */</style>
</head>
<body>
    <div class="email-container">
        <!-- Header from AdminEmailHelper -->
        <div class="email-header">
            🛡️ Brand Name
            AI-Powered Fund Recovery Platform
            [Logo Image]
        </div>
        
        <!-- Body content from database -->
        <div class="email-body">
            <h2>❌ Auszahlungsantrag Abgelehnt</h2>
            <p>Sehr geehrte/r John Doe,</p>
            <!-- All your content here -->
        </div>
        
        <!-- Footer from AdminEmailHelper -->
        <div class="email-footer">
            <div class="signature">
                [Logo Image]
                Brand Team
                Address | Email | Website
                FCA Reference
                Legal Notice
            </div>
        </div>
    </div>
    <div class="footer">
        © 2026 Brand Name
    </div>
</body>
</html>
```

## Benefits

### For Content Management:
✅ **Simpler** - Only 100-150 lines instead of 300+
✅ **Focused** - Only message content, no structure
✅ **Easy to Edit** - Edit in phpMyAdmin or SQL
✅ **No CSS** - No need to manage styles

### For Branding:
✅ **Consistent** - Same wrapper for all emails
✅ **Professional** - Logo, header, footer automatic
✅ **Unified** - Single source (AdminEmailHelper)
✅ **Flexible** - Change branding once, applies everywhere

### For Maintenance:
✅ **Centralized** - Update wrapper once
✅ **Less Code** - Reduced duplication
✅ **Easier Updates** - Content vs structure separated
✅ **Better Organization** - Clear separation of concerns

## Inline Style Guidelines

Use inline styles for:

### Colors (Status-specific):
```html
<!-- Success/Approved -->
<h2 style="color: #28a745;">✓ Success</h2>

<!-- Warning/Pending -->
<h2 style="color: #ff9800;">⏳ Pending</h2>

<!-- Error/Rejected -->
<h2 style="color: #dc3545;">❌ Rejected</h2>
```

### Information Boxes:
```html
<div style="background-color: #f8f9fa; border-left: 4px solid #2950a8; padding: 20px; margin: 25px 0; border-radius: 4px;">
    <h3 style="margin: 0 0 15px 0; color: #2950a8; font-size: 16px;">Information</h3>
    <p>Content here...</p>
</div>
```

### Tables:
```html
<table style="width: 100%; border-collapse: collapse;">
    <tr style="border-bottom: 1px solid #e0e0e0;">
        <td style="padding: 12px 0; font-weight: 600;">Label:</td>
        <td style="padding: 12px 0; text-align: right;">Value</td>
    </tr>
</table>
```

### Buttons:
```html
<a href="{dashboard_url}" style="display: inline-block; background: linear-gradient(135deg, #2950a8, #2da9e3); color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 25px; font-weight: 600;">
    View Dashboard
</a>
```

### Alert Boxes:
```html
<!-- Info -->
<div style="background-color: #d1ecf1; border: 1px solid #17a2b8; padding: 15px; border-radius: 4px;">
    <p style="color: #0c5460;">Information message</p>
</div>

<!-- Warning -->
<div style="background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px;">
    <p style="color: #856404;">Warning message</p>
</div>

<!-- Danger -->
<div style="background-color: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 4px;">
    <p style="color: #721c24;">Error message</p>
</div>
```

## Variable Placeholders

Keep all variable placeholders intact:

### Common Variables:
- `{first_name}`, `{last_name}`, `{full_name}`
- `{email}`, `{user_id}`
- `{balance}`, `{amount}`
- `{reference}`, `{transaction_id}`
- `{date}`, `{current_date}`, `{current_year}`
- `{brand_name}`, `{site_url}`
- `{contact_email}`, `{support_email}`
- `{dashboard_url}`, `{login_url}`

### Template-Specific Variables:
- Withdrawal: `{payment_method}`, `{payment_details}`, `{rejection_reason}`
- Deposit: `{deposit_amount}`, `{deposit_reference}`, `{deposit_status}`
- KYC: `{document_type}`, `{kyc_id}`, `{kyc_status}`

## Migration Process

### For New Installation:
1. Use updated SQL files directly
2. Templates automatically wrapped by AdminEmailHelper
3. No additional configuration needed

### For Existing Database:
1. Backup current templates:
   ```sql
   CREATE TABLE email_templates_backup AS SELECT * FROM email_templates;
   ```

2. Update templates using SQL:
   ```sql
   mysql -u user -p database < email_template_withdrawal_rejected.sql
   ```

3. Or update via UPDATE statement:
   ```sql
   UPDATE email_templates 
   SET content = '<h2>New body content...</h2>'
   WHERE template_key = 'template_name';
   ```

4. Test email sending
5. Verify wrapper appears correctly
6. Check all variables replaced

## Testing

### Test Script:
```php
$emailHelper = new AdminEmailHelper($pdo);

$customVars = [
    'reference' => 'WD-12345',
    'amount' => '1000.00',
    'rejection_reason' => 'Insufficient verification',
    'payment_method' => 'Bank Transfer',
    'transaction_date' => date('d.m.Y')
];

// Send test email
$emailHelper->sendTemplateEmail('withdrawal_rejected', $userId, $customVars);
```

### Verify:
✅ Email has professional header with logo
✅ Content from template displays correctly
✅ All variables replaced
✅ Signature with logo appears
✅ Footer with copyright present
✅ Responsive on mobile devices

## Troubleshooting

### Issue: Email missing header/footer
**Solution:** Ensure AdminEmailHelper.sendTemplateEmail() is being used, not raw sendEmail()

### Issue: Double wrapper (nested headers)
**Solution:** Template should not have <!DOCTYPE> or <html> tags - remove them

### Issue: Variables not replaced
**Solution:** Check variable names match exactly, use {variable_name} syntax

### Issue: Styles not working
**Solution:** Use inline styles, not CSS classes (classes from removed <style> blocks won't work)

## Summary

✅ **Templates**: Body content only (no complete HTML)
✅ **Wrapper**: AdminEmailHelper provides automatically
✅ **Result**: Professional emails with consistent branding
✅ **Maintenance**: Simpler, easier, centralized
✅ **Status**: withdrawal_rejected completed, others pending

## Next Steps

1. Update remaining templates using same approach
2. Test each template after updating
3. Verify all emails look professional
4. Update documentation as needed
5. Consider creating template editor UI for easy management

---

**Files:**
- `email_template_withdrawal_rejected.sql` - Updated (body content only)
- `email_template_withdrawal_rejected_OLD.sql` - Backup (complete HTML)
- More templates to follow same pattern

**Last Updated:** 2026-03-01
**Status:** In Progress (1 of ~10 templates completed)
