# PHPMailer Quick Reference Card

## 🚀 Quick Setup (3 Steps)

### 1. Install PHPMailer
```bash
composer require phpmailer/phpmailer
```

### 2. Create smtp_settings Table
```bash
mysql -u username -p cryptofinanze < smtp_settings_table.sql
```

### 3. Configure SMTP (Choose Your Provider)

**Gmail:**
```sql
UPDATE smtp_settings SET 
  smtp_host = 'smtp.gmail.com',
  smtp_port = 587,
  smtp_encryption = 'tls',
  smtp_username = 'your-email@gmail.com',
  smtp_password = 'your-app-password',
  smtp_from_email = 'noreply@cryptofinanze.de',
  smtp_from_name = 'Crypto Finanz'
WHERE id = 1;
```

**Generic SMTP:**
```sql
UPDATE smtp_settings SET 
  smtp_host = 'mail.yourdomain.com',
  smtp_port = 587,
  smtp_encryption = 'tls',
  smtp_username = 'user@yourdomain.com',
  smtp_password = 'your-password',
  smtp_from_email = 'noreply@yourdomain.com',
  smtp_from_name = 'Your Company'
WHERE id = 1;
```

---

## ✅ Testing

### Test Email:
1. Complete onboarding as test user
2. Check logs: `tail -f /var/log/apache2/error.log`
3. Look for: `Onboarding completion email sent successfully`
4. Check inbox (and spam folder)

### Verify Configuration:
```sql
SELECT smtp_host, smtp_port, smtp_from_email FROM smtp_settings WHERE id = 1;
```

### Check Email Logs:
```sql
SELECT * FROM email_logs ORDER BY sent_at DESC LIMIT 5;
```

---

## 🔧 Common Issues

### "Class 'PHPMailer' not found"
```bash
composer require phpmailer/phpmailer
ls vendor/phpmailer/phpmailer/  # Verify installation
```

### "SMTP connect() failed"
- Check smtp_host and smtp_port
- Verify firewall allows outbound SMTP
- Test: `telnet smtp.gmail.com 587`

### "Could not authenticate"
- For Gmail: Use App Password (not regular password)
- Generate at: https://myaccount.google.com/security
- Enable "Less secure app access" or use OAuth

### Email not received
- Check spam folder
- Verify from_email domain is valid
- Check email_logs table for errors
- Test with different email address

---

## 📊 Monitoring

### Success Log Entry:
```
Onboarding completion email sent successfully to: user@example.com
```

### Error Log Entry:
```
PHPMailer Error: SMTP connect() failed
```

### Database Status:
```sql
SELECT status, COUNT(*) as count 
FROM email_logs 
WHERE email_type = 'onboarding_completed' 
GROUP BY status;
```

---

## 📚 Documentation

- **EMAIL_TROUBLESHOOTING.md** - Complete troubleshooting guide
- **smtp_settings_table.sql** - Table structure and examples
- **DEPLOYMENT_GUIDE.md** - Full deployment documentation

---

## 🔐 Security Tips

✅ Use App Passwords (Gmail, Yahoo)
✅ Enable 2FA on email accounts
✅ Use TLS/SSL encryption
✅ Don't commit passwords to Git
✅ Restrict database access
✅ Use environment variables in production

---

## 📞 Support

**Files Modified:**
- `onboarding.php` - Email sending with PHPMailer

**Files Created:**
- `smtp_settings_table.sql` - SMTP configuration
- `EMAIL_TROUBLESHOOTING.md` - Troubleshooting guide
- `PHPMAILER_QUICK_REFERENCE.md` - This file

**Status:** ✅ Production Ready

---

**Quick Start:** Install PHPMailer → Import SQL → Configure SMTP → Test! 🚀
