# Onboarding Email Template Installation Guide

## Overview
This guide explains how to install the professional German onboarding completion email template into your database.

## File Information
- **File:** `insert_email_template.sql`
- **Template Name:** `onboarding_completed`
- **Language:** German (Professional)
- **Size:** 6,986 bytes
- **Format:** HTML with inline CSS

## Email Template Features

### 1. Professional Design
- Gradient header (purple to violet)
- Modern card-based layout
- Responsive design
- Color-coded alerts and sections
- Professional typography

### 2. Content Sections
- Welcome message
- Success confirmation
- Critical verification warning
- Bank account details card
- Cryptocurrency wallet details card
- 3-step verification process
- Security explanation (5 key points)
- Call-to-action button
- Support contact information
- Professional footer with company info

### 3. Template Variables (19 total)
The template uses the following variables that will be replaced dynamically:

#### From User Data:
- `{{user_name}}` - User's full name

#### From Settings Table:
- `{{company_name}}` - Platform/company name
- `{{support_email}}` - Support email address
- `{{support_phone}}` - Support phone number
- `{{company_address}}` - Company street address
- `{{company_city}}` - Company city
- `{{company_country}}` - Company country

#### From Onboarding Data:
- `{{bank_name}}` - User's bank name
- `{{account_holder}}` - Bank account holder name
- `{{iban}}` - IBAN number
- `{{bic}}` - BIC/SWIFT code
- `{{cryptocurrency}}` - Crypto type (BTC, ETH, etc.)
- `{{network}}` - Blockchain network
- `{{wallet_address}}` - Cryptocurrency wallet address

#### Auto-Generated:
- `{{dashboard_url}}` - Link to user dashboard
- `{{website_url}}` - Website homepage URL
- `{{terms_url}}` - Terms and conditions page
- `{{privacy_url}}` - Privacy policy page
- `{{current_year}}` - Current year for copyright

## Installation Instructions

### Prerequisites
- MySQL 5.7+ or MariaDB 10.3+
- Database with `email_templates` table
- Proper database credentials

### Method 1: Direct MySQL Command
```bash
mysql -u your_username -p your_database_name < insert_email_template.sql
```

### Method 2: MySQL Command Line
```bash
mysql -u your_username -p
USE your_database_name;
SOURCE /path/to/insert_email_template.sql;
```

### Method 3: phpMyAdmin
1. Log into phpMyAdmin
2. Select your database
3. Click "SQL" tab
4. Copy and paste the contents of `insert_email_template.sql`
5. Click "Go"

### Method 4: Database Management Tool
Use tools like:
- MySQL Workbench
- DBeaver
- HeidiSQL
- Sequel Pro (Mac)

## Verification

After installation, verify the template was inserted:

```sql
SELECT name, subject, LENGTH(body) as body_length, variables 
FROM email_templates 
WHERE name = 'onboarding_completed';
```

Expected results:
- **name:** onboarding_completed
- **subject:** Willkommen bei {{company_name}} - Verifizierung erforderlich
- **body_length:** ~6900 bytes
- **variables:** List of 19 comma-separated variable names

## Usage in PHP

The template is automatically loaded by `onboarding.php` when a user completes registration:

```php
// Load template from database
$stmt = $pdo->prepare("SELECT * FROM email_templates WHERE name = ?");
$stmt->execute(['onboarding_completed']);
$template = $stmt->fetch(PDO::FETCH_ASSOC);

// Replace variables
$emailBody = $template['body'];
foreach ($variables as $key => $value) {
    $emailBody = str_replace('{{'.$key.'}}', $value, $emailBody);
}

// Send email
mail($userEmail, $template['subject'], $emailBody, $headers);
```

## Email Preview

### Subject Line:
```
Willkommen bei [Company Name] - Verifizierung erforderlich
```

### Email Content Includes:
1. **Header:** Welcome message with gradient background
2. **Success Alert:** Green confirmation box
3. **Warning Alert:** Red verification requirement notice
4. **Bank Details:** Card showing all bank account info
5. **Crypto Details:** Card showing cryptocurrency wallet info
6. **Verification Steps:** Numbered 3-step process guide
7. **Security Info:** Explanation of why verification matters
8. **CTA Button:** Link to dashboard
9. **Support:** Contact information
10. **Footer:** Company information and links

## Customization

### To Modify the Template:
1. Update the record in the database:
```sql
UPDATE email_templates 
SET body = 'your_new_html_content', 
    updated_at = NOW() 
WHERE name = 'onboarding_completed';
```

2. Or delete and re-insert:
```sql
DELETE FROM email_templates WHERE name = 'onboarding_completed';
-- Then run insert_email_template.sql again
```

### Settings Table Requirements
Ensure your `settings` table has these fields populated:
- `site_name` (or `company_name`)
- `support_email`
- `support_phone`
- `company_address`
- `company_city`
- `company_country`
- `from_email`

## Troubleshooting

### Error: Duplicate Entry
If you get a duplicate entry error:
```sql
-- Delete existing template first
DELETE FROM email_templates WHERE name = 'onboarding_completed';
-- Then run the insert again
```

### Error: Table doesn't exist
Create the email_templates table:
```sql
CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Email Not Sending
Check:
1. PHP mail() function is configured
2. SMTP settings are correct (if using SMTP)
3. `onboarding.php` has email sending code enabled
4. Settings table has all required fields
5. Check email logs table for error messages

## Support
For issues or questions, refer to the main repository documentation or contact the development team.

## Version History
- **v1.0** (2026-02-19): Initial professional German email template with payment details

---

**Last Updated:** 2026-02-19
**Status:** Production Ready ✅
