# Deposit Email Templates - Visual Guide

## What Was Changed

### Before ❌
```
add_deposit.php
├── Hardcoded HTML (30+ lines)
├── Manual variable insertion
├── Difficult to update
└── Code changes required for email updates
```

### After ✅
```
add_deposit.php
├── Clean code (10 lines)
├── Template reference
├── Easy to update
└── No code changes for email updates

email_templates table
├── deposit_completed (Green theme)
└── deposit_pending (Yellow theme)
```

---

## How Email Flow Works

```
┌─────────────────────────────────────────────────────────────┐
│                    ADMIN CREATES DEPOSIT                     │
│  Admin Panel → Add Deposit → Select User → Enter Amount     │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                  add_deposit.php EXECUTES                    │
│  • Validates input                                           │
│  • Inserts to database                                       │
│  • Updates user balance (if completed)                       │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│               DETERMINE EMAIL TEMPLATE                       │
│  IF status = 'completed' → deposit_completed                │
│  IF status = 'pending'   → deposit_pending                  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│            PREPARE CUSTOM VARIABLES                          │
│  deposit_amount    → "1,000.00"                             │
│  deposit_reference → "DEP-1709123456-ABC123"               │
│  payment_method    → "bank_transfer"                        │
│  deposit_status    → "Completed"                            │
│  date              → "01.03.2026 10:30"                     │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│        AdminEmailHelper->sendTemplateEmail()                 │
│  1. Fetches template from email_templates table             │
│  2. Fetches user data from users table                      │
│  3. Fetches company data from system_settings               │
│  4. Fetches bank/crypto data from user_payment_methods      │
│  5. Combines all variables (46+ total)                      │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              REPLACE TEMPLATE VARIABLES                      │
│  {first_name}      → "John"                                 │
│  {last_name}       → "Doe"                                  │
│  {deposit_amount}  → "1,000.00"                             │
│  {dashboard_url}   → "https://site.com/dashboard"           │
│  {contact_email}   → "support@site.com"                     │
│  ... (41+ more variables)                                   │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│            WRAP IN PROFESSIONAL HTML TEMPLATE                │
│  • Add email header with logo                               │
│  • Apply responsive CSS                                      │
│  • Add footer with unsubscribe                              │
│  • Format for email clients                                 │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                  SEND VIA PHPMAILER                          │
│  • Connect to SMTP server                                    │
│  • Set from/to addresses                                     │
│  • Attach email content                                      │
│  • Send email                                                │
└────────────────────┬────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                   LOG RESULT                                 │
│  ✅ Success → Log: "Email sent to user ID: X"               │
│  ❌ Failure → Log: "Failed to send email to user ID: X"     │
└─────────────────────────────────────────────────────────────┘
```

---

## Template Selection Logic

```php
// Simple if-else determines which template
if ($status === 'completed') {
    $templateKey = 'deposit_completed';   // Green success template
} else {
    $templateKey = 'deposit_pending';     // Yellow pending template
}

// Then send using that template
$emailHelper->sendTemplateEmail($templateKey, $userId, $customVars);
```

---

## Email Templates Comparison

### deposit_completed (Green Theme)
```
┌──────────────────────────────────────────┐
│  ✓ Deposit Completed                     │ ← Green header
├──────────────────────────────────────────┤
│  Dear John Doe,                          │
│                                          │
│  Great news! Your deposit has been       │
│  successfully processed...               │
│                                          │
│  ┌────────────────────────────────────┐ │
│  │ Deposit Details                    │ │ ← Gray box
│  │ Amount:    €1,000.00              │ │
│  │ Reference: DEP-1709123456-ABC123  │ │
│  │ Method:    Bank Transfer          │ │
│  │ Status:    ✓ Completed            │ │ ← Green
│  │ Date:      01.03.2026 10:30       │ │
│  └────────────────────────────────────┘ │
│                                          │
│  ┌────────────────────────────────────┐ │
│  │ ✓ Your balance has been updated   │ │ ← Green alert
│  │   View in dashboard                │ │
│  └────────────────────────────────────┘ │
│                                          │
│  [    View Dashboard    ]                │ ← Blue button
│                                          │
│  Questions? Contact support@site.com    │
└──────────────────────────────────────────┘
```

### deposit_pending (Yellow Theme)
```
┌──────────────────────────────────────────┐
│  ⏳ Deposit Pending                      │ ← Yellow header
├──────────────────────────────────────────┤
│  Dear John Doe,                          │
│                                          │
│  We have received your deposit and       │
│  it is being processed...                │
│                                          │
│  ┌────────────────────────────────────┐ │
│  │ Deposit Details                    │ │ ← Gray box
│  │ Amount:    €1,000.00              │ │
│  │ Reference: DEP-1709123456-ABC123  │ │
│  │ Method:    Bank Transfer          │ │
│  │ Status:    ⏳ Pending             │ │ ← Yellow
│  │ Date:      01.03.2026 10:30       │ │
│  └────────────────────────────────────┘ │
│                                          │
│  ┌────────────────────────────────────┐ │
│  │ ⏳ Your deposit is being processed │ │ ← Yellow alert
│  │   Usually takes 1-2 business days  │ │
│  └────────────────────────────────────┘ │
│                                          │
│  ┌────────────────────────────────────┐ │
│  │ ℹ️ What happens next?             │ │ ← Blue info box
│  │ • Team verifies payment            │ │
│  │ • Balance updated automatically    │ │
│  │ • Confirmation email sent          │ │
│  └────────────────────────────────────┘ │
│                                          │
│  [    View Dashboard    ]                │ ← Blue button
│                                          │
│  Questions? Contact support@site.com    │
└──────────────────────────────────────────┘
```

---

## Variables in Templates

### How Variables Work

```html
<!-- In email_templates.content -->
<p>Dear {first_name} {last_name},</p>
<p>Amount: €{deposit_amount}</p>

<!-- After AdminEmailHelper processes -->
<p>Dear John Doe,</p>
<p>Amount: €1,000.00</p>
```

### Variable Categories

```
Custom Variables (5)
├── deposit_amount
├── deposit_reference
├── deposit_status
├── payment_method
└── date

User Variables (10+)
├── first_name
├── last_name
├── email
├── balance
└── ...

Company Variables (10+)
├── brand_name
├── contact_email
├── site_url
└── ...

System Variables (10+)
├── dashboard_url
├── current_date
├── current_year
└── ...

Financial Variables (10+)
├── bank_name
├── iban
├── wallet_address
└── ...
```

---

## Update Process

### Updating Email Content

```
BEFORE (Code Change Required)
────────────────────────────
1. Edit add_deposit.php
2. Change HTML code
3. Test changes
4. Deploy to server
5. Restart PHP-FPM
   └─> Downtime + Code deployment

AFTER (Database Update Only)
────────────────────────────
1. Update email_templates table
2. Done!
   └─> Instant + No deployment
```

### Example Update

```sql
-- Change email greeting
UPDATE email_templates 
SET content = REPLACE(
    content, 
    'Dear {first_name} {last_name}',
    'Hello {first_name}'
)
WHERE template_key = 'deposit_completed';

-- Add new section
UPDATE email_templates 
SET content = CONCAT(
    content,
    '<p>New section here...</p>'
)
WHERE template_key = 'deposit_pending';
```

---

## Installation Steps

```
Step 1: Run SQL
───────────────
$ mysql -u user -p db < email_template_deposit.sql
Enter password: ****
Query OK, 2 rows affected

Step 2: Verify
──────────────
mysql> SELECT template_key FROM email_templates 
    -> WHERE template_key LIKE 'deposit_%';
+--------------------+
| template_key       |
+--------------------+
| deposit_completed  |
| deposit_pending    |
+--------------------+
2 rows in set

Step 3: Test
────────────
Create test deposit → Check email received ✓
```

---

## Quick Reference

### Files
```
email_template_deposit.sql           → Install templates
admin/admin_ajax/add_deposit.php     → Uses templates
DEPOSIT_EMAIL_TEMPLATES.md           → Full documentation
QUICK_START_DEPOSIT_TEMPLATES.md     → Quick guide
```

### Commands
```bash
# Install
mysql -u user -p db < email_template_deposit.sql

# Verify
mysql -u user -p db -e "SELECT template_key FROM email_templates WHERE template_key LIKE 'deposit_%'"

# Update
mysql -u user -p db -e "UPDATE email_templates SET content='...' WHERE template_key='deposit_completed'"
```

### Templates
```
deposit_completed  → Green theme, "✓ Completed"
deposit_pending    → Yellow theme, "⏳ Pending"
```

---

## Benefits Summary

```
✅ No code changes for email updates
✅ Instant updates (no deployment)
✅ Professional design
✅ 46+ variables available
✅ Easy customization
✅ Consistent branding
✅ Status-specific templates
✅ Responsive design
✅ Better user experience
✅ Easier maintenance
```

---

That's the complete visual guide to understanding the deposit email template system! 🎉
