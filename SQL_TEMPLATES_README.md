# SQL Email Template Files

## Overview

This directory contains SQL files for inserting the professional German onboarding completion email template into the database.

## Files

### 1. `insert_email_template.sql`
- **Format:** Minified (single line HTML)
- **Size:** ~7KB
- **Purpose:** Production-optimized version
- **Pros:** Smaller file size, faster parsing
- **Cons:** Harder to read and edit

### 2. `insert_email_template_formatted.sql` ✅ RECOMMENDED
- **Format:** Readable (multi-line HTML)
- **Size:** ~10KB
- **Purpose:** Development and maintenance
- **Pros:** Easy to read, edit, and debug
- **Cons:** Slightly larger file size

**Both files are functionally identical and will produce the same result in the database.**

## Which One to Use?

### For Development/Editing:
Use **`insert_email_template_formatted.sql`**
- Easier to read and understand
- Better for making changes
- Clear structure visible

### For Production:
Either file works fine! MySQL handles both equally well.
- Use formatted version for better maintainability
- Both are production-ready

## Installation

### Method 1: Command Line
```bash
mysql -u your_username -p your_database < insert_email_template_formatted.sql
```

### Method 2: MySQL Interactive
```bash
mysql -u your_username -p your_database
source /path/to/insert_email_template_formatted.sql
```

### Method 3: phpMyAdmin
1. Open phpMyAdmin
2. Select your database
3. Go to "SQL" tab
4. Copy and paste the content from the file
5. Click "Go"

## Verification

After installation, verify the template was inserted:

```sql
SELECT 
    name,
    subject,
    LENGTH(body) as body_length,
    variables,
    created_at
FROM email_templates 
WHERE name = 'onboarding_completed';
```

**Expected Result:**
- name: onboarding_completed
- subject: Willkommen bei {{company_name}} - Verifizierung erforderlich
- body_length: ~6900-7000 characters
- variables: 19 comma-separated variable names
- created_at: Current timestamp

## Template Features

### Email Includes:
✅ Professional German greeting
✅ Success confirmation
✅ Critical verification warning
✅ User's bank account details
✅ User's crypto wallet details
✅ 3-step verification guide
✅ Security explanation
✅ Dashboard call-to-action button
✅ Support contact information
✅ Professional company footer

### Variables (19 total):
1. user_name
2. company_name
3. bank_name
4. account_holder
5. iban
6. bic
7. cryptocurrency
8. network
9. wallet_address
10. dashboard_url
11. support_email
12. support_phone
13. company_address
14. company_city
15. company_country
16. website_url
17. terms_url
18. privacy_url
19. current_year

## Integration with onboarding.php

The template is automatically loaded and used by `onboarding.php` after Step 4 completion:

```php
// Load template
$stmt = $pdo->prepare("SELECT * FROM email_templates WHERE name = 'onboarding_completed'");
$stmt->execute();
$template = $stmt->fetch(PDO::FETCH_ASSOC);

// Replace variables
$emailBody = str_replace('{{variable}}', $actualValue, $template['body']);

// Send email
mail($userEmail, $subject, $emailBody, $headers);
```

## Troubleshooting

### Issue: "Table doesn't exist"
**Solution:** Ensure the email_templates table exists:
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

### Issue: "Duplicate entry"
**Solution:** Template already exists. Delete and re-insert:
```sql
DELETE FROM email_templates WHERE name = 'onboarding_completed';
-- Then run the INSERT again
```

### Issue: "Emails not sending"
**Check:**
1. Settings table has required fields (support_email, etc.)
2. PHP mail() function is configured
3. Email logs for error messages

## Customization

To customize the email template:
1. Use the **formatted** version for editing
2. Modify HTML/CSS as needed
3. Test the HTML in an email client
4. Update the database with new template:
   ```sql
   UPDATE email_templates 
   SET body = 'your_updated_html', 
       updated_at = NOW() 
   WHERE name = 'onboarding_completed';
   ```

## Notes

- ✅ Both SQL files are syntactically correct
- ✅ Both produce identical results in database
- ✅ Use formatted version for better maintainability
- ✅ Template is production-ready
- ✅ No errors in SQL query structure

## Support

For questions or issues:
- Check `EMAIL_TEMPLATE_INSTALLATION.md` for detailed instructions
- Review `onboarding.php` for integration code
- Contact development team

---

**Last Updated:** 2026-02-19
**Status:** Production Ready ✅
