# Quick Start: Deposit Email Templates

## 1. Install Templates (2 minutes)

```bash
# Navigate to your project
cd /path/to/crfiagent

# Run SQL file
mysql -u your_username -p your_database < email_template_deposit.sql
```

Or copy the SQL and paste into phpMyAdmin SQL tab.

## 2. Verify Installation

```sql
SELECT template_key, subject 
FROM email_templates 
WHERE template_key LIKE 'deposit_%';
```

Should show:
- `deposit_completed`
- `deposit_pending`

## 3. Done! 

The code in `add_deposit.php` is already updated. Templates will be used automatically.

## How It Works

When admin creates a deposit:

**Completed Deposit:**
```
Status: completed → Uses "deposit_completed" template → Green success email
```

**Pending Deposit:**
```
Status: pending → Uses "deposit_pending" template → Yellow pending email
```

## Templates Include

**deposit_completed (Green):**
- ✓ Balance updated confirmation
- Amount, reference, method, date
- Dashboard link
- Success styling

**deposit_pending (Yellow):**
- ⏳ Processing message
- Processing timeline (1-2 days)
- What happens next
- Dashboard link
- Pending styling

## Customize Templates

### Via Database:
```sql
UPDATE email_templates 
SET content = 'Your HTML here with {variables}'
WHERE template_key = 'deposit_completed';
```

### Via phpMyAdmin:
1. Open `email_templates` table
2. Find `deposit_completed` or `deposit_pending`
3. Edit `content` field
4. Save

## Available Variables

Use these in your templates:

**Deposit Info:**
- `{deposit_amount}` - Amount (formatted)
- `{deposit_reference}` - Reference number
- `{payment_method}` - Payment method
- `{date}` - Date and time

**User Info:**
- `{first_name}`, `{last_name}` - User name
- `{email}` - User email
- `{balance}` - Current balance

**Links:**
- `{dashboard_url}` - Dashboard link
- `{login_url}` - Login link

**Company:**
- `{brand_name}` - Company name
- `{contact_email}` - Support email
- `{contact_phone}` - Support phone

**41+ more variables available!**

## Test

Create a test deposit:
```
Status: completed → Check user receives green email
Status: pending → Check user receives yellow email
```

## Troubleshooting

**Template not found?**
→ Run SQL installation again

**Variables not replaced?**
→ Use exact variable names: `{first_name}` not `{firstName}`

**No email sent?**
→ Check error logs and SMTP settings

## Full Documentation

See `DEPOSIT_EMAIL_TEMPLATES.md` for complete guide.

## Support

- Check error logs: Look for "Template not found" or email errors
- Verify SMTP settings in database
- Test with simple template first
- Review AdminEmailHelper.php documentation

---

That's it! Your deposit emails now use professional templates from the database. 🎉
