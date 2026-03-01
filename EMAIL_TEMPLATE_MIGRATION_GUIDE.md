# Email Template Migration Guide

## How to Update Email Templates to Use Body Content Only

This guide explains how to update your email templates in the `email_templates` table to contain only body content, allowing AdminEmailHelper to provide the professional HTML wrapper automatically.

---

## Understanding the Change

### Before (Old Approach):
Email templates contained **complete HTML documents** with DOCTYPE, html, head, body tags, and all styling.

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Complete CSS styling */
    </style>
</head>
<body>
    <div style="...">
        <h2>✓ Deposit Completed</h2>
        <p>Dear {first_name} {last_name},</p>
        <p>Your deposit has been processed.</p>
    </div>
</body>
</html>
```

### After (New Approach):
Email templates contain **only body content** (the message itself), and AdminEmailHelper wraps it in professional HTML automatically.

```html
<h2 style="color: #28a745; margin-bottom: 20px;">✓ Deposit Completed</h2>
<p>Dear {first_name} {last_name},</p>
<p>Good news! Your deposit has been successfully processed and your account balance has been updated.</p>
<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <strong>Deposit Details:</strong>
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
```

---

## Why This Approach is Better

### Benefits:

1. **Simpler Templates**
   - Only focus on the message content
   - Less HTML to manage
   - Easier to edit in phpMyAdmin or SQL

2. **Consistent Branding**
   - All emails use the same professional wrapper
   - Logo appears automatically in header and signature
   - Uniform header, footer, and styling

3. **Easier Maintenance**
   - Update wrapper once in AdminEmailHelper
   - Change applies to all emails automatically
   - No need to update each template

4. **Better Separation of Concerns**
   - Templates: Content and message
   - AdminEmailHelper: Structure and branding
   - Clean architecture

5. **Automatic Professional Features**
   - Logo in header and signature
   - Gradient blue header
   - Professional signature with company info
   - FCA reference and legal notice
   - Responsive design
   - Copyright footer

---

## Migration Steps

### Step 1: Backup Your Database

**Always backup before making changes!**

```bash
mysqldump -u username -p database_name email_templates > email_templates_backup.sql
```

### Step 2: Choose Your Migration Method

You have three options:

#### Option A: Use Updated SQL Files (Recommended)

```bash
# Update English templates
mysql -u username -p database_name < email_template_deposit.sql

# Update German templates
mysql -u username -p database_name < email_template_deposit_german.sql
```

#### Option B: Manual SQL UPDATE Statements

See SQL statements section below.

#### Option C: Update via phpMyAdmin

1. Open phpMyAdmin
2. Navigate to `email_templates` table
3. Edit each template record
4. Replace content with body-only version
5. Save changes

---

## SQL UPDATE Statements

### For Existing Database Templates

#### English Deposit Templates:

**deposit_completed:**
```sql
UPDATE email_templates 
SET content = '
<h2 style="color: #28a745; margin-bottom: 20px;">✓ Deposit Completed</h2>

<p>Dear {first_name} {last_name},</p>

<p>Good news! Your deposit has been successfully processed and your account balance has been updated.</p>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <strong>Deposit Details:</strong>
    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="padding: 5px 0;"><strong>Amount:</strong></td>
            <td style="padding: 5px 0;">€{deposit_amount}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Reference:</strong></td>
            <td style="padding: 5px 0;">{deposit_reference}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Payment Method:</strong></td>
            <td style="padding: 5px 0;">{payment_method}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Status:</strong></td>
            <td style="padding: 5px 0; color: #28a745; font-weight: bold;">{deposit_status}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Date:</strong></td>
            <td style="padding: 5px 0;">{date}</td>
        </tr>
    </table>
</div>

<div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
    <p style="margin: 0;"><strong>✓ Your account balance has been updated.</strong></p>
    <p style="margin: 5px 0 0 0;">You can view your updated balance and transaction history in your dashboard.</p>
</div>

<p>
    <a href="{dashboard_url}" style="display: inline-block; background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 0;">
        View Dashboard
    </a>
</p>

<p>If you have any questions about this deposit, please don''t hesitate to contact our support team at {contact_email}.</p>
'
WHERE template_key = 'deposit_completed';
```

**deposit_pending:**
```sql
UPDATE email_templates 
SET content = '
<h2 style="color: #ff9800; margin-bottom: 20px;">⏳ Deposit Pending</h2>

<p>Dear {first_name} {last_name},</p>

<p>We have received your deposit request and it is currently being processed.</p>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <strong>Deposit Details:</strong>
    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="padding: 5px 0;"><strong>Amount:</strong></td>
            <td style="padding: 5px 0;">€{deposit_amount}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Reference:</strong></td>
            <td style="padding: 5px 0;">{deposit_reference}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Payment Method:</strong></td>
            <td style="padding: 5px 0;">{payment_method}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Status:</strong></td>
            <td style="padding: 5px 0; color: #ff9800; font-weight: bold;">{deposit_status}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Date:</strong></td>
            <td style="padding: 5px 0;">{date}</td>
        </tr>
    </table>
</div>

<div style="background-color: #fff3cd; border-left: 4px solid #ff9800; padding: 15px; margin: 20px 0;">
    <p style="margin: 0;"><strong>⏳ Your deposit is being processed.</strong></p>
    <p style="margin: 5px 0 0 0;">This usually takes 1-2 business days.</p>
</div>

<div style="background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p style="margin: 0 0 10px 0;"><strong>ℹ️ What Happens Next?</strong></p>
    <ul style="margin: 0; padding-left: 20px;">
        <li>Our team will verify your payment</li>
        <li>Once verified, your balance will be updated automatically</li>
        <li>You will receive a confirmation email</li>
        <li>Processing time: Usually within 1-2 business days</li>
    </ul>
</div>

<p>
    <a href="{dashboard_url}" style="display: inline-block; background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 0;">
        View Dashboard
    </a>
</p>

<p>You can track the status of your deposit in your dashboard. If you have any questions, please contact our support team at {contact_email}.</p>
'
WHERE template_key = 'deposit_pending';
```

#### German Deposit Templates:

**deposit_completed_de:**
```sql
UPDATE email_templates 
SET content = '
<h2 style="color: #28a745; margin-bottom: 20px;">✓ Einzahlung Abgeschlossen</h2>

<p>Sehr geehrte(r) {first_name} {last_name},</p>

<p>Gute Nachrichten! Ihre Einzahlung wurde erfolgreich verarbeitet und Ihr Kontoguthaben wurde aktualisiert.</p>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <strong>Einzahlungsdetails:</strong>
    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="padding: 5px 0;"><strong>Betrag:</strong></td>
            <td style="padding: 5px 0;">€{deposit_amount}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Referenznummer:</strong></td>
            <td style="padding: 5px 0;">{deposit_reference}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Zahlungsmethode:</strong></td>
            <td style="padding: 5px 0;">{payment_method}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Status:</strong></td>
            <td style="padding: 5px 0; color: #28a745; font-weight: bold;">{deposit_status}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Datum:</strong></td>
            <td style="padding: 5px 0;">{date}</td>
        </tr>
    </table>
</div>

<div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0;">
    <p style="margin: 0;"><strong>✓ Ihr Kontoguthaben wurde aktualisiert.</strong></p>
    <p style="margin: 5px 0 0 0;">Sie können Ihr aktualisiertes Guthaben und Ihre Transaktionshistorie in Ihrem Dashboard einsehen.</p>
</div>

<p>
    <a href="{dashboard_url}" style="display: inline-block; background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 0;">
        Dashboard Anzeigen
    </a>
</p>

<p>Wenn Sie Fragen zu dieser Einzahlung haben, zögern Sie bitte nicht, unser Support-Team unter {contact_email} zu kontaktieren.</p>
'
WHERE template_key = 'deposit_completed_de';
```

**deposit_pending_de:**
```sql
UPDATE email_templates 
SET content = '
<h2 style="color: #ff9800; margin-bottom: 20px;">⏳ Einzahlung In Bearbeitung</h2>

<p>Sehr geehrte(r) {first_name} {last_name},</p>

<p>Wir haben Ihre Einzahlungsanfrage erhalten und sie wird derzeit bearbeitet.</p>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <strong>Einzahlungsdetails:</strong>
    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="padding: 5px 0;"><strong>Betrag:</strong></td>
            <td style="padding: 5px 0;">€{deposit_amount}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Referenznummer:</strong></td>
            <td style="padding: 5px 0;">{deposit_reference}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Zahlungsmethode:</strong></td>
            <td style="padding: 5px 0;">{payment_method}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Status:</strong></td>
            <td style="padding: 5px 0; color: #ff9800; font-weight: bold;">{deposit_status}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0;"><strong>Datum:</strong></td>
            <td style="padding: 5px 0;">{date}</td>
        </tr>
    </table>
</div>

<div style="background-color: #fff3cd; border-left: 4px solid #ff9800; padding: 15px; margin: 20px 0;">
    <p style="margin: 0;"><strong>⏳ Ihre Einzahlung wird bearbeitet.</strong></p>
    <p style="margin: 5px 0 0 0;">Dies dauert normalerweise 1-2 Werktage.</p>
</div>

<div style="background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <p style="margin: 0 0 10px 0;"><strong>ℹ️ Was passiert als Nächstes?</strong></p>
    <ul style="margin: 0; padding-left: 20px;">
        <li>Unser Team überprüft Ihre Zahlung</li>
        <li>Nach der Überprüfung wird Ihr Guthaben automatisch aktualisiert</li>
        <li>Sie erhalten eine Bestätigungs-E-Mail</li>
        <li>Bearbeitungszeit: Normalerweise innerhalb von 1-2 Werktagen</li>
    </ul>
</div>

<p>
    <a href="{dashboard_url}" style="display: inline-block; background: linear-gradient(135deg, #2950a8, #2da9e3); color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 0;">
        Dashboard Anzeigen
    </a>
</p>

<p>Sie können den Status Ihrer Einzahlung in Ihrem Dashboard verfolgen. Wenn Sie Fragen haben, kontaktieren Sie bitte unser Support-Team unter {contact_email}.</p>
'
WHERE template_key = 'deposit_pending_de';
```

---

## What to Include in Template Content

### ✅ DO Include:

- **Content Elements:**
  - Headings (h1, h2, h3, h4)
  - Paragraphs (p)
  - Lists (ul, ol, li)
  - Tables (table, tr, td, th)
  - Divs for grouping content
  - Line breaks (br)
  - Links (a href)
  - Bold (strong, b)
  - Italic (em, i)
  - Images (img) - for content images

- **Inline Styles:**
  - Colors for specific elements
  - Padding/margin for spacing
  - Background colors for highlights
  - Font sizes for emphasis
  - Border styles for boxes

- **Variable Placeholders:**
  - {first_name}, {last_name}
  - {deposit_amount}, {deposit_reference}
  - {dashboard_url}, {contact_email}
  - Any custom variables

### ❌ DON'T Include:

- **Document Structure:**
  - `<!DOCTYPE html>`
  - `<html>`, `</html>`
  - `<head>`, `</head>`
  - `<body>`, `</body>`

- **CSS Blocks:**
  - `<style>` tags
  - External CSS files
  - Complete stylesheets

- **Branding Elements:**
  - Company logo (provided by wrapper)
  - Header section (provided by wrapper)
  - Footer section (provided by wrapper)
  - Signature (provided by wrapper)
  - Copyright notice (provided by wrapper)

- **Meta Tags:**
  - charset
  - viewport
  - Any meta information

---

## How AdminEmailHelper Wraps Your Content

When you use `sendTemplateEmail()`, AdminEmailHelper:

1. Fetches your template content from database
2. Replaces all variable placeholders
3. Wraps it in professional HTML structure:

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Professional CSS styling */
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4;">
    <div class="email-container" style="max-width:600px; margin:0 auto; background-color:white;">
        
        <!-- PROFESSIONAL HEADER -->
        <div class="email-header" style="background: linear-gradient(135deg, #2950a8, #2da9e3); color:white; text-align:center; padding:30px;">
            <h1 style="margin:0; font-size:28px;">🛡️ {brand_name}</h1>
            <p style="margin:5px 0 0 0; font-size:14px;">AI-Powered Fund Recovery Platform</p>
        </div>
        
        <!-- YOUR TEMPLATE CONTENT GOES HERE -->
        <div class="email-body" style="padding:30px; color:#333;">
            [YOUR TEMPLATE CONTENT FROM DATABASE]
        </div>
        
        <!-- PROFESSIONAL FOOTER WITH SIGNATURE -->
        <div class="email-footer" style="background-color:#f8f9fa; padding:30px; color:#666;">
            <p style="margin:0 0 15px 0;">Mit freundlichen Grüßen,</p>
            <div class="signature">
                <img src="{logo_url}" alt="{brand_name}" style="height:50px; margin-bottom:10px;">
                <p style="margin:5px 0;"><strong>{brand_name} Team</strong></p>
                <p style="margin:5px 0; font-size:14px;">{company_address}</p>
                <p style="margin:5px 0; font-size:14px;">
                    <a href="mailto:{contact_email}">{contact_email}</a> | 
                    <a href="{site_url}">{site_url}</a>
                </p>
                <p style="margin:15px 0 5px 0; font-size:12px;">FCA Reference: {fca_reference}</p>
                <p style="margin:5px 0; font-size:11px; color:#999;">
                    This email contains confidential information...
                </p>
            </div>
        </div>
    </div>
    
    <!-- COPYRIGHT FOOTER -->
    <div class="footer" style="text-align:center; padding:20px; color:#999; font-size:12px;">
        <p style="margin:0;">© {current_year} {brand_name}. Alle Rechte vorbehalten.</p>
    </div>
</body>
</html>
```

---

## Testing Your Templates

After updating templates, test them:

### 1. Test English Templates

```php
// In add_deposit.php or test script
$emailHelper = new AdminEmailHelper($pdo);

$customVars = [
    'deposit_amount' => '1000.00',
    'deposit_reference' => 'DEP-12345',
    'payment_method' => 'Bank Transfer',
    'deposit_status' => 'Completed',
    'date' => date('d.m.Y H:i')
];

// Test completed
$emailHelper->sendTemplateEmail('deposit_completed', $userId, $customVars);

// Test pending
$emailHelper->sendTemplateEmail('deposit_pending', $userId, $customVars);
```

### 2. Test German Templates

```php
// Test German completed
$emailHelper->sendTemplateEmail('deposit_completed_de', $userId, $customVars);

// Test German pending
$emailHelper->sendTemplateEmail('deposit_pending_de', $userId, $customVars);
```

### 3. Check the Email

✅ Header appears with logo and gradient
✅ Your template content displays in middle
✅ Footer appears with signature
✅ All variables replaced
✅ Links work correctly
✅ Responsive on mobile
✅ Professional appearance

---

## Troubleshooting

### Problem: Email still shows complete HTML

**Solution:** Template might still have old structure. Update it using SQL statements above.

### Problem: Variables not replaced

**Solution:** Check variable names match exactly: `{first_name}` not `{firstName}`

### Problem: Styling looks wrong

**Solution:** 
- Check inline styles in template
- Don't use `<style>` tags in template
- Let AdminEmailHelper provide structure

### Problem: Logo not showing

**Solution:**
- Check `logo_url` in system_settings table
- Verify URL is accessible
- AdminEmailHelper fetches it automatically

### Problem: Double header/footer

**Solution:**
- Remove header/footer from template
- Only include body content
- AdminEmailHelper adds wrapper

---

## Best Practices

### 1. Keep It Simple
- Focus on the message
- Use clean HTML
- Minimal inline styles

### 2. Use Variables
- Personalize with {first_name}
- Dynamic data with {deposit_amount}
- Links with {dashboard_url}

### 3. Semantic HTML
- Use proper headings (h2 for main title)
- Paragraphs for text
- Tables for data
- Lists for steps

### 4. Inline Styles Only
- Color for emphasis: `style="color: #28a745;"`
- Spacing: `style="margin: 20px 0;"`
- Backgrounds: `style="background-color: #f8f9fa;"`
- No `<style>` blocks

### 5. Test Thoroughly
- Test with real data
- Check on multiple devices
- Verify all variables work
- Review in different email clients

---

## Example Complete Migration

### Before (Old Template):

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: blue; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Company Name</h1>
        </div>
        <div class="content">
            <h2>Deposit Completed</h2>
            <p>Dear {first_name},</p>
            <p>Your deposit was processed.</p>
        </div>
        <div class="footer">
            <p>© 2026 Company</p>
        </div>
    </div>
</body>
</html>
```

### After (New Template - Body Content Only):

```html
<h2 style="color: #28a745;">✓ Deposit Completed</h2>

<p>Dear {first_name} {last_name},</p>

<p>Your deposit has been successfully processed and your account balance has been updated.</p>

<div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
    <strong>Deposit Details:</strong>
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

<p>
    <a href="{dashboard_url}" style="display: inline-block; background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">
        View Dashboard
    </a>
</p>
```

**Result:** AdminEmailHelper automatically adds professional header with logo, footer with signature, and complete HTML structure!

---

## Summary

✅ **Update templates** to contain only body content
✅ **Remove** complete HTML structure (DOCTYPE, html, head, body tags)
✅ **Keep** content elements (h2, p, table, div, etc.)
✅ **Use** inline styles for specific formatting
✅ **Let** AdminEmailHelper provide the wrapper
✅ **Test** thoroughly after migration

**Your templates are now simpler, easier to maintain, and will automatically have professional appearance with logo, header, footer, and consistent branding!**

---

For questions or support, contact the development team.
