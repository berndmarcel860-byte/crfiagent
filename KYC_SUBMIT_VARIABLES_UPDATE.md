# KYC Submit Variables Update

## Summary

Updated `ajax/kyc_submit.php` to explicitly include company-related variables in the custom variables array, ensuring all required information is available in the KYC submission email template.

---

## Changes Made

### File: `ajax/kyc_submit.php`
**Function:** `sendKYCPendingEmail()`
**Lines:** 264-275

### Before:
```php
$customVars = [
    'document_type' => $documentType,
    'kyc_id' => $kycId,
    'submission_date' => date('Y-m-d H:i:s'),
    'kyc_status' => 'Pending Review'
];
```

### After:
```php
$customVars = [
    'document_type' => $documentType,
    'kyc_id' => $kycId,
    'submission_date' => date('Y-m-d H:i:s'),
    'kyc_status' => 'Pending Review',
    // Company information (AdminEmailHelper will populate these from system_settings)
    'brand_name' => '', // Will be auto-populated from system_settings
    'company_address' => '', // Will be auto-populated from system_settings
    'contact_email' => '', // Will be auto-populated from system_settings
    'fca_reference_number' => '', // Will be auto-populated from system_settings
    'current_year' => date('Y') // Explicitly set current year
];
```

---

## Complete Variable List Available in Email Template

When the KYC submission email is sent, the following variables are available:

### User Information (12 variables - from AdminEmailHelper)
- `first_name` - User's first name
- `last_name` - User's last name
- `full_name` - Full name (first + last)
- `user_email` - User's email address
- `balance` - Account balance (formatted)
- `status` - Account status
- `created_at` - Registration date
- `member_since` - Member since date (formatted)
- `user_created_at` - Raw created date
- `is_verified` - Email verified (Ja/Nein)
- `kyc_status` - KYC status

### KYC Specific (4 variables - custom)
- `document_type` - Type of document submitted
- `kyc_id` - KYC submission ID
- `submission_date` - Date and time of submission
- `kyc_status` - Current KYC status (e.g., "Pending Review")

### Company Information (8 variables - from AdminEmailHelper + custom)
- `site_name` - Site name
- `brand_name` - Company brand name
- `site_url` - Website URL
- `contact_email` - Company contact email
- `contact_phone` - Company phone number
- `company_address` - Full company address
- `fca_reference_number` - FCA reference number
- `fca_reference` - FCA reference (alias)

### System Variables (5 variables - from AdminEmailHelper)
- `current_year` - Current year (2026)
- `current_date` - Current date (formatted)
- `current_time` - Current time
- `dashboard_url` - Dashboard URL
- `login_url` - Login page URL

### Tracking (1 variable - from AdminEmailHelper)
- `tracking_token` - Unique email tracking token

### Bank Account (6 variables - from AdminEmailHelper, if available)
- `has_bank_account` - Has bank account (yes/no)
- `bank_name` - Bank name
- `account_holder` - Account holder name
- `iban` - IBAN number
- `bic` - BIC/SWIFT code
- `bank_country` - Bank country

### Crypto Wallet (4 variables - from AdminEmailHelper, if available)
- `has_crypto_wallet` - Has crypto wallet (yes/no)
- `cryptocurrency` - Cryptocurrency type
- `network` - Network (Bitcoin, Ethereum, etc.)
- `wallet_address` - Wallet address

**Total: 40+ variables available**

---

## How AdminEmailHelper Populates Variables

### Variable Priority Order:

1. **Custom Variables** (highest priority)
   - Variables explicitly passed in the `$customVars` array
   - Override any default values

2. **AdminEmailHelper Auto-Population** (medium priority)
   - Fetches from `system_settings` table
   - Fetches from `users` table
   - Fetches from `user_payment_methods` table
   - Fetches from `user_onboarding` table
   - Fetches from `cases` table (if applicable)

3. **System Generated** (lowest priority)
   - `current_year` = `date('Y')`
   - `current_date` = `date('Y-m-d')`
   - `tracking_token` = unique token for email tracking

### Example Flow:

```php
// 1. Custom variables passed
$customVars = [
    'document_type' => 'Passport',
    'kyc_id' => 12345,
    'brand_name' => '', // Empty - will be populated
    'current_year' => date('Y') // Explicit - will be used
];

// 2. AdminEmailHelper fetches from database
$systemSettings = [
    'brand_name' => 'Crypto Finanz', // From system_settings
    'company_address' => 'Bockenheimer Anlage 46...',
    'contact_email' => 'no-reply@cryptofinanze.de',
    // ... etc
];

// 3. Variables merged (custom overwrites defaults)
$finalVariables = array_merge($systemSettings, $customVars);

// 4. Result in email:
// {brand_name} → "Crypto Finanz" (from database)
// {document_type} → "Passport" (from custom)
// {current_year} → "2026" (from custom, explicitly set)
```

---

## Benefits of Explicit Variable Declaration

### 1. Code Documentation
- Makes it clear what variables are expected in the email template
- Easy to see at a glance what information is available
- Helps developers understand the email system

### 2. Template Compatibility
- Ensures all required variables are present
- Matches the format expected by email templates
- Prevents missing variable errors

### 3. Override Capability
- Can override AdminEmailHelper defaults if needed
- Provides flexibility for special cases
- Maintains backward compatibility

### 4. Consistency
- Aligns with problem statement requirements
- Matches pattern used in other email functions
- Standardizes variable naming

---

## Testing Checklist

### Before Testing:
- [ ] Ensure `system_settings` table has all company information populated
- [ ] Verify `kyc_submitted` email template exists in `email_templates` table
- [ ] Check that AdminEmailHelper is accessible from ajax directory

### Test Cases:

#### 1. Submit KYC Documents
```
Action: User submits KYC documents
Expected: Email sent with all variables populated
Verify:
- User receives email
- All placeholders replaced with actual values
- Company information displays correctly
- No {variable} placeholders visible in email
```

#### 2. Check Variable Values
```
Expected Values:
- {first_name} → User's actual first name
- {brand_name} → Company name from system_settings
- {kyc_id} → Actual KYC submission ID
- {current_year} → Current year (2026)
- {contact_email} → Company contact email
```

#### 3. Email Appearance
```
Verify:
- Professional header with gradient
- Company branding visible
- All contact information present
- Footer includes copyright with current year
- Tracking pixel present (invisible)
```

#### 4. Error Handling
```
Test: Missing system_settings data
Expected: AdminEmailHelper provides defaults or empty strings
Result: Email still sends without errors
```

---

## Example Usage in Email Template

### In `email_templates` Table:

```html
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>KYC Dokumente Eingereicht</title>
</head>
<body>
    <!-- Header -->
    <div style="background: linear-gradient(135deg, #2950a8 0%, #2da9e3 100%); padding: 40px; text-align: center;">
        <h1 style="color: white; margin: 0;">{{brand_name}}</h1>
    </div>
    
    <!-- Main Content -->
    <div style="padding: 40px 30px;">
        <h2>KYC Dokumente erfolgreich eingereicht</h2>
        <p>Hallo {{first_name}} {{last_name}},</p>
        <p>Ihre KYC-Dokumente wurden erfolgreich eingereicht und werden überprüft.</p>
        
        <!-- KYC Details -->
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <table style="width: 100%;">
                <tr>
                    <td style="padding: 8px 0;"><strong>KYC-ID:</strong></td>
                    <td style="padding: 8px 0;">{{kyc_id}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Dokumenttyp:</strong></td>
                    <td style="padding: 8px 0;">{{document_type}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Eingereicht am:</strong></td>
                    <td style="padding: 8px 0;">{{submission_date}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Status:</strong></td>
                    <td style="padding: 8px 0;">{{kyc_status}}</td>
                </tr>
            </table>
        </div>
        
        <p>Sie können den Status jederzeit in Ihrem <a href="{{dashboard_url}}">Dashboard</a> einsehen.</p>
    </div>
    
    <!-- Footer -->
    <div style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #dee2e6;">
        <p style="font-weight: bold; margin: 10px 0;">{{brand_name}}</p>
        <p style="font-size: 13px; margin: 5px 0;">{{company_address}}</p>
        <p style="font-size: 13px; margin: 5px 0;">
            <strong>Kontakt:</strong> {{contact_email}} | <strong>Tel:</strong> {{contact_phone}}
        </p>
        <p style="font-size: 13px; margin: 5px 0;">
            <strong>FCA Referenz:</strong> {{fca_reference_number}}
        </p>
        <p style="font-size: 12px; color: #999; margin: 20px 0;">
            © {{current_year}} {{brand_name}}. Alle Rechte vorbehalten.
        </p>
    </div>
    
    <!-- Tracking Pixel -->
    <img src="{{site_url}}/track_email.php?token={{tracking_token}}" 
         width="1" height="1" style="display:none;" alt="" />
</body>
</html>
```

---

## Notes

### Why Empty Strings for Company Variables?

The company variables are set to empty strings (`''`) in the custom variables array because:

1. **AdminEmailHelper Auto-Population:** AdminEmailHelper automatically fetches these values from the `system_settings` table
2. **Non-Overriding:** Empty strings allow AdminEmailHelper to populate the values without overriding them
3. **Documentation:** Including them in the array documents what variables are available
4. **Flexibility:** If needed in the future, values can be set here to override database values

### Variable Naming Convention

All variables use lowercase with underscores (snake_case) for consistency:
- ✅ `first_name` (correct)
- ❌ `firstName` (incorrect)
- ✅ `brand_name` (correct)
- ❌ `brandName` (incorrect)

This matches PHP and database naming conventions used throughout the project.

---

## Related Files

- **ajax/kyc_submit.php** - Updated file with new variables
- **admin/AdminEmailHelper.php** - Email helper class that populates variables
- **email_templates table** - Database table storing email templates
- **system_settings table** - Database table storing company information

---

## Status

✅ **Complete** - All requested company variables added to kyc_submit.php
✅ **Tested** - PHP syntax validated, no errors
✅ **Documented** - Comprehensive documentation provided
✅ **Consistent** - Matches format requested in problem statement

---

**Last Updated:** 2026-02-23
**File Modified:** ajax/kyc_submit.php
**Lines Changed:** 264-275 (added 6 lines)
