# German Deposit Email Templates Guide
## Deutsche Einzahlungs-E-Mail-Vorlagen Anleitung

Complete guide for using German deposit email templates.

---

## 📋 Overview / Überblick

The German deposit email templates provide professional, native German translations for deposit notifications.

Die deutschen Einzahlungs-E-Mail-Vorlagen bieten professionelle, native deutsche Übersetzungen für Einzahlungsbenachrichtigungen.

---

## 🚀 Quick Installation

### Step 1: Install Templates

```bash
mysql -u username -p database_name < email_template_deposit_german.sql
```

Replace:
- `username` with your MySQL username
- `database_name` with your database name

### Step 2: Verify Installation

```sql
SELECT template_key, subject 
FROM email_templates 
WHERE template_key IN ('deposit_completed_de', 'deposit_pending_de');
```

Expected output:
```
+----------------------+------------------------------------------+
| template_key         | subject                                  |
+----------------------+------------------------------------------+
| deposit_completed_de | Einzahlung Abgeschlossen - €{deposit_...}|
| deposit_pending_de   | Einzahlung In Bearbeitung - €{deposit...}|
+----------------------+------------------------------------------+
```

---

## 📧 Available Templates

### 1. deposit_completed_de (Abgeschlossene Einzahlung)

**Used when:** Deposit is completed and balance is updated  
**Color theme:** Green (Success)  
**Subject:** Einzahlung Abgeschlossen - €{deposit_amount}

**Key Features:**
- ✓ Success confirmation message
- ✓ Updated balance notification
- ✓ Complete deposit details table
- ✓ Dashboard link
- ✓ Support contact information

**German Text Includes:**
- "Gute Nachrichten! Ihre Einzahlung wurde erfolgreich verarbeitet"
- "Ihr Kontoguthaben wurde aktualisiert"
- "Dashboard Anzeigen"
- Professional formatting with Einzahlungsdetails table

### 2. deposit_pending_de (In Bearbeitung)

**Used when:** Deposit is pending verification  
**Color theme:** Yellow/Orange (Pending)  
**Subject:** Einzahlung In Bearbeitung - €{deposit_amount}

**Key Features:**
- ⏳ Processing status message
- ⏳ Timeline explanation (1-2 Werktage)
- ⏳ "What happens next" section
- ⏳ Dashboard link
- ⏳ Support contact information

**German Text Includes:**
- "Ihre Einzahlungsanfrage wird derzeit bearbeitet"
- "Dies dauert normalerweise 1-2 Werktage"
- "Was passiert als Nächstes?" section
- Processing steps in German

---

## 🔧 How to Use

### Option 1: Update add_deposit.php to Always Use German

**File:** `admin/admin_ajax/add_deposit.php`

Find this line (~line 168):
```php
$templateKey = ($status === 'completed') ? 'deposit_completed' : 'deposit_pending';
```

Change to:
```php
$templateKey = ($status === 'completed') ? 'deposit_completed_de' : 'deposit_pending_de';
```

### Option 2: Language Detection (Automatic)

Add language detection based on user preference:

```php
// Get user's language preference (from database or session)
$userLanguage = $user['language'] ?? 'en'; // default to English

// Select template based on language
if ($userLanguage === 'de') {
    $templateKey = ($status === 'completed') ? 'deposit_completed_de' : 'deposit_pending_de';
} else {
    $templateKey = ($status === 'completed') ? 'deposit_completed' : 'deposit_pending';
}

$emailHelper->sendTemplateEmail($templateKey, $userId, $customVars);
```

### Option 3: Admin Selection

Allow admin to choose language when creating deposit:

```php
// Get language from POST parameter
$emailLanguage = $_POST['email_language'] ?? 'en'; // 'de' or 'en'

// Select template
$suffix = ($emailLanguage === 'de') ? '_de' : '';
$templateKey = ($status === 'completed') ? "deposit_completed{$suffix}" : "deposit_pending{$suffix}";

$emailHelper->sendTemplateEmail($templateKey, $userId, $customVars);
```

---

## 📝 German Terminology Reference

| English | German | Usage |
|---------|--------|-------|
| Deposit | Einzahlung | Title and headings |
| Completed | Abgeschlossen | Status description |
| Pending | In Bearbeitung | Status description |
| Amount | Betrag | Deposit amount field |
| Reference | Referenznummer | Transaction reference |
| Payment Method | Zahlungsmethode | Payment type |
| Status | Status | Status field |
| Date | Datum | Date field |
| Balance | Guthaben | Account balance |
| Updated | Aktualisiert | Balance update |
| Dashboard | Dashboard | User dashboard |
| View | Anzeigen | View/display action |
| Processing | Bearbeitung | Processing status |
| Confirmation | Bestätigung | Confirmation message |
| Business days | Werktage | Time reference |
| Support Team | Support-Team | Customer support |
| Contact | Kontaktieren | Contact action |

---

## 🎨 Email Design

Both German templates maintain the same professional design as English versions:

### Layout Structure:
```
┌─────────────────────────────────────┐
│ Header (Colored border)             │
│ ✓/⏳ [Status Title]                 │
├─────────────────────────────────────┤
│ Greeting                            │
│ Main message                        │
├─────────────────────────────────────┤
│ Details Table (Gray box)            │
│ • Betrag                            │
│ • Referenznummer                    │
│ • Zahlungsmethode                   │
│ • Status                            │
│ • Datum                             │
├─────────────────────────────────────┤
│ Status Box (Colored)                │
│ Key message                         │
├─────────────────────────────────────┤
│ [Dashboard Anzeigen] Button         │
├─────────────────────────────────────┤
│ Support info                        │
│ Automatic message notice            │
└─────────────────────────────────────┘
```

### Color Scheme:
- **Completed:** Green (#28a745) - Success, positive
- **Pending:** Yellow/Orange (#ffc107) - Warning, waiting

---

## 🧪 Testing

### Test Completed Deposit (German):

1. Create deposit with status = 'completed'
2. Use template: `deposit_completed_de`
3. Check email contains:
   - Subject: "Einzahlung Abgeschlossen - €[amount]"
   - "Gute Nachrichten!"
   - "Guthaben aktualisiert"
   - Green color theme
   - "Dashboard Anzeigen" button

### Test Pending Deposit (German):

1. Create deposit with status = 'pending'
2. Use template: `deposit_pending_de`
3. Check email contains:
   - Subject: "Einzahlung In Bearbeitung - €[amount]"
   - "wird derzeit bearbeitet"
   - "1-2 Werktage"
   - "Was passiert als Nächstes?"
   - Yellow/orange color theme

---

## 🔍 Available Variables

All templates support these variables:

### User Variables:
- `{first_name}` - User's first name
- `{last_name}` - User's last name
- `{full_name}` - Full name
- `{email}` - User's email
- `{user_id}` - User ID
- `{balance}` - Account balance

### Deposit Variables (Custom):
- `{deposit_amount}` - Deposit amount (formatted)
- `{deposit_reference}` - Transaction reference
- `{payment_method}` - Payment method used
- `{deposit_status}` - Deposit status
- `{date}` - Transaction date/time

### Company Variables:
- `{brand_name}` - Company name
- `{site_url}` - Website URL
- `{contact_email}` - Support email
- `{contact_phone}` - Support phone
- `{company_address}` - Company address

### System Variables:
- `{dashboard_url}` - User dashboard link
- `{login_url}` - Login page link
- `{current_year}` - Current year
- `{current_date}` - Current date
- `{current_time}` - Current time

---

## 🛠️ Customization

### Update Template Content

You can customize templates via database:

```sql
UPDATE email_templates 
SET content = 'Your custom German HTML content here'
WHERE template_key = 'deposit_completed_de';
```

### Via phpMyAdmin:
1. Open `email_templates` table
2. Find `deposit_completed_de` or `deposit_pending_de`
3. Edit `content` field
4. Save changes

### Important:
- Keep variable placeholders: `{variable_name}`
- Maintain HTML structure for proper rendering
- Test after changes

---

## 📚 Related Files

### English Templates:
- `email_template_deposit.sql` - English deposit templates
- Templates: `deposit_completed`, `deposit_pending`

### German Templates:
- `email_template_deposit_german.sql` - German deposit templates (THIS FILE)
- Templates: `deposit_completed_de`, `deposit_pending_de`

### Other German Templates:
- `admin/german_email_templates.sql` - Other German templates
  - `kyc_reminder_de`
  - `login_reminder_de`
  - `withdraw_reminder_de`
  - `onboarding_reminder_de`
  - `inactive_user_de`
  - `balance_alert_de`

### Documentation:
- `DEPOSIT_EMAIL_TEMPLATES.md` - Full English templates guide
- `QUICK_START_DEPOSIT_TEMPLATES.md` - Quick start (English)
- `DEPOSIT_EMAIL_VISUAL_GUIDE.md` - Visual guide

---

## ❓ Troubleshooting

### Issue: Template not found

**Solution:**
```sql
-- Check if templates exist
SELECT template_key FROM email_templates 
WHERE template_key LIKE 'deposit%de';

-- If empty, reinstall:
mysql -u username -p database_name < email_template_deposit_german.sql
```

### Issue: Variables not replaced

**Problem:** Email shows `{first_name}` instead of actual name

**Solution:**
- Check custom variables are passed to `sendTemplateEmail()`
- Verify variable names match exactly (case-sensitive)
- Check AdminEmailHelper is fetching user data

### Issue: Wrong language sent

**Solution:**
- Check template_key in add_deposit.php
- Verify it uses `_de` suffix for German
- Check language detection logic if implemented

### Issue: Formatting looks broken

**Solution:**
- Check HTML tags are closed properly
- Verify CSS styles are inline (email clients require inline CSS)
- Test in multiple email clients

---

## 🎯 Best Practices

1. **Language Detection:**
   - Store user language preference in database
   - Auto-select template based on preference
   - Allow manual override if needed

2. **Fallback:**
   - Always have English as fallback
   - If German template not found, use English

3. **Testing:**
   - Test both templates thoroughly
   - Check in multiple email clients (Gmail, Outlook, etc.)
   - Verify all variables are replaced

4. **Consistency:**
   - Use same terminology across all German templates
   - Maintain consistent formatting
   - Keep color scheme consistent with English

5. **Updates:**
   - When updating English templates, update German too
   - Keep translations synchronized
   - Document any changes

---

## 📞 Support

### Need Help?

- **Email Templates Issues:** Check AdminEmailHelper.php
- **Translation Questions:** Review German terminology reference
- **Database Issues:** Check connection in config.php
- **Testing:** Use test procedures above

### Additional Resources:

- AdminEmailHelper documentation
- Email template system guide
- German language templates collection
- PHPMailer configuration guide

---

## ✅ Summary

**German deposit templates provide:**
- ✅ Professional German translations
- ✅ Native language support for German users
- ✅ Same features as English templates
- ✅ Easy installation and configuration
- ✅ Consistent branding and design
- ✅ Full variable support
- ✅ Responsive email design

**Installation:** Single SQL file  
**Usage:** Update template_key in code  
**Maintenance:** Easy database updates  
**Compatibility:** Works with existing system

---

**Ready to use!** Install the templates and start sending professional German deposit notifications. 🇩🇪
