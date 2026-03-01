# Email Custom Header Guide

## Overview

AdminEmailHelper now supports custom headers in email templates. Templates can optionally include their own header content instead of using the default header.

---

## How It Works

### Default Behavior (No Custom Header)

By default, all emails get this header:

```html
<div class="email-header">
    <h1>🛡️ Brand Name</h1>
    <p>AI-Powered Fund Recovery Platform</p>
</div>
```

### Custom Header Behavior

If your template includes `<!-- CUSTOM_HEADER -->` marker, AdminEmailHelper will:
1. **Not** add the default header
2. Use your custom header from the template content
3. Remove the marker before sending

---

## Extracted Header Content (Body-Only)

Here's the default header content extracted as body-only HTML that you can customize and add to your templates:

### Default Header (Standard)

```html
<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">🛡️ {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">AI-Powered Fund Recovery Platform</p>
</div>
```

### Success Header (Green)

```html
<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">✓ {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Transaction Successful</p>
</div>
```

### Warning Header (Orange)

```html
<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #ff9800, #f57c00); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">⚠️ {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Action Required</p>
</div>
```

### Error Header (Red)

```html
<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">✗ {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Transaction Failed</p>
</div>
```

### Info Header (Blue)

```html
<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">ℹ️ {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Information Update</p>
</div>
```

### Verification Header (Purple)

```html
<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #6f42c1, #5a32a3); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">🔐 {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Account Verification</p>
</div>
```

---

## Usage Examples

### Example 1: Deposit Success with Custom Header

**Template content to store in database:**

```html
<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">✓ {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Deposit Successful</p>
</div>

<h2 style="color: #28a745; margin-bottom: 20px;">Deposit Completed Successfully!</h2>
<p>Dear {first_name} {last_name},</p>
<p>Good news! Your deposit has been successfully processed and your account balance has been updated.</p>

<div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 5px;">
    <strong style="color: #155724;">Deposit Details:</strong>
    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td><strong>Amount:</strong></td>
            <td>€{deposit_amount}</td>
        </tr>
        <tr>
            <td><strong>Reference:</strong></td>
            <td>{deposit_reference}</td>
        </tr>
    </table>
</div>

<p>Your account balance has been updated. You can view your transaction history in your dashboard.</p>

<p style="text-align: center;">
    <a href="{dashboard_url}" class="button" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #28a745, #20c997); color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">View Dashboard</a>
</p>
```

### Example 2: Withdrawal Rejected with Custom Header

**Template content to store in database:**

```html
<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">✗ {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Withdrawal Rejected</p>
</div>

<h2 style="color: #dc3545; margin-bottom: 20px;">Withdrawal Request Rejected</h2>
<p>Dear {first_name} {last_name},</p>
<p>We regret to inform you that your withdrawal request has been rejected.</p>

<div style="background-color: #f8d7da; border: 1px solid #f5c6cb; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; border-radius: 5px;">
    <strong style="color: #721c24;">Rejection Reason:</strong>
    <p style="margin: 10px 0 0 0; color: #721c24;">{rejection_reason}</p>
</div>

<p>Your account balance has been refunded. If you have any questions, please contact our support team.</p>
```

### Example 3: Standard Template (No Custom Header)

**Template content without custom header (uses default):**

```html
<h2 style="color: #2950a8; margin-bottom: 20px;">Account Update</h2>
<p>Dear {first_name} {last_name},</p>
<p>Your account information has been updated.</p>
<p>If you did not make this change, please contact support immediately.</p>
```

This template will get the default blue header automatically.

---

## SQL Examples

### Create Template with Custom Success Header

```sql
INSERT INTO email_templates (template_key, subject, content, created_at) 
VALUES (
    'deposit_completed_custom',
    'Deposit Completed - €{deposit_amount}',
    '<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">✓ {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Deposit Successful</p>
</div>

<h2 style="color: #28a745;">Deposit Completed!</h2>
<p>Dear {first_name} {last_name},</p>
<p>Your deposit of €{deposit_amount} has been successfully processed.</p>',
    NOW()
) ON DUPLICATE KEY UPDATE 
    subject = VALUES(subject),
    content = VALUES(content),
    updated_at = NOW();
```

### Update Existing Template to Use Custom Header

```sql
UPDATE email_templates 
SET content = CONCAT(
    '<!-- CUSTOM_HEADER -->
<div class="email-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
    <h1 style="margin: 0; font-size: 28px; font-weight: 700;">✗ {brand_name}</h1>
    <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Withdrawal Rejected</p>
</div>

',
    content
)
WHERE template_key = 'withdrawal_rejected';
```

---

## Color Schemes by Status

### Success (Green)
- Background: `linear-gradient(135deg, #28a745, #20c997)`
- Use for: Completed transactions, approvals, verifications
- Emoji: ✓

### Warning (Orange)
- Background: `linear-gradient(135deg, #ff9800, #f57c00)`
- Use for: Pending transactions, actions required
- Emoji: ⚠️

### Error (Red)
- Background: `linear-gradient(135deg, #dc3545, #c82333)`
- Use for: Rejected transactions, errors, failures
- Emoji: ✗

### Info (Blue - Default)
- Background: `linear-gradient(135deg, #2950a8, #2da9e3)`
- Use for: General information, updates
- Emoji: 🛡️ or ℹ️

### Verification (Purple)
- Background: `linear-gradient(135deg, #6f42c1, #5a32a3)`
- Use for: Account verification, security
- Emoji: 🔐

### Processing (Teal)
- Background: `linear-gradient(135deg, #17a2b8, #138496)`
- Use for: In-progress transactions
- Emoji: ⏳

---

## Important Notes

### 1. Marker Placement
- Always put `<!-- CUSTOM_HEADER -->` at the very beginning of your template content
- This marker tells AdminEmailHelper that your template has a custom header

### 2. Styling
- Use inline styles in your custom header
- The `email-header` class is already defined in the wrapper CSS
- You can override default styles with inline styles

### 3. Variables
- All standard variables work in custom headers: `{brand_name}`, `{first_name}`, etc.
- Make sure to use proper variable syntax

### 4. Responsive Design
- Custom headers should be responsive
- Test on mobile devices
- Keep text readable on small screens

### 5. Backwards Compatibility
- Templates without `<!-- CUSTOM_HEADER -->` marker get the default header
- Existing templates continue to work without changes
- Only add custom headers where needed

---

## Testing

### Test with Custom Header

```php
$emailHelper = new AdminEmailHelper($pdo);

$customVars = [
    'deposit_amount' => '1000.00',
    'deposit_reference' => 'DEP-12345'
];

// This template has <!-- CUSTOM_HEADER --> marker
$emailHelper->sendTemplateEmail('deposit_completed_custom', $userId, $customVars);
```

### Test without Custom Header (Default)

```php
$emailHelper = new AdminEmailHelper($pdo);

$customVars = [
    'notification_text' => 'Your account has been updated.'
];

// This template doesn't have <!-- CUSTOM_HEADER --> marker
// Will get default blue header automatically
$emailHelper->sendTemplateEmail('account_update', $userId, $customVars);
```

---

## Benefits

### Flexibility
✅ Different headers for different email types
✅ Status-specific colors (green, red, orange, etc.)
✅ Custom messages per template

### Consistency
✅ Default header still works for standard emails
✅ Backwards compatible with existing templates
✅ Easy to maintain

### Branding
✅ Customize branding per email type
✅ Professional appearance
✅ Status-appropriate styling

---

## Troubleshooting

### Custom Header Not Showing
- Check that marker `<!-- CUSTOM_HEADER -->` is at the beginning
- Verify marker is exactly: `<!-- CUSTOM_HEADER -->` (case-sensitive)
- Check for typos in marker

### Default Header Still Appears
- Marker might be missing or misspelled
- Check template content in database
- Verify marker is on its own line at the top

### Styling Issues
- Use inline styles in custom header
- Test in different email clients
- Keep styles simple and inline

---

## Summary

**To add custom header to template:**
1. Add `<!-- CUSTOM_HEADER -->` marker at the beginning
2. Include your custom header HTML with inline styles
3. Use appropriate color scheme for status
4. Include variable placeholders as needed
5. Store in database

**Default header (no custom header):**
- Blue gradient background
- 🛡️ Brand name
- "AI-Powered Fund Recovery Platform" tagline
- Used when no `<!-- CUSTOM_HEADER -->` marker present

**Result:**
- Flexible header customization
- Status-appropriate colors
- Professional appearance
- Easy to implement
- Backwards compatible

Now you have complete control over email headers while maintaining simplicity and consistency!
