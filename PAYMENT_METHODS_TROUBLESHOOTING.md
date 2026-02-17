# Payment Methods Troubleshooting Guide

## Quick Diagnosis

**Problem:** Payment methods not working with database

**Quick Fix:** Run the diagnostic tool first!
```
Visit: http://yoursite.com/test_payment_methods.php
```

This will show you exactly what's wrong and how to fix it.

---

## Common Issues & Solutions

### Issue 1: Table Doesn't Exist

**Symptoms:**
- Error: "Table 'user_payment_methods' doesn't exist"
- Can't add payment methods
- Page shows error or nothing

**Solution:**
```bash
mysql -u username -p database_name < admin/migrations/003_enhance_user_payment_methods.sql
```

**What it does:**
- Creates `user_payment_methods` table
- Adds all 31 required columns
- Sets up proper indexes and foreign keys

---

### Issue 2: Missing Columns

**Symptoms:**
- Error: "Unknown column 'verification_status'"
- Error: "Unknown column 'updated_at'"
- Some features don't work

**Solution:**
```bash
# Run additional migrations
mysql -u username -p database_name < admin/migrations/004_add_wallet_verification_system.sql
```

**Or update to latest database:**
```bash
mysql -u username -p database_name < admin/cryptofinanze_updated.sql
```

---

### Issue 3: No Users in Database

**Symptoms:**
- Error: "Cannot add or update a child row: a foreign key constraint fails"
- Error mentions `user_id`

**Cause:**
The `user_payment_methods` table has a foreign key to `users` table. You need at least one user.

**Solution:**
1. Create a user account first
2. Log in with that account
3. Then try adding payment methods

---

### Issue 4: INSERT Query Fails

**Symptoms:**
- Payment method doesn't save
- Browser shows "Failed to add payment method"
- No error message shown

**Diagnosis Steps:**

**1. Check Browser Console (F12):**
```javascript
// Look for red errors
// Check Network tab for AJAX responses
```

**2. Test AJAX Endpoint Directly:**
```javascript
// Open browser console, paste this:
fetch('ajax/add_payment_method.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'type=fiat&payment_method=Bank Transfer&label=Test Bank&account_holder=Test User&bank_name=Test Bank&iban=DE89370400440532013000'
}).then(r => r.json()).then(data => {
    console.log('Response:', data);
    if (!data.success) {
        console.error('Error:', data.message);
    }
});
```

**3. Check PHP Error Logs:**
```bash
# On Linux/Unix
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log

# On XAMPP/WAMP
# Check xampp/apache/logs/error.log
```

---

### Issue 5: Strict SQL Mode Problems

**Symptoms:**
- Error: "Field 'column_name' doesn't have a default value"
- Error: "Data too long for column 'column_name'"
- INSERT queries fail

**Check SQL Mode:**
```sql
SELECT @@sql_mode;
```

**If STRICT_TRANS_TABLES is enabled:**
```sql
-- Temporarily disable (not recommended for production)
SET SESSION sql_mode='';

-- Or add default values to all columns
```

**Better Solution:**
Ensure `add_payment_method.php` includes all required fields with proper defaults.

---

### Issue 6: Session/Login Issues

**Symptoms:**
- Error: "Unauthorized access"
- Redirect to login page
- AJAX returns 401

**Solution:**
1. Make sure you're logged in
2. Check session is active:
```javascript
// In browser console
fetch('ajax/add_payment_method.php', {
    method: 'POST',
    body: 'type=fiat&payment_method=test'
}).then(r => r.json()).then(console.log);

// Should not say "Unauthorized"
```

3. Clear cookies and log in again

---

### Issue 7: Cryptocurrency Dropdown Empty

**Symptoms:**
- Crypto dropdown shows nothing
- Can't select cryptocurrency
- Network dropdown empty

**Solution:**
```bash
# Run crypto tables migration
mysql -u username -p database_name < admin/migrations/005_create_crypto_and_network_tables.sql
```

**Verify:**
```
Visit: http://yoursite.com/debug_crypto_system.php
```

Should show:
- ✓ 10 cryptocurrencies
- ✓ 26+ networks

**If still empty:**
```sql
-- Activate all cryptocurrencies
UPDATE cryptocurrencies SET is_active = 1;
UPDATE crypto_networks SET is_active = 1;
```

---

## Complete Testing Checklist

### Database Tests:
- [ ] Run `test_payment_methods.php`
- [ ] All 7 tests pass (green checkmarks)
- [ ] Table has 31 columns
- [ ] At least 1 user exists
- [ ] MySQL connection works

### User Interface Tests:
- [ ] Visit `payment-methods.php`
- [ ] Can see "Add Bank Account" button
- [ ] Can see "Add Crypto Wallet" button
- [ ] Click button opens modal
- [ ] Crypto dropdown has options
- [ ] Network dropdown updates

### Functionality Tests:
- [ ] Add fiat payment (bank account)
- [ ] Add crypto payment (wallet)
- [ ] Set payment as default
- [ ] Delete payment method
- [ ] See list of payment methods

### AJAX Tests:
- [ ] Browser console (F12) shows no errors
- [ ] Network tab shows successful responses
- [ ] No 401 or 500 errors
- [ ] JSON responses are valid

---

## File Structure Verification

### Required Files:

**Main Page:**
- `payment-methods.php` - User interface

**AJAX Endpoints:**
- `ajax/add_payment_method.php` - Add new method
- `ajax/get_payment_methods.php` - Fetch methods
- `ajax/delete_payment_method.php` - Delete method
- `ajax/set_default_payment_method.php` - Set default
- `ajax/update-payment-method.php` - Update method (if exists)

**Database:**
- `admin/cryptofinanze_updated.sql` - Complete database
- `admin/migrations/003_enhance_user_payment_methods.sql` - Table creation
- `admin/migrations/004_add_wallet_verification_system.sql` - Verification fields
- `admin/migrations/005_create_crypto_and_network_tables.sql` - Crypto support

**Diagnostic Tools:**
- `test_payment_methods.php` - Payment methods tester
- `debug_crypto_system.php` - Crypto system tester

### Check File Permissions:

```bash
# Make sure PHP can read these files
chmod 644 payment-methods.php
chmod 644 ajax/*.php
chmod 644 config.php

# Make sure web server can access
chown www-data:www-data payment-methods.php ajax/*.php
```

---

## Database Structure Reference

### user_payment_methods Table (31 columns):

**Core Fields:**
- `id` INT - Primary key
- `user_id` INT - Foreign key to users table
- `payment_method` VARCHAR(50) - Type name
- `type` ENUM('fiat','crypto') - Payment type
- `is_default` TINYINT(1) - Default flag

**Timestamps:**
- `created_at` TIMESTAMP - Creation time
- `updated_at` TIMESTAMP - Last update (auto)

**Labels:**
- `label` VARCHAR(100) - User-friendly label
- `notes` TEXT - Additional notes

**Fiat Fields:**
- `account_holder` VARCHAR(255)
- `bank_name` VARCHAR(255)
- `iban` VARCHAR(34)
- `bic` VARCHAR(11)
- `account_number` VARCHAR(50)
- `routing_number` VARCHAR(20)
- `sort_code` VARCHAR(10)

**Crypto Fields:**
- `wallet_address` VARCHAR(255)
- `cryptocurrency` VARCHAR(20)
- `network` VARCHAR(50)

**Status Fields:**
- `status` ENUM('active','pending','suspended')
- `is_verified` TINYINT(1)
- `verification_date` TIMESTAMP
- `last_used_at` TIMESTAMP

**Verification Fields:**
- `verification_status` ENUM('pending','verifying','verified','failed')
- `verification_amount` DECIMAL(20,10)
- `verification_address` VARCHAR(255)
- `verification_txid` VARCHAR(255)
- `verification_requested_at` TIMESTAMP
- `verified_by` INT
- `verified_at` TIMESTAMP
- `verification_notes` TEXT

---

## Support Resources

### Diagnostic Tools:
1. **test_payment_methods.php** - Main diagnostic
2. **debug_crypto_system.php** - Crypto diagnostics
3. **test_crypto_tables.php** - Basic crypto check

### Documentation:
1. **PAYMENT_METHODS_TROUBLESHOOTING.md** - This file
2. **CRYPTO_DROPDOWN_FIX_GUIDE.md** - Crypto dropdown issues
3. **DATABASE_README.md** - Database structure
4. **START_HERE_CRYPTO_FIX.md** - Quick start

### Quick Commands:

**Import Fresh Database:**
```bash
mysql -u username -p database_name < admin/cryptofinanze_updated.sql
```

**Run All Migrations:**
```bash
mysql -u username -p database_name < admin/migrations/003_enhance_user_payment_methods.sql
mysql -u username -p database_name < admin/migrations/004_add_wallet_verification_system.sql
mysql -u username -p database_name < admin/migrations/005_create_crypto_and_network_tables.sql
```

**Check Table Structure:**
```sql
DESCRIBE user_payment_methods;
SELECT COUNT(*) FROM user_payment_methods;
SELECT * FROM user_payment_methods LIMIT 5;
```

**Activate All Cryptos:**
```sql
UPDATE cryptocurrencies SET is_active = 1;
UPDATE crypto_networks SET is_active = 1;
```

---

## Still Not Working?

If you've tried everything and payment methods still don't work:

### Gather This Information:

1. **Run diagnostic:**
   ```
   Visit: test_payment_methods.php
   Take screenshot
   ```

2. **Browser console:**
   ```
   Press F12
   Go to Console tab
   Take screenshot of any errors
   ```

3. **Network tab:**
   ```
   Press F12
   Go to Network tab
   Try adding payment method
   Click on failed request
   Take screenshot of response
   ```

4. **PHP errors:**
   ```
   Check error logs
   Copy any relevant errors
   ```

5. **Database info:**
   ```sql
   SELECT VERSION();
   SELECT @@sql_mode;
   SHOW TABLES LIKE '%payment%';
   DESCRIBE user_payment_methods;
   ```

### Then:
- Check all documentation guides
- Review migration files
- Compare with working examples
- Test on fresh database

---

## Success Indicators

You'll know it's working when:

✅ test_payment_methods.php shows all green checkmarks
✅ payment-methods.php page loads without errors
✅ Crypto dropdown has 10+ options
✅ Network dropdown updates when crypto selected
✅ Can add bank account successfully
✅ Can add crypto wallet successfully
✅ Payment methods appear in list
✅ Can set default method
✅ Can delete payment methods
✅ Browser console has no errors
✅ AJAX requests return success=true

---

**Last Updated:** February 17, 2026
**Status:** Complete troubleshooting guide
**Tool:** test_payment_methods.php
