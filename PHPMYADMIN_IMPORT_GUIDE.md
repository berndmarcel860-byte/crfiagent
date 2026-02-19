# phpMyAdmin Import Guide - German Email Template

## Quick Start

The file `complete_email_template_phpmyadmin.sql` is **ready to import** into phpMyAdmin.

### 3-Step Import Process:

1. **Open phpMyAdmin** → Select your database
2. **Click "Import" tab** → Choose file: `complete_email_template_phpmyadmin.sql`
3. **Click "Go"** → Done! ✅

---

## What Gets Imported

### Table Created:
```sql
email_templates (
    id, template_key, subject, content, variables, created_at
)
```

### Email Template Added:
- **template_key:** `onboarding_completed`
- **Language:** Professional German
- **Subject:** "Willkommen bei {{company_name}} - Verifizierung erforderlich"
- **Content:** Complete HTML email (~7000 characters)
- **Variables:** 19 template variables

---

## German Email Content

### Sections Included:
1. 🎉 Welcome Header (Gradient purple)
2. ✓ Success Confirmation
3. ⚠️ Verification Warning
4. 🏦 Bank Account Details
5. 💰 Crypto Wallet Details
6. 3-Step Verification Guide
7. 🛡️ Security Explanation
8. Dashboard Button
9. Support Contact
10. Professional Footer

### Language Quality:
- ✅ Formal business German
- ✅ Professional terminology
- ✅ Security-focused messaging
- ✅ Clear and polite tone

---

## After Import

### Verify Success:
```sql
SELECT template_key, subject 
FROM email_templates 
WHERE template_key = 'onboarding_completed';
```

### Expected Result:
```
template_key: onboarding_completed
subject: Willkommen bei {{company_name}} - Verifizierung erforderlich
```

### PHP Integration:
The template is automatically used by `onboarding.php` after user completes registration.

---

## Requirements

- **MySQL:** 5.7+ or MariaDB 10.3+
- **phpMyAdmin:** 4.x or 5.x
- **Character Set:** UTF-8 (utf8mb4)
- **Permissions:** CREATE, INSERT

---

**Ready to import!** 🚀
