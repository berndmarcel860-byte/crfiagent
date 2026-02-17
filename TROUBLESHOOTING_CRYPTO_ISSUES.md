# Troubleshooting: Cryptocurrency Management Issues

## Problem: "Nothing showing on admin cryptocurrency page and payment methods"

### Quick Fix Checklist

1. **[ ] Apply database migration first!**
   ```bash
   mysql -u username -p database_name < admin/migrations/005_create_crypto_and_network_tables.sql
   ```

2. **[ ] Run test utility**
   - Visit: `http://yoursite.com/test_crypto_tables.php`
   - Should show 10 cryptocurrencies and 26+ networks
   - If tables missing, go back to step 1

3. **[ ] Clear browser cache**
   - Press Ctrl+Shift+Delete (or Cmd+Shift+Delete on Mac)
   - Clear cached files
   - Reload the page

4. **[ ] Check session**
   - Admin page: Must be logged in as admin
   - User page: Must be logged in as user
   - Try logging out and back in

## Issue 1: Admin Cryptocurrency Page Shows Nothing

### Symptoms:
- Page loads but no cryptocurrencies display
- "Failed to load cryptocurrencies" error
- Blank page or loading spinner forever

### Root Causes & Fixes:

**A. Database Tables Don't Exist**
```bash
# Check if tables exist
Visit: test_crypto_tables.php

# If tables missing, run migration:
mysql -u username -p database_name < admin/migrations/005_create_crypto_and_network_tables.sql
```

**B. Wrong Config Path (FIXED)**
- All admin AJAX files now use `../../config.php`
- Update applied in commit ca4089f

**C. Not Logged In as Admin**
```php
// Check admin session
Visit admin login page
Login with admin credentials
Return to crypto management page
```

**D. JavaScript Not Loading**
```javascript
// Check browser console (F12)
// Should NOT see these errors:
- "$ is not defined" (means jQuery not loaded)
- "Failed to load cryptocurrencies" (means AJAX failed)
- 404 errors on admin_ajax files
```

### Test Steps:
1. Visit: `admin/admin_crypto_management.php`
2. Open browser console (F12)
3. Look for errors
4. Should see AJAX call to `admin_ajax/get_all_cryptocurrencies.php`
5. Should display list of cryptocurrencies

## Issue 2: Cannot Add New Cryptocurrency

### Symptoms:
- Click "Add Cryptocurrency" button - nothing happens
- Form submits but no success message
- Page reloads but cryptocurrency not added

### Root Causes & Fixes:

**A. Database Connection Error**
```php
// Check config.php has correct credentials
$host = 'localhost';      // Check this
$dbname = 'your_db';      // Check this
$username = 'your_user';  // Check this
$password = 'your_pass';  // Check this
```

**B. Duplicate Symbol**
- Cannot add cryptocurrency with same symbol (e.g., BTC already exists)
- Use different symbol or delete existing one first

**C. Missing Required Fields**
- Symbol (required)
- Name (required)
- Icon, description optional

### Test Steps:
1. Click "Add Cryptocurrency"
2. Fill form:
   - Symbol: TEST
   - Name: Test Coin
   - Icon: fas fa-coins
   - Description: Test cryptocurrency
3. Click Save
4. Should show success message
5. Should appear in list

## Issue 3: User Dropdown Shows No Cryptocurrencies

### Symptoms:
- Click "Add Crypto Wallet"
- Cryptocurrency dropdown is empty or shows only "Select..."
- Network dropdown stays empty

### Root Causes & Fixes:

**A. No Active Cryptocurrencies**
```sql
-- Check if any cryptos are active
SELECT * FROM cryptocurrencies WHERE is_active = 1;

-- If none, activate some
UPDATE cryptocurrencies SET is_active = 1 WHERE symbol IN ('BTC', 'ETH', 'USDT');
```

**B. User Not Logged In**
- Must have active user session
- Login as user
- Session must have user_id

**C. JavaScript Error**
```javascript
// Check browser console (F12)
// Look for:
console.error('Failed to load cryptocurrencies');

// Test AJAX manually:
Visit: ajax/get_available_cryptocurrencies.php
Should return JSON with cryptocurrencies
```

### Test Steps:
1. Login as user
2. Visit: `payment-methods.php`
3. Click "Add Crypto Wallet"
4. Cryptocurrency dropdown should populate
5. Select "Bitcoin (BTC)"
6. Network dropdown should show "Bitcoin"
7. Select "Tether (USDT)"
8. Network dropdown should show 5 options

## Issue 4: Payment Methods Not Saving

### Symptoms:
- Fill form and click Save
- No success message
- Form stays open
- Method not in list

### Root Causes & Fixes:

**A. Missing created_at Field (FIXED)**
- Already fixed in previous commit
- `ajax/add_payment_method.php` now includes created_at

**B. Validation Errors**
```php
// Common validation issues:
- Empty wallet address
- Invalid IBAN format
- Missing required fields
- Network not selected
```

**C. Database Constraints**
```sql
-- Check table structure
DESCRIBE user_payment_methods;

-- Ensure columns exist:
- type (fiat/crypto)
- cryptocurrency
- network
- wallet_address
- created_at
```

### Test Steps:

**Test Bank Account:**
1. Click "Add Bank Account"
2. Fill:
   - Payment Type: Bank Transfer
   - Label: My Main Bank
   - Account Holder: John Doe
   - Bank Name: Test Bank
   - IBAN: DE89370400440532013000
3. Click Save
4. Should show success message
5. Should appear in bank accounts list

**Test Crypto Wallet:**
1. Click "Add Crypto Wallet"
2. Fill:
   - Cryptocurrency: Bitcoin (BTC)
   - Network: Bitcoin
   - Wallet Address: 1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa
   - Label: My BTC Wallet
3. Click Save
4. Should show success message
5. Should appear in crypto wallets list

## Diagnostic Tools

### 1. Test Utility (test_crypto_tables.php)
```
Visit: http://yoursite.com/test_crypto_tables.php

Expected Output:
=== Test 1: Check cryptocurrencies table ===
✓ Table 'cryptocurrencies' EXISTS
  Rows: 10
  Sample data:
    - BTC: Bitcoin (Active: 1)
    - ETH: Ethereum (Active: 1)

=== Test 2: Check crypto_networks table ===
✓ Table 'crypto_networks' EXISTS
  Rows: 26
```

### 2. Browser Console
```javascript
// Open: F12 or Ctrl+Shift+I
// Go to Console tab
// Look for errors:
- Red error messages
- Failed AJAX calls
- 404 errors
- JavaScript exceptions
```

### 3. Server Error Logs
```bash
# Check PHP error log
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log
# or
tail -f /var/log/php-fpm/error.log
```

### 4. Network Tab (Browser DevTools)
```
1. Open F12
2. Go to Network tab
3. Reload page
4. Look for failed requests (red)
5. Check AJAX calls:
   - admin_ajax/get_all_cryptocurrencies.php
   - ajax/get_available_cryptocurrencies.php
   - ajax/add_payment_method.php
```

## Common Error Messages & Solutions

### "Unauthorized access"
**Cause**: Not logged in or session expired
**Solution**: 
- Login again
- Check session configuration
- Verify session cookies enabled

### "Failed to load cryptocurrencies"
**Cause**: AJAX error or database connection failed
**Solution**:
- Check config.php database credentials
- Run test_crypto_tables.php
- Check server error logs

### "Table 'cryptocurrencies' doesn't exist"
**Cause**: Migration not applied
**Solution**:
```bash
mysql -u username -p database_name < admin/migrations/005_create_crypto_and_network_tables.sql
```

### "Duplicate entry for key 'symbol'"
**Cause**: Cryptocurrency with that symbol already exists
**Solution**:
- Use different symbol
- Or delete existing one first
- Or edit existing one instead

### "Failed to add payment method"
**Cause**: Various - check specific error
**Solution**:
- Check browser console for details
- Verify all required fields filled
- Check database has all columns

## Database Quick Fixes

### Reset Cryptocurrencies
```sql
-- Delete all and reimport
TRUNCATE TABLE crypto_networks;
TRUNCATE TABLE cryptocurrencies;

-- Then run migration to re-seed
SOURCE admin/migrations/005_create_crypto_and_network_tables.sql;
```

### Activate All Cryptocurrencies
```sql
UPDATE cryptocurrencies SET is_active = 1;
UPDATE crypto_networks SET is_active = 1;
```

### Check What's Active
```sql
-- See active cryptos
SELECT symbol, name, is_active FROM cryptocurrencies;

-- See networks per crypto
SELECT c.symbol, cn.network_name, cn.is_active 
FROM cryptocurrencies c 
JOIN crypto_networks cn ON c.id = cn.crypto_id 
ORDER BY c.symbol, cn.network_name;
```

## Still Not Working?

### Final Checklist:
1. [ ] Database migration applied
2. [ ] test_crypto_tables.php shows data
3. [ ] Logged in (admin or user)
4. [ ] Browser cache cleared
5. [ ] No JavaScript errors in console
6. [ ] Config.php has correct database credentials
7. [ ] Server has no errors in logs
8. [ ] Tables have data (check with SQL)
9. [ ] All files have correct permissions
10. [ ] PHP session is working

### Get Help:
If all above checked and still not working:
1. Run test_crypto_tables.php
2. Take screenshot of output
3. Check browser console (F12)
4. Take screenshot of any errors
5. Check server error logs
6. Note exact error messages
7. Verify which step fails

### Contact Support With:
- URL of test_crypto_tables.php output
- Screenshots of browser console errors
- Server error log excerpts
- Which step in testing fails
- Whether you're admin or regular user
- What happens when you try to add crypto/payment method

---

**Last Updated**: 2026-02-17
**Version**: 1.0
**Status**: All known issues have fixes documented
