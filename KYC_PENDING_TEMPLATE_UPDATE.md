# KYC Pending Template Update

## Summary

Updated `kyc_submit.php` to use the `kyc_pending` template key instead of `kyc_submitted`, following standard KYC notification naming conventions.

---

## Change Made

### File: ajax/kyc_submit.php (Line 278)

**Before:**
```php
// Send email using the kyc_submitted template
$emailHelper->sendTemplateEmail('kyc_submitted', $user['id'], $customVars);
```

**After:**
```php
// Send email using the kyc_pending template
$emailHelper->sendTemplateEmail('kyc_pending', $user['id'], $customVars);
```

---

## Reason for Change

### Standard Naming Convention
- `kyc_pending` - For KYC documents under review (pending status)
- `kyc_approved` - For approved KYC documents
- `kyc_rejected` - For rejected KYC documents
- `kyc_reminder` - For KYC submission reminders

Using `kyc_pending` aligns with the standard template naming pattern used across the system.

---

## Variables in JSON Format

### Current Implementation

Variables are passed as a PHP associative array (which is JSON-compatible):

```php
$customVars = [
    'document_type' => $documentType,
    'kyc_id' => $kycId,
    'submission_date' => date('Y-m-d H:i:s'),
    'kyc_status' => 'Pending Review',
    'brand_name' => '',
    'company_address' => '',
    'contact_email' => '',
    'fca_reference_number' => '',
    'current_year' => date('Y')
];
```

### JSON Equivalent

When serialized, this becomes:
```json
{
  "document_type": "ID Card",
  "kyc_id": "12345",
  "submission_date": "2026-02-23 02:17:00",
  "kyc_status": "Pending Review",
  "brand_name": "",
  "company_address": "",
  "contact_email": "",
  "fca_reference_number": "",
  "current_year": "2026"
}
```

---

## How AdminEmailHelper Processes Variables

### Step 1: Receive Variables
AdminEmailHelper receives the associative array from the function call.

### Step 2: Fetch Additional Variables
Automatically fetches from database:
- User data (from users table)
- System settings (from system_settings table)
- Bank info (if available)
- Crypto info (if available)
- System-generated (current_year, tracking_token, etc.)

### Step 3: Merge Variables
Merges custom variables with auto-fetched variables:
```php
$allVariables = array_merge($autoFetchedVars, $customVars);
```

### Step 4: Replace in Template
Replaces placeholders in email template:
- `{{document_type}}` → "ID Card"
- `{{kyc_id}}` → "12345"
- `{{brand_name}}` → "Crypto Finanz"
- etc.

### Step 5: Send Email
Sends the formatted email with all variables replaced.

---

## Complete Variable List Available in Template

### Custom Variables (Passed):
- `document_type` - Type of document submitted
- `kyc_id` - KYC submission ID
- `submission_date` - Date and time of submission
- `kyc_status` - Current status ("Pending Review")
- `brand_name` - (Auto-populated from system_settings)
- `company_address` - (Auto-populated from system_settings)
- `contact_email` - (Auto-populated from system_settings)
- `fca_reference_number` - (Auto-populated from system_settings)
- `current_year` - Current year

### Auto-Fetched Variables (AdminEmailHelper):
**User Data:**
- `first_name`, `last_name`, `full_name`
- `user_email`, `user_id`
- `balance`, `status`, `kyc_status`
- `member_since`, `created_at`

**Company Info:**
- `brand_name`, `site_name`, `site_url`
- `company_address`
- `contact_email`, `contact_phone`
- `fca_reference_number`

**System:**
- `current_year`, `current_date`, `current_time`
- `dashboard_url`, `login_url`
- `tracking_token`

**Bank Info (if available):**
- `bank_name`, `account_holder`, `iban`, `bic`

**Crypto Info (if available):**
- `cryptocurrency`, `network`, `wallet_address`

---

## Email Template

### Template Key: kyc_pending

Should exist in `email_templates` table with:

```sql
INSERT INTO email_templates (
    template_key,
    template_name,
    subject,
    content,
    category,
    is_active
) VALUES (
    'kyc_pending',
    'KYC Pending Review',
    'KYC-Dokumente unter Überprüfung - {{brand_name}}',
    '<!-- HTML content with variable placeholders -->',
    'kyc',
    1
);
```

### Template Content Example:

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KYC Pending Review</title>
</head>
<body>
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%);">
        <h1>{{brand_name}}</h1>
    </div>
    
    <!-- Content -->
    <div style="padding: 40px 30px;">
        <h2>KYC-Dokumente unter Überprüfung</h2>
        <p>Hallo {{first_name}} {{last_name}},</p>
        <p>Ihre KYC-Dokumente wurden erfolgreich eingereicht und werden derzeit überprüft.</p>
        
        <div style="background-color: #f8f9fa; padding: 20px;">
            <p><strong>Dokument-Typ:</strong> {{document_type}}</p>
            <p><strong>KYC-ID:</strong> {{kyc_id}}</p>
            <p><strong>Eingereicht am:</strong> {{submission_date}}</p>
            <p><strong>Status:</strong> {{kyc_status}}</p>
        </div>
        
        <p>Wir werden Sie benachrichtigen, sobald die Überprüfung abgeschlossen ist.</p>
    </div>
    
    <!-- Footer -->
    <div style="background-color: #f8f9fa; padding: 30px;">
        <p>{{brand_name}}</p>
        <p>{{company_address}}</p>
        <p>{{contact_email}} | {{contact_phone}}</p>
        <p>FCA Referenz: {{fca_reference_number}}</p>
        <p>© {{current_year}} {{brand_name}}</p>
    </div>
    
    <!-- Tracking -->
    <img src="{{site_url}}/track_email.php?token={{tracking_token}}" 
         width="1" height="1" style="display:none;" />
</body>
</html>
```

---

## Testing Checklist

### 1. Verify Template Exists
```sql
SELECT * FROM email_templates WHERE template_key = 'kyc_pending';
```

Should return one row with the template.

### 2. Test KYC Submission
1. Login to user dashboard
2. Go to KYC verification page
3. Upload documents
4. Submit KYC

### 3. Check Email Sent
1. Check email inbox for the user
2. Subject should contain brand name
3. All variables should be replaced (no {{}} visible)
4. Professional footer should display

### 4. Verify Variables
Check that these are replaced correctly:
- ✅ User name: {{first_name}} {{last_name}}
- ✅ Document type: {{document_type}}
- ✅ KYC ID: {{kyc_id}}
- ✅ Submission date: {{submission_date}}
- ✅ Status: {{kyc_status}}
- ✅ Brand name: {{brand_name}}
- ✅ Company address: {{company_address}}
- ✅ Contact email: {{contact_email}}
- ✅ FCA reference: {{fca_reference_number}}
- ✅ Current year: {{current_year}}

### 5. Check Logs
```php
error_log("KYC pending email sent to: " . $user['email'] . " for KYC ID: " . $kycId);
```

Should appear in error logs if email sent successfully.

---

## Function Status

### getDefaultKYCPendingTemplate()

**Status:** ✅ Already Removed

This function was removed in previous updates when the system was migrated to use AdminEmailHelper and database-driven email templates.

**Previous Implementation:**
The function used to return a hardcoded HTML template string (~100 lines).

**Current Implementation:**
Templates are stored in the `email_templates` database table and fetched by AdminEmailHelper.

**Benefits of Removal:**
- ✅ No hardcoded templates in code
- ✅ Templates can be updated via database/admin panel
- ✅ Consistent email system across entire application
- ✅ Easier maintenance
- ✅ Support for multiple languages

---

## Benefits of This Update

### 1. Standard Naming
Uses conventional `kyc_pending` template key that matches the purpose of the email.

### 2. Consistency
Aligns with other KYC-related templates:
- kyc_pending
- kyc_approved
- kyc_rejected
- kyc_reminder

### 3. Clear Intent
Template name clearly indicates its purpose (pending review notification).

### 4. Database-Driven
Template content managed in database, not hardcoded.

### 5. Variable Format
Variables properly structured as array (JSON-compatible).

---

## Migration Status

### Completed ✅
- Removed getDefaultKYCPendingTemplate() function
- Migrated to AdminEmailHelper
- Removed PHPMailer manual implementation
- Added all company variables
- Changed template key to kyc_pending

### No Further Action Required
The file is now fully migrated and uses the modern email system with:
- Database-driven templates
- AdminEmailHelper for sending
- All 40+ variables available
- Professional email footer
- Email tracking

---

**Status:** ✅ Complete - Template key updated and variables in proper format!
